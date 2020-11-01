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
		 * Array of font weights.
		 *
		 * @var $font_weights
		 */
		private $font_weights = array(
			''    => 'Default',
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


		/**
		 * Init customizer class.
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'load_customizer' ) );
			add_action( 'customize_register', array( $this, 'settings_override' ), 999 );
			add_action( 'trending_mag_dynamic_css', array( $this, 'css_styles' ) );
		}


		/**
		 * Generates the css.
		 */
		private function render_css( $selector, $property, $value ) {
			$css = $selector . '{ ' . $property . ':' . esc_attr( $value ) . '; }';
			return $css;
		}

		private function frontpage_typography_controls( $wp_customize, $options ) {

			if ( ! function_exists( 'trending_mag_register_option' ) ) {
				return;
			}

			if ( ! function_exists( 'trending_mag_customizer_fields_settings_id' ) ) {
				return;
			}

			if ( ! function_exists( 'trending_mag_get_customizer_section_id' ) ) {
				return;
			}

			$panel_id   = $options['panel_id'];
			$section_id = $options['section_id'];
			$unset      = isset( $options['unset'] ) ? $options['unset'] : false;

			$font_weights = $this->font_weights;

			$active_callback = "trending_mag_customizer_is_{$section_id}_enabled";

			/**
			 * Options for section headings.
			 */

			if ( 'heading' !== $unset ) {
				trending_mag_register_option(
					$wp_customize,
					array(
						'type'              => 'select',
						'name'              => trending_mag_customizer_fields_settings_id( $panel_id, $section_id, 'heading_font_weight' ),
						'default'           => 'default',
						'active_callback'   => $active_callback,
						'sanitize_callback' => 'trending_mag_sanitize_select',
						'label'             => esc_html__( 'Heading Font Weight', 'trending-mag' ),
						'choices'           => $font_weights,
						'description'       => __( 'Notice: Font weight might not display expected result with every fonts.', 'trending-mag-pro' ),
						'section'           => trending_mag_get_customizer_section_id( $panel_id, $section_id ),
						'priority'          => 40,
					)
				);
				trending_mag_register_option(
					$wp_customize,
					array(
						'type'              => 'number',
						'name'              => trending_mag_customizer_fields_settings_id( $panel_id, $section_id, 'heading_font_size' ),
						'active_callback'   => $active_callback,
						'sanitize_callback' => 'absint',
						'input_attrs'       => array(
							'min' => 0,
						),
						'label'             => esc_html__( 'Heading Font Size', 'trending-mag' ),
						'description'       => __( 'Font size in pixels.', 'trending-mag-pro' ),
						'section'           => trending_mag_get_customizer_section_id( $panel_id, $section_id ),
						'priority'          => 45,
					)
				);
			}

			/**
			 * Options for section contents.
			 */
			if ( 'content' !== $unset ) {
				trending_mag_register_option(
					$wp_customize,
					array(
						'type'              => 'select',
						'name'              => trending_mag_customizer_fields_settings_id( $panel_id, $section_id, 'content_font_weight' ),
						'default'           => 'default',
						'active_callback'   => $active_callback,
						'sanitize_callback' => 'trending_mag_sanitize_select',
						'label'             => esc_html__( 'Content Font Weight', 'trending-mag' ),
						'choices'           => $font_weights,
						'description'       => __( 'Notice: Font weight might not display expected result with every fonts.', 'trending-mag-pro' ),
						'section'           => trending_mag_get_customizer_section_id( $panel_id, $section_id ),
						'priority'          => 50,
					)
				);
				trending_mag_register_option(
					$wp_customize,
					array(
						'type'              => 'number',
						'name'              => trending_mag_customizer_fields_settings_id( $panel_id, $section_id, 'content_font_size' ),
						'active_callback'   => $active_callback,
						'sanitize_callback' => 'absint',
						'input_attrs'       => array(
							'min' => 0,
						),
						'label'             => esc_html__( 'Content Font Size', 'trending-mag' ),
						'description'       => __( 'Font size in pixels.', 'trending-mag-pro' ),
						'section'           => trending_mag_get_customizer_section_id( $panel_id, $section_id ),
						'priority'          => 55,
					)
				);
			}

		}


		/**
		 * Does the css changes according to the fields data.
		 */
		public function css_styles( $custom_css ) {
			$mods = get_theme_mod( 'trending_mag_theme_options' );

			$colors = isset( $mods['colors']['colors'] ) ? $mods['colors']['colors'] : '';

			$typography = isset( $mods['general_options']['typography'] ) ? $mods['general_options']['typography'] : '';

			$topbar_background_color      = isset( $colors['topbar_background_color'] ) ? sanitize_hex_color( $colors['topbar_background_color'] ) : '';
			$topbar_text_color            = isset( $colors['topbar_text_color'] ) ? sanitize_hex_color( $colors['topbar_text_color'] ) : '#333333';
			$header_background_color      = isset( $colors['header_background_color'] ) ? sanitize_hex_color( $colors['header_background_color'] ) : '';
			$menu_color                   = isset( $colors['menu_color'] ) ? sanitize_hex_color( $colors['menu_color'] ) : '#ffffff';
			$menu_hover_color             = isset( $colors['menu_hover_color'] ) ? sanitize_hex_color( $colors['menu_hover_color'] ) : '#333333';
			$submenu_color                = isset( $colors['submenu_color'] ) ? sanitize_hex_color( $colors['submenu_color'] ) : '';
			$submenu_hover_color          = isset( $colors['submenu_hover_color'] ) ? sanitize_hex_color( $colors['submenu_hover_color'] ) : '#EA2027';
			$search_icon_color            = isset( $colors['search_icon_color'] ) ? sanitize_hex_color( $colors['search_icon_color'] ) : '#333333';
			$header_widgets_icon_color    = isset( $colors['header_widgets_icon_color'] ) ? sanitize_hex_color( $colors['header_widgets_icon_color'] ) : '#333333';
			$section_title_bar_color      = isset( $colors['section_title_bar_color'] ) ? sanitize_hex_color( $colors['section_title_bar_color'] ) : '';
			$section_title_bar_font_color = isset( $colors['section_title_bar_font_color'] ) ? sanitize_hex_color( $colors['section_title_bar_font_color'] ) : '';
			$footer_background_color      = isset( $colors['footer_background_color'] ) ? sanitize_hex_color( $colors['footer_background_color'] ) : '';
			$footer_font_color            = isset( $colors['footer_font_color'] ) ? sanitize_hex_color( $colors['footer_font_color'] ) : '';
			$footer_link_color            = isset( $colors['footer_link_color'] ) ? sanitize_hex_color( $colors['footer_link_color'] ) : '';

			$custom_css .= $this->render_css( '.rm-header-s1 .header-top-block', 'background-color', $topbar_background_color );
			$custom_css .= $this->render_css( '.rm-header-s1 .header-top-block *', 'color', $topbar_text_color );
			$custom_css .= $this->render_css( '.rm-header-s1 .rm-logo-block', 'background-color', $header_background_color );
			$custom_css .= $this->render_css( '.bottom-header .primary-navigation-wrap .menu li a', 'color', $menu_color );
			$custom_css .= $this->render_css( '.bottom-header .primary-navigation-wrap .menu li a:hover', 'color', "{$menu_hover_color} !important" );
			$custom_css .= $this->render_css( '.bottom-header .site-navigation .menu li .sub-menu li a', 'color', $submenu_color );
			$custom_css .= $this->render_css( '.bottom-header .site-navigation .menu li .sub-menu li a:hover', 'color', "{$submenu_hover_color} !important" );
			$custom_css .= $this->render_css( '.mastheader .search-trigger', 'color', $search_icon_color );
			$custom_css .= $this->render_css( '.mastheader .canvas-trigger', 'color', $header_widgets_icon_color );
			$custom_css .= $this->render_css( '.widget-title', 'background-color', $section_title_bar_color );
			$custom_css .= $this->render_css( '.widget-title .title', 'color', $section_title_bar_font_color );
			$custom_css .= $this->render_css( '.footer .footer-inner', 'background-color', $footer_background_color );
			$custom_css .= $this->render_css( '.footer *, .calendar_wrap caption', 'color', $footer_font_color );
			$custom_css .= $this->render_css( '.footer a', 'color', $footer_link_color );

			/**
			 * Frontpage color mods.
			 */
			$frontpage   = isset( $mods['front_page'] ) ? $mods['front_page'] : '';
			$news_ticker = isset( $frontpage['news_ticker'] ) ? $frontpage['news_ticker'] : '';

			$heading_background_color = isset( $news_ticker['heading_background_color'] ) ? sanitize_hex_color( $news_ticker['heading_background_color'] ) : '';
			$heading_text_color       = isset( $news_ticker['heading_text_color'] ) ? sanitize_hex_color( $news_ticker['heading_text_color'] ) : '';
			$posts_background_color   = isset( $news_ticker['posts_background_color'] ) ? sanitize_hex_color( $news_ticker['posts_background_color'] ) : '';
			$posts_text_color         = isset( $news_ticker['posts_text_color'] ) ? sanitize_hex_color( $news_ticker['posts_text_color'] ) : '';

			$custom_css .= $this->render_css( '.nt_title', 'background-color', $heading_background_color );
			$custom_css .= $this->render_css( '.nt_title', 'color', $heading_text_color );
			$custom_css .= $this->render_css( '.tickercontainer .mask', 'background-color', $posts_background_color );
			$custom_css .= $this->render_css( '.tickercontainer .mask a', 'color', $posts_text_color );

			/**
			 * Typography styles.
			 */
			if ( isset( $typography['posts_title_font_weight'] ) && ! empty( $typography['posts_title_font_weight'] ) ) {
				$custom_css .= $this->render_css( 'q, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, blockquote', 'font-weight', $typography['posts_title_font_weight'] );
			}
			if ( isset( $typography['posts_title_font_size'] ) && ! empty( $typography['posts_title_font_size'] ) ) {
				$custom_css .= $this->render_css( 'h2', 'font-size', "{$typography['posts_title_font_size']}px" );
			}
			if ( isset( $typography['posts_content_font_weight'] ) && ! empty( $typography['posts_content_font_weight'] ) ) {
				$custom_css .= $this->render_css( 'p', 'font-weight', $typography['posts_content_font_weight'] );
			}
			if ( isset( $typography['posts_content_font_size'] ) && ! empty( $typography['posts_content_font_size'] ) ) {
				$custom_css .= $this->render_css( 'p', 'font-size', "{$typography['posts_content_font_size']}px" );
			}
			if ( isset( $typography['line_spacing'] ) && ! empty( $typography['line_spacing'] ) ) {
				$custom_css .= $this->render_css( 'p', 'line-height', "{$typography['line_spacing']}px" );
			}

			/**
			 * Frontpage sections typography mods.
			 */
			$frontpage_typo_options = array(
				'front_page' => array(
					'news_ticker'   => array(
						'heading' => '.nt_title',
						'content' => '#webticker li a span',
					),
					'banner_slider' => array(
						'heading' => '.rm-banner-s1 .post-title h2',
					),
					'section_one'   => array(
						'heading' => '.rm-full-widget-area.full-layout-1 .rm-col.right .widget-inn-tt h2',
						'content' => '.rm-full-widget-area.full-layout-1 .rm-col.right p',
					),
					'section_two'   => array(
						'heading' => '.rm-full-widget-area .layout-2 .rm-col.right .widget-inn-tt h2',
						'content' => '.rm-full-widget-area .layout-2 .rm-col.right p',
					),
					'section_three' => array(
						'heading' => '.rm-full-widget-area .layout-3 .small-widget-area .widget-inn-tt .tt-in',
					),
					'section_four'  => array(
						'heading' => '.rm-full-widget-area.rm-banner-s3 .banner-inner .rm-row .slick-slide .rm-widget-area-wraper .post-title h2',
					),
					'section_five'  => array(
						'heading' => '.rm-full-widget-area .layout-5 .row .widget-inn-tt h2',
						'content' => '.rm-full-widget-area .layout-5 .row p',
					),
				),
			);

			/**
			 * Let's roll it.
			 */
			if ( is_array( $frontpage_typo_options ) && ! empty( $frontpage_typo_options ) ) {
				foreach ( $frontpage_typo_options as $panel_id => $sections ) {

					if ( is_array( $sections ) && ! empty( $sections ) ) {
						foreach ( $sections as $section_id => $typo_settings_type ) {

							$heading_font_weight = ! empty( $frontpage[ $section_id ]['heading_font_weight'] ) ? $frontpage[ $section_id ]['heading_font_weight'] : '';
							$heading_font_size   = ! empty( $frontpage[ $section_id ]['heading_font_size'] ) ? $frontpage[ $section_id ]['heading_font_size'] : '';
							$content_font_weight = ! empty( $frontpage[ $section_id ]['content_font_weight'] ) ? $frontpage[ $section_id ]['content_font_weight'] : '';
							$content_font_size   = ! empty( $frontpage[ $section_id ]['content_font_size'] ) ? $frontpage[ $section_id ]['content_font_size'] : '';

							$heading_class = ! empty( $typo_settings_type['heading'] ) ? $typo_settings_type['heading'] : '';
							$content_class = ! empty( $typo_settings_type['content'] ) ? $typo_settings_type['content'] : '';

							/**
							 * Do the magic !!!
							 */
							if ( $heading_class && $heading_font_weight && 'default' !== $heading_font_weight ) {
								$custom_css .= $this->render_css( $heading_class, 'font-weight', $heading_font_weight );
							}

							if ( $heading_class && $heading_font_size && 'default' !== $heading_font_size ) {
								$custom_css .= $this->render_css( $heading_class, 'font-size', "{$heading_font_size}px" );
							}

							if ( $content_class && $content_font_weight && 'default' !== $content_font_weight ) {
								$custom_css .= $this->render_css( $content_class, 'font-weight', $content_font_weight );
							}

							if ( $content_class && $content_font_size && 'default' !== $content_font_size ) {
								$custom_css .= $this->render_css( $content_class, 'font-size', "{$content_font_size}px" );
							}
						}
					}
				}
			}

			/**
			 * For header layouts.
			 */
			$custom_css .= $this->render_css( '.header-layout-two', 'width', '50%' );
			$custom_css .= $this->render_css( '.header-layout-two', 'margin', '0 auto' );

			return $custom_css;
		}

		/**
		 * We will override the required settings from this function.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function settings_override( $wp_customize ) {

			if ( ! function_exists( 'trending_mag_customizer_fields_settings_id' ) ) {
				return $wp_customize;
			}

			/**
			 * Front Page > Section Three > Category.
			 * Remove category selection limitation.
			 */
			$wp_customize->get_control( trending_mag_customizer_fields_settings_id( 'front_page', 'section_three', 'category' ) )->description          = esc_html__( 'Select categories for your contents', 'trending-mag' );
			$wp_customize->get_control( trending_mag_customizer_fields_settings_id( 'front_page', 'section_three', 'category' ) )->input_attrs['limit'] = false;

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
			$this->header_options( $wp_customize );
			$this->footer_options( $wp_customize );
			$this->typography( $wp_customize );
			$this->frontpage_section_colors( $wp_customize );
			$this->frontpage_section_typography( $wp_customize );
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
					'priority'          => 10,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'topbar_text_color' ),
					'default'           => '#333333',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Topbar Text Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 12,
				)
			);

			/**
			 * Menus and sub menus.
			 */
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'menu_color' ),
					'default'           => '#ffffff',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Menu Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 14,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'menu_hover_color' ),
					'default'           => '#333333',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Menu Hover Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 16,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'submenu_color' ),
					'default'           => '#333333',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Sub Menu Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 18,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'submenu_hover_color' ),
					'default'           => '#EA2027',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Sub Menu Hover Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 20,
				)
			);

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'search_icon_color' ),
					'default'           => '#333333',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Search Icon Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 22,
				)
			);

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'header_widgets_icon_color' ),
					'default'           => '#333333',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Header Widgets Icon Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 24,
				)
			);

			// .rm-header-s1 .rm-logo-block
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'Header Background Color' ),
					'default'           => '#ffffff',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Header Background Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 26,
				)
			);

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'section_title_bar_color' ),
					'default'           => '#eeeeee',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Section Title Bar Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 28,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'section_title_bar_font_color' ),
					'default'           => '#000000',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Section Title Bar Font Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 30,
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
					'priority'          => 32,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'footer_font_color' ),
					'default'           => '#ffffff',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Footer Font Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 34,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( 'colors', 'colors', 'footer_link_color' ),
					'default'           => '#949494',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Footer Link Color', 'trending-mag' ),
					'section'           => 'colors',
					'priority'          => 36,
				)
			);

		}

		private function header_options( $wp_customize ) {

			$header_layouts = array(
				'header-one' => __( 'Header One [ Default ]', 'trending-mag-pro' ),
				'header-two' => __( 'Header Two', 'trending-mag-pro' ),
			);

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'select',
					'name'              => trending_mag_customizer_fields_settings_id( 'general_options', 'header', 'header_layout' ),
					'default'           => 'header-one',
					'sanitize_callback' => 'trending_mag_sanitize_select',
					'label'             => esc_html__( 'Header Layout', 'trending-mag' ),
					'choices'           => $header_layouts,
					'section'           => trending_mag_get_customizer_section_id( 'general_options', 'header' ),
					'priority'          => 20,
				)
			);

		}

		private function footer_options( $wp_customize ) {

			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'textarea',
					'name'              => trending_mag_customizer_fields_settings_id( 'general_options', 'footer_options', 'copyright_text' ),
					'sanitize_callback' => 'wp_kses_post',
					'label'             => esc_html__( 'Copyright Text', 'trending-mag' ),
					'description'       => __( 'You can enter your custom copyright text from here.', 'trending-mag-pro' ),
					'section'           => trending_mag_get_customizer_section_id( 'general_options', 'footer_options' ),
					'priority'          => 35,
				)
			);
		}


		/**
		 * Fonts typography pro features.
		 */
		private function typography( $wp_customize ) {

			$font_weights = $this->font_weights;

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

		private function frontpage_section_colors( $wp_customize ) {

			$trending_mag_panel_name = 'front_page';

			/**
			 * Newsticker
			 */
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( $trending_mag_panel_name, 'news_ticker', 'heading_background_color' ),
					'default'           => '#333333',
					'active_callback'   => 'trending_mag_customizer_is_news_ticker_enabled',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Heading Background Color', 'trending-mag' ),
					'section'           => trending_mag_get_customizer_section_id( $trending_mag_panel_name, 'news_ticker' ),
					'priority'          => 20,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( $trending_mag_panel_name, 'news_ticker', 'heading_text_color' ),
					'default'           => '#FFFFFF',
					'active_callback'   => 'trending_mag_customizer_is_news_ticker_enabled',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Heading Text Color', 'trending-mag' ),
					'section'           => trending_mag_get_customizer_section_id( $trending_mag_panel_name, 'news_ticker' ),
					'priority'          => 25,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( $trending_mag_panel_name, 'news_ticker', 'posts_background_color' ),
					'default'           => '#f1f1f1', // .tickercontainer .mask
					'active_callback'   => 'trending_mag_customizer_is_news_ticker_enabled',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Posts Background Color', 'trending-mag' ),
					'section'           => trending_mag_get_customizer_section_id( $trending_mag_panel_name, 'news_ticker' ),
					'priority'          => 30,
				)
			);
			trending_mag_register_option(
				$wp_customize,
				array(
					'type'              => 'color',
					'custom_control'    => 'WP_Customize_Color_Control',
					'name'              => trending_mag_customizer_fields_settings_id( $trending_mag_panel_name, 'news_ticker', 'posts_text_color' ),
					'default'           => '#333333',
					'active_callback'   => 'trending_mag_customizer_is_news_ticker_enabled',
					'sanitize_callback' => 'sanitize_hex_color',
					'label'             => esc_html__( 'Posts Text Color', 'trending-mag' ),
					'section'           => trending_mag_get_customizer_section_id( $trending_mag_panel_name, 'news_ticker' ),
					'priority'          => 35,
				)
			);
		}

		private function frontpage_section_typography( $wp_customize ) {

			$options = array(
				'front_page' => array(
					'news_ticker'   => false,
					'banner_slider' => 'content',
					'section_one'   => false,
					'section_two'   => false,
					'section_three' => 'content',
					'section_four'  => 'content',
					'section_five'  => false,
				),
			);

			if ( is_array( $options ) && ! empty( $options ) ) {
				foreach ( $options as $panel_id => $sections ) {

					if ( is_array( $sections ) && ! empty( $sections ) ) {
						foreach ( $sections as $section_id => $unset ) {

							$this->frontpage_typography_controls(
								$wp_customize,
								array(
									'panel_id'   => $panel_id,
									'section_id' => $section_id,
									'unset'      => $unset,
								)
							);

						}
					}
				}
			}

		}

	}

	new Trending_Mag_Pro_Customizer();
}


