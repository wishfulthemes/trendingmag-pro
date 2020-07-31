<?php
/**
 * Here we register all the custom post types for this plugin.
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'trending_mag_pro_custom_post_type' ) ) {

	/**
	 * Register custom post type.
	 */
	function trending_mag_pro_custom_post_type() {

		$labels = array(
			'name'                  => _x( 'Polls', 'Post Type General Name', 'trending-mag-pro' ),
			'singular_name'         => _x( 'Poll', 'Post Type Singular Name', 'trending-mag-pro' ),
			'menu_name'             => __( 'Polls', 'trending-mag-pro' ),
			'name_admin_bar'        => __( 'Polls', 'trending-mag-pro' ),
			'archives'              => __( 'Polls Archives', 'trending-mag-pro' ),
			'attributes'            => __( 'Polls Attributes', 'trending-mag-pro' ),
			'parent_item_colon'     => __( 'Parent Poll:', 'trending-mag-pro' ),
			'all_items'             => __( 'All Polls', 'trending-mag-pro' ),
			'add_new_item'          => __( 'New Poll', 'trending-mag-pro' ),
			'add_new'               => __( 'New Poll', 'trending-mag-pro' ),
			'new_item'              => __( 'New Item', 'trending-mag-pro' ),
			'edit_item'             => __( 'Edit Item', 'trending-mag-pro' ),
			'update_item'           => __( 'Update Item', 'trending-mag-pro' ),
			'view_item'             => __( 'View Poll', 'trending-mag-pro' ),
			'view_items'            => __( 'View Polls', 'trending-mag-pro' ),
			'search_items'          => __( 'Search Poll', 'trending-mag-pro' ),
			'not_found'             => __( 'Not found', 'trending-mag-pro' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'trending-mag-pro' ),
			'items_list'            => __( 'Polls list', 'trending-mag-pro' ),
			'items_list_navigation' => __( 'Polls list navigation', 'trending-mag-pro' ),
			'filter_items_list'     => __( 'Filter Polls list', 'trending-mag-pro' ),
		);
		$args   = array(
			'label'               => __( 'Polls', 'trending-mag-pro' ),
			'description'         => __( 'Create polls to under your users in a better way.', 'trending-mag-pro' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-chart-bar',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
		);
		register_post_type( 'trending-mag-polls', $args );

	}
	add_action( 'init', 'trending_mag_pro_custom_post_type', 10 );

}


/**
 * Modifies the post update message.
 *
 * @param array $messages Post update messages.
 */
function trending_mag_pro_alter_post_update_messages( $messages ) {

	if ( 'trending-mag-polls' === get_post_type() ) {
		$messages['post'][1] = esc_html__( 'Poll updated.', 'trending-mag-pro' );
		$messages['post'][6] = esc_html__( 'Poll published.', 'trending-mag-pro' );
		$messages['post'][7] = esc_html__( 'Poll saved.', 'trending-mag-pro' );
	}

	return $messages;
}
add_filter( 'post_updated_messages', 'trending_mag_pro_alter_post_update_messages' );
