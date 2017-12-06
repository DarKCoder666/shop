<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


get_header( 'shop' ); 

// В данном блоке получаем данные с get запроса, преобразуем в валидный для запроса на товары вид, и записываем в переменную $filter_params
$filter_params = array();

foreach ($_GET as $filter_name => $filter_values) {
	if( stristr( $filter_name, 'filter_' ) ) {
		$filter_params[ substr( $filter_name, 7 ) ] = explode(  ',', $filter_values );
	}
}
////////////////////////////////////////////////////////////////////////
?>

<!--Главные категории-->
<div class="wrap_block">
	<div class="top_category">
		<ul class="ul_cat">
			<li><img src="<?php bloginfo('template_directory'); ?>/images/shirts.png"><a href="<?php echo get_term_link( 26 ,'product_cat') ?>">Текстиль и одежда<span>&#9660;</span></a>
				<?php woocommerce_subcats_from_parentcat_by_ID(26); ?>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/ham.png"><a href="<?php echo get_term_link( 29 ,'product_cat') ?>">Фасфуд и напитки<span>&#9660;</span></a>
				<?php woocommerce_subcats_from_parentcat_by_ID(29); ?>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/cake.png"><a href="<?php echo get_term_link( 27 ,'product_cat') ?>">Сладости и кофе<span>&#9660;</span></a>
				<?php woocommerce_subcats_from_parentcat_by_ID(27); ?>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/market.png"><a href="<?php echo get_term_link( 28 ,'product_cat') ?>">Маркет<span>&#9660;</span></a>
				<?php woocommerce_subcats_from_parentcat_by_ID(28); ?>
			</li>
		</ul>
		<div class="clear"></div>
	</div>
</div>

<div class="wrap_block pad_top cat_page">
	<div class="path_block"><?php woocommerce_breadcrumb(); ?></div>

	<div class="left_cat_block mobile_products_filters">
		<div class="mobile_products_filters_wrap">
			<?php dynamic_sidebar( 'siderbar-left' ); ?>
		</div>
		
		<div class="mobile_products_filters_hamburger">
			<i class="fa fa-cogs" aria-hidden="true"></i>
		</div>
	</div>
	<!--Левый блок-->
	<div class="left_block center_cat_block">




		<div class="sales_block background_none">


	<?php
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 * @hooked WC_Structured_Data::generate_website_data() - 30
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

			<!--Блок о доставке и оплаты-->
			<div class="home_banner banner_cat">
				<a href="">
					<img src="<?php bloginfo('template_directory'); ?>/images/shipping.png" alt="">
					<div>
						<h2>Доставка бесплатно</h2>
					</div>
				</a>
				<a href="">
					<img src="<?php bloginfo('template_directory'); ?>/images/money.png" alt="">
					<div>
						<h2>Форма оплаты любая</h2>
					</div>
				</a>
				<div class="clear"></div>
			</div>


    <header class="woocommerce-products-header">

		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

			<h1 class="woocommerce-products-header__title page-title" data-cat-name="<?php echo get_queried_object()->slug ?>"><?php woocommerce_page_title(); ?></h1>

		<?php endif; ?>

		<?php
			/**
			 * woocommerce_archive_description hook.
			 *
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' );
		?>

    </header>

	<!-- Самописаня функция, для отображения товаров на странице -->
	<!-- Описана в файле get_filtred_products.php -->
	<?php get_filtred_products( $filter_params, get_queried_object_id() ); ?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
	?>



	<div class="clear"></div>
</div>

	</div><div class="clear"></div>
</div>


<?php get_footer( 'shop' ); ?>
