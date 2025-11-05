<?php
/**
 * Single Lesson Template
 *
 * @package HappyMarket_Learning
 */

get_header();

require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';
require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-youtube.php';

while ( have_posts() ) :
	the_post();

	$lesson_id = get_the_ID();
	$youtube_id = get_post_meta( $lesson_id, '_hm_lesson_youtube_id', true );
	$series    = HM_Helpers::get_lesson_series( $lesson_id );
	?>
	<div class="hm-lesson-single hm-container">
		<?php if ( $series ) : ?>
			<nav class="hm-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'happy-market-learning' ); ?>">
				<ol class="hm-breadcrumb-list">
					<li class="hm-breadcrumb-item">
						<a href="<?php echo esc_url( get_post_type_archive_link( 'hm_series' ) ); ?>"><?php esc_html_e( 'Series', 'happy-market-learning' ); ?></a>
					</li>
					<li class="hm-breadcrumb-separator" aria-hidden="true">›</li>
					<li class="hm-breadcrumb-item">
						<a href="<?php echo esc_url( get_permalink( $series->ID ) ); ?>"><?php echo esc_html( $series->post_title ); ?></a>
					</li>
					<li class="hm-breadcrumb-separator" aria-hidden="true">›</li>
					<li class="hm-breadcrumb-item hm-breadcrumb-current" aria-current="page"><?php the_title(); ?></li>
				</ol>
			</nav>
		<?php endif; ?>

		<article id="lesson-<?php the_ID(); ?>" <?php post_class( 'hm-lesson-article' ); ?>>
			<header class="hm-lesson-header">
				<h1 class="hm-lesson-title"><?php the_title(); ?></h1>
			</header>

			<div class="hm-lesson-content">
				<?php if ( ! empty( $youtube_id ) ) : ?>
					<section class="hm-video-section">
						<?php
						$ads_before = HM_Helpers::get_lesson_ads( $lesson_id, 'before_video' );
						if ( ! empty( $ads_before ) ) :
							?>
							<div class="hm-ads-container hm-ads-before-video">
								<?php foreach ( $ads_before as $ad ) : ?>
									<div class="hm-ad-item hm-ad-inline">
										<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank" rel="nofollow noopener" class="hm-ad-link">
											<img src="<?php echo esc_url( $ad['image_url'] ); ?>" alt="<?php echo esc_attr( $ad['alt_text'] ); ?>" class="hm-ad-image" loading="lazy" />
										</a>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<div class="hm-video-wrapper">
							<div class="hm-video-container">
								<?php
								$embed_url = HM_YouTube::get_embed_url( $youtube_id );
								?>
								<iframe
									src="<?php echo esc_url( $embed_url ); ?>"
									class="hm-video-iframe"
									width="100%"
									height="500"
									frameborder="0"
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
									allowfullscreen
									title="<?php echo esc_attr( get_the_title() ); ?>"
								></iframe>
							</div>
						</div>

						<?php
						$ads_after = HM_Helpers::get_lesson_ads( $lesson_id, 'after_video' );
						if ( ! empty( $ads_after ) ) :
							?>
							<div class="hm-ads-container hm-ads-after-video">
								<?php foreach ( $ads_after as $ad ) : ?>
									<div class="hm-ad-item hm-ad-inline">
										<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank" rel="nofollow noopener" class="hm-ad-link">
											<img src="<?php echo esc_url( $ad['image_url'] ); ?>" alt="<?php echo esc_attr( $ad['alt_text'] ); ?>" class="hm-ad-image" loading="lazy" />
										</a>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</section>
				<?php endif; ?>

				<section class="hm-lesson-description">
					<div class="hm-lesson-description-content">
						<?php the_content(); ?>
					</div>
				</section>

				<?php
				// Products section
				if ( class_exists( 'WooCommerce' ) && get_option( 'hm_enable_woocommerce', false ) ) {
					$products = HM_Helpers::get_lesson_products( $lesson_id, 'after_video' );
					if ( ! empty( $products ) ) {
						?>
						<section class="hm-products-section">
							<h2 class="hm-products-section-title"><?php esc_html_e( 'Recommended Products', 'happy-market-learning' ); ?></h2>
							<?php echo do_shortcode( '[hm_lesson_products lesson_id="' . $lesson_id . '" position="after_video"]' ); ?>
						</section>
						<?php
					}
				}
				?>

				<nav class="hm-lesson-navigation" aria-label="<?php esc_attr_e( 'Lesson navigation', 'happy-market-learning' ); ?>">
					<?php echo do_shortcode( '[hm_lesson_navigation lesson_id="' . $lesson_id . '"]' ); ?>
				</nav>
			</div>
		</article>
	</div>

	<?php
	// Sidebar ads
	$sidebar_ads = HM_Helpers::get_lesson_ads( $lesson_id, 'sidebar' );
	if ( ! empty( $sidebar_ads ) ) :
		?>
		<aside class="hm-sidebar-ads" aria-label="<?php esc_attr_e( 'Advertisement', 'happy-market-learning' ); ?>">
			<?php foreach ( $sidebar_ads as $ad ) : ?>
				<div class="hm-ad-item hm-ad-sidebar">
					<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank" rel="nofollow noopener" class="hm-ad-link">
						<img src="<?php echo esc_url( $ad['image_url'] ); ?>" alt="<?php echo esc_attr( $ad['alt_text'] ); ?>" class="hm-ad-image" loading="lazy" />
					</a>
				</div>
			<?php endforeach; ?>
		</aside>
	<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
