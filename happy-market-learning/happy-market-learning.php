<?php
/**
 * Plugin Name: HappyMarket Learning Manager
 * Plugin URI: https://github.com/your-username/happy-market-learning
 * Description: Quản lý và hiển thị các trang landing page cho các bài học video được nhúng từ YouTube. Hỗ trợ quảng cáo và tích hợp WooCommerce để up-sale sản phẩm.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: happy-market-learning
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Phiên bản hiện tại của plugin
 */
define( 'HM_VERSION', '1.0.0' );

/**
 * Đường dẫn đến thư mục plugin
 */
define( 'HM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * URL của plugin
 */
define( 'HM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Đường dẫn đến file chính của plugin
 */
define( 'HM_PLUGIN_FILE', __FILE__ );

/**
 * Tên plugin
 */
define( 'HM_PLUGIN_NAME', 'HappyMarket Learning Manager' );

/**
 * Code để chạy trong quá trình activation của plugin
 */
function activate_happy_market_learning() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hm-activator.php';
	HM_Activator::activate();
}

/**
 * Code để chạy trong quá trình deactivation của plugin
 */
function deactivate_happy_market_learning() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hm-deactivator.php';
	HM_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_happy_market_learning' );
register_deactivation_hook( __FILE__, 'deactivate_happy_market_learning' );

/**
 * Core plugin class được sử dụng để định nghĩa:
 * - internationalization
 * - admin-specific hooks
 * - public-facing site hooks
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hm-core.php';

/**
 * Bắt đầu thực thi plugin
 */
function run_happy_market_learning() {
	$plugin = new HM_Core();
	$plugin->run();
}
run_happy_market_learning();
