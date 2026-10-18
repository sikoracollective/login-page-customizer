<?php
/**
 * Shared helpers for Sikorix Login Page Customizer integration tests.
 *
 * @package SikorixLoginPageCustomizer
 */

/**
 * Base test case with attachment fixture helpers.
 */
class Sikorix_Login_Test_Case extends WP_UnitTestCase {

	/**
	 * Creates a Media Library attachment from a fixture file.
	 *
	 * @param string $fixture_filename Filename under tests/fixtures/.
	 * @param string $mime_type        MIME type stored on the attachment post.
	 * @return int Attachment ID.
	 */
	protected function create_attachment_from_fixture( $fixture_filename, $mime_type ) {
		$source = TESTS_FIXTURES_DIR . '/' . $fixture_filename;
		$this->assertFileExists( $source );

		$upload_dir = wp_upload_dir();
		$this->assertEmpty( $upload_dir['error'], 'Uploads directory is not available.' );

		$dest = trailingslashit( $upload_dir['path'] ) . wp_unique_filename( $upload_dir['path'], $fixture_filename );
		$this->assertTrue( copy( $source, $dest ), 'Failed to copy fixture into uploads.' );

		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => $mime_type,
				'post_title'     => sanitize_file_name( $fixture_filename ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$dest
		);

		$this->assertNotWPError( $attachment_id );
		$this->assertGreaterThan( 0, (int) $attachment_id );

		return (int) $attachment_id;
	}

	/**
	 * Creates a valid PNG attachment for background/logo tests.
	 *
	 * @return int Attachment ID.
	 */
	protected function create_valid_png_attachment() {
		return $this->create_attachment_from_fixture( 'valid.png', 'image/png' );
	}

	/**
	 * Creates an SVG file labeled as JPEG (metadata lies; contents do not).
	 *
	 * @return int Attachment ID.
	 */
	protected function create_fake_jpeg_attachment() {
		return $this->create_attachment_from_fixture( 'fake-jpeg.jpg', 'image/jpeg' );
	}
}
