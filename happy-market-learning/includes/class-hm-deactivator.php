<?php
/**
 * Fired during plugin deactivation
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Fired during plugin deactivation.
 *
 * Class này định nghĩa tất cả code cần thiết chạy trong quá trình deactivation của plugin.
 */
class HM_Deactivator {

	/**
	 * Deactivation hook.
	 *
	 * Clean up temporary data, flush rewrite rules, etc.
	 */
	public static function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();

		// Clear any scheduled events
		wp_clear_scheduled_hook( 'hm_daily_cleanup' );
	}
}
