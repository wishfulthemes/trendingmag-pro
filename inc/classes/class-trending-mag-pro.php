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
		 * Returns the array of localized data.
		 */
		private static function localized_data() {
			$data = array(
				'field_name' => array(
					'option_input_field' => (string) trending_mag_pro_generate_field_name( 'poll_options[option_input_field][]', 'trending_mag_polls', false ),
				),
			);

			return $data;
		}

		/**
		 * Enqueues admin side assets.
		 */
		public static function admin_assets() {
			$data     = self::localized_data();
			$root_url = TRENDING_MAG_PRO_ROOT_URL;
			wp_register_script( 'trending-mag-pro-admin-script', "{$root_url}assets/js/admin.js", array( 'jquery' ), '1.0.0', true );
			wp_localize_script( 'trending-mag-pro-admin-script', 'trending_mag_pro', $data );
			wp_enqueue_script( 'trending-mag-pro-admin-script' );
		}

		/**
		 * Include all the files.
		 */
		public static function includes() {

			$root_dir = TRENDING_MAG_PRO_ROOT;

			$files_path = array(
				'inc/helpers.php',

				'inc/admin/register-custom-post-types.php',
				'inc/admin/register-metaboxes.php',

				'inc/classes/class-trending-mag-pro-save-posts.php',
				'inc/classes/class-trending-mag-pro-widget.php',
				'inc/classes/class-trending-mag-pro-customizer.php',
			);

			foreach ( $files_path as $file ) {
				require_once $root_dir . $file;
			}

		}
	}
}
