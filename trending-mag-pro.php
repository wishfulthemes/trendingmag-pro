<?php
/**
 * Plugin Name: Trending Mag Pro
 * Plugin URI: https://www.wishfulthemes.com/
 * Description: This plugin provides you the best premium features for your Trending Mag theme to enhance your website to next level.
 * Author: Wishful Themes
 * Author URI: https://www.wishfulthemes.com/
 * Version: 1.0.0
 * Text Domain: trending-mag-pro
 * License: GPLv2 or later
 * License URI: LICENSE
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$trending_mag_pro_plugin_data    = function_exists( 'get_file_data' ) ? get_file_data( __FILE__, array( 'Version' => 'Version' ), false ) : array();
$trending_mag_pro_plugin_version = isset( $trending_mag_pro_plugin_data['Version'] ) ? $trending_mag_pro_plugin_data['Version'] : '1.0.0';

! defined( 'TRENDING_MAG_PRO_ROOT' ) ? define( 'TRENDING_MAG_PRO_ROOT', plugin_dir_path( __FILE__ ) ) : '';
! defined( 'TRENDING_MAG_PRO_ROOT_URL' ) ? define( 'TRENDING_MAG_PRO_ROOT_URL', plugin_dir_url( __FILE__ ) ) : '';
! defined( 'TRENDING_MAG_PRO_CURRENT_VERSION' ) ? define( 'TRENDING_MAG_PRO_CURRENT_VERSION', $trending_mag_pro_plugin_version ) : '';

if ( ! function_exists( 'trending_mag_pro' ) ) {

	/**
	 * Init plugin.
	 */
	function trending_mag_pro() {
		require_once TRENDING_MAG_PRO_ROOT . 'inc/classes/class-trending-mag-pro.php';
		Trending_Mag_Pro::init();
	}
	add_action( 'plugins_loaded', 'trending_mag_pro' );
}

