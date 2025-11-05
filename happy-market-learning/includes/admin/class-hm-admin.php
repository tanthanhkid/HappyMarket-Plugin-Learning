<?php
/**
 * Admin functionality
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/admin
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin Class
 */
class HM_Admin {

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=hm_series',
			__( 'Settings', 'happy-market-learning' ),
			__( 'Settings', 'happy-market-learning' ),
			'manage_hm_settings',
			'hm-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		// General Settings
		register_setting( 'hm_general_settings', 'hm_default_access_type' );
		register_setting( 'hm_general_settings', 'hm_default_ad_position' );
		register_setting( 'hm_general_settings', 'hm_youtube_api_key' );
		register_setting( 'hm_general_settings', 'hm_enable_analytics' );

		// WooCommerce Settings
		register_setting( 'hm_woocommerce_settings', 'hm_enable_woocommerce' );
		register_setting( 'hm_woocommerce_settings', 'hm_default_product_position' );
		register_setting( 'hm_woocommerce_settings', 'hm_default_product_columns' );
		register_setting( 'hm_woocommerce_settings', 'hm_default_product_limit' );
		register_setting( 'hm_woocommerce_settings', 'hm_show_price' );
		register_setting( 'hm_woocommerce_settings', 'hm_show_add_to_cart' );
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<nav class="nav-tab-wrapper">
				<a href="?post_type=hm_series&page=hm-settings&tab=general" class="nav-tab <?php echo 'general' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'General', 'happy-market-learning' ); ?>
				</a>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<a href="?post_type=hm_series&page=hm-settings&tab=woocommerce" class="nav-tab <?php echo 'woocommerce' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'WooCommerce', 'happy-market-learning' ); ?>
				</a>
				<?php endif; ?>
			</nav>

			<form action="options.php" method="post">
				<?php
				if ( 'general' === $active_tab ) {
					settings_fields( 'hm_general_settings' );
					do_settings_sections( 'hm_general_settings' );
					$this->render_general_settings();
				} elseif ( 'woocommerce' === $active_tab && class_exists( 'WooCommerce' ) ) {
					settings_fields( 'hm_woocommerce_settings' );
					do_settings_sections( 'hm_woocommerce_settings' );
					$this->render_woocommerce_settings();
				}
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render general settings
	 */
	private function render_general_settings() {
		$default_access_type = get_option( 'hm_default_access_type', 'public' );
		$default_ad_position = get_option( 'hm_default_ad_position', 'sidebar' );
		$youtube_api_key     = get_option( 'hm_youtube_api_key', '' );
		$enable_analytics    = get_option( 'hm_enable_analytics', false );
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="hm_default_access_type"><?php esc_html_e( 'Default Access Type', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<select name="hm_default_access_type" id="hm_default_access_type">
						<option value="public" <?php selected( $default_access_type, 'public' ); ?>><?php esc_html_e( 'Public', 'happy-market-learning' ); ?></option>
						<option value="login" <?php selected( $default_access_type, 'login' ); ?>><?php esc_html_e( 'Login Required', 'happy-market-learning' ); ?></option>
						<option value="membership" <?php selected( $default_access_type, 'membership' ); ?>><?php esc_html_e( 'Membership Required', 'happy-market-learning' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Default access type for new series and lessons.', 'happy-market-learning' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_default_ad_position"><?php esc_html_e( 'Default Ad Position', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<select name="hm_default_ad_position" id="hm_default_ad_position">
						<option value="sidebar" <?php selected( $default_ad_position, 'sidebar' ); ?>><?php esc_html_e( 'Sidebar', 'happy-market-learning' ); ?></option>
						<option value="popup" <?php selected( $default_ad_position, 'popup' ); ?>><?php esc_html_e( 'Popup', 'happy-market-learning' ); ?></option>
						<option value="after_video" <?php selected( $default_ad_position, 'after_video' ); ?>><?php esc_html_e( 'After Video', 'happy-market-learning' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_youtube_api_key"><?php esc_html_e( 'YouTube API Key', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<input type="text" name="hm_youtube_api_key" id="hm_youtube_api_key" value="<?php echo esc_attr( $youtube_api_key ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Optional. Used to fetch video metadata automatically.', 'happy-market-learning' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_enable_analytics"><?php esc_html_e( 'Enable Analytics', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="hm_enable_analytics" id="hm_enable_analytics" value="1" <?php checked( $enable_analytics, true ); ?> />
					<p class="description"><?php esc_html_e( 'Track views and clicks.', 'happy-market-learning' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render WooCommerce settings
	 */
	private function render_woocommerce_settings() {
		$enable_woocommerce      = get_option( 'hm_enable_woocommerce', false );
		$default_product_position = get_option( 'hm_default_product_position', 'after_video' );
		$default_product_columns  = get_option( 'hm_default_product_columns', 3 );
		$default_product_limit    = get_option( 'hm_default_product_limit', 4 );
		$show_price               = get_option( 'hm_show_price', true );
		$show_add_to_cart         = get_option( 'hm_show_add_to_cart', true );
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="hm_enable_woocommerce"><?php esc_html_e( 'Enable WooCommerce Integration', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="hm_enable_woocommerce" id="hm_enable_woocommerce" value="1" <?php checked( $enable_woocommerce, true ); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_default_product_position"><?php esc_html_e( 'Default Product Position', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<select name="hm_default_product_position" id="hm_default_product_position">
						<option value="sidebar" <?php selected( $default_product_position, 'sidebar' ); ?>><?php esc_html_e( 'Sidebar', 'happy-market-learning' ); ?></option>
						<option value="after_video" <?php selected( $default_product_position, 'after_video' ); ?>><?php esc_html_e( 'After Video', 'happy-market-learning' ); ?></option>
						<option value="popup" <?php selected( $default_product_position, 'popup' ); ?>><?php esc_html_e( 'Popup', 'happy-market-learning' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_default_product_columns"><?php esc_html_e( 'Default Product Columns', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<select name="hm_default_product_columns" id="hm_default_product_columns">
						<option value="1" <?php selected( $default_product_columns, 1 ); ?>>1</option>
						<option value="2" <?php selected( $default_product_columns, 2 ); ?>>2</option>
						<option value="3" <?php selected( $default_product_columns, 3 ); ?>>3</option>
						<option value="4" <?php selected( $default_product_columns, 4 ); ?>>4</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_default_product_limit"><?php esc_html_e( 'Default Product Limit', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<input type="number" name="hm_default_product_limit" id="hm_default_product_limit" value="<?php echo esc_attr( $default_product_limit ); ?>" min="1" max="20" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_show_price"><?php esc_html_e( 'Show Price', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="hm_show_price" id="hm_show_price" value="1" <?php checked( $show_price, true ); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_show_add_to_cart"><?php esc_html_e( 'Show Add to Cart Button', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="hm_show_add_to_cart" id="hm_show_add_to_cart" value="1" <?php checked( $show_add_to_cart, true ); ?> />
				</td>
			</tr>
		</table>
		<?php
	}
}
