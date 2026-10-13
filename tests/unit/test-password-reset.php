<?php
/**
 * Tests for password-reset option sanitization and login filters.
 *
 * @package LoginPageCustomizer
 */

/**
 * Password reset coverage.
 */
class Password_Reset_Test extends WP_UnitTestCase {

	/**
	 * @return void
	 */
	public function set_up() {
		parent::set_up();
		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 0, false );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_disable_password_reset_checked() {
		$this->assertSame( 1, sikora_sanitize_disable_password_reset( '1' ) );
		$this->assertSame( 1, sikora_sanitize_disable_password_reset( 1 ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_disable_password_reset_unchecked() {
		$this->assertSame( 0, sikora_sanitize_disable_password_reset( '0' ) );
		$this->assertSame( 0, sikora_sanitize_disable_password_reset( '' ) );
		$this->assertSame( 0, sikora_sanitize_disable_password_reset( 'yes' ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_disable_password_reset_keeps_previous_when_omitted() {
		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 1, false );
		$this->assertSame( 1, sikora_sanitize_disable_password_reset( null ) );

		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 0, false );
		$this->assertSame( 0, sikora_sanitize_disable_password_reset( null ) );
	}

	/**
	 * @return void
	 */
	public function test_is_password_reset_disabled_reads_option() {
		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 0, false );
		$this->assertFalse( sikora_login_is_password_reset_disabled() );

		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 1, false );
		$this->assertTrue( sikora_login_is_password_reset_disabled() );
	}

	/**
	 * @return void
	 */
	public function test_lost_password_link_removed_when_disabled() {
		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 1, false );
		$link = '<a href="https://example.com/wp-login.php?action=lostpassword">Lost?</a>';

		$this->assertSame( '', sikora_login_filter_lost_password_html_link( $link ) );
	}

	/**
	 * @return void
	 */
	public function test_lost_password_link_preserved_when_enabled() {
		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 0, false );
		$link = '<a href="https://example.com/wp-login.php?action=lostpassword">Lost?</a>';

		$this->assertSame( $link, sikora_login_filter_lost_password_html_link( $link ) );
	}

	/**
	 * @return void
	 */
	public function test_login_link_separator_removed_when_reset_disabled() {
		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 1, false );
		$this->assertSame( '', sikora_login_filter_login_link_separator( ' | ' ) );
	}

	/**
	 * @return void
	 */
	public function test_login_link_separator_preserved_when_reset_enabled() {
		update_option( SIKORA_LOGIN_DISABLE_RESET_OPTION, 0, false );
		$this->assertSame( ' | ', sikora_login_filter_login_link_separator( ' | ' ) );
	}
}
