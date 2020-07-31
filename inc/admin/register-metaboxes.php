<?php
/**
 * Register meta box(es).
 *
 * @package wishful-ad-manager
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'trending_mag_pro_register_metaboxes' ) ) {

	/**
	 * Registers metaboxes.
	 */
	function trending_mag_pro_register_metaboxes() {

		add_meta_box(
			'poll-options',
			__( 'Poll Options', 'wishful-ad-manager' ),
			'trending_mag_pro_metabox_poll_options',
			'trending-mag-polls'
		);

	}
	add_action( 'add_meta_boxes', 'trending_mag_pro_register_metaboxes' );
}

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function trending_mag_pro_metabox_poll_options( $post ) {
	?>
	<div id="trending-mag-pro-poll-options">
		<div class="container">

			<div class="field-wrap">
				<label class="field-label" for="selection-type">
					<?php esc_html_e( 'Selection Type', 'trending-mag-pro' ); ?>
				</label>
				<select name="" id="selection-type">
					<option value="single"><?php esc_html_e( 'Single', 'trending-mag-pro' ); ?></option>
					<option value="multiple"><?php esc_html_e( 'Multiple', 'trending-mag-pro' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'Specify whether the users can select multiple options or single option.', 'trending-mag-pro' ); ?></p>
			</div>

			<div class="field-wrap">
				<h3>
					<?php esc_html_e( 'Options List', 'trending-mag-pro' ); ?>
				</h3>
				<div class="options-list-container">
					<button class="button secondary" id="add-new-option"><?php esc_html_e( 'Add New Option' ); ?></button>
					<div id="options-list"></div>
				</div>
			</div>

		</div>
	</div>
	<?php
}
