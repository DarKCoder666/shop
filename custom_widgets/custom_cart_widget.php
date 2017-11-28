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

    function __construct() {
		$widget_ops = array( 'classname' => 'Custom_Cart_Widget', 'description' => __('A widget that displays the authors name ', 'Custom_Cart_Widget') );
		

		parent::__construct( 'Custom_Cart_Widget', __('Custom_Cart_Widget', 'Custom_Cart_Widget'), $widget_ops );
	}

    function widget( $args, $instance ) {
		extract($args, EXTR_SKIP);

		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		
		
		
        $cart = WC()->cart->get_cart();
        $total = 0;
		?>
        <div class='custom_cart_widget'>
			<div class='custom_cart_products'>
				<?php
				if (!empty($title)) {
					echo $before_title . $title . $after_title;;
				}
				foreach ($cart as $prod ) {
					$product = new WC_Product( $prod['product_id'] );
					$prod_image = $product->get_image($size = 'shop_thumbnail');

					$quantity = $prod['quantity'];
					$price = $prod['data']->get_price();

					$total += $price * $quantity; 
					
					$product_name =  $prod['data']->get_title();
					$price_line = "<span class='custom_cart_quantity'>$quantity</span> x <span class='custom_cart_price'>$price</span> сум";
					$variations = get_variations_of_product($prod['variation']);
					$url = get_permalink( $prod['product_id'] );

					?>
						<div class="custom_cart_product">
							<div class="custom_cart_product_img">
								<?php echo $prod_image; ?>
							</div>

							<div class="custom_cart_product_info">
								<a class="custom_cart_title" href="<?php echo $url ?>"> <?php echo $product_name ?> </a> 
								<span product_key="<?php echo $prod['key'] ?>" class="remove_item_from_cart_widget_btn">
									<i class="fa fa-times" aria-hidden="true"></i>
								</span> 


								<?php foreach($variations as $var_name => $var_val): ?>
									<b> <?php echo $var_name ?> </b>
									<span> <?php echo $var_val ?> </span>
									<br>
									
								<?php endforeach; ?>
								
								<p><?php echo $price_line; ?></p>
							</div>
						</div>
						<hr>
				<?php
				}
				?>
			</div>
            <p class="custom_cart_total_price">
                <b>Подытог: </b> <span> <span class="custom_cart_total_price_num"> <?php echo $total ?></span> сум</span>
            </p>
        </div>
		<div class="custom_cart_buttons">
			<a href="<?php echo home_url() ?>/cart/" class="custom_cart_widget_look">Просмотр корзины</a>
			<a href="<?php echo home_url() ?>/checkout/" class="custom_cart_widget_offer_an_order">Оформить заказ</a>
		</div>

		<script>
			jQuery(document).ready(function($) {
				$('.remove_item_from_cart_widget_btn').click( remove_item_from_cart_widget );
				$('.custom_cart_products').bind('DOMSubtreeModified', function() {
					$('.remove_item_from_cart_widget_btn').click( remove_item_from_cart_widget );
					update_custom_cart_total_price();
				});

				var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
				
				function update_custom_cart_total_price() {
					var total = 0;
					$('.custom_cart_product').each(function() {
						var quantity = parseInt( this.querySelector('.custom_cart_quantity').innerText );
						var price = parseInt( this.querySelector('.custom_cart_price').innerText );
						total += quantity * price;
					});

					$('.custom_cart_total_price_num').text(total);
				}
				
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

        <?php
    
        

    }

    function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		return $instance;
    }

    function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Заголовок:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
		</p>
		<?php
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