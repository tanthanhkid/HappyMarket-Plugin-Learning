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

<div class="hm-lesson-archive">
	<?php if ( have_posts() ) : ?>
		<div class="hm-lesson-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$lesson_id = get_the_ID();
				$youtube_id = get_post_meta( $lesson_id, '_hm_lesson_youtube_id', true );
				$series    = HM_Helpers::get_lesson_series( $lesson_id );
				?>
				<article id="lesson-<?php the_ID(); ?>" <?php post_class( 'hm-lesson-item' ); ?>>
					<?php if ( ! empty( $youtube_id ) ) : ?>
						<a href="<?php the_permalink(); ?>">
							<img src="<?php echo esc_url( HM_YouTube::get_thumbnail_url( $youtube_id ) ); ?>" alt="<?php the_title_attribute(); ?>" />
						</a>
					<?php endif; ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php if ( $series ) : ?>
						<div class="lesson-series">
							<a href="<?php echo esc_url( get_permalink( $series->ID ) ); ?>">
								<?php echo esc_html( $series->post_title ); ?>
							</a>
						</div>
					<?php endif; ?>
					<?php
					$duration = get_post_meta( $lesson_id, '_hm_lesson_duration', true );
					if ( ! empty( $duration ) ) {
						echo '<div class="lesson-duration">' . esc_html( $duration ) . '</div>';
					}
					?>
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
