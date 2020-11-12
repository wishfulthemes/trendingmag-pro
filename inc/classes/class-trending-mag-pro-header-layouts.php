<?php
/**
 * Class file for header layout options.
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Trending_Mag_Pro_Header_Layouts' ) ) {

	/**
	 * Class for header layout options.
	 */
	class Trending_Mag_Pro_Header_Layouts {

		/**
		 * Header layout option.
		 *
		 * @var $header_layout
		 */
		public $header_layout = null;

		public function __construct() {
			add_action( 'wp_head', array( $this, 'init' ) );

		}

		public function init() {
			if ( ! function_exists( 'trending_mag_get_theme_mod' ) ) {
				return;
			}

			$header_layout = trending_mag_get_theme_mod( 'general_options', 'header', 'header_layout' );

			if ( 'header-one' === $header_layout ) {
				return;
			}

			$this->header_layout = $header_layout;
			$this->remove_actions();

			if ( 'header-two' === $header_layout ) {
				add_action( 'trending_mag_header_logo_contents', array( $this, 'header_layout_two' ) );
			}
		}

		public function remove_actions() {
			$header_layout = $this->header_layout;

			if ( 'header-two' === $header_layout ) {
				remove_action( 'trending_mag_header_logo_contents', 'trending_mag_header_site_identity' );
				remove_action( 'trending_mag_header_logo_contents', 'trending_mag_header_ad', 15 );
			}

		}

		public function header_layout_two() {
			$panel   = 'title_tagline';
			$section = 'title_tagline';

			$site_title = ! trending_mag_get_theme_mod( $panel, $section, 'hide_site_title' ) ? get_bloginfo() : '';
			$tagline    = ! trending_mag_get_theme_mod( $panel, $section, 'hide_tagline' ) && get_bloginfo( 'description' ) ? sprintf( '<p class="site-description">%s</p>', esc_html( get_bloginfo( 'description' ) ) ) : '';
			$has_logo   = function_exists( 'has_custom_logo' ) && function_exists( 'the_custom_logo' ) && has_custom_logo();

			?>
			<!-- header-layout-two -->
			<div class="rm-col header-layout-two">

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

	}

	new Trending_Mag_Pro_Header_Layouts();
}
