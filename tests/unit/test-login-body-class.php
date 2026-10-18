<?php
/**
 * Tests for login body class filters.
 *
 * @package SikorixLoginPageCustomizer
 */

/**
 * Login body class coverage.
 */
class Login_Body_Class_Test extends Sikorix_Login_Test_Case {

	/**
	 * @return void
	 */
	public function set_up() {
		parent::set_up();
		update_option( SIKORIX_LOGIN_DISABLE_RESET_OPTION, 0, false );
		update_option( SIKORIX_LOGIN_BG_OPTION, 0, false );
		update_option( SIKORIX_LOGIN_LOGO_OPTION, 0, false );
		update_option( SIKORIX_LOGIN_BG_COLOR_OPTION, '#eaeaea', false );
	}

	/**
	 * @return void
	 */
	public function test_password_reset_disabled_class() {
		update_option( SIKORIX_LOGIN_DISABLE_RESET_OPTION, 1, false );
		$classes = sikorix_login_body_class( array() );

		$this->assertContains( 'sikorix-password-reset-disabled', $classes );
	}

	/**
	 * @return void
	 */
	public function test_has_bg_and_light_links_when_background_image_set() {
		$id = $this->create_valid_png_attachment();
		update_option( SIKORIX_LOGIN_BG_OPTION, $id, false );

		$classes = sikorix_login_body_class( array() );

		$this->assertContains( 'sikorix-has-bg', $classes );
		$this->assertContains( 'sikorix-light-links', $classes );
	}

	/**
	 * @return void
	 */
	public function test_has_logo_class_when_logo_set() {
		$id = $this->create_valid_png_attachment();
		update_option( SIKORIX_LOGIN_LOGO_OPTION, $id, false );

		$classes = sikorix_login_body_class( array() );

		$this->assertContains( 'sikorix-has-logo', $classes );
	}

	/**
	 * @return void
	 */
	public function test_light_links_for_dark_background_color_without_image() {
		update_option( SIKORIX_LOGIN_BG_COLOR_OPTION, '#000000', false );
		$classes = sikorix_login_body_class( array() );

		$this->assertContains( 'sikorix-light-links', $classes );
		$this->assertNotContains( 'sikorix-has-bg', $classes );
	}

	/**
	 * @return void
	 */
	public function test_no_light_links_for_light_background_color_without_image() {
		update_option( SIKORIX_LOGIN_BG_COLOR_OPTION, '#eaeaea', false );
		$classes = sikorix_login_body_class( array() );

		$this->assertNotContains( 'sikorix-light-links', $classes );
		$this->assertNotContains( 'sikorix-has-bg', $classes );
	}
}
