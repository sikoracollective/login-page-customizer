<?php
/**
 * Tests for activation and option defaults.
 *
 * @package SikorixLoginPageCustomizer
 */

/**
 * Activation / defaults coverage.
 */
class Activation_Test extends Sikorix_Login_Test_Case {

	/**
	 * @return void
	 */
	public function set_up() {
		parent::set_up();
		delete_option( SIKORIX_LOGIN_BG_COLOR_OPTION );
		delete_option( SIKORIX_LOGIN_DISABLE_RESET_OPTION );
		delete_option( SIKORIX_LOGIN_BG_OPTION );
		delete_option( SIKORIX_LOGIN_LOGO_OPTION );
		delete_transient( SIKORIX_LOGIN_INVALID_BG_NOTICE );
	}

	/**
	 * @return void
	 */
	public function test_store_bg_color_default_creates_row() {
		$this->assertTrue( sikorix_login_store_bg_color_default() );
		$this->assertSame( SIKORIX_LOGIN_BG_COLOR_DEFAULT, get_option( SIKORIX_LOGIN_BG_COLOR_OPTION ) );
		$this->assertFalse( sikorix_login_store_bg_color_default(), 'Second call should not recreate the row.' );
	}

	/**
	 * @return void
	 */
	public function test_store_reset_option_default_creates_row() {
		$this->assertTrue( sikorix_login_store_reset_option_default() );
		$this->assertSame( 0, (int) get_option( SIKORIX_LOGIN_DISABLE_RESET_OPTION ) );
		$this->assertFalse( sikorix_login_store_reset_option_default() );
	}

	/**
	 * @return void
	 */
	public function test_activate_seeds_defaults() {
		sikorix_login_activate();

		$this->assertSame( SIKORIX_LOGIN_BG_COLOR_DEFAULT, get_option( SIKORIX_LOGIN_BG_COLOR_OPTION ) );
		$this->assertSame( 0, (int) get_option( SIKORIX_LOGIN_DISABLE_RESET_OPTION ) );
	}

	/**
	 * @return void
	 */
	public function test_bootstrap_option_defaults_on_admin_init() {
		sikorix_login_bootstrap_option_defaults();

		$this->assertSame( SIKORIX_LOGIN_BG_COLOR_DEFAULT, get_option( SIKORIX_LOGIN_BG_COLOR_OPTION ) );
		$this->assertSame( 0, (int) get_option( SIKORIX_LOGIN_DISABLE_RESET_OPTION ) );
	}

	/**
	 * @return void
	 */
	public function test_clear_invalid_bg_option_removes_bad_attachment() {
		$id = $this->create_fake_jpeg_attachment();
		update_option( SIKORIX_LOGIN_BG_OPTION, $id, false );

		sikorix_login_clear_invalid_bg_option();

		$this->assertSame( 0, (int) get_option( SIKORIX_LOGIN_BG_OPTION ) );
		$this->assertNotFalse( get_transient( SIKORIX_LOGIN_INVALID_BG_NOTICE ) );
	}

	/**
	 * @return void
	 */
	public function test_clear_invalid_bg_option_keeps_valid_attachment() {
		$id = $this->create_valid_png_attachment();
		update_option( SIKORIX_LOGIN_BG_OPTION, $id, false );

		sikorix_login_clear_invalid_bg_option();

		$this->assertSame( $id, (int) get_option( SIKORIX_LOGIN_BG_OPTION ) );
		$this->assertFalse( get_transient( SIKORIX_LOGIN_INVALID_BG_NOTICE ) );
	}

	/**
	 * @return void
	 */
	public function test_clear_invalid_logo_option_removes_bad_attachment() {
		$id = $this->create_fake_jpeg_attachment();
		update_option( SIKORIX_LOGIN_LOGO_OPTION, $id, false );

		sikorix_login_clear_invalid_logo_option();

		$this->assertSame( 0, (int) get_option( SIKORIX_LOGIN_LOGO_OPTION ) );
	}
}
