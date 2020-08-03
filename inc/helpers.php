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

		if ( ! $echo ) {
			return $field_name;
		}

		echo esc_attr( $field_name );
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


if ( ! function_exists( 'trending_mag_pro_get_submitted_data' ) ) {

	/**
	 * Returns sanitized the data of POST method.
	 */
	function trending_mag_pro_get_submitted_data() {

		if ( ! isset( $_POST['_trending_mag_pro_nonce'] ) || ! isset( $_POST['trending_mag_pro'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( $_POST['_trending_mag_pro_nonce'] ), '_trending_mag_pro_nonce_action' ) ) {
			return;
		}

		$submitted_data = sanitize_meta( 'trending_mag_pro', $_POST, 'post' );
		return $submitted_data['trending_mag_pro'];
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
							<li>
								<label class="rm-label" for="trending-mag-pro-poll-<?php echo esc_attr( $option_input_value ); ?>">
									<input value="<?php echo esc_attr( $option_input_value ); ?>" type="<?php echo esc_attr( $type ); ?>" id="trending-mag-pro-poll-<?php echo esc_attr( $option_input_value ); ?>" name="<?php trending_mag_pro_generate_field_name( $name, 'trending-mag-polls' ); ?>"> <?php echo esc_html( $option_input_value ); ?>
								</label>
							</li>
							<?php
						}
						?>
					</ul>
					<p>
					<input type="submit" name="vote" value="<?php esc_attr_e( 'Vote', 'trending-mag-pro' ); ?>" class="rm-button-primary small vote"></p>
				</div>
				<input type="hidden" name="<?php trending_mag_pro_generate_field_name( 'poll_options[poll_id]', 'trending-mag-polls' ); ?>" value="<?php echo esc_attr( $poll_id ); ?>">
				<?php wp_nonce_field( '_trending_mag_pro_nonce_action', '_trending_mag_pro_nonce' ); ?>
			</form>
			<?php
		}

		return ob_get_clean();
	}
}


if ( ! function_exists( 'trending_mag_pro_get_supported_sharer' ) ) {

	/**
	 * Returns the supported social sharer array.
	 */
	function trending_mag_pro_get_supported_sharer() {
		return array(
			'facebook',
			'twitter',
			'whatsapp',
			'linkedin',
			'pinterest',
		);

	}
}



if ( ! function_exists( 'trending_mag_pro_list_sharer_button' ) ) {

	/**
	 * Retrives the social sharer buttons list tags.
	 */
	function trending_mag_pro_list_sharer_button( $args ) {

		if ( ! $args ) {
			return;
		}

		$url   = get_the_permalink();
		$title = get_the_title();
		$image = get_the_post_thumbnail_url();

		$sharer_links = array(
			'facebook'  => "//www.facebook.com/sharer/sharer.php?u={$url}&t={$title}",
			'twitter'   => "//twitter.com/share?url={$url}&text={$title}",
			'whatsapp'  => "//api.whatsapp.com/send?text={$url}",
			'linkedin'  => "//www.linkedin.com/sharing/share-offsite/?url={$url}",
			'pinterest' => "//pinterest.com/pin/create/link/?url={$url}&media={$image}&description={$title}",
		);

		ob_start();
		if ( is_array( $sharer_links ) && ! empty( $sharer_links ) ) {
			foreach ( $sharer_links as $social => $links ) {
				if ( in_array( $social, $args, true ) ) {
					$fa_class = "fa fa-{$social}";
					if ( 'linkedin' === $social ) {
						$fa_class = "{$fa_class}-in";
					}
					?>
					<li class="<?php echo esc_attr( $social ); ?>">
						<a href="<?php echo esc_url( $links ); ?>" target="_blank" rel="noopener noreferrer">
							<i class="<?php echo esc_attr( $fa_class ); ?>"></i> <?php esc_html_e( 'Share', 'trending-mag-pro' ); ?>
						</a>
					</li>
					<?php
				}
			}
		}
		$content = ob_get_clean();

		echo $content;

	}
}

