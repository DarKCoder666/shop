<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i" rel="stylesheet">
<?php wp_head(); ?>
<link href="<?php bloginfo('template_directory'); ?>/css/jquery.formstyler.css" rel="stylesheet" />
<link href="<?php bloginfo('template_directory'); ?>/css/jquery.formstyler.theme.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/style.css">
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.formstyler.min.js"></script>
<script>
	jQuery( document ).ready(function() {
		jQuery('input, select').styler();
		jQuery('.m_left').bind('click', function () {
			jQuery( ".open_m_menu" ).slideToggle( "slow");
		});
	});
</script>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'storefront_before_site' ); ?>

<div class="mobile_menu">
	<div class="wrap_block">
		<div class="m_left">
			<img src="<?php bloginfo('template_directory'); ?>/images/menu_icon.png" alt="">
		</div>
		<div class="m_right">
			<a href=""><img src="<?php bloginfo('template_directory'); ?>/images/ship.png" alt=""></a>
		</div>
		<div class="clear"></div>
		<div class="open_m_menu">
			<ul class="m_lang">
				<li class="active"><a href="">Ру</a></li>
				<li><a href="">Uz</a></li>
			</ul>
			<div class="clear"></div>
			<ul class="m_big_menu">
				<li><a href="">Текстиль и одежда</a></li>
				<li><a href="">Фасфуд и напитки</a></li>
				<li><a href="">Сладости и кофе</a></li>
				<li><a href="">Маркет</a></li>
			</ul>
			<ul class="m_top_menu">
				<li><a href="">Доставка</a></li>
				<li><a href="">Оплата</a></li>
				<li><a href="">Контакты</a></li>
			</ul>
		</div>
	</div>
</div>

<!--Шапка сайта-->
<div class="wrap_block">
	<div class="header_block">
		<!--Верхний блок с доп меню-->
		<div class="top_header">
			<?php wp_nav_menu( array( 'theme_location' => 'top_menu' ) ); ?>
			<div class="top_phone">телефон поддержки: <span>(+998 90) 777-77-77</span></div>
			<div class="social_top">
				<a href=""><img src="<?php bloginfo('template_directory'); ?>/images/f.png" /></a>
				<a href=""><img src="<?php bloginfo('template_directory'); ?>/images/r.png" /></a>
				<a href=""><img src="<?php bloginfo('template_directory'); ?>/images/m.png" /></a>
			</div>
		</div>
		<!--Окно поиска и логотип-->
		<div class="header_search">
			<ul class="lang_menu">
				<li><a href="" class="active">Ру</a></li>
				<li><a href="">Uz</a></li>
			</ul>
			<a href="/" class="logo"><img src="<?php bloginfo('template_directory'); ?>/images/logo.png"></a>
			<div class="search_form">
				<form method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">

					<?php if (class_exists('WooCommerce')) : ?>

						<?php
						if(isset($_REQUEST['product_cat']) && !empty($_REQUEST['product_cat']))
						{
							$optsetlect=$_REQUEST['product_cat'];
						}
						else{
							$optsetlect=0;
						}
						$args = array(
							'show_option_all' => esc_html__( 'Все категории', 'woocommerce' ),
							'hierarchical' => 1,
							'class' => 'cat',
							'echo' => 1,
							'value_field' => 'slug',
							'selected' => $optsetlect
						);
						$args['taxonomy'] = 'product_cat';
						$args['name'] = 'product_cat';
						$args['class'] = 'cate-dropdown hidden-xs';
						wp_dropdown_categories($args);

						?>

						<input type="hidden" value="product" name="post_type">
					<?php endif; ?>

					<input class="search_field" type="text" name="s" class="" id="s2" value="<?php echo get_search_query(); ?>" placeholder="Поиск по товарам.." />

					<button type="submit" class="submit_button" value=""><img src="<?php bloginfo('template_directory'); ?>/images/search-icon.png" width="10"> найти</button>

				</form>
			</div>
		</div>

	</div>
</div>
