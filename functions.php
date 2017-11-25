<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version' => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce = require 'inc/woocommerce/class-storefront-woocommerce.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';

	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
		require 'inc/nux/class-storefront-nux-starter-content.php';
	}
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

//add_action( 'woocommerce_before_single_product', 'cspl_change_single_product_layout' );
//function cspl_change_single_product_layout() {
//	// Disable the hooks so that their order can be changed.
//	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
//	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
//	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
//	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
//	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
//	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
//	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
//	// Put the price first.
//	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 5 );
//	// Include the category/tags info.
//	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 10 );
//	// Then the product short description.
//	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 40 );
//	// Move the title to near the end.
//	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 50 );
//	// And finally include the 'Add to cart' section.
//	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 60 );
//}


function woocommerce_subcats_from_parentcat_by_ID($parent_cat_ID) {
	$args = array(
		'hierarchical' => 1,
		'show_option_none' => '',
		'hide_empty' => 0,
		'parent' => $parent_cat_ID,
		'taxonomy' => 'product_cat'
	);
	$subcats = get_categories($args);
	echo '<ul>';
	foreach ($subcats as $sc) {
		$link = get_term_link( $sc->slug, $sc->taxonomy );
		echo '<li><a href="'. $sc->link .'">'.$sc->name.'</a></li>';
	}
	echo '</ul>';
}

function twentyten_widgets_init() {

	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Левая панель', 'twentyten' ),
		'id' => 'siderbar-left',
		'description' => __( '', 'twentyten' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Подвал 1', 'twentyten' ),
		'id' => 'footer_1',
		'description' => __( '', 'twentyten' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Подвал 2', 'twentyten' ),
		'id' => 'footer_2',
		'description' => __( '', 'twentyten' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Подвал 3', 'twentyten' ),
		'id' => 'footer_3',
		'description' => __( '', 'twentyten' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Подвал 4', 'twentyten' ),
		'id' => 'footer_4',
		'description' => __( '', 'twentyten' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );



}
/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'twentyten_widgets_init' );



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

		<a href="<?php echo home_url() ?>/cart/">Просмотр корзины</a>
		<a href="<?php echo home_url() ?>/checkout/">Оформить заказ</a>

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