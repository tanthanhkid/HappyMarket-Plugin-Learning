<?php
/**
 * Fired during plugin activation
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Fired during plugin activation.
 *
 * Class này định nghĩa tất cả code cần thiết chạy trong quá trình activation của plugin.
 */
class HM_Activator {

	/**
	 * Activation hook.
	 *
	 * Tạo các database tables (nếu cần), set default options, etc.
	 */
	public static function activate() {
		// Flush rewrite rules để register custom post types
		flush_rewrite_rules();

		// Set default options
		$default_options = array(
			'hm_default_access_type'     => 'public',
			'hm_default_ad_position'     => 'sidebar',
			'hm_youtube_api_key'         => '',
			'hm_enable_analytics'        => false,
			'hm_enable_woocommerce'      => false,
			'hm_default_product_position' => 'after_video',
			'hm_default_product_columns' => 3,
			'hm_default_product_limit'   => 4,
			'hm_show_price'              => true,
			'hm_show_add_to_cart'        => true,
		);

		foreach ( $default_options as $key => $value ) {
			if ( false === get_option( $key ) ) {
				add_option( $key, $value );
			}
		}

		// Set default capabilities
		$role = get_role( 'administrator' );
		if ( $role ) {
			$role->add_cap( 'manage_hm_series' );
			$role->add_cap( 'manage_hm_lessons' );
			$role->add_cap( 'manage_hm_settings' );
		}
	}
}
