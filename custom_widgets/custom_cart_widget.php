<?php
if( wp_doing_ajax() ) {
    add_action('wp_ajax_nopriv_remove_item_from_cart_widget', 'remove_item_from_cart_widget');
    add_action('wp_ajax_remove_item_from_cart_widget', 'remove_item_from_cart_widget');
}



function remove_item_from_cart_widget() {
    global $woocommerce;
    if (isset($_POST['product_key'])) {
        $product_key =  sanitize_text_field( $_POST['product_key'] );
        
        echo WC()->cart->remove_cart_item( $product_key );
    }

    wp_die();
}


add_action( 'widgets_init', 'register_my_widget' );

function register_my_widget() {
    register_widget( 'Custom_Cart_Widget' );
}

class Custom_Cart_Widget extends WP_Widget { 

    function Custom_Cart_Widget() {
		$widget_ops = array( 'classname' => 'Custom_Cart_Widget', 'description' => __('A widget that displays the authors name ', 'Custom_Cart_Widget') );
		
		
		$this->WP_Widget( 'Custom_Cart_Widget', __('Custom_Cart_Widget', 'Custom_Cart_Widget'), $widget_ops );
	}

    function widget( $args, $instance ) {
        $cart = WC()->cart->get_cart();
        $total = 0;

        echo "<div class='custom_cart_widget'>";
        
        foreach ($cart as $prod ) {
            $product = new WC_Product( $prod['product_id'] );
            $prod_image = $product->get_image($size = 'shop_thumbnail');

            $quantity = $prod['quantity'];
            $price = $prod['data']->get_price();

            $total += $price * $quantity; 
            
            $product_name =  $prod['data']->get_title();
            $price_line = "$quantity x $price сум";
            $variations = get_variations_of_product($prod['variation']);
            $url = get_permalink( $prod['product_id'] );


            ?>
                <div class="custom_cart_product">
                    <a href="<?php echo $url ?>"> <?php echo $product_name ?> </a> 
                    <span product_key="<?php echo $prod['key'] ?>" class="remove_item_from_cart_widget_btn">x</span>  <br>

                    <?php echo $prod_image; ?>

                    <?php foreach($variations as $var_name => $var_val): ?>
                        <b> <?php echo $var_name ?> </b>
                        <span> <?php echo $var_val ?> </span>
                        <br>
                        
                    <?php endforeach; ?>
                    
                    <p><?php echo $price_line; ?></p>
                    <hr>
                </div>

            <?php
        }
        ?>
            <p>
                <b>Подытог: </b> <span><?php echo $total ?> сум</span>
            </p>

            <a href="<?php echo home_url() ?>/cart/" class="custom_cart_widget_look">Просмотр корзины</a>
            <a href="<?php echo home_url() ?>/checkout/" class="custom_cart_widget_offer_an_order">Оформить заказ</a>

            <script>
                jQuery(document).ready(function($) {

                    var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
                    
                    $('.remove_item_from_cart_widget_btn').click( remove_item_from_cart_widget );
                    
                    function remove_item_from_cart_widget() {
                        var data = {
                            action: 'remove_item_from_cart_widget',
                            product_key: this.getAttribute('product_key')
                        };
                        var that = this;

                        jQuery.post(ajaxurl, data, function(res) {
                            if( parseInt( res ) === 1 ) {
                                var parent_element = find_needen_parent(that, 'custom_cart_product');
                                if(parent_element) {
                                    parent_element.remove();
                                }
                            }
                        });
                    }
                    function find_needen_parent(element, parent_classname) {
                        if(element == document.body) {
                            return false;
                        }

                        var parent = element.parentNode;
                        if( parent.classList.contains(parent_classname) ) {
                            return parent;
                        } else {
                            return find_needen_parent( parent, parent_classname );
                        }
                    }
                });

            </script>
        </div>
        <?php
    
        

    }

    function update( $new_instance, $old_instance ) {
        return $old_instance;
    }

    function form($instance) {
        echo "<h1> Here is a custom cart widget! </h1>";
    }
}

function get_variations_of_product( $variations ) {
    $result = array();
    foreach( $variations as $key => $value ) {
        $decoded_variation = urldecode( $key );

        // Берём подстроку, т.к. значение $decoded_variation имеет значение attribute_названиеВариации
        // Пример: attribute_цвет или attribute_размер
        $vartiation_name = substr( $decoded_variation, 10 );

        $result[$vartiation_name] = $value;
    }

    // var_dump( $result );
    return $result;
}

?>