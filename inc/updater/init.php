<?php
/**
 * Trending Mag Pro Plugin Updater
 *
 * @package EDD Sample Theme
 */

// Includes the files needed for the plugin updater
if ( ! class_exists( 'Trending_Mag_Pro_License_Page' ) ) {
	require_once dirname( __FILE__ ) . '/product-license-page.php';
}

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$trending_mag_pro = get_plugin_data( TRENDING_MAG_PRO_ROOT . 'trending-mag-pro.php' );

$current_version = $trending_mag_pro['Version'];
$current_version = ! empty( $current_version ) ? $current_version : '1.0.0';

// Config settings
$config = array(
	'host_url'    => 'https://wishfulthemes.com', // Site where EDD is hosted
	'version'     => $current_version, // The current version of this plugin
	'author'      => 'WishfulThemes', // The author of this plugin
	'download_id' => '', // Download ID of a product
	'text_domain' => 'trending-mag-pro',
	'name'        => $trending_mag_pro['Name'],
);

// Loads the updater classes
$updater = new Trending_Mag_Pro_License_Page( $config );
