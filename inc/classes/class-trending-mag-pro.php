<?php
/**
 * Main class file for initializing this plugin.
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Trending_Mag_Pro' ) ) {

	/**
	 * Main class for plugin.
	 */
	class Trending_Mag_Pro {

		/**
		 * Init class.
		 */
		public static function init() {
			self::includes();
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
		}

		/**
		 * Enqueues admin side assets.
		 */
		public static function admin_assets() {
			$root_url = TRENDING_MAG_PRO_ROOT_URL;
			wp_register_script( 'trending-mag-pro-admin-script', "{$root_url}assets/js/admin.js", array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'trending-mag-pro-admin-script' );
		}

		/**
		 * Include all the files.
		 */
		public static function includes() {
			$root_dir = TRENDING_MAG_PRO_ROOT;

			$files_path = array();

		}
	}
}
