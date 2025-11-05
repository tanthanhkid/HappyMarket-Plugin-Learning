<?php
/**
 * Shortcodes
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/public
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Shortcodes Class
 */
class HM_Shortcodes {

	/**
	 * Register shortcodes
	 */
	public function register_shortcodes() {
		add_shortcode( 'hm_lesson_series', array( $this, 'render_lesson_series' ) );
		add_shortcode( 'hm_lesson_video', array( $this, 'render_lesson_video' ) );
		add_shortcode( 'hm_lesson_ads', array( $this, 'render_lesson_ads' ) );
		add_shortcode( 'hm_lesson_products', array( $this, 'render_lesson_products' ) );
		add_shortcode( 'hm_lesson_navigation', array( $this, 'render_lesson_navigation' ) );
	}

	/**
	 * Render lesson series shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_lesson_series( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'             => 0,
				'layout'         => 'grid',
				'columns'        => 3,
				'show_thumbnails' => 'true',
				'show_duration'  => 'true',
				'show_excerpt'   => 'false',
			),
			$atts,
			'hm_lesson_series'
		);

		$series_id = intval( $atts['id'] );
		if ( empty( $series_id ) ) {
			return '';
		}

		require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';
		$query = HM_Helpers::get_series_lessons( $series_id );

		if ( ! $query->have_posts() ) {
			return '';
		}

		ob_start();
		?>
		<div class="hm-lesson-series hm-layout-<?php echo esc_attr( $atts['layout'] ); ?>" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr( $atts['columns'] ); ?>, 1fr); gap: 20px;">
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				?>
				<div class="hm-lesson-item">
					<?php if ( 'true' === $atts['show_thumbnails'] ) : ?>
						<?php
						$youtube_id = get_post_meta( get_the_ID(), '_hm_lesson_youtube_id', true );
						require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-youtube.php';
						$thumbnail = ! empty( $youtube_id ) ? HM_YouTube::get_thumbnail_url( $youtube_id ) : '';
						?>
						<?php if ( ! empty( $thumbnail ) ) : ?>
							<a href="<?php the_permalink(); ?>">
								<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php the_title_attribute(); ?>" />
							</a>
						<?php endif; ?>
					<?php endif; ?>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php if ( 'true' === $atts['show_duration'] ) : ?>
						<?php
						$duration = get_post_meta( get_the_ID(), '_hm_lesson_duration', true );
						if ( ! empty( $duration ) ) {
							echo '<span class="hm-duration">' . esc_html( $duration ) . '</span>';
						}
						?>
					<?php endif; ?>
					<?php if ( 'true' === $atts['show_excerpt'] ) : ?>
						<div class="hm-excerpt"><?php the_excerpt(); ?></div>
					<?php endif; ?>
				</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render lesson video shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_lesson_video( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'        => 0,
				'autoplay'  => 'false',
				'width'     => '100%',
				'height'    => 'auto',
				'controls'  => 'true',
				'show_title' => 'true',
			),
			$atts,
			'hm_lesson_video'
		);

		$lesson_id = intval( $atts['id'] );
		if ( empty( $lesson_id ) ) {
			global $post;
			if ( 'hm_lesson' === get_post_type( $post ) ) {
				$lesson_id = $post->ID;
			} else {
				return '';
			}
		}

		$youtube_id = get_post_meta( $lesson_id, '_hm_lesson_youtube_id', true );
		if ( empty( $youtube_id ) ) {
			return '';
		}

		require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-youtube.php';

		$embed_params = array(
			'autoplay' => 'true' === $atts['autoplay'] ? 1 : 0,
			'controls' => 'true' === $atts['controls'] ? 1 : 0,
		);

		$embed_url = HM_YouTube::get_embed_url( $youtube_id, $embed_params );

		ob_start();
		?>
		<div class="hm-video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%;">
			<iframe
				src="<?php echo esc_url( $embed_url ); ?>"
				style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
				frameborder="0"
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
				allowfullscreen
			></iframe>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render lesson ads shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_lesson_ads( $atts ) {
		$atts = shortcode_atts(
			array(
				'lesson_id' => 0,
				'position'  => 'sidebar',
				'limit'     => 1,
			),
			$atts,
			'hm_lesson_ads'
		);

		$lesson_id = intval( $atts['lesson_id'] );
		if ( empty( $lesson_id ) ) {
			global $post;
			if ( 'hm_lesson' === get_post_type( $post ) ) {
				$lesson_id = $post->ID;
			} else {
				return '';
			}
		}

		require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';
		$ads = HM_Helpers::get_lesson_ads( $lesson_id, $atts['position'] );

		if ( empty( $ads ) ) {
			return '';
		}

		$ads = array_slice( $ads, 0, intval( $atts['limit'] ) );

		ob_start();
		?>
		<div class="hm-ads-container hm-ads-<?php echo esc_attr( $atts['position'] ); ?>">
			<?php foreach ( $ads as $ad ) : ?>
				<div class="hm-ad-item hm-ad-inline">
					<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank" rel="nofollow noopener" class="hm-ad-link">
						<img src="<?php echo esc_url( $ad['image_url'] ); ?>" alt="<?php echo esc_attr( $ad['alt_text'] ); ?>" class="hm-ad-image" loading="lazy" />
					</a>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render lesson products shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_lesson_products( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'lesson_id'        => 0,
				'position'         => 'after_video',
				'columns'          => 3,
				'limit'            => 4,
				'show_price'       => 'true',
				'show_add_to_cart' => 'true',
			),
			$atts,
			'hm_lesson_products'
		);

		$lesson_id = intval( $atts['lesson_id'] );
		if ( empty( $lesson_id ) ) {
			global $post;
			if ( 'hm_lesson' === get_post_type( $post ) ) {
				$lesson_id = $post->ID;
			} else {
				return '';
			}
		}

		require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';
		$product_ids = HM_Helpers::get_lesson_products( $lesson_id, $atts['position'] );

		if ( empty( $product_ids ) ) {
			return '';
		}

		$product_ids = array_slice( $product_ids, 0, intval( $atts['limit'] ) );

		ob_start();
		?>
		<div class="hm-products hm-products-<?php echo esc_attr( $atts['position'] ); ?>" style="grid-template-columns: repeat(<?php echo esc_attr( $atts['columns'] ); ?>, 1fr);">
			<?php
			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( ! $product ) {
					continue;
				}
				?>
				<div class="hm-product-item">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="hm-product-link">
						<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'hm-product-image' ) ); ?>
						<h3 class="hm-product-title"><?php echo esc_html( $product->get_name() ); ?></h3>
					</a>
					<?php if ( 'true' === $atts['show_price'] ) : ?>
						<div class="hm-product-price"><?php echo $product->get_price_html(); ?></div>
					<?php endif; ?>
					<?php if ( 'true' === $atts['show_add_to_cart'] ) : ?>
						<div class="hm-product-add-to-cart">
							<?php
							echo do_shortcode( '[add_to_cart id="' . $product_id . '"]' );
							?>
						</div>
					<?php endif; ?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render lesson navigation shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_lesson_navigation( $atts ) {
		$atts = shortcode_atts(
			array(
				'lesson_id'       => 0,
				'show_series_link' => 'true',
				'show_thumbnails' => 'true',
			),
			$atts,
			'hm_lesson_navigation'
		);

		$lesson_id = intval( $atts['lesson_id'] );
		if ( empty( $lesson_id ) ) {
			global $post;
			if ( 'hm_lesson' === get_post_type( $post ) ) {
				$lesson_id = $post->ID;
			} else {
				return '';
			}
		}

		require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';

		$prev_lesson = HM_Helpers::get_previous_lesson( $lesson_id );
		$next_lesson = HM_Helpers::get_next_lesson( $lesson_id );
		$series      = HM_Helpers::get_lesson_series( $lesson_id );

		ob_start();
		?>
		<div class="hm-lesson-navigation-inner">
			<div class="hm-nav-prev">
				<?php if ( $prev_lesson ) : ?>
					<a href="<?php echo esc_url( get_permalink( $prev_lesson->ID ) ); ?>" class="hm-nav-link hm-nav-link-prev">
						<span class="hm-nav-icon">←</span>
						<span class="hm-nav-text"><?php echo esc_html( $prev_lesson->post_title ); ?></span>
					</a>
				<?php else : ?>
					<span class="hm-nav-link hm-nav-link-disabled">
						<span class="hm-nav-icon">←</span>
						<span class="hm-nav-text"><?php esc_html_e( 'First Lesson', 'happy-market-learning' ); ?></span>
					</span>
				<?php endif; ?>
			</div>
			<div class="hm-nav-series">
				<?php if ( 'true' === $atts['show_series_link'] && $series ) : ?>
					<a href="<?php echo esc_url( get_permalink( $series->ID ) ); ?>" class="hm-nav-series-link">
						<?php echo esc_html( $series->post_title ); ?>
					</a>
				<?php endif; ?>
			</div>
			<div class="hm-nav-next">
				<?php if ( $next_lesson ) : ?>
					<a href="<?php echo esc_url( get_permalink( $next_lesson->ID ) ); ?>" class="hm-nav-link hm-nav-link-next">
						<span class="hm-nav-text"><?php echo esc_html( $next_lesson->post_title ); ?></span>
						<span class="hm-nav-icon">→</span>
					</a>
				<?php else : ?>
					<span class="hm-nav-link hm-nav-link-disabled">
						<span class="hm-nav-text"><?php esc_html_e( 'Last Lesson', 'happy-market-learning' ); ?></span>
						<span class="hm-nav-icon">→</span>
					</span>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
