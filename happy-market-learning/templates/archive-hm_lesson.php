<?php
/**
 * Archive Lesson Template
 *
 * @package HappyMarket_Learning
 */

get_header();
require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';
require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-youtube.php';
?>

<div class="hm-lesson-archive hm-container">
	<?php if ( have_posts() ) : ?>
		<div class="hm-lesson-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$lesson_id = get_the_ID();
				$youtube_id = get_post_meta( $lesson_id, '_hm_lesson_youtube_id', true );
				$series    = HM_Helpers::get_lesson_series( $lesson_id );
				$duration = get_post_meta( $lesson_id, '_hm_lesson_duration', true );
				?>
				<article id="lesson-<?php the_ID(); ?>" <?php post_class( 'hm-lesson-card' ); ?>>
					<?php if ( ! empty( $youtube_id ) ) : ?>
						<div class="hm-lesson-card-image">
							<a href="<?php the_permalink(); ?>" class="hm-lesson-card-link">
								<img src="<?php echo esc_url( HM_YouTube::get_thumbnail_url( $youtube_id ) ); ?>" alt="<?php the_title_attribute(); ?>" class="hm-lesson-thumbnail" loading="lazy" />
								<?php if ( ! empty( $duration ) ) : ?>
									<span class="hm-lesson-duration-badge"><?php echo esc_html( $duration ); ?></span>
								<?php endif; ?>
								<div class="hm-lesson-play-overlay">
									<svg class="hm-lesson-play-icon" viewBox="0 0 24 24" fill="currentColor">
										<path d="M8 5v14l11-7z"/>
									</svg>
								</div>
							</a>
						</div>
					<?php endif; ?>
					<div class="hm-lesson-card-content">
						<h2 class="hm-lesson-card-title">
							<a href="<?php the_permalink(); ?>" class="hm-lesson-card-link"><?php the_title(); ?></a>
						</h2>
						<?php if ( $series ) : ?>
							<div class="hm-lesson-card-meta">
								<span class="hm-lesson-series-badge">
									<a href="<?php echo esc_url( get_permalink( $series->ID ) ); ?>">
										<?php echo esc_html( $series->post_title ); ?>
									</a>
								</span>
							</div>
						<?php endif; ?>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>

		<?php
		the_posts_pagination();
	else :
		?>
		<p><?php esc_html_e( 'No lessons found.', 'happy-market-learning' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
