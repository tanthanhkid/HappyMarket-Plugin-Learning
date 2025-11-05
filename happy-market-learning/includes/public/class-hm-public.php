<?php
/**
 * Public functionality
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/public
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Public Class
 */
class HM_Public {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Access control hooks
		add_action( 'template_redirect', array( $this, 'check_lesson_access' ) );
	}
	/**
	 * Check lesson access
	 */
	public function check_lesson_access() {
		if ( ! is_singular( 'hm_lesson' ) ) {
			return;
		}

		global $post;
		require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';

		$has_access = HM_Helpers::check_lesson_access( $post->ID );

		if ( ! $has_access ) {
			$redirect_url = apply_filters( 'hm_lesson_access_redirect', wp_login_url( get_permalink() ), $post->ID );
			wp_redirect( $redirect_url );
			exit;
		}

		// Track lesson view
		do_action( 'hm_lesson_viewed', $post->ID, get_current_user_id() );
	}
}
