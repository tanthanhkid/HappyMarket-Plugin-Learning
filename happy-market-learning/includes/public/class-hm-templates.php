<?php
/**
 * Templates
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/public
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Templates Class
 */
class HM_Templates {

	/**
	 * Include template
	 *
	 * @param string $template Template path.
	 * @return string Template path.
	 */
	public function include_template( $template ) {
		return $template;
	}

	/**
	 * Get single template
	 *
	 * @param string $template Template path.
	 * @return string Template path.
	 */
	public function get_single_template( $template ) {
		global $post;

		if ( 'hm_lesson' === get_post_type( $post ) ) {
			$theme_template = locate_template( array( 'happy-market-learning/single-hm_lesson.php' ) );
			if ( $theme_template ) {
				return $theme_template;
			}

			$plugin_template = HM_PLUGIN_DIR . 'templates/single-hm_lesson.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}

		return $template;
	}

	/**
	 * Get archive template
	 *
	 * @param string $template Template path.
	 * @return string Template path.
	 */
	public function get_archive_template( $template ) {
		$post_type = get_post_type();

		if ( 'hm_series' === $post_type ) {
			$theme_template = locate_template( array( 'happy-market-learning/archive-hm_series.php' ) );
			if ( $theme_template ) {
				return $theme_template;
			}

			$plugin_template = HM_PLUGIN_DIR . 'templates/archive-hm_series.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}

		if ( 'hm_lesson' === $post_type ) {
			$theme_template = locate_template( array( 'happy-market-learning/archive-hm_lesson.php' ) );
			if ( $theme_template ) {
				return $theme_template;
			}

			$plugin_template = HM_PLUGIN_DIR . 'templates/archive-hm_lesson.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}

		return $template;
	}
}
