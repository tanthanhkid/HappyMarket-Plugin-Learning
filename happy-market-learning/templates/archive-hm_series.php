<?php
/**
 * Archive Series Template
 *
 * @package HappyMarket_Learning
 */

get_header();
?>

<div class="hm-series-archive hm-container">
	<?php if ( have_posts() ) : ?>
		<div class="hm-series-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				require_once HM_PLUGIN_DIR . 'includes/utils/class-hm-helpers.php';
				$lessons = HM_Helpers::get_series_lessons( get_the_ID() );
				?>
				<article id="series-<?php the_ID(); ?>" <?php post_class( 'hm-series-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="hm-series-card-image">
							<a href="<?php the_permalink(); ?>" class="hm-series-card-link">
								<?php the_post_thumbnail( 'medium', array( 'class' => 'hm-series-thumbnail' ) ); ?>
							</a>
						</div>
					<?php endif; ?>
					<div class="hm-series-card-content">
						<h2 class="hm-series-card-title">
							<a href="<?php the_permalink(); ?>" class="hm-series-card-link"><?php the_title(); ?></a>
						</h2>
						<?php if ( has_excerpt() ) : ?>
							<div class="hm-series-card-excerpt"><?php the_excerpt(); ?></div>
						<?php endif; ?>
						<div class="hm-series-card-meta">
							<span class="hm-series-lessons-count">
								<?php
								// translators: %d is the number of lessons
								printf( esc_html__( '%d lessons', 'happy-market-learning' ), $lessons->post_count );
								?>
							</span>
						</div>
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
