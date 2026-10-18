<?php
/**
 * PHPUnit bootstrap for Sikorix Login Page Customizer.
 *
 * @package SikorixLoginPageCustomizer
 */

define( 'TESTS_PLUGIN_DIR', dirname( __DIR__ ) );
define( 'TESTS_FIXTURES_DIR', __DIR__ . '/fixtures' );

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! defined( 'WP_CORE_DIR' ) ) {
	$_wp_core_dir = getenv( 'WP_CORE_DIR' );
	if ( ! $_wp_core_dir ) {
		$_wp_core_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress';
	}
	define( 'WP_CORE_DIR', $_wp_core_dir );
}

// Composer autoload (PHPUnit polyfills, etc.).
$autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run composer test-install?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin under test.
 *
 * @return void
 */
function sikorix_login_tests_manually_load_plugin() {
	require TESTS_PLUGIN_DIR . '/sikorix-login-page-customizer.php';
}
tests_add_filter( 'muplugins_loaded', 'sikorix_login_tests_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

require_once __DIR__ . '/includes/class-sikorix-login-test-case.php';
require_once __DIR__ . '/includes/class-sikorix-login-result-printer.php';
