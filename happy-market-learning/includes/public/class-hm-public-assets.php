<?php
/**
 * Public Assets
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/public
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Public Assets Class
 */
class HM_Public_Assets {

	/**
	 * Enqueue public styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'hm-public',
			HM_PLUGIN_URL . 'assets/css/public.css',
			array(),
			HM_VERSION
		);
	}

	/**
	 * Enqueue public scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'hm-public',
			HM_PLUGIN_URL . 'assets/js/public.js',
			array( 'jquery' ),
			HM_VERSION,
			true
		);

		wp_localize_script(
			'hm-public',
			'hmPublic',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'hm_public_nonce' ),
			)
		);
	}
}
