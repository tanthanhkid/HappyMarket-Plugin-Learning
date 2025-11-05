<?php
/**
 * Series Post Type
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/post-types
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Series Post Type Class
 */
class HM_Series {

	/**
	 * Register Series post type
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Series', 'Post Type General Name', 'happy-market-learning' ),
			'singular_name'         => _x( 'Series', 'Post Type Singular Name', 'happy-market-learning' ),
			'menu_name'             => __( 'Series', 'happy-market-learning' ),
			'name_admin_bar'        => __( 'Series', 'happy-market-learning' ),
			'archives'              => __( 'Series Archives', 'happy-market-learning' ),
			'attributes'            => __( 'Series Attributes', 'happy-market-learning' ),
			'parent_item_colon'     => __( 'Parent Series:', 'happy-market-learning' ),
			'all_items'             => __( 'All Series', 'happy-market-learning' ),
			'add_new_item'          => __( 'Add New Series', 'happy-market-learning' ),
			'add_new'               => __( 'Add New', 'happy-market-learning' ),
			'new_item'              => __( 'New Series', 'happy-market-learning' ),
			'edit_item'             => __( 'Edit Series', 'happy-market-learning' ),
			'update_item'           => __( 'Update Series', 'happy-market-learning' ),
			'view_item'             => __( 'View Series', 'happy-market-learning' ),
			'view_items'            => __( 'View Series', 'happy-market-learning' ),
			'search_items'          => __( 'Search Series', 'happy-market-learning' ),
			'not_found'             => __( 'Not found', 'happy-market-learning' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'happy-market-learning' ),
			'featured_image'        => __( 'Featured Image', 'happy-market-learning' ),
			'set_featured_image'    => __( 'Set featured image', 'happy-market-learning' ),
			'remove_featured_image' => __( 'Remove featured image', 'happy-market-learning' ),
			'use_featured_image'    => __( 'Use as featured image', 'happy-market-learning' ),
			'insert_into_item'      => __( 'Insert into series', 'happy-market-learning' ),
			'uploaded_to_this_item' => __( 'Uploaded to this series', 'happy-market-learning' ),
			'items_list'            => __( 'Series list', 'happy-market-learning' ),
			'items_list_navigation' => __( 'Series list navigation', 'happy-market-learning' ),
			'filter_items_list'     => __( 'Filter series list', 'happy-market-learning' ),
		);

		$args = array(
			'label'                 => __( 'Series', 'happy-market-learning' ),
			'description'           => __( 'Series of lessons', 'happy-market-learning' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 20,
			'menu_icon'             => 'dashicons-video-alt3',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
			'rest_base'             => 'hm_series',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		register_post_type( 'hm_series', $args );
	}
}
