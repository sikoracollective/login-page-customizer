# Login Page Customizer tests

PHPUnit integration tests for the plugin. This directory is **not** included in the distributable ZIP (`build.sh` only packs plugin runtime files).

## Requirements

| Tool | Purpose |
| --- | --- |
| **PHP 7.4+** | Runs PHPUnit and the plugin under test |
| **Composer** | Installs PHPUnit and other test packages into `vendor/` |
| **MySQL or MariaDB** | WordPress integration tests need a real database |
| **Subversion (`svn`)** | Used by `install-wp-tests.sh` to download the WP test suite |

On macOS with Homebrew:

```bash
brew install composer mysql svn
brew services start mysql
```

To stop MySQL later:

```bash
brew services stop mysql
```

## MySQL database and user

The Composer `test-install` script defaults to:

| Setting | Value |
| --- | --- |
| Database | `wordpress_test` |
| User | `root` |
| Password | `root` |
| Host | `127.0.0.1` |

**1. Connect as root** (Homebrew often has no password at first):

```bash
mysql -u root
```

If that fails:

```bash
mysql -u root -p
```

**2. In the MySQL prompt:**

```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'root';
CREATE DATABASE IF NOT EXISTS wordpress_test;
FLUSH PRIVILEGES;
EXIT;
```

**3. Confirm it works:**

```bash
mysql -u root -proot -h 127.0.0.1 -e "SHOW DATABASES;"
```

If you prefer to keep root passwordless, skip the `ALTER USER` step and change the `test-install` script in `composer.json` (or run `bin/install-wp-tests.sh` directly) with an empty password.

## Install the WordPress test suite and run tests

From this directory:

```bash
composer install
composer test-install
composer test
```

`test-install` creates/resets the WordPress test tables in `wordpress_test`. Override credentials by editing the `test-install` script in `composer.json` or by running `bin/install-wp-tests.sh` directly.

While tests run, each result is printed to the screen as `VERDICT Class::method` (for example `PASS Sanitize_Color_Test::test_sanitize_bg_color_accepts_hex`). A tab-separated log is written to `logs/results.log` with columns: **verdict**, **filename**, **test method** (`PASS`, `FAIL`, `ERROR`, etc.). That `logs/` directory is gitignored.

## Layout

```
tests/
├── unit/                 # WP_UnitTestCase suites
├── includes/             # Shared test helpers and logger
├── fixtures/             # Sample images used by attachment tests
├── logs/                 # results.log (created at runtime, gitignored)
├── bin/install-wp-tests.sh
├── bootstrap.php
├── composer.json
└── phpunit.xml.dist
```
