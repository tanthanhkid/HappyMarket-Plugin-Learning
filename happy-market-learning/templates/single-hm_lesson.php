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
	<div class="hm-lesson-single">
		<?php if ( $series ) : ?>
			<div class="hm-breadcrumb">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'hm_series' ) ); ?>"><?php esc_html_e( 'Series', 'happy-market-learning' ); ?></a> &raquo;
				<a href="<?php echo esc_url( get_permalink( $series->ID ) ); ?>"><?php echo esc_html( $series->post_title ); ?></a> &raquo;
				<span><?php the_title(); ?></span>
			</div>
		<?php endif; ?>

		<article id="lesson-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>

			<div class="entry-content">
				<?php if ( ! empty( $youtube_id ) ) : ?>
					<div class="hm-video-wrapper">
						<?php
						$ads_before = HM_Helpers::get_lesson_ads( $lesson_id, 'before_video' );
						if ( ! empty( $ads_before ) ) :
							?>
							<div class="hm-ads-before-video">
								<?php foreach ( $ads_before as $ad ) : ?>
									<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank" rel="nofollow">
										<img src="<?php echo esc_url( $ad['image_url'] ); ?>" alt="<?php echo esc_attr( $ad['alt_text'] ); ?>" />
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<div class="hm-video-container">
							<?php
							$embed_url = HM_YouTube::get_embed_url( $youtube_id );
							?>
							<iframe
								src="<?php echo esc_url( $embed_url ); ?>"
								width="100%"
								height="500"
								frameborder="0"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								allowfullscreen
							></iframe>
						</div>

						<?php
						$ads_after = HM_Helpers::get_lesson_ads( $lesson_id, 'after_video' );
						if ( ! empty( $ads_after ) ) :
							?>
							<div class="hm-ads-after-video">
								<?php foreach ( $ads_after as $ad ) : ?>
									<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank" rel="nofollow">
										<img src="<?php echo esc_url( $ad['image_url'] ); ?>" alt="<?php echo esc_attr( $ad['alt_text'] ); ?>" />
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="hm-lesson-description">
					<?php the_content(); ?>
				</div>

				<?php
				// Products section
				if ( class_exists( 'WooCommerce' ) && get_option( 'hm_enable_woocommerce', false ) ) {
					$products = HM_Helpers::get_lesson_products( $lesson_id, 'after_video' );
					if ( ! empty( $products ) ) {
						echo '<div class="hm-products-section">';
						echo '<h3>' . esc_html__( 'Recommended Products', 'happy-market-learning' ) . '</h3>';
						echo do_shortcode( '[hm_lesson_products lesson_id="' . $lesson_id . '" position="after_video"]' );
						echo '</div>';
					}
				}
				?>

				<?php echo do_shortcode( '[hm_lesson_navigation lesson_id="' . $lesson_id . '"]' ); ?>
			</div>
		</article>
	</div>

	<?php
	// Sidebar ads
	$sidebar_ads = HM_Helpers::get_lesson_ads( $lesson_id, 'sidebar' );
	if ( ! empty( $sidebar_ads ) ) :
		?>
		<aside class="hm-sidebar-ads">
			<?php foreach ( $sidebar_ads as $ad ) : ?>
				<div class="hm-ad-item">
					<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank" rel="nofollow">
						<img src="<?php echo esc_url( $ad['image_url'] ); ?>" alt="<?php echo esc_attr( $ad['alt_text'] ); ?>" />
					</a>
				</div>
			<?php endforeach; ?>
		</aside>
	<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
