<?php

if( wp_doing_ajax() ) {
    add_action('wp_ajax_nopriv_filter_products_by_price', 'filter_products_by_price');
    add_action('wp_ajax_filter_products_by_price', 'filter_products_by_price');
}

function filter_products_by_price() {
    $data = $_POST;

    if( $data['min_price'] == null || $data['max_price'] == null ) {
        echo 'Error: Не верно переданны данные!';
        wp_die();
    }


}


add_action( 'widgets_init', 'register_price_filter_widget' );

function register_price_filter_widget() {
    register_widget( 'Custom_Price_Filter_Widget' );
    add_action( 'wp_footer', 'custom_price_filter_widget_frontend_js' );
}

function custom_price_filter_widget_frontend_js() {
    ?>
        <script>
            jQuery(document).ready(function($) {
                var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";

                $('.custom_price_widget_btn').click( function() {
                    var category_name = $('.woocommerce-products-header .woocommerce-products-header__title').data('cat-name');

                    var filter_data = getFilterData(true);

                    var data = {
                        action: 'find_products',
                        category_name: category_name,
                        filter_data: filter_data
                    };

                    jQuery.post( ajaxurl, data, function(res) {
                        $('.products_list_wrapper').replaceWith(res);
                        setParamsToUrl();
                        jQuery('input, select').styler();
    
                        $('.product_item form').submit(function() {
                            var that = this;
                            var data = {
                                action: "add_product_to_cart_custom",
                                quantity: $(this).find("input[name='quantity']").val(),
                                product_id: $(this).attr("product_id")
                            };
                            
                            var product = {
                                src: $(that).closest('.product_item').find('.img_product').attr('href'),
                                img_src: $(that).closest('.product_item').find('.img_product img').attr('src'),
                                title: $(that).closest('.product_item').find('.title_profuct').text(),
                                quantity: $(that).closest('.product_item').find("input[name='quantity']").val(),
                                price: parseInt( $(that).closest('.product_item').find('ins .amount').text() ) || parseInt( $(that).closest('.product_item').find('meta[itemprop="price"]').attr('content') ) 
                            };

                            var btn_txt = $(this).find('button[type="submit"]').text();
                            $(this).find('button[type="submit"]').html('<img class="dual_ring" src="<?php bloginfo('template_directory'); ?>/images/DualRing.gif">');

                            jQuery.post(ajaxurl, data, function(res) {
                                if( res == "Error" ) {
                                    alert('Что-то пошло не так!');
                                    return;
                                }
                                $(that).find('button[type="submit"]').html( btn_txt );
                                update_custom_cart(product, res);
                            });

                            return false;
                        });

                        set_handler_for_add_to_cart_buttons();
                    });
                });
            });
        </script>
    <?php
}

class Custom_Price_Filter_Widget extends WP_Widget { 

    function __construct() {
		$widget_ops = array( 'classname' => 'Price Filter', 'description' => __('A widget that filter products by price ', 'Custom_Price_Filter_Widget') );
		

		parent::__construct( 'Custom_Price_Filter_Widget', __('Custom_Price_Filter_Widget', 'Custom_Price_Filter_Widget'), $widget_ops );
	}

    function widget( $args, $instance ) {
        extract($args, EXTR_SKIP);
      
        // В данном блоке получаем данные с get запроса, преобразуем в валидный для сравнения вид, и записываем в переменную $filter_params
        $filter_params = array();
        foreach ($_GET as $filter_name => $filter_values) {
            if( stristr( $filter_name, 'filter_' ) ) {
                $filter_params[ substr( $filter_name, 7 ) ] = explode(  ',', $filter_values );
            }
        }

        // Преобразовывает полученные из url цены фильтрации. Функция описана в файле get_filtred_products.php
        $prices = get_prices_from_filter_params( $filter_params );
        //////////////////////////////////////////////////////////
        
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
        $min_price = empty($instance['min_price']) ? ' ' : apply_filters('widget_min_price', $instance['min_price']);
        $max_price = empty($instance['max_price']) ? ' ' : apply_filters('widget_max_price', $instance['max_price']);

        $radio_button_id = "cpwb_radio_id_" . $min_price . "_" . $max_price;
        // Проверяем на совпадение цен данного виджета с теми, что были указаны в url. Функция описана в файле get_filtred_products.php
        $has_checked = check_price_in_prices($prices, $min_price, $max_price);

        ?>
        <div class="custom_price_filter_widget custom_filter">
            <div class="custom_price_widget_btn">
                <input type="radio" <?php echo $has_checked ? 'checked' : '' ?> name ="cpwb_radio_button" id="<?php echo $radio_button_id ?>">
                <label for="<?php echo $radio_button_id ?>">
                    <span class="cpwb_min_price"> <?php echo $min_price ?> </span> 
                    <span> <?php echo get_woocommerce_currency_symbol() ?> </span>
                    -
                    <span class="cpwb_max_price"> <?php echo $max_price ?> </span>
                    <span> <?php echo get_woocommerce_currency_symbol() ?> </span>
                </label>
            </div>
        </div>
        <?php
    }

    function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['min_price'] = $new_instance['min_price'];
		$instance['max_price'] = $new_instance['max_price'];
		return $instance;
    }

    function form($instance) {
        $title = " ";
        $min_price = " ";
        $max_price = " ";
        if( !empty($instance) ) {
            $title = $instance['title'];
            $min_price = $instance['min_price'];
            $max_price = $instance['max_price'];
        }

        $title_id = $this->get_field_id('title');
        $title_name = $this->get_field_name('title');

        $min_price_id = $this->get_field_id('min_price');
        $min_price_name = $this->get_field_name('min_price');

        $max_price_id = $this->get_field_id('max_price');
        $max_price_name = $this->get_field_name('max_price');  
		?>
        
        <p>
            <label for="<?php echo $title_id ?>">Заголовок:</label>
            <input class="widefat" id="<?php echo $title_id ?>" value="<?php echo $title ?>" name="<?php echo $title_name ?>" type="text" />
        </p>
        
        <p>
            <label for="<?php echo $min_price_id ?>">Min price:</label>
            <input class="widefat" id="<?php echo $min_price_id ?>" value="<?php echo $min_price ?>" name="<?php echo $min_price_name ?>" type="text" />
        </p>
        
        <p>
            <label for="<?php echo $max_price_id ?>">Max price:</label>
            <input class="widefat" id="<?php echo $max_price_id ?>" value="<?php echo $max_price ?>" name="<?php echo $max_price_name ?>" type="text" />
        </p>

		<?php
    }
}

?>