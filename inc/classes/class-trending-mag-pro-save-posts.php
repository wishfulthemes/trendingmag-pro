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
			add_action( 'save_post', array( $this, 'admin_save_poll' ) );
			$this->frontend_poll_vote();
		}

		/**
		 * Save admin side poll.
		 *
		 * @param int $poll_id Current post id.
		 */
		public function admin_save_poll( $poll_id ) {

			$polls_data = array();

			if ( empty( $poll_id ) ) {
				return $poll_id;
			}

			if ( 'trending-mag-polls' !== get_post_type( $poll_id ) ) {
				return;
			}

			if ( ! is_admin() ) {
				return $poll_id;
			}

			$polls_data     = (array) trending_mag_pro_get_post_data( $poll_id );
			$submitted_data = trending_mag_pro_get_submitted_data();

			$polls = ! empty( $submitted_data['trending_mag_polls'] ) ? $submitted_data['trending_mag_polls'] : '';

			$poll_options = ! empty( $polls['poll_options'] ) ? $polls['poll_options'] : '';

			$polls_data['trending_mag_polls']['poll_options'] = $poll_options;

			if ( ! empty( $submitted_data ) ) {
				update_post_meta( $poll_id, 'trending_mag_pro', $polls_data );
			}

		}

		/**
		 * Saves the poll stats.
		 */
		public function frontend_poll_vote() {

			if ( is_admin() ) {
				return;
			}

			$submitted_data = trending_mag_pro_get_submitted_data();

			$polls = ! empty( $submitted_data['trending_mag_polls'] ) ? $submitted_data['trending_mag_polls'] : '';

			$poll_options = ! empty( $polls['poll_options'] ) ? $polls['poll_options'] : '';

			$selected_poll = ! empty( $poll_options['selected_poll'] ) ? $poll_options['selected_poll'] : '';
			$poll_id       = ! empty( $poll_options['poll_id'] ) ? $poll_options['poll_id'] : '';

			if ( ! $poll_id ) {
				return;
			}

			$polls_data = trending_mag_pro_get_post_data( $poll_id );

			if ( ! empty( $selected_poll ) ) {
				if ( is_array( $selected_poll ) ) {
					foreach ( $selected_poll as $poll ) {
						$polls_data['trending_mag_polls']['poll_stats'][ $poll ][] = time();
					}
				} else {
					$polls_data['trending_mag_polls']['poll_stats'][ $selected_poll ][] = time();
				}
			}

			if ( $polls_data ) {
				update_post_meta( $poll_id, 'trending_mag_pro', $polls_data );
			}

		}

	}

	new Trending_Mag_Pro_Save_Posts();

}

