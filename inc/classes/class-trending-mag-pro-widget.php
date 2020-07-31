<?php
/**
 * Class file for creating the poll widget.
 *
 * @package trending-mag-pro
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Trending_Mag_Pro_Poll_Widget' ) ) {

	/**
	 * Class for creating the poll widget.
	 */
	class Trending_Mag_Pro_Poll_Widget extends WP_Widget {

		/**
		 * Sets up the widgets name etc.
		 */
		public function __construct() {
			$widget_ops = array(
				'classname' => 'Trending_Mag_Pro_Poll_Widget',
			);
			parent::__construct( 'Trending_Mag_Pro_Poll_Widget', 'Trending Mag Pro Poll', $widget_ops );
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$poll_id = ! empty( $instance['poll_id'] ) ? $instance['poll_id'] : 0;

			$content = $args['before_widget'];

			if ( ! empty( $title ) ) {
				$content .= $args['before_title'];
				$content .= apply_filters( 'widget_title', $title );
				$content .= $args['after_title'];
			}

			$content .= trending_mag_pro_get_poll_content( $poll_id );

			$content .= $args['after_widget'];

			echo $content; //phpcs:ignore
		}

		/**
		 * Returns the array of poll posts.
		 */
		private function get_polls() {

			$polls = array();

			$the_query = new WP_Query(
				array(
					'post_type'      => 'trending-mag-polls',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				)
			);

			if ( $the_query->have_posts() ) {
				$polls[0] = __( '--Select--', 'trending-mag-pro' );
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$polls[ get_the_ID() ] = get_the_title();
				}
			}

			wp_reset_postdata();

			return $polls;
		}

		/**
		 * Prints the select options for the poll posts.
		 */
		private function polls_dropdown( $value = '' ) {

			$polls = $this->get_polls();

			if ( is_array( $polls ) && ! empty( $polls ) ) {
				foreach ( $polls as $poll_id => $poll_title ) {
					$selected = $value === $poll_id ? 'selected' : '';
					?>
					<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $poll_id ); ?>"><?php echo esc_html( $poll_title ); ?></option>
					<?php
				}
			}

		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$poll_id = ! empty( $instance['poll_id'] ) ? $instance['poll_id'] : 0;
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php esc_attr_e( 'Title:', 'trending-mag-pro' ); ?>
				</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'poll_id' ) ); ?>">
					<?php esc_attr_e( 'Polls:', 'trending-mag-pro' ); ?>
				</label>
				<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'poll_id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'poll_id' ) ); ?>">
					<?php $this->polls_dropdown( $poll_id ); ?>
				</select>
			</p>

			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance            = array();
			$instance['title']   = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
			$instance['poll_id'] = ( ! empty( $new_instance['poll_id'] ) ) ? absint( $new_instance['poll_id'] ) : 0;

			return $instance;
		}

	}

}


if ( ! function_exists( 'trending_mag_pro_poll_widget' ) ) {

	/**
	 * Init widget.
	 */
	function trending_mag_pro_poll_widget() {
		register_widget( 'Trending_Mag_Pro_Poll_Widget' );
	}
	add_action( 'widgets_init', 'trending_mag_pro_poll_widget' );
}
