<?php
/**
 * This handles the saving the custom post types data to post meta.
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Trending_Mag_Pro_Save_Posts' ) ) {

	/**
	 * Handles the saving of custom post type post meta data.
	 */
	class Trending_Mag_Pro_Save_Posts {

		/**
		 * Init class.
		 */
		public function __construct() {
			add_action( 'save_post', array( $this, 'admin_save_post' ) );
		}

		/**
		 * Save wishful posts.
		 *
		 * @param int $post_id Current post id.
		 */
		public function admin_save_post( $post_id ) {

			if ( empty( $post_id ) ) {
				return $post_id;
			}

			$submitted_data = trending_mag_pro_get_submitted_data();

			if ( ! empty( $submitted_data ) ) {
				update_post_meta( $post_id, 'trending_mag_pro', $submitted_data );
			}

		}

	}

	new Trending_Mag_Pro_Save_Posts();

}

