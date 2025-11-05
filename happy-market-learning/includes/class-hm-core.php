<?php
/**
 * The core plugin class.
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class.
 *
 * Load dependencies, define internationalization, admin hooks, và public-facing site hooks.
 */
class HM_Core {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @var HM_Loader
	 */
	protected $loader;

	/**
	 * Define core functionality của plugin.
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_post_type_hooks();
	}

	/**
	 * Load các dependencies cần thiết.
	 */
	private function load_dependencies() {
		/**
		 * Class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hm-loader.php';

		/**
		 * Class responsible for defining internationalization functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hm-i18n.php';

		/**
		 * Post types
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-hm-series.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-hm-lesson.php';

		/**
		 * Admin classes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/admin/class-hm-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/admin/class-hm-meta-boxes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/admin/class-hm-admin-assets.php';

		/**
		 * Public classes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/public/class-hm-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/public/class-hm-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/public/class-hm-templates.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/public/class-hm-public-assets.php';

		/**
		 * Utils
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/utils/class-hm-youtube.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/utils/class-hm-helpers.php';

		/**
		 * WooCommerce integration (optional)
		 */
		if ( class_exists( 'WooCommerce' ) && get_option( 'hm_enable_woocommerce', false ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/integrations/class-hm-woocommerce.php';
		}

		$this->loader = new HM_Loader();
	}

	/**
	 * Define locale cho plugin.
	 */
	private function set_locale() {
		$plugin_i18n = new HM_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register admin hooks.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new HM_Admin();
		$plugin_meta_boxes = new HM_Meta_Boxes();
		$plugin_admin_assets = new HM_Admin_Assets();

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_meta_boxes, 'add_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_meta_boxes, 'save_meta_boxes' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin_assets, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin_assets, 'enqueue_scripts' );
	}

	/**
	 * Register public-facing hooks.
	 */
	private function define_public_hooks() {
		$plugin_public = new HM_Public();
		$plugin_shortcodes = new HM_Shortcodes();
		$plugin_templates = new HM_Templates();
		$plugin_public_assets = new HM_Public_Assets();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public_assets, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public_assets, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_shortcodes, 'register_shortcodes' );
		$this->loader->add_filter( 'template_include', $plugin_templates, 'include_template' );
		$this->loader->add_filter( 'single_template', $plugin_templates, 'get_single_template' );
		$this->loader->add_filter( 'archive_template', $plugin_templates, 'get_archive_template' );
	}

	/**
	 * Register post type hooks.
	 */
	private function define_post_type_hooks() {
		$plugin_series = new HM_Series();
		$plugin_lesson = new HM_Lesson();

		$this->loader->add_action( 'init', $plugin_series, 'register_post_type' );
		$this->loader->add_action( 'init', $plugin_lesson, 'register_post_type' );
		$this->loader->add_action( 'init', $plugin_lesson, 'register_taxonomies' );
	}

	/**
	 * Run the loader để execute tất cả hooks.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Reference to the loader that's responsible for maintaining and registering all hooks.
	 *
	 * @return HM_Loader
	 */
	public function get_loader() {
		return $this->loader;
	}
}
