<?php
add_action( 'widgets_init', 'register_filters_reset_widget' );

function register_filters_reset_widget() {
    register_widget( 'Custom_Filters_Reset_Widget' );
    add_action( 'wp_footer', 'custom_filters_reset_frontend_js' );
}

function custom_filters_reset_frontend_js() {
    ?>
        <script>
            jQuery(document).ready(function($) {
                $('.custom_filters_reset_btn button').click( function() {
                    $('.custom_filter input[type="checkbox"]:checked').parent().removeClass('checked');
                    $('.custom_filter input[type="checkbox"]:checked').removeAttr('checked');
                    $('.custom_filter input[type="radio"]:checked').parent().removeClass('checked');
                    $('.custom_filter input[type="radio"]:checked').removeAttr('checked');


                    updateProductsAndUrl();
                });
            });
        </script>
    <?php
}

class Custom_Filters_Reset_Widget extends WP_Widget { 

    function __construct() {
		$widget_ops = array( 'classname' => 'Reset Filters', 'description' => __('A widget that reset all custom filters', 'Custom_Filters_Reset_Widget') );
		

		parent::__construct( 'Custom_Filters_Reset_Widget', __('Custom_Filters_Reset_Widget', 'Custom_Filters_Reset_Widget'), $widget_ops );
	}

    function widget( $args, $instance ) {
        extract($args, EXTR_SKIP);  

        $button_value = empty($instance['button_value']) ? ' ' : apply_filters( 'widget_title', $instance['button_value'] );

        ?>
        <div class="custom_filters_reset_btn custom_filter">
            <button> <?php echo $button_value ?> </button>
        </div>
        <?php
    }

    function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['button_value'] = $new_instance['button_value'];
		return $instance;
    }

    function form($instance) {
        $button_value = " ";
        if( !empty($instance) ) {
            $button_value = $instance['button_value'];
        }

        $button_value_id = $this->get_field_id('button_value');
        $button_value_name = $this->get_field_name('button_value');

		?>

        <p>
            <label for="<?php echo $button_value_id ?>">Button value:</label>
            <input class="widefat" id="<?php echo $button_value_id ?>" value="<?php echo $button_value ?>" name="<?php echo $button_value_name ?>" type="text" />
        </p>

		<?php
    }
}

?>