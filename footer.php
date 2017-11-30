<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>
<div class="footer_block">
	<div class="wrap_block">
		<div class="copy">
			<img src="<?php bloginfo('template_directory'); ?>/images/logo-footer.png" alt="">
			<p>
				© 2017 «Hamma Shotta.Uz».<br>
				<a href="">О компании</a>  /  <a href="">Контакты</a>
			</p>
		</div>

		<?php dynamic_sidebar( 'footer_1' ); ?>
<!-- 
		<div class="f_menu_1">
			<ul>
				<li><a href="">Главная</a></li>
				<li><a href="">О нас</a></li>
				<li><a href="">Доставка</a></li>
				<li><a href="">Оплата</a></li>
			</ul>
		</div> -->
		<div class="f_menu_2">
			<ul>
				<li><a href="">Текстиль и Одежда</a></li>
				<li><a href="">Фастфуд и напитки</a></li>
				<li><a href="">Сладости и кофе</a></li>
				<li><a href="">Маркет</a></li>
			</ul>
		</div>
		<div class="f_phone">
			Тел. (+998 90) 777-77-77<br>
			Факс. (+998 71) 155-55-66
		</div>
		<div class="f_social">
			<a href=""><img src="<?php bloginfo('template_directory'); ?>/images/f.png" /></a>
			<a href=""><img src="<?php bloginfo('template_directory'); ?>/images/r.png" /></a>
			<a href=""><img src="<?php bloginfo('template_directory'); ?>/images/m.png" /></a>
		</div>
		<div class="clear"></div>
	</div>
</div>

	<?php do_action( 'storefront_after_footer' ); ?>



<?php wp_footer(); ?>

</body>
</html>
