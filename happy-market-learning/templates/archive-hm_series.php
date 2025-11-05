<?php
/**
 * Archive Series Template
 *
 * @package HappyMarket_Learning
 */

get_header();
?>

<div class="hm-series-archive">
	<?php if ( have_posts() ) : ?>
		<div class="hm-series-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="series-<?php the_ID(); ?>" <?php post_class( 'hm-series-item' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'medium' ); ?>
						</a>
					<?php endif; ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php if ( has_excerpt() ) : ?>
						<div class="series-excerpt"><?php the_excerpt(); ?></div>
					<?php endif; ?>
					<?php
					require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';
					$lessons = HM_Helpers::get_series_lessons( get_the_ID() );
					?>
					<div class="series-lessons-count">
						<?php
						// translators: %d is the number of lessons
						printf( esc_html__( '%d lessons', 'happy-market-learning' ), $lessons->post_count );
						?>
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
		<p><?php esc_html_e( 'No series found.', 'happy-market-learning' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
