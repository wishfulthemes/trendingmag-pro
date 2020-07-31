<?php

/**
 * This file has all the required helpers functions and definitions.
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'trending_mag_pro_generate_field_name' ) ) {

	/**
	 * Generates the key for the fields name attribute.
	 *
	 * @param string $name Name for the current field.
	 * @param bool   $echo Whether to echo or return the result.
	 */
	function trending_mag_pro_generate_field_name( $name, $post_type = false, $echo = true ) {
		$post_type = ! $post_type ? get_post_type() : $post_type;
		$post_type = str_replace( array( '-', ' ' ), '_', $post_type );

		$name = str_replace( ']', '', $name );
		$name = explode( '[', $name );
		$name = implode( '][', $name );

		$field_name = "trending_mag_pro[{$post_type}][{$name}]";

		if ( ! $post_type ) {
			$field_name = "trending_mag_pro[{$name}]";
		}

		if ( $echo ) {
			echo esc_attr( $field_name );
		}

		return $field_name;
	}
}



if ( ! function_exists( 'trending_mag_pro_get_post_data' ) ) {

	/**
	 * Returns the saved post meta value.
	 *
	 * @param int $post_id Post ID.
	 */
	function trending_mag_pro_get_post_data( $post_id = '' ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		return get_post_meta( $post_id, 'trending_mag_pro', true );
	}
}



if ( ! function_exists( 'trending_mag_pro_get_poll_content' ) ) {

	/**
	 * Returns the poll html.
	 */
	function trending_mag_pro_get_poll_content( $poll_id ) {
		if ( ! $poll_id ) {
			return;
		}

		$post_data    = trending_mag_pro_get_post_data( $poll_id );
		$polls_data   = ! empty( $post_data['trending_mag_polls'] ) ? $post_data['trending_mag_polls'] : array();
		$poll_options = ! empty( $polls_data['poll_options'] ) ? $polls_data['poll_options'] : array();

		$selection_type     = ! empty( $poll_options['selection_type'] ) ? $poll_options['selection_type'] : 'single';
		$poll_title         = get_the_title( $poll_id );
		$option_input_field = ! empty( $poll_options['option_input_field'] ) ? $poll_options['option_input_field'] : '';

		$type = 'single' === $selection_type ? 'radio' : 'checkbox';
		$name = 'single' === $selection_type ? 'poll_options[selected_poll]' : 'poll_options[selected_poll][]';

		ob_start();
		if ( is_array( $option_input_field ) && ! empty( $option_input_field ) ) {
			?>
			<form method="POST">

				<?php if ( $poll_title ) { ?>
					<p><strong><?php echo esc_html( $poll_title ); ?></strong></p>
				<?php } ?>

				<div class="rm-polls-ans">
					<ul class="poling-list">
						<?php
						foreach ( $option_input_field as $option_input_value ) {
							?>
								<li><label class="rm-label" for="trending-mag-pro-poll-<?php echo esc_attr( $option_input_value ); ?>"><input type="<?php echo esc_attr( $type ); ?>" id="trending-mag-pro-poll-<?php echo esc_attr( $option_input_value ); ?>" name="<?php trending_mag_pro_generate_field_name( $name, 'trending-mag-polls' ); ?>"> <?php echo esc_html( $option_input_value ); ?></label></li>
								<?php
						}
						?>
					</ul>
					<p>
					<input type="button" name="vote" value="<?php esc_attr_e( 'Vote', 'trending-mag-pro' ); ?>" class="rm-button-primary small vote"></p>
				</div>

			</form>
			<?php
		}

		return ob_get_clean();
	}
}
