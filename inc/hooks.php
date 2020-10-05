<?php
/**
 * Include all the pluggable here,
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Saves the sharer count.
 * Migrated from the theme to the plugin.
 */
function trending_mag_pro_save_sharer_count() {
	if ( ! is_single() ) {
		return;
	}
	if ( ! isset( $_POST['trending_mag'] ) ) {
		return;
	}
	if ( ! isset( $_POST['trending_mag_sharer_nonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['trending_mag_sharer_nonce'] ) ), 'trending_mag_sharer_nonce' ) ) {
		return;
	}
	$submitted_data = sanitize_meta( 'trending_mag', $_POST, 'post' );
	$post_id        = ! empty( $submitted_data['trending_mag']['sharer']['post_id'] ) ? $submitted_data['trending_mag']['sharer']['post_id'] : '';
	if ( empty( $post_id ) ) {
		return;
	}
	$prev_count = get_post_meta( $post_id, 'trending_mag_sharer_count', true );
	if ( '' !== $prev_count ) {
		$prev_count++;
		update_post_meta( $post_id, 'trending_mag_sharer_count', $prev_count );
	} else {
		add_post_meta( $post_id, 'trending_mag_sharer_count', 0 );
	}
}
add_action( 'wp_head', 'trending_mag_pro_save_sharer_count' );


/**
 * Modify the excerpt more.
 */
function trending_mag_pro_custom_excerpt_more( $more ) {

	$excerpt_more = trending_mag_get_theme_mod( 'general_options', 'typography', 'excerpt_more' );

	if ( 'default' === $excerpt_more || ! $excerpt_more ) {
		return $more;
	}

	if ( 'dots' === $excerpt_more ) {
		return '...';
	}

	if ( 'link' === $excerpt_more ) {
		return ' <a class="read-more-link" href="' . get_permalink() . '">' . esc_html__( '<< Read More >>', 'trending-mag' ) . '</a>';
	}

	return $more;
}
add_filter( 'excerpt_more', 'trending_mag_pro_custom_excerpt_more' );

