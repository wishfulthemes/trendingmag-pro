<?php
/**
 * This file handles the required customizer class for enabling some pro features.
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Trending_Mag_Pro_Customizer' ) ) {

	/**
	 * Pro options in customizer.
	 */
	class Trending_Mag_Pro_Customizer {

		/**
		 * Init customizer class.
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'load_customizer' ) );
			add_action( 'trending_mag_dynamic_css', array( $this, 'css_styles' ) );
		}


		/**
		 * Generates the css.
		 */
		private function render_css( $selector, $property, $value ) {
			$css = $selector . '{ ' . $property . ':' . $value . '; }';
			return $css;
		}


		/**
		 * Does the css changes according to the fields data.
		 */
		public function css_styles( $custom_css ) {
			$mods = get_theme_mod( 'trending_mag_theme_options' );

			$colors = isset( $mods['colors']['colors'] ) ? $mods['colors']['colors'] : '';

			$topbar_background_color = isset( $colors['topbar_background_color'] ) ? sanitize_hex_color( $colors['topbar_background_color'] ) : '';
			$header_background_color = isset( $colors['header_background_color'] ) ? sanitize_hex_color( $colors['header_background_color'] ) : '';
			$footer_background_color = isset( $colors['footer_background_color'] ) ? sanitize_hex_color( $colors['footer_background_color'] ) : '';

			$custom_css .= $this->render_css( '.rm-header-s1 .header-top-block', 'background-color', $topbar_background_color );
			$custom_css .= $this->render_css( '.rm-header-s1 .rm-logo-block', 'background-color', $header_background_color );
			$custom_css .= $this->render_css( '.footer .footer-inner', 'background-color', $footer_background_color );

			return $custom_css;
		}


		/**
		 * Load customizer methods.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function load_customizer( $wp_customize ) {

			/**
			 * Bail early if trending mag theme is not activated.
			 * Or when the required function is not found.
			 */
			if ( ! function_exists( 'trending_mag_register_option' ) ) {
				return $wp_customize;
			}

			/**
			 * Bail early if trending mag theme is not activated.
			 * Or when the required function is not found.
			 */
			if ( ! function_exists( 'trending_mag_customizer_fields_settings_id' ) ) {
				return $wp_customize;
			}

			$this->fields( $wp_customize );
		}


		/**
		 * Init fields.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		private function fields( $wp_customize ) {

			/**
			 * ===================
			 * Colors Options
			 * ===================
			 */

			// .rm-header-s1 .header-top-block
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'Topbar Background Color' ),
					'default'           => '#f1f1f1',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Topbar Background Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 12,
				)
			);

			// .rm-header-s1 .rm-logo-block
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'Header Background Color' ),
					'default'           => '#fffff',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Header Background Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 14,
				)
			);

			// .footer .footer-inner
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'Footer Background Color' ),
					'default'           => '#111111',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Footer Background Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 30,
				)
			);

		}

	}

	new Trending_Mag_Pro_Customizer();
}


