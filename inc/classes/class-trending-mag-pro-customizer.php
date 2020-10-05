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

			$this->color_options( $wp_customize );
			$this->typography( $wp_customize );
		}


		/**
		 * Init fields.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		private function color_options( $wp_customize ) {

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


		/**
		 * Fonts typography pro features.
		 */
		private function typography( $wp_customize ) {

			$font_weights = array(
				''    => 'default',
				'100' => '100',
				'200' => '200',
				'300' => '300',
				'400' => '400',
				'500' => '500',
				'600' => '600',
				'700' => '700',
				'800' => '800',
				'900' => '900',
			);

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'select',
					'name'              => trending_mag_customizer_fields_settings_id( 'general_options', 'typography', 'excerpt_more' ),
					'default'           => 'default',
					'sanitize_callback' => 'trending_mag_sanitize_select',
					'label'             => esc_html__( 'Excerpt More', 'trending-mag' ),
					'choices'           => array(
						'default' => __( 'Default', 'trending-mag-pro' ) . ' [...]',
						'dots'    => __( 'Three Dots', 'trending-mag-pro' ) . ' ...',
						'link'    => __( 'Read More', 'trending-mag-pro' ) . ' ( link )',
					),
					'section'           => trending_mag_get_customizer_section_id( 'general_options', 'typography' ),
					'priority'          => 15,
				)
			);

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'select',
					'name'              => trending_mag_customizer_fields_settings_id( 'general_options', 'typography', 'posts_title_font_weight' ),
					'default'           => 'default',
					'sanitize_callback' => 'trending_mag_sanitize_select',
					'label'             => esc_html__( 'Posts Title Font Weight', 'trending-mag' ),
					'choices'           => $font_weights,
					'description'       => __( 'Notice: Font weight might not display expected result with every fonts.', 'trending-mag-pro' ),
					'section'           => trending_mag_get_customizer_section_id( 'general_options', 'typography' ),
					'priority'          => 20,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'number',
					'name'              => trending_mag_customizer_fields_settings_id( 'general_options', 'typography', 'posts_title_font_size' ),
					'sanitize_callback' => 'absint',
					'input_attrs'       => array(
						'min' => 0,
					),
					'label'             => esc_html__( 'Posts Title Font Size', 'trending-mag' ),
					'description'       => __( 'Font size in pixels.', 'trending-mag-pro' ),
					'section'           => trending_mag_get_customizer_section_id( 'general_options', 'typography' ),
					'priority'          => 25,
				)
			);

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'select',
					'name'              => trending_mag_customizer_fields_settings_id( 'general_options', 'typography', 'posts_content_font_weight' ),
					'default'           => 'default',
					'sanitize_callback' => 'trending_mag_sanitize_select',
					'label'             => esc_html__( 'Posts Content Font Weight', 'trending-mag' ),
					'choices'           => $font_weights,
					'description'       => __( 'Notice: Font weight might not display expected result with every fonts.', 'trending-mag-pro' ),
					'section'           => trending_mag_get_customizer_section_id( 'general_options', 'typography' ),
					'priority'          => 30,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'number',
					'name'              => trending_mag_customizer_fields_settings_id( 'general_options', 'typography', 'posts_content_font_size' ),
					'sanitize_callback' => 'absint',
					'input_attrs'       => array(
						'min' => 0,
					),
					'label'             => esc_html__( 'Posts Content Font Size', 'trending-mag' ),
					'description'       => __( 'Font size in pixels.', 'trending-mag-pro' ),
					'section'           => trending_mag_get_customizer_section_id( 'general_options', 'typography' ),
					'priority'          => 35,
				)
			);

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'number',
					'name'              => trending_mag_customizer_fields_settings_id( 'general_options', 'typography', 'line_spacing' ),
					'sanitize_callback' => 'absint',
					'input_attrs'       => array(
						'min' => 0,
					),
					'label'             => esc_html__( 'Line Spacing', 'trending-mag' ),
					'description'       => __( 'Line spacing in pixels.', 'trending-mag-pro' ),
					'section'           => trending_mag_get_customizer_section_id( 'general_options', 'typography' ),
					'priority'          => 35,
				)
			);
		}

	}

	new Trending_Mag_Pro_Customizer();
}


