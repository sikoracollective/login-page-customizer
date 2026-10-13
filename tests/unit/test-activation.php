<?php
/**
 * Tests for activation and option defaults.
 *
 * @package LoginPageCustomizer
 */

/**
 * Activation / defaults coverage.
 */
class Activation_Test extends Sikora_Login_Test_Case {

	/**
	 * @return void
	 */
	public function set_up() {
		parent::set_up();
		delete_option( SIKORA_LOGIN_BG_COLOR_OPTION );
		delete_option( SIKORA_LOGIN_DISABLE_RESET_OPTION );
		delete_option( SIKORA_LOGIN_BG_OPTION );
		delete_option( SIKORA_LOGIN_LOGO_OPTION );
		delete_transient( SIKORA_LOGIN_INVALID_BG_NOTICE );
	}

	/**
	 * @return void
	 */
	public function test_store_bg_color_default_creates_row() {
		$this->assertTrue( sikora_login_store_bg_color_default() );
		$this->assertSame( SIKORA_LOGIN_BG_COLOR_DEFAULT, get_option( SIKORA_LOGIN_BG_COLOR_OPTION ) );
		$this->assertFalse( sikora_login_store_bg_color_default(), 'Second call should not recreate the row.' );
	}

	/**
	 * @return void
	 */
	public function test_store_reset_option_default_creates_row() {
		$this->assertTrue( sikora_login_store_reset_option_default() );
		$this->assertSame( 0, (int) get_option( SIKORA_LOGIN_DISABLE_RESET_OPTION ) );
		$this->assertFalse( sikora_login_store_reset_option_default() );
	}

	/**
	 * @return void
	 */
	public function test_activate_seeds_defaults() {
		sikora_login_activate();

		$this->assertSame( SIKORA_LOGIN_BG_COLOR_DEFAULT, get_option( SIKORA_LOGIN_BG_COLOR_OPTION ) );
		$this->assertSame( 0, (int) get_option( SIKORA_LOGIN_DISABLE_RESET_OPTION ) );
	}

	/**
	 * @return void
	 */
	public function test_bootstrap_option_defaults_on_admin_init() {
		sikora_login_bootstrap_option_defaults();

		$this->assertSame( SIKORA_LOGIN_BG_COLOR_DEFAULT, get_option( SIKORA_LOGIN_BG_COLOR_OPTION ) );
		$this->assertSame( 0, (int) get_option( SIKORA_LOGIN_DISABLE_RESET_OPTION ) );
	}

	/**
	 * @return void
	 */
	public function test_clear_invalid_bg_option_removes_bad_attachment() {
		$id = $this->create_fake_jpeg_attachment();
		update_option( SIKORA_LOGIN_BG_OPTION, $id, false );

		sikora_login_clear_invalid_bg_option();

		$this->assertSame( 0, (int) get_option( SIKORA_LOGIN_BG_OPTION ) );
		$this->assertNotFalse( get_transient( SIKORA_LOGIN_INVALID_BG_NOTICE ) );
	}

	/**
	 * @return void
	 */
	public function test_clear_invalid_bg_option_keeps_valid_attachment() {
		$id = $this->create_valid_png_attachment();
		update_option( SIKORA_LOGIN_BG_OPTION, $id, false );

		sikora_login_clear_invalid_bg_option();

		$this->assertSame( $id, (int) get_option( SIKORA_LOGIN_BG_OPTION ) );
		$this->assertFalse( get_transient( SIKORA_LOGIN_INVALID_BG_NOTICE ) );
	}

	/**
	 * @return void
	 */
	public function test_clear_invalid_logo_option_removes_bad_attachment() {
		$id = $this->create_fake_jpeg_attachment();
		update_option( SIKORA_LOGIN_LOGO_OPTION, $id, false );

		sikora_login_clear_invalid_logo_option();

		$this->assertSame( 0, (int) get_option( SIKORA_LOGIN_LOGO_OPTION ) );
	}
}
