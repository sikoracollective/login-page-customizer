<?php
/**
 * Uninstall cleanup for Sikorix Login Page Customizer.
 *
 * Fired when the plugin is deleted via the WordPress admin.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'sikorix_login_logo_image' );
delete_option( 'sikorix_login_bg_image' );
delete_option( 'sikorix_login_bg_color' );
delete_option( 'sikorix_login_disable_password_reset' );
delete_transient( 'sikorix_login_invalid_bg_notice' );

// Multisite: remove options from each site.
if ( is_multisite() ) {
    $sikorix_login_site_ids = get_sites( array( 'fields' => 'ids', 'number' => 0 ) );

    foreach ( $sikorix_login_site_ids as $sikorix_login_site_id ) {
        switch_to_blog( (int) $sikorix_login_site_id );
        delete_option( 'sikorix_login_logo_image' );
        delete_option( 'sikorix_login_bg_image' );
        delete_option( 'sikorix_login_bg_color' );
        delete_option( 'sikorix_login_disable_password_reset' );
        delete_transient( 'sikorix_login_invalid_bg_notice' );
        restore_current_blog();
    }
}
