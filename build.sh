#!/usr/bin/env bash
# Build an installable WordPress plugin ZIP in the project root.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Prefer the main plugin file whose basename matches Text Domain.
MAIN_FILE="$(
	find "${ROOT_DIR}" -maxdepth 1 -type f -name '*.php' -print \
		| while IFS= read -r candidate; do
			if grep -Eq '^[[:space:]]*\*[[:space:]]*Plugin Name:' "${candidate}" \
				&& grep -Eq '^[[:space:]]*\*[[:space:]]*Text Domain:' "${candidate}"; then
				echo "${candidate}"
				break
			fi
		done
)"

if [[ -z "${MAIN_FILE}" || ! -f "${MAIN_FILE}" ]]; then
	echo "error: could not find main plugin PHP file with Plugin Name and Text Domain headers" >&2
	exit 1
fi

# Slug comes from Text Domain so the ZIP always matches the plugin identity.
PLUGIN_SLUG="$(
	grep -E '^[[:space:]]*\*[[:space:]]*Text Domain:[[:space:]]*' "${MAIN_FILE}" \
		| head -n 1 \
		| sed -E 's/^[[:space:]]*\*[[:space:]]*Text Domain:[[:space:]]*//' \
		| tr -d '[:space:]'
)"

if [[ -z "${PLUGIN_SLUG}" ]]; then
	echo "error: could not read Text Domain from ${MAIN_FILE}" >&2
	exit 1
fi

EXPECTED_MAIN="${ROOT_DIR}/${PLUGIN_SLUG}.php"
if [[ "${MAIN_FILE}" != "${EXPECTED_MAIN}" ]]; then
	echo "error: main file must be named ${PLUGIN_SLUG}.php (found $(basename "${MAIN_FILE}"))" >&2
	exit 1
fi

# Read Version from the plugin header.
VERSION="$(
	grep -E '^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*' "${MAIN_FILE}" \
		| head -n 1 \
		| sed -E 's/^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*//' \
		| tr -d '[:space:]'
)"

if [[ -z "${VERSION}" ]]; then
	echo "error: could not read Version from ${MAIN_FILE}" >&2
	exit 1
fi

ZIP_NAME="${PLUGIN_SLUG}.zip"
ZIP_PATH="${ROOT_DIR}/${ZIP_NAME}"
BUILD_DIR="$(mktemp -d "${TMPDIR:-/tmp}/${PLUGIN_SLUG}-build.XXXXXX")"
STAGE_DIR="${BUILD_DIR}/${PLUGIN_SLUG}"

cleanup() {
	rm -rf "${BUILD_DIR}"
}
trap cleanup EXIT

mkdir -p "${STAGE_DIR}/assets"

# Only the files required for the distributable plugin.
# Intentionally omits tests/, README.md, build.sh, and Composer files.
INCLUDE_FILES=(
	"${PLUGIN_SLUG}.php"
	"uninstall.php"
	"readme.txt"
	"assets/admin.js"
	"assets/login.css"
)

for path in "${INCLUDE_FILES[@]}"; do
	src="${ROOT_DIR}/${path}"
	if [[ ! -f "${src}" ]]; then
		echo "error: required file missing: ${src}" >&2
		exit 1
	fi
	cp "${src}" "${STAGE_DIR}/${path}"
done

rm -f "${ZIP_PATH}"

(
	cd "${BUILD_DIR}"
	zip -r "${ZIP_PATH}" "${PLUGIN_SLUG}" >/dev/null
)

echo "Created ${ZIP_NAME} (version ${VERSION})"
FILE_COUNT="$(unzip -Z1 "${ZIP_PATH}" | grep -cv '/$' || true)"
echo "Files included: ${FILE_COUNT}"
unzip -Z1 "${ZIP_PATH}" | sort | while IFS= read -r entry; do
	# Skip directory-only entries for a cleaner file list.
	[[ "${entry}" == */ ]] && continue
	# Strip the plugin root directory from the printed path.
	echo "  ${entry#"${PLUGIN_SLUG}/"}"
done
