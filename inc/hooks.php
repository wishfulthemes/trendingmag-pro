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

	if ( ! function_exists( 'trending_mag_get_theme_mod' ) ) {
		return $more;
	}

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



if ( ! function_exists( 'trending_mag_pro_copyright_text' ) ) {

	/**
	 * Sets custom copyright text.
	 */
	function trending_mag_pro_copyright_text( $copyright ) {

		if ( ! $copyright || ! function_exists( 'trending_mag_get_theme_mod' ) ) {
			return $copyright;
		}

		$custom_copyright = trending_mag_get_theme_mod( 'general_options', 'footer_options', 'copyright_text' );

		if ( $custom_copyright ) {
			$copyright  = '';
			$copyright .= '<div class="rm-copy-right">';
			$copyright .= wpautop( $custom_copyright );
			$copyright .= '</div><!-- // rm-copy-right -->';
		}

		return $copyright;
	}
	add_filter( 'trending_mag_footer_copyright', 'trending_mag_pro_copyright_text' );
}


if ( function_exists( 'trending_mag_get_theme_mod' ) && 'header-one' !== trending_mag_get_theme_mod( 'general_options', 'header', 'header_layout' ) ) {

	$header_layout = trending_mag_get_theme_mod( 'general_options', 'header', 'header_layout' );

	if ( 'header-two' === $header_layout ) {
		$is_removed = remove_action( 'trending_mag_header_logo_contents', 'trending_mag_header_site_identity', 15 );
		remove_action( 'trending_mag_header_logo_contents', 'trending_mag_header_ad', 20 );
	}

	error_log(
		print_r(
			array(
				'is-removed'    => $is_removed,
				'header-layout' => $header_layout,
			),
			true
		)
	);


	if ( ! function_exists( 'trending_mag_pro_header_layout_two' ) ) {

		/**
		 * Set html for header layout two.
		 */
		function trending_mag_pro_header_layout_two() {
			$panel   = 'title_tagline';
			$section = 'title_tagline';

			$site_title = ! trending_mag_get_theme_mod( $panel, $section, 'hide_site_title' ) ? get_bloginfo() : '';
			$tagline    = ! trending_mag_get_theme_mod( $panel, $section, 'hide_tagline' ) && get_bloginfo( 'description' ) ? sprintf( '<p class="site-description">%s</p>', esc_html( get_bloginfo( 'description' ) ) ) : '';
			$has_logo   = function_exists( 'has_custom_logo' ) && function_exists( 'the_custom_logo' ) && has_custom_logo();

			?>
			<!-- header-layout-two -->
			<div class="rm-col">

				<?php
				if ( $has_logo ) {
					?>
					<div class="site-identity">
						<?php the_custom_logo(); ?>
					</div><!-- // site-identity -->
				<?php } ?>

				<?php if ( $site_title || $tagline ) { ?>
					<div class="site-branding-text" >
						<?php if ( $site_title ) { ?>
							<h1 class="site-title">
								<a href="<?php echo esc_url( home_url() ); ?>" rel="home"><?php echo esc_html( $site_title ); ?></a>
							</h1>
						<?php } ?>
						<?php echo wp_kses_post( $tagline ); ?>
					</div>
				<?php } ?>

			</div><!-- // rm-col -->
			<!-- // header-layout-two -->
			<?php
		}
		add_action( 'trending_mag_header_logo_contents', 'trending_mag_pro_header_layout_two', 15 );
	}
}
