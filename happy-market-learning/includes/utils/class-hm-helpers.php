<?php
/**
 * Helper Functions
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/utils
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Helper Functions Class
 */
class HM_Helpers {

	/**
	 * Get series lessons
	 *
	 * @param int $series_id Series ID.
	 * @return WP_Query Query object.
	 */
	public static function get_series_lessons( $series_id ) {
		$args = array(
			'post_type'      => 'hm_lesson',
			'posts_per_page' => -1,
			'meta_key'       => '_hm_lesson_series_id',
			'meta_value'     => $series_id,
			'orderby'        => 'meta_value_num',
			'order'           => 'ASC',
			'meta_query'      => array(
				array(
					'key'     => '_hm_lesson_order',
					'compare' => 'EXISTS',
				),
			),
		);

		return new WP_Query( $args );
	}

	/**
	 * Get lesson series
	 *
	 * @param int $lesson_id Lesson ID.
	 * @return WP_Post|false Series post or false.
	 */
	public static function get_lesson_series( $lesson_id ) {
		$series_id = get_post_meta( $lesson_id, '_hm_lesson_series_id', true );
		if ( empty( $series_id ) ) {
			return false;
		}

		return get_post( $series_id );
	}

	/**
	 * Get next lesson in series
	 *
	 * @param int $lesson_id Current lesson ID.
	 * @return WP_Post|false Next lesson or false.
	 */
	public static function get_next_lesson( $lesson_id ) {
		$series_id = get_post_meta( $lesson_id, '_hm_lesson_series_id', true );
		if ( empty( $series_id ) ) {
			return false;
		}

		$current_order = get_post_meta( $lesson_id, '_hm_lesson_order', true );
		$current_order = ! empty( $current_order ) ? intval( $current_order ) : 0;

		$args = array(
			'post_type'      => 'hm_lesson',
			'posts_per_page' => 1,
			'meta_key'       => '_hm_lesson_series_id',
			'meta_value'     => $series_id,
			'orderby'        => 'meta_value_num',
			'order'           => 'ASC',
			'meta_query'      => array(
				array(
					'key'     => '_hm_lesson_order',
					'value'   => $current_order,
					'compare' => '>',
				),
			),
		);

		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			return $query->posts[0];
		}

		return false;
	}

	/**
	 * Get previous lesson in series
	 *
	 * @param int $lesson_id Current lesson ID.
	 * @return WP_Post|false Previous lesson or false.
	 */
	public static function get_previous_lesson( $lesson_id ) {
		$series_id = get_post_meta( $lesson_id, '_hm_lesson_series_id', true );
		if ( empty( $series_id ) ) {
			return false;
		}

		$current_order = get_post_meta( $lesson_id, '_hm_lesson_order', true );
		$current_order = ! empty( $current_order ) ? intval( $current_order ) : 0;

		$args = array(
			'post_type'      => 'hm_lesson',
			'posts_per_page' => 1,
			'meta_key'       => '_hm_lesson_series_id',
			'meta_value'     => $series_id,
			'orderby'        => 'meta_value_num',
			'order'           => 'DESC',
			'meta_query'      => array(
				array(
					'key'     => '_hm_lesson_order',
					'value'   => $current_order,
					'compare' => '<',
				),
			),
		);

		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			return $query->posts[0];
		}

		return false;
	}

	/**
	 * Check if user has access to lesson
	 *
	 * @param int $lesson_id Lesson ID.
	 * @param int $user_id User ID (optional, defaults to current user).
	 * @return bool True if user has access.
	 */
	public static function check_lesson_access( $lesson_id, $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$access_type = get_post_meta( $lesson_id, '_hm_lesson_access_type', true );
		if ( empty( $access_type ) ) {
			// Check series access
			$series = self::get_lesson_series( $lesson_id );
			if ( $series ) {
				$access_type = get_post_meta( $series->ID, '_hm_series_access_type', true );
			}
		}

		if ( empty( $access_type ) || 'public' === $access_type ) {
			return true;
		}

		if ( 'login' === $access_type ) {
			return is_user_logged_in();
		}

		if ( 'membership' === $access_type ) {
			// Allow membership plugins to hook in
			return apply_filters( 'hm_lesson_access_check', is_user_logged_in(), $lesson_id, $user_id );
		}

		return true;
	}

	/**
	 * Get lesson ads
	 *
	 * @param int    $lesson_id Lesson ID.
	 * @param string $position Ad position (optional).
	 * @return array Array of ads.
	 */
	public static function get_lesson_ads( $lesson_id, $position = '' ) {
		$ads_json = get_post_meta( $lesson_id, '_hm_lesson_ads', true );
		$ads      = ! empty( $ads_json ) ? json_decode( $ads_json, true ) : array();

		if ( empty( $ads ) || ! is_array( $ads ) ) {
			return array();
		}

		// Filter by position if specified
		if ( ! empty( $position ) ) {
			$ads = array_filter(
				$ads,
				function( $ad ) use ( $position ) {
					return isset( $ad['position'] ) && $ad['position'] === $position && isset( $ad['active'] ) && $ad['active'];
				}
			);
		} else {
			// Only return active ads
			$ads = array_filter(
				$ads,
				function( $ad ) {
					return isset( $ad['active'] ) && $ad['active'];
				}
			);
		}

		return apply_filters( 'hm_lesson_ads', array_values( $ads ), $lesson_id, $position );
	}

	/**
	 * Get lesson products
	 *
	 * @param int    $lesson_id Lesson ID.
	 * @param string $position Product position (optional).
	 * @return array Array of product IDs.
	 */
	public static function get_lesson_products( $lesson_id, $position = '' ) {
		$products_json = get_post_meta( $lesson_id, '_hm_lesson_products', true );
		$products      = ! empty( $products_json ) ? json_decode( $products_json, true ) : array();

		if ( empty( $products ) || ! is_array( $products ) ) {
			return array();
		}

		// If products are stored as simple array of IDs
		if ( isset( $products[0] ) && is_numeric( $products[0] ) ) {
			return apply_filters( 'hm_lesson_products', $products, $lesson_id, $position );
		}

		// If products are stored as objects with metadata
		$product_ids = array();
		foreach ( $products as $product ) {
			if ( is_array( $product ) && isset( $product['id'] ) ) {
				if ( empty( $position ) || ( isset( $product['position'] ) && $product['position'] === $position ) ) {
					$product_ids[] = $product['id'];
				}
			} elseif ( is_numeric( $product ) ) {
				$product_ids[] = $product;
			}
		}

		return apply_filters( 'hm_lesson_products', $product_ids, $lesson_id, $position );
	}
}
