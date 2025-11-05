<?php
/**
 * Lesson Post Type
 *
 * @package    HappyMarket_Learning
 * @subpackage HappyMarket_Learning/includes/post-types
 */

// Nếu file này được gọi trực tiếp, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Lesson Post Type Class
 */
class HM_Lesson {

	/**
	 * Register Lesson post type
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Lessons', 'Post Type General Name', 'happy-market-learning' ),
			'singular_name'         => _x( 'Lesson', 'Post Type Singular Name', 'happy-market-learning' ),
			'menu_name'             => __( 'Lessons', 'happy-market-learning' ),
			'name_admin_bar'        => __( 'Lesson', 'happy-market-learning' ),
			'archives'              => __( 'Lesson Archives', 'happy-market-learning' ),
			'attributes'            => __( 'Lesson Attributes', 'happy-market-learning' ),
			'parent_item_colon'     => __( 'Parent Lesson:', 'happy-market-learning' ),
			'all_items'             => __( 'All Lessons', 'happy-market-learning' ),
			'add_new_item'          => __( 'Add New Lesson', 'happy-market-learning' ),
			'add_new'               => __( 'Add New', 'happy-market-learning' ),
			'new_item'              => __( 'New Lesson', 'happy-market-learning' ),
			'edit_item'             => __( 'Edit Lesson', 'happy-market-learning' ),
			'update_item'           => __( 'Update Lesson', 'happy-market-learning' ),
			'view_item'             => __( 'View Lesson', 'happy-market-learning' ),
			'view_items'            => __( 'View Lessons', 'happy-market-learning' ),
			'search_items'          => __( 'Search Lesson', 'happy-market-learning' ),
			'not_found'             => __( 'Not found', 'happy-market-learning' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'happy-market-learning' ),
			'featured_image'        => __( 'Featured Image', 'happy-market-learning' ),
			'set_featured_image'    => __( 'Set featured image', 'happy-market-learning' ),
			'remove_featured_image' => __( 'Remove featured image', 'happy-market-learning' ),
			'use_featured_image'    => __( 'Use as featured image', 'happy-market-learning' ),
			'insert_into_item'      => __( 'Insert into lesson', 'happy-market-learning' ),
			'uploaded_to_this_item' => __( 'Uploaded to this lesson', 'happy-market-learning' ),
			'items_list'            => __( 'Lessons list', 'happy-market-learning' ),
			'items_list_navigation' => __( 'Lessons list navigation', 'happy-market-learning' ),
			'filter_items_list'     => __( 'Filter lessons list', 'happy-market-learning' ),
		);

		$args = array(
			'label'                 => __( 'Lesson', 'happy-market-learning' ),
			'description'           => __( 'Video lessons', 'happy-market-learning' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'taxonomies'            => array( 'hm_lesson_category', 'hm_lesson_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 21,
			'menu_icon'             => 'dashicons-playlist-video',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
			'rest_base'             => 'hm_lesson',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		register_post_type( 'hm_lesson', $args );
	}

	/**
	 * Register taxonomies for lessons
	 */
	public function register_taxonomies() {
		// Lesson Category
		$category_labels = array(
			'name'              => _x( 'Lesson Categories', 'taxonomy general name', 'happy-market-learning' ),
			'singular_name'     => _x( 'Lesson Category', 'taxonomy singular name', 'happy-market-learning' ),
			'search_items'      => __( 'Search Categories', 'happy-market-learning' ),
			'all_items'         => __( 'All Categories', 'happy-market-learning' ),
			'parent_item'       => __( 'Parent Category', 'happy-market-learning' ),
			'parent_item_colon' => __( 'Parent Category:', 'happy-market-learning' ),
			'edit_item'         => __( 'Edit Category', 'happy-market-learning' ),
			'update_item'       => __( 'Update Category', 'happy-market-learning' ),
			'add_new_item'      => __( 'Add New Category', 'happy-market-learning' ),
			'new_item_name'     => __( 'New Category Name', 'happy-market-learning' ),
			'menu_name'         => __( 'Categories', 'happy-market-learning' ),
		);

		$category_args = array(
			'hierarchical'      => true,
			'labels'            => $category_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'lesson-category' ),
		);

		register_taxonomy( 'hm_lesson_category', array( 'hm_lesson' ), $category_args );

		// Lesson Tags
		$tag_labels = array(
			'name'                       => _x( 'Lesson Tags', 'taxonomy general name', 'happy-market-learning' ),
			'singular_name'              => _x( 'Lesson Tag', 'taxonomy singular name', 'happy-market-learning' ),
			'search_items'               => __( 'Search Tags', 'happy-market-learning' ),
			'popular_items'              => __( 'Popular Tags', 'happy-market-learning' ),
			'all_items'                  => __( 'All Tags', 'happy-market-learning' ),
			'edit_item'                  => __( 'Edit Tag', 'happy-market-learning' ),
			'update_item'                => __( 'Update Tag', 'happy-market-learning' ),
			'add_new_item'               => __( 'Add New Tag', 'happy-market-learning' ),
			'new_item_name'              => __( 'New Tag Name', 'happy-market-learning' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'happy-market-learning' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'happy-market-learning' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', 'happy-market-learning' ),
			'not_found'                  => __( 'No tags found.', 'happy-market-learning' ),
			'menu_name'                  => __( 'Tags', 'happy-market-learning' ),
		);

		$tag_args = array(
			'hierarchical'          => false,
			'labels'                => $tag_labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'show_in_rest'          => true,
			'rewrite'               => array( 'slug' => 'lesson-tag' ),
		);

		register_taxonomy( 'hm_lesson_tag', array( 'hm_lesson' ), $tag_args );
	}
}
