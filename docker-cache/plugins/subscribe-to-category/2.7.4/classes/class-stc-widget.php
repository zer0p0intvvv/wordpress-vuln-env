<?php
/**
 *
 * Class for widget
 *
 * @author Sidney van de Stouwe <sidney@vandestouwe.com>
 * @package subscribe-to-category
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

if ( class_exists( 'STC_Widget' ) ) {
	$stc_widget = new STC_Widget();
}

/**
 *
 * STC Widget class
 */
class STC_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'stc_widget', // Base ID.
			__( 'Subscribe to Category', 'subscribe-to-category' ), // Name.
			array( 'description' => __( 'Adding the subscribe form to a widget area.', 'subscribe-to-category' ) ) // Args.
		);
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
                // hide the widget 
                $atts = shortcode_parse_atts($instance['attributes']);
                if ( isset($atts['hide_widget_on_page']) && $atts['hide_widget_on_page'] === get_the_title() ) {
                } else {
                        // @codingStandardsIgnoreLine because no user input is involved and we want to send html code.
                        echo $args['before_widget'];

                        $attributes = isset( $instance['attributes'] ) && ! empty( $instance['attributes'] ) ? $instance['attributes']:'';

                        if ( ! empty( $instance['title'] ) ) {
                                // @codingStandardsIgnoreLine because no user input is involved and we want to send html code for one and three two is escaped.
                                echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . $args['after_title'];
                        }

                        echo do_shortcode( '[stc-subscribe ' . $attributes . ' ]');

                        // @codingStandardsIgnoreLine because no user input is involved and we want to send html code.
                        echo $args['after_widget'];
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
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Subscribe', 'subscribe-to-category' );
		$attributes = isset( $instance['attributes'] ) ? $instance['attributes'] : '';

		?>
		<div class="stc-widget-admin-wrapper">
		<p>
		<label for="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'subscribe-to-category' ); ?></label>
		<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">

		<label for="<?php echo esc_html( $this->get_field_id( 'attributes' ) ); ?>"><?php esc_html_e( 'Attributes:', 'subscribe-to-category' ); ?></label>
		<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'attributes' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'attributes' ) ); ?>" type="text" value="<?php echo esc_attr( $attributes ); ?>">

		</p>
		</div>
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

		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['attributes'] = ( ! empty( $new_instance['attributes'] ) ) ? $new_instance['attributes'] : '';

		return $instance;
	}

} // class Foo_Widget

/**
 * Register Widget
 */
function register_stc_widget() {
	register_widget( 'STC_Widget' );
}
add_action( 'widgets_init', 'register_stc_widget' );
?>
