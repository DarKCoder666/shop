<?php
if( wp_doing_ajax() ) {
    add_action('wp_ajax_nopriv_remove_item_from_cart_widget', 'remove_item_from_cart_widget');
    add_action('wp_ajax_remove_item_from_cart_widget', 'remove_item_from_cart_widget');
	add_action('wp_ajax_nopriv_add_product_to_cart_custom', 'add_product_to_cart_custom');
	add_action('wp_ajax_add_product_to_cart_custom', 'add_product_to_cart_custom');
}

function add_product_to_cart_custom() {
	$product_id;
	$quantity;
	if( $_POST['quantity'] !== null && $_POST['product_id'] !== null  ) {
		if( is_numeric( $_POST['quantity'] ) && is_numeric( $_POST['product_id'] ) ) {
			$product_id = $_POST['product_id'];
			$quantity = $_POST['quantity'];
		} else {
			echo "Error";
			wp_die();
		}
	} else {
		echo "Error";
		wp_die();
	}
	global $woocommerce;
	$result = $woocommerce->cart->add_to_cart( $product_id, $quantity, $variation_id = 0, $variation = array(), $cart_item_data = array() );
	if($result) {
		echo( $result );
	}
	wp_die();
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
					
					if(  $prod['variation_id'] !== 0 ) {
						$variations = get_variations_of_product($prod['variation_id']);
					}
					$url = get_permalink( $prod['product_id'] );

					?>
						<div class="custom_cart_product">
							<div class="custom_cart_product_img">
								<?php echo $prod_image; ?>
							</div>

							<div class="custom_cart_product_info">
								<span product_key="<?php echo $prod['key'] ?>" class="remove_item_from_cart_widget_btn">
									<i class="fa fa-times" aria-hidden="true"></i>
								</span> 
								<a class="custom_cart_title" href="<?php echo $url ?>"> <?php echo $product_name ?> </a> 

								<span class="custom_widget_product_variations">
								<?php 

								if(  $prod['variation_id'] !== 0 ) {
									foreach($variations as $var_val) {
										echo "$var_val; "; 									
									} 
								}
								?>
								</span>
								
								<p><?php echo $price_line; ?></p>
							</div>
						</div>
				<?php
				}
				?>
			</div>
            <p class="custom_cart_total_price">
                <b><?php echo __('[:uz]Jami[:ru]Итого'); ?>: </b> <span> <span class="custom_cart_total_price_num"> <?php echo $total ?></span> сум</span>
            </p>
        </div>
		<div class="custom_cart_buttons">
			<a href="<?php echo home_url() ?>/cart/" class="custom_cart_widget_look"><?php echo __('[:uz]Savatni ko\'rish[:ru]Просмотр корзины'); ?></a>
			<a href="<?php echo home_url() ?>/checkout/" class="custom_cart_widget_offer_an_order"><?php echo __('[:uz]Buyurtma bering[:ru]Оформить заказ'); ?></a>
		</div>

		<script>
			var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
			var $ = jQuery;
			var is_adding = false;

			jQuery(document).ready(function($) {
				$('.remove_item_from_cart_widget_btn').click( remove_item_from_cart_widget );
				$('.custom_cart_products').bind('DOMSubtreeModified', function() {
					$('.remove_item_from_cart_widget_btn').click( remove_item_from_cart_widget );
					update_custom_cart_total_price();
				});

				set_handler_for_add_to_cart_buttons();
			});
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
							update_custom_cart_total_price();
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
			

			function set_handler_for_add_to_cart_buttons() {

				$('.product_item form').unbind('submit');

				$('.product_item form').submit(function() {
					if( is_adding ) {
						return false;
					}
					is_adding = true;

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
						is_adding = false;
						if( res == "Error" ) {
							alert('Что-то пошло не так!');
							return;
						}
						$(that).find('button[type="submit"]').html( btn_txt );
						update_custom_cart(product, res);
					});
					
					return false;
				});
			}

			function create_custom_cart_product_HTML( product, product_key ) {
				var html = 
					"<div class='custom_cart_product'> " +
						"<div class='custom_cart_product_img'>" +
							"<img src='" + product.img_src + "' class='attachment-shop_thumbnail size-shop_thumbnail wp-post-image'>" +
						"</div>" +
						"<div class='custom_cart_product_info'>" +		
							"<span product_key='" + product_key + "' class='remove_item_from_cart_widget_btn'> <i class=\"fa fa-times\" aria-hidden=\"true\"></i> </span>" +
							"<a class='custom_cart_title' href='" + product.src + "'>" + product.title + "</a>" +
							"<br>" +
							"<p>" +
								"<span class='custom_cart_quantity'>" + product.quantity + "</span>" + 
								" x " +
								"<span class='custom_cart_price'>" + product.price + "</span>" + 
								" сум" +
							"</p>" +
						"</div>" +
					"</div>";

				return html;
			}

			function update_custom_cart( changes, product_key ) {
				cart_have_been_updated = false;
				$('.custom_cart_product').each(function() {
					if( $.trim( $(this).find('.custom_cart_title').text() ) == $.trim( changes.title ) ) {
						var product_quantity = $(this).find('.custom_cart_quantity').text();
						$(this).find('.custom_cart_quantity').text( parseInt(product_quantity) + parseInt( changes.quantity ) );
						cart_have_been_updated = true;
						return false;
					}
				});
				if( !cart_have_been_updated ) {
					var new_product_html = create_custom_cart_product_HTML( changes, product_key );
					var cart_products_html = $('.custom_cart_products').html();
					$('.custom_cart_products').html( cart_products_html + new_product_html );
				}
			}

	

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
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" />
		</p>
		<?php
    }
}

function get_variation_data_from_variation_id( $item_id ) {
    $_product = new WC_Product_Variation( $item_id );
    $variation_data = $_product->get_variation_attributes();
    $variation_detail = wc_get_formatted_variation( $variation_data, true );  // this will give all variation detail in one line
    // $variation_detail = woocommerce_get_formatted_variation( $variation_data, false);  // this will give all variation detail one by one
    return $variation_detail; // $variation_detail will return string containing variation detail which can be used to print on website
    // return $variation_data; // $variation_data will return only the data which can be used to store variation data
}

function get_variations_of_product( $variation_id ) {
	$variations = explode(', ', get_variation_data_from_variation_id( $variation_id ) );
	$variations_values = array();
	
	foreach ( $variations as $key ) {
		array_push( $variations_values, explode(': ', $key)[1] );
	}
	return $variations_values;
}

?>