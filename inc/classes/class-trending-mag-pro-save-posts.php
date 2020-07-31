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
			add_action( 'save_post', array( $this, 'save_post' ) );
		}

		/**
		 * Returns the santized submitted data.
		 */
		public function get_submitted_data() {

			if ( ! isset( $_POST['_trending_mag_pro_nonce'] ) || ! isset( $_POST['trending_mag_pro'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( sanitize_key( $_POST['_trending_mag_pro_nonce'] ), '_trending_mag_pro_nonce_action' ) ) {
				return;
			}

			$submitted_data = sanitize_meta( 'trending_mag_pro', $_POST, 'post' );
			return $submitted_data['trending_mag_pro'];
		}

		/**
		 * Save wishful posts.
		 *
		 * @param int $post_id Current post id.
		 */
		public function save_post( $post_id ) {

			if ( empty( $post_id ) ) {
				return $post_id;
			}

			$submitted_data = $this->get_submitted_data();

			if ( ! empty( $submitted_data ) ) {
				update_post_meta( $post_id, 'trending_mag_pro', $submitted_data );
			}

		}

	}

	new Trending_Mag_Pro_Save_Posts();

}

