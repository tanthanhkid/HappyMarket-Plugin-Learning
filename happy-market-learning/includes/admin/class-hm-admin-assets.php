<?php
/**
 * Admin Assets
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/admin
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin Assets Class
 */
class HM_Admin_Assets {

	/**
	 * Enqueue admin styles
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();
		if ( ! $screen || ( 'hm_series' !== $screen->post_type && 'hm_lesson' !== $screen->post_type ) ) {
			return;
		}

		wp_enqueue_style(
			'hm-admin',
			HM_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			HM_VERSION
		);
	}

	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || ( 'hm_series' !== $screen->post_type && 'hm_lesson' !== $screen->post_type ) ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'hm-admin',
			HM_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			HM_VERSION,
			true
		);

		wp_localize_script(
			'hm-admin',
			'hmAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'hm_admin_nonce' ),
			)
		);
	}
}
