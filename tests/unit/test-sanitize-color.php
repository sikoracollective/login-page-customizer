<?php
/**
 * Tests for background color sanitization and luminance helpers.
 *
 * @package LoginPageCustomizer
 */

/**
 * Color helper coverage.
 */
class Sanitize_Color_Test extends WP_UnitTestCase {

	/**
	 * @return void
	 */
	public function set_up() {
		parent::set_up();
		delete_option( SIKORA_LOGIN_BG_COLOR_OPTION );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_color_accepts_hex() {
		$this->assertSame( '#1d2327', sikora_sanitize_bg_color( '#1d2327' ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_color_adds_missing_hash() {
		$this->assertSame( '#abcdef', sikora_sanitize_bg_color( 'abcdef' ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_color_clears_on_empty_string() {
		update_option( SIKORA_LOGIN_BG_COLOR_OPTION, '#eaeaea', false );
		$this->assertSame( '', sikora_sanitize_bg_color( '' ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_color_keeps_previous_when_field_omitted() {
		update_option( SIKORA_LOGIN_BG_COLOR_OPTION, '#112233', false );
		$this->assertSame( '#112233', sikora_sanitize_bg_color( null ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_color_keeps_previous_on_invalid_value() {
		update_option( SIKORA_LOGIN_BG_COLOR_OPTION, '#112233', false );
		$this->assertSame( '#112233', sikora_sanitize_bg_color( 'not-a-color' ) );
	}

	/**
	 * @return void
	 */
	public function test_get_bg_color_returns_default_when_unset() {
		$this->assertSame( SIKORA_LOGIN_BG_COLOR_DEFAULT, sikora_login_get_bg_color() );
	}

	/**
	 * @return void
	 */
	public function test_get_bg_color_returns_empty_when_cleared() {
		update_option( SIKORA_LOGIN_BG_COLOR_OPTION, '', false );
		$this->assertSame( '', sikora_login_get_bg_color() );
	}

	/**
	 * @return void
	 */
	public function test_is_dark_color_detects_dark_and_light() {
		$this->assertTrue( sikora_login_is_dark_color( '#000000' ) );
		$this->assertTrue( sikora_login_is_dark_color( '#1d2327' ) );
		$this->assertFalse( sikora_login_is_dark_color( '#ffffff' ) );
		$this->assertFalse( sikora_login_is_dark_color( '#eaeaea' ) );
	}

	/**
	 * @return void
	 */
	public function test_is_dark_color_supports_shorthand_hex() {
		$this->assertTrue( sikora_login_is_dark_color( '#000' ) );
		$this->assertFalse( sikora_login_is_dark_color( '#fff' ) );
	}

	/**
	 * @return void
	 */
	public function test_is_dark_color_rejects_invalid_input() {
		$this->assertFalse( sikora_login_is_dark_color( '' ) );
		$this->assertFalse( sikora_login_is_dark_color( 'red' ) );
		$this->assertFalse( sikora_login_is_dark_color( '#gg0000' ) );
	}
}
