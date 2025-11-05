<?php
/**
 * WooCommerce Integration
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/integrations
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * WooCommerce Integration Class
 */
class HM_WooCommerce {

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Add hooks here if needed
		add_action( 'hm_lesson_product_added_to_cart', array( $this, 'track_product_added' ), 10, 3 );
	}

	/**
	 * Track product added to cart from lesson
	 *
	 * @param int $product_id Product ID.
	 * @param int $lesson_id  Lesson ID.
	 * @param int $user_id    User ID.
	 */
	public function track_product_added( $product_id, $lesson_id, $user_id ) {
		// Track analytics if enabled
		if ( get_option( 'hm_enable_analytics', false ) ) {
			// Implementation for tracking
			do_action( 'hm_analytics_product_added', $product_id, $lesson_id, $user_id );
		}
	}
}
