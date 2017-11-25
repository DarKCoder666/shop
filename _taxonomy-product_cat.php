<?php
/* Template Name: Мой шаблон */

get_header(); ?>
<!--Главные категории-->



<div class="wrap_block pad_top cat_page">
	<div class="path_block"><?php woocommerce_breadcrumb(); ?></div>

	<div class="left_cat_block"><?php dynamic_sidebar( 'siderbar-left' ); ?></div>
	<!--Левый блок-->
	<div class="left_block center_cat_block">
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



<div class="sales_block background_none">
	<?php if ( have_posts() ) : ?>

		<?php
		/**
		 * woocommerce_before_shop_loop hook.
		 *
		 * @hooked wc_print_notices - 10
		 * @hooked woocommerce_result_count - 20
		 * @hooked woocommerce_catalog_ordering - 30
		 */
		do_action( 'woocommerce_before_shop_loop' );
		?>

		<?php woocommerce_product_loop_start(); ?>

		<?php woocommerce_product_subcategories(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php
			/**
			 * woocommerce_shop_loop hook.
			 *
			 * @hooked WC_Structured_Data::generate_product_data() - 10
			 */
			do_action( 'woocommerce_shop_loop' );
			?>

			<?php wc_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

		<?php
		/**
		 * woocommerce_after_shop_loop hook.
		 *
		 * @hooked woocommerce_pagination - 10
		 */
		do_action( 'woocommerce_after_shop_loop' );
		?>

	<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

		<?php
		/**
		 * woocommerce_no_products_found hook.
		 *
		 * @hooked wc_no_products_found - 10
		 */
		do_action( 'woocommerce_no_products_found' );
		?>

	<?php endif; ?>

	<?php
	/**
	 * woocommerce_after_main_content hook.
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'woocommerce_after_main_content' );
	?>


	<?php


	while ( have_posts() ) : the_post();
			++$cont_num;
			?>
			<div class="product_item">
				<div class="border_right <?php if($cont_num==4) echo 'border_none'; ?>">
					<a href="<?php the_permalink() ?>" class="img_product">
						<?php echo woocommerce_get_product_thumbnail(); ?>
					</a>
					<div class="item_text_block">
						<a href="<?php the_permalink() ?>" title="Ссылка на: <?php the_title_attribute(); ?>" class="title_profuct"><?php the_title(); ?></a>
						<div class="new_price"><?php echo $product->price; ?> сум</div>

						<meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>" />
						<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
						<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
					</div>
					<div class="right_but_add">
						<?php
						if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
							$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
							$html .= '<div class="num_select">'.woocommerce_quantity_input( array(), $product, false ).'</div>';
							$html .= '<div class="but_add"><button type="submit">добавить</button></div>';
							$html .= '</form>';
							echo $html;
						} elseif ( $product->is_type( 'variable' ) ) {
							?>
							<div class="but_add"><a href="<?php the_permalink() ?>">выбрать</a></div>
						<?php } ?>
					</div>
					<div class="clear"></div>
				</div>
			</div>
	<?php endwhile;	?>
	<div class="clear"></div>

</div>





	</div>
	<?php do_action( 'storefront_sidebar' ); ?>
	
	<div class="clear"></div>
</div>
<?php
get_footer();
