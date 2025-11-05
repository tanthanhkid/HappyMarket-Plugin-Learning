<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    HappyMarket_Learning
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Xóa options
delete_option( 'hm_default_access_type' );
delete_option( 'hm_default_ad_position' );
delete_option( 'hm_youtube_api_key' );
delete_option( 'hm_enable_analytics' );
delete_option( 'hm_enable_woocommerce' );
delete_option( 'hm_default_product_position' );
delete_option( 'hm_default_product_columns' );
delete_option( 'hm_default_product_limit' );
delete_option( 'hm_show_price' );
delete_option( 'hm_show_add_to_cart' );

// Xóa transients
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_hm_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_hm_%'" );

// Note: Không xóa posts và meta data để giữ lại dữ liệu của user
