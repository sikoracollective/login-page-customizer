<?php
/**
 * Tests for background/logo image validation and sanitization.
 *
 * @package LoginPageCustomizer
 */

/**
 * Image sanitizer coverage.
 */
class Sanitize_Images_Test extends Sikora_Login_Test_Case {

	/**
	 * @return void
	 */
	public function test_allowed_png_attachment_passes_validation() {
		$id = $this->create_valid_png_attachment();
		$this->assertTrue( sikora_is_allowed_bg_image( $id ) );
	}

	/**
	 * @return void
	 */
	public function test_zero_attachment_id_is_rejected() {
		$this->assertFalse( sikora_is_allowed_bg_image( 0 ) );
	}

	/**
	 * @return void
	 */
	public function test_missing_attachment_is_rejected() {
		$this->assertFalse( sikora_is_allowed_bg_image( 999999 ) );
	}

	/**
	 * @return void
	 */
	public function test_svg_disguised_as_jpeg_is_rejected() {
		$id = $this->create_fake_jpeg_attachment();
		$this->assertFalse( sikora_is_allowed_bg_image( $id ) );
	}

	/**
	 * @return void
	 */
	public function test_trashed_attachment_is_rejected() {
		$id = $this->create_valid_png_attachment();
		wp_trash_post( $id );
		$this->assertFalse( sikora_is_allowed_bg_image( $id ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_image_keeps_previous_when_field_omitted() {
		$id = $this->create_valid_png_attachment();
		update_option( SIKORA_LOGIN_BG_OPTION, $id, false );

		$this->assertSame( $id, sikora_sanitize_bg_image( null ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_image_clears_on_explicit_zero() {
		$id = $this->create_valid_png_attachment();
		update_option( SIKORA_LOGIN_BG_OPTION, $id, false );

		$this->assertSame( 0, sikora_sanitize_bg_image( 0 ) );
		$this->assertSame( 0, sikora_sanitize_bg_image( '0' ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_image_accepts_valid_attachment() {
		$id = $this->create_valid_png_attachment();
		$this->assertSame( $id, sikora_sanitize_bg_image( $id ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_bg_image_keeps_previous_when_new_value_invalid() {
		$valid = $this->create_valid_png_attachment();
		$fake  = $this->create_fake_jpeg_attachment();
		update_option( SIKORA_LOGIN_BG_OPTION, $valid, false );

		$this->assertSame( $valid, sikora_sanitize_bg_image( $fake ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_logo_image_keeps_previous_when_field_omitted() {
		$id = $this->create_valid_png_attachment();
		update_option( SIKORA_LOGIN_LOGO_OPTION, $id, false );

		$this->assertSame( $id, sikora_sanitize_logo_image( null ) );
	}

	/**
	 * @return void
	 */
	public function test_sanitize_logo_image_clears_on_explicit_zero() {
		$id = $this->create_valid_png_attachment();
		update_option( SIKORA_LOGIN_LOGO_OPTION, $id, false );

		$this->assertSame( 0, sikora_sanitize_logo_image( 0 ) );
	}

	/**
	 * @return void
	 */
	public function test_validated_bg_url_returns_empty_for_invalid_image() {
		$fake = $this->create_fake_jpeg_attachment();
		$this->assertSame( '', sikora_get_validated_bg_url( $fake ) );
	}

	/**
	 * @return void
	 */
	public function test_validated_bg_url_returns_url_for_valid_image() {
		$id  = $this->create_valid_png_attachment();
		$url = sikora_get_validated_bg_url( $id );

		$this->assertNotSame( '', $url );
		$this->assertStringContainsString( 'http', $url );
	}
}
