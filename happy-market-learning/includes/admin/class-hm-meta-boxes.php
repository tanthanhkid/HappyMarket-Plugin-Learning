<?php
/**
 * Meta Boxes
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/admin
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Meta Boxes Class
 */
class HM_Meta_Boxes {

	/**
	 * Add meta boxes
	 */
	public function add_meta_boxes() {
		// Series meta boxes
		add_meta_box(
			'hm_series_access',
			__( 'Access Settings', 'happy-market-learning' ),
			array( $this, 'render_series_access_meta_box' ),
			'hm_series',
			'side',
			'default'
		);

		add_meta_box(
			'hm_series_order',
			__( 'Display Order', 'happy-market-learning' ),
			array( $this, 'render_series_order_meta_box' ),
			'hm_series',
			'side',
			'default'
		);

		// Lesson meta boxes
		add_meta_box(
			'hm_lesson_series',
			__( 'Series Selection', 'happy-market-learning' ),
			array( $this, 'render_lesson_series_meta_box' ),
			'hm_lesson',
			'side',
			'high'
		);

		add_meta_box(
			'hm_lesson_video',
			__( 'Video Settings', 'happy-market-learning' ),
			array( $this, 'render_lesson_video_meta_box' ),
			'hm_lesson',
			'normal',
			'high'
		);

		add_meta_box(
			'hm_lesson_order',
			__( 'Series Order', 'happy-market-learning' ),
			array( $this, 'render_lesson_order_meta_box' ),
			'hm_lesson',
			'side',
			'default'
		);

		add_meta_box(
			'hm_lesson_access',
			__( 'Access Settings', 'happy-market-learning' ),
			array( $this, 'render_lesson_access_meta_box' ),
			'hm_lesson',
			'side',
			'default'
		);

		add_meta_box(
			'hm_lesson_ads',
			__( 'Quảng cáo', 'happy-market-learning' ),
			array( $this, 'render_lesson_ads_meta_box' ),
			'hm_lesson',
			'normal',
			'default'
		);

		if ( class_exists( 'WooCommerce' ) && get_option( 'hm_enable_woocommerce', false ) ) {
			add_meta_box(
				'hm_lesson_products',
				__( 'WooCommerce Products', 'happy-market-learning' ),
				array( $this, 'render_lesson_products_meta_box' ),
				'hm_lesson',
				'normal',
				'default'
			);
		}
	}

	/**
	 * Render series access meta box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_series_access_meta_box( $post ) {
		wp_nonce_field( 'hm_series_meta_box', 'hm_series_meta_box_nonce' );

		$access_type = get_post_meta( $post->ID, '_hm_series_access_type', true );
		if ( empty( $access_type ) ) {
			$access_type = get_option( 'hm_default_access_type', 'public' );
		}
		?>
		<p>
			<label for="hm_series_access_type"><?php esc_html_e( 'Access Type:', 'happy-market-learning' ); ?></label><br/>
			<select name="hm_series_access_type" id="hm_series_access_type" style="width: 100%;">
				<option value="public" <?php selected( $access_type, 'public' ); ?>><?php esc_html_e( 'Public', 'happy-market-learning' ); ?></option>
				<option value="login" <?php selected( $access_type, 'login' ); ?>><?php esc_html_e( 'Login Required', 'happy-market-learning' ); ?></option>
				<option value="membership" <?php selected( $access_type, 'membership' ); ?>><?php esc_html_e( 'Membership Required', 'happy-market-learning' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Render series order meta box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_series_order_meta_box( $post ) {
		$order = get_post_meta( $post->ID, '_hm_series_order', true );
		$order = ! empty( $order ) ? intval( $order ) : 0;
		?>
		<p>
			<label for="hm_series_order"><?php esc_html_e( 'Order:', 'happy-market-learning' ); ?></label><br/>
			<input type="number" name="hm_series_order" id="hm_series_order" value="<?php echo esc_attr( $order ); ?>" min="0" style="width: 100%;" />
			<span class="description"><?php esc_html_e( 'Lower numbers appear first.', 'happy-market-learning' ); ?></span>
		</p>
		<?php
	}

	/**
	 * Render lesson series meta box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_lesson_series_meta_box( $post ) {
		wp_nonce_field( 'hm_lesson_meta_box', 'hm_lesson_meta_box_nonce' );

		$series_id = get_post_meta( $post->ID, '_hm_lesson_series_id', true );

		$series_query = new WP_Query(
			array(
				'post_type'      => 'hm_series',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		?>
		<p>
			<label for="hm_lesson_series_id"><?php esc_html_e( 'Select Series:', 'happy-market-learning' ); ?></label><br/>
			<select name="hm_lesson_series_id" id="hm_lesson_series_id" style="width: 100%;">
				<option value=""><?php esc_html_e( '-- Select Series --', 'happy-market-learning' ); ?></option>
				<?php
				if ( $series_query->have_posts() ) {
					while ( $series_query->have_posts() ) {
						$series_query->the_post();
						$selected = selected( get_the_ID(), $series_id, false );
						echo '<option value="' . esc_attr( get_the_ID() ) . '" ' . $selected . '>' . esc_html( get_the_title() ) . '</option>';
					}
					wp_reset_postdata();
				}
				?>
			</select>
		</p>
		<?php
	}

	/**
	 * Render lesson video meta box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_lesson_video_meta_box( $post ) {
		$youtube_url = get_post_meta( $post->ID, '_hm_lesson_youtube_url', true );
		$youtube_id  = get_post_meta( $post->ID, '_hm_lesson_youtube_id', true );
		$duration    = get_post_meta( $post->ID, '_hm_lesson_duration', true );

		require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-youtube.php';
		$thumbnail_url = ! empty( $youtube_id ) ? HM_YouTube::get_thumbnail_url( $youtube_id ) : '';
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="hm_lesson_youtube_url"><?php esc_html_e( 'YouTube URL:', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<input type="url" name="hm_lesson_youtube_url" id="hm_lesson_youtube_url" value="<?php echo esc_attr( $youtube_url ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Enter full YouTube URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID)', 'happy-market-learning' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Video Preview:', 'happy-market-learning' ); ?></th>
				<td>
					<?php if ( ! empty( $thumbnail_url ) ) : ?>
						<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php esc_attr_e( 'Video thumbnail', 'happy-market-learning' ); ?>" style="max-width: 320px; height: auto;" />
						<p class="description"><?php esc_html_e( 'Video ID:', 'happy-market-learning' ); ?> <strong><?php echo esc_html( $youtube_id ); ?></strong></p>
					<?php else : ?>
						<p class="description"><?php esc_html_e( 'Enter YouTube URL to see preview', 'happy-market-learning' ); ?></p>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="hm_lesson_duration"><?php esc_html_e( 'Duration (MM:SS):', 'happy-market-learning' ); ?></label>
				</th>
				<td>
					<input type="text" name="hm_lesson_duration" id="hm_lesson_duration" value="<?php echo esc_attr( $duration ); ?>" placeholder="10:30" />
					<p class="description"><?php esc_html_e( 'Optional. Leave empty to auto-fetch with API key.', 'happy-market-learning' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render lesson order meta box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_lesson_order_meta_box( $post ) {
		$order = get_post_meta( $post->ID, '_hm_lesson_order', true );
		$order = ! empty( $order ) ? intval( $order ) : 0;
		?>
		<p>
			<label for="hm_lesson_order"><?php esc_html_e( 'Order in Series:', 'happy-market-learning' ); ?></label><br/>
			<input type="number" name="hm_lesson_order" id="hm_lesson_order" value="<?php echo esc_attr( $order ); ?>" min="0" style="width: 100%;" />
			<span class="description"><?php esc_html_e( 'Lower numbers appear first in series.', 'happy-market-learning' ); ?></span>
		</p>
		<?php
	}

	/**
	 * Render lesson access meta box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_lesson_access_meta_box( $post ) {
		$access_type = get_post_meta( $post->ID, '_hm_lesson_access_type', true );
		if ( empty( $access_type ) ) {
			$access_type = get_option( 'hm_default_access_type', 'public' );
		}
		?>
		<p>
			<label for="hm_lesson_access_type"><?php esc_html_e( 'Access Type:', 'happy-market-learning' ); ?></label><br/>
			<select name="hm_lesson_access_type" id="hm_lesson_access_type" style="width: 100%;">
				<option value="public" <?php selected( $access_type, 'public' ); ?>><?php esc_html_e( 'Public', 'happy-market-learning' ); ?></option>
				<option value="login" <?php selected( $access_type, 'login' ); ?>><?php esc_html_e( 'Login Required', 'happy-market-learning' ); ?></option>
				<option value="membership" <?php selected( $access_type, 'membership' ); ?>><?php esc_html_e( 'Membership Required', 'happy-market-learning' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'Override series access setting for this lesson.', 'happy-market-learning' ); ?></p>
		</p>
		<?php
	}

	/**
	 * Render lesson ads meta box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_lesson_ads_meta_box( $post ) {
		$ads_json = get_post_meta( $post->ID, '_hm_lesson_ads', true );
		$ads      = ! empty( $ads_json ) ? json_decode( $ads_json, true ) : array();

		if ( ! is_array( $ads ) ) {
			$ads = array();
		}
		?>
		<div id="hm-ads-container">
			<?php if ( ! empty( $ads ) ) : ?>
				<?php foreach ( $ads as $index => $ad ) : ?>
					<div class="hm-ad-item" data-index="<?php echo esc_attr( $index ); ?>">
						<h4><?php esc_html_e( 'Ad', 'happy-market-learning' ); ?> #<?php echo esc_html( $index + 1 ); ?></h4>
						<table class="form-table">
							<tr>
								<th><label><?php esc_html_e( 'Image URL:', 'happy-market-learning' ); ?></label></th>
								<td>
									<input type="url" name="hm_lesson_ads[<?php echo esc_attr( $index ); ?>][image_url]" value="<?php echo esc_attr( $ad['image_url'] ?? '' ); ?>" class="regular-text" />
									<button type="button" class="button hm-upload-image"><?php esc_html_e( 'Upload Image', 'happy-market-learning' ); ?></button>
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Link URL:', 'happy-market-learning' ); ?></label></th>
								<td>
									<input type="url" name="hm_lesson_ads[<?php echo esc_attr( $index ); ?>][link_url]" value="<?php echo esc_attr( $ad['link_url'] ?? '' ); ?>" class="regular-text" />
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Alt Text:', 'happy-market-learning' ); ?></label></th>
								<td>
									<input type="text" name="hm_lesson_ads[<?php echo esc_attr( $index ); ?>][alt_text]" value="<?php echo esc_attr( $ad['alt_text'] ?? '' ); ?>" class="regular-text" />
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Position:', 'happy-market-learning' ); ?></label></th>
								<td>
									<select name="hm_lesson_ads[<?php echo esc_attr( $index ); ?>][position]">
										<option value="sidebar" <?php selected( $ad['position'] ?? '', 'sidebar' ); ?>><?php esc_html_e( 'Sidebar', 'happy-market-learning' ); ?></option>
										<option value="popup" <?php selected( $ad['position'] ?? '', 'popup' ); ?>><?php esc_html_e( 'Popup', 'happy-market-learning' ); ?></option>
										<option value="before_video" <?php selected( $ad['position'] ?? '', 'before_video' ); ?>><?php esc_html_e( 'Before Video', 'happy-market-learning' ); ?></option>
										<option value="after_video" <?php selected( $ad['position'] ?? '', 'after_video' ); ?>><?php esc_html_e( 'After Video', 'happy-market-learning' ); ?></option>
										<option value="between_video" <?php selected( $ad['position'] ?? '', 'between_video' ); ?>><?php esc_html_e( 'Between Video', 'happy-market-learning' ); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th><label><?php esc_html_e( 'Active:', 'happy-market-learning' ); ?></label></th>
								<td>
									<input type="checkbox" name="hm_lesson_ads[<?php echo esc_attr( $index ); ?>][active]" value="1" <?php checked( $ad['active'] ?? true, true ); ?> />
								</td>
							</tr>
						</table>
						<button type="button" class="button hm-remove-ad"><?php esc_html_e( 'Remove Ad', 'happy-market-learning' ); ?></button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<button type="button" class="button button-primary hm-add-ad"><?php esc_html_e( 'Add Ad', 'happy-market-learning' ); ?></button>
		<?php
	}

	/**
	 * Render lesson products meta box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_lesson_products_meta_box( $post ) {
		$products_json = get_post_meta( $post->ID, '_hm_lesson_products', true );
		$products      = ! empty( $products_json ) ? json_decode( $products_json, true ) : array();

		if ( ! is_array( $products ) ) {
			$products = array();
		}

		$product_ids = array();
		foreach ( $products as $product ) {
			if ( is_array( $product ) && isset( $product['id'] ) ) {
				$product_ids[] = $product['id'];
			} elseif ( is_numeric( $product ) ) {
				$product_ids[] = $product;
			}
		}
		?>
		<div id="hm-products-container">
			<p>
				<label for="hm_product_search"><?php esc_html_e( 'Search Products:', 'happy-market-learning' ); ?></label>
				<input type="text" id="hm_product_search" class="regular-text" placeholder="<?php esc_attr_e( 'Type to search...', 'happy-market-learning' ); ?>" />
			</p>
			<div id="hm-selected-products">
				<?php if ( ! empty( $product_ids ) ) : ?>
					<?php foreach ( $product_ids as $product_id ) : ?>
						<?php
						$product = wc_get_product( $product_id );
						if ( ! $product ) {
							continue;
						}
						?>
						<div class="hm-product-item" data-product-id="<?php echo esc_attr( $product_id ); ?>">
							<span><?php echo esc_html( $product->get_name() ); ?></span>
							<button type="button" class="button-link hm-remove-product"><?php esc_html_e( 'Remove', 'happy-market-learning' ); ?></button>
							<input type="hidden" name="hm_lesson_products[]" value="<?php echo esc_attr( $product_id ); ?>" />
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save meta boxes
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_meta_boxes( $post_id ) {
		// Check if autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save Series meta
		if ( isset( $_POST['hm_series_meta_box_nonce'] ) && wp_verify_nonce( $_POST['hm_series_meta_box_nonce'], 'hm_series_meta_box' ) ) {
			if ( isset( $_POST['hm_series_access_type'] ) ) {
				update_post_meta( $post_id, '_hm_series_access_type', sanitize_text_field( $_POST['hm_series_access_type'] ) );
			}

			if ( isset( $_POST['hm_series_order'] ) ) {
				update_post_meta( $post_id, '_hm_series_order', intval( $_POST['hm_series_order'] ) );
			}
		}

		// Save Lesson meta
		if ( isset( $_POST['hm_lesson_meta_box_nonce'] ) && wp_verify_nonce( $_POST['hm_lesson_meta_box_nonce'], 'hm_lesson_meta_box' ) ) {
			// Series ID
			if ( isset( $_POST['hm_lesson_series_id'] ) ) {
				update_post_meta( $post_id, '_hm_lesson_series_id', intval( $_POST['hm_lesson_series_id'] ) );
			}

			// YouTube URL and ID
			if ( isset( $_POST['hm_lesson_youtube_url'] ) ) {
				$youtube_url = esc_url_raw( $_POST['hm_lesson_youtube_url'] );
				update_post_meta( $post_id, '_hm_lesson_youtube_url', $youtube_url );

				require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-youtube.php';
				$youtube_id = HM_YouTube::extract_video_id( $youtube_url );
				if ( $youtube_id ) {
					update_post_meta( $post_id, '_hm_lesson_youtube_id', sanitize_text_field( $youtube_id ) );
				}
			}

			// Duration
			if ( isset( $_POST['hm_lesson_duration'] ) ) {
				update_post_meta( $post_id, '_hm_lesson_duration', sanitize_text_field( $_POST['hm_lesson_duration'] ) );
			}

			// Order
			if ( isset( $_POST['hm_lesson_order'] ) ) {
				update_post_meta( $post_id, '_hm_lesson_order', intval( $_POST['hm_lesson_order'] ) );
			}

			// Access Type
			if ( isset( $_POST['hm_lesson_access_type'] ) ) {
				update_post_meta( $post_id, '_hm_lesson_access_type', sanitize_text_field( $_POST['hm_lesson_access_type'] ) );
			}

			// Ads
			if ( isset( $_POST['hm_lesson_ads'] ) && is_array( $_POST['hm_lesson_ads'] ) ) {
				$ads = array();
				foreach ( $_POST['hm_lesson_ads'] as $index => $ad ) {
					$ads[] = array(
						'id'        => isset( $ad['id'] ) ? sanitize_text_field( $ad['id'] ) : 'ad_' . time() . '_' . $index,
						'image_url' => isset( $ad['image_url'] ) ? esc_url_raw( $ad['image_url'] ) : '',
						'link_url'  => isset( $ad['link_url'] ) ? esc_url_raw( $ad['link_url'] ) : '',
						'alt_text'  => isset( $ad['alt_text'] ) ? sanitize_text_field( $ad['alt_text'] ) : '',
						'position'  => isset( $ad['position'] ) ? sanitize_text_field( $ad['position'] ) : 'sidebar',
						'active'     => isset( $ad['active'] ) && '1' === $ad['active'],
					);
				}
				update_post_meta( $post_id, '_hm_lesson_ads', wp_json_encode( $ads ) );
			} else {
				delete_post_meta( $post_id, '_hm_lesson_ads' );
			}

			// Products
			if ( isset( $_POST['hm_lesson_products'] ) && is_array( $_POST['hm_lesson_products'] ) ) {
				$products = array_map( 'intval', $_POST['hm_lesson_products'] );
				update_post_meta( $post_id, '_hm_lesson_products', wp_json_encode( $products ) );
			} else {
				delete_post_meta( $post_id, '_hm_lesson_products' );
			}
		}
	}
}
