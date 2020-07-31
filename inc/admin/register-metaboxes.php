<?php
/**
 * Register meta box(es).
 *
 * @package trending-mag-pro
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
			__( 'Poll Options', 'trending-mag-pro' ),
			'trending_mag_pro_metabox_poll_options',
			'trending-mag-polls'
		);

		add_meta_box(
			'poll-statistics',
			__( 'Poll Statistics', 'trending-mag-pro' ),
			'trending_mag_pro_metabox_poll_statistics',
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

	$post_id = is_object( $post ) && isset( $post->ID ) ? $post->ID : '';

	if ( empty( $post_id ) ) {
		return;
	}

	$post_data    = trending_mag_pro_get_post_data( $post_id );
	$polls_data   = ! empty( $post_data['trending_mag_polls'] ) ? $post_data['trending_mag_polls'] : array();
	$poll_options = ! empty( $polls_data['poll_options'] ) ? $polls_data['poll_options'] : array();

	$selection_type     = ! empty( $poll_options['selection_type'] ) ? $poll_options['selection_type'] : 'single';
	$option_input_field = ! empty( $poll_options['option_input_field'] ) ? $poll_options['option_input_field'] : '';

	?>
	<div id="trending-mag-pro-poll-options">

		<div class="container">

			<div class="field-wrap">
				<label class="field-label" for="selection-type">
					<?php esc_html_e( 'Selection Type', 'trending-mag-pro' ); ?>
				</label>
				<select name="<?php trending_mag_pro_generate_field_name( 'poll_options[selection_type]' ); ?>" id="selection-type">
					<option <?php selected( $selection_type, 'single' ); ?> value="single"><?php esc_html_e( 'Single', 'trending-mag-pro' ); ?></option>
					<option <?php selected( $selection_type, 'multiple' ); ?>  value="multiple"><?php esc_html_e( 'Multiple', 'trending-mag-pro' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'Specify whether the users can select multiple options or single option.', 'trending-mag-pro' ); ?></p>
			</div>

			<div class="field-wrap">
				<h3>
					<?php esc_html_e( 'Options List', 'trending-mag-pro' ); ?>
				</h3>
				<div class="options-list-container">
					<button class="button secondary" id="add-new-option"><?php esc_html_e( 'Add New Option' ); ?></button>

					<!-- We are using this div to dynamiclly append elements inside it on button#add-new-option click. -->
					<div id="options-list">
						<?php
						if ( is_array( $option_input_field ) && ! empty( $option_input_field ) ) {
							foreach ( $option_input_field as $option_input_value ) {
								?>
								<div class="option-input-field-wrap">
									<input type="text" class="option-input-field" value="<?php echo esc_attr( $option_input_value ); ?>" name="<?php trending_mag_pro_generate_field_name( 'poll_options[option_input_field][]' ); ?>" >
									<button class="button delete-option-input-field">X</button>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
			</div>

		</div>

		<?php wp_nonce_field( '_trending_mag_pro_nonce_action', '_trending_mag_pro_nonce' ); ?>
	</div>
	<?php
}



/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function trending_mag_pro_metabox_poll_statistics( $post ) {
	?>
	<div id="trending-mag-pro-poll-statistics">
		<div class="container">
			<h3>No information available</h3>
		</div>
	</div>
	<?php
}

