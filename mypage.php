<?php
/* Template Name: Мой шаблон */


get_header(); 


?>



<!--Главные категории-->
<div class="wrap_block">
	<div class="top_category">
		<ul class="ul_cat">
			<li><img src="<?php bloginfo('template_directory'); ?>/images/shirts.png"><a href="<?php echo get_term_link( 15 ,'product_cat') ?>">Текстиль и одежда<span>&#9660;</span></a>
				<?php woocommerce_subcats_from_parentcat_by_ID(15); ?>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/ham.png"><a href="">Фасфуд и напитки<span>&#9660;</span></a>
				<ul>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
				</ul>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/cake.png"><a href="">Сладости и кофе<span>&#9660;</span></a>
				<ul>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
				</ul>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/market.png"><a href="">Маркет<span>&#9660;</span></a>
				<ul>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
					<li><a href="">Пункт меню 1</a></li>
				</ul>
			</li>
		</ul>
		<div class="clear"></div>
	</div>
</div>


<div class="wrap_block">
	<!--Левый блок-->
	<div class="left_block">
		<!--Блок о доставке и оплаты-->
		<div class="home_banner">
			<a href="">
				<img src="<?php bloginfo('template_directory'); ?>/images/shipping.png" alt="">
				<div>
					<h1>Доставка бесплатно</h1>
					Бесплатная доставка по Ташкенту<br>
					Проверить зону доставки
				</div>
			</a>
			<a href="">
				<img src="<?php bloginfo('template_directory'); ?>/images/money.png" alt="">
				<div>
					<h1>Форма оплаты любая</h1>
					Оплата онлайн через Click или Payme,<br>
					Наличными или через терминал<br>
					при доставке
				</div>
			</a>
			<div class="clear"></div>
		</div>

		<div class="sales_block">
			<div class="sales_title">
				<h2><span>Распродажа</span></h2>
			</div>
			<?php
			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => 4,
				'meta_query'     => array(
					'relation' => 'OR',
					array( // Simple products type
						'key'           => '_sale_price',
						'value'         => 0,
						'compare'       => '>',
						'type'          => 'numeric'
					),
					array( // Variable products type
						'key'           => '_min_variation_sale_price',
						'value'         => 0,
						'compare'       => '>',
						'type'          => 'numeric'
					)
				)
			);

			$cont_num =0;
			$loop = new WP_Query( $args );
			if ( $loop->have_posts() ) {
				while ( $loop->have_posts() ) : $loop->the_post();
					++$cont_num;
					?>
					<div class="product_item">
						<div class="border_right <?php if($cont_num==4) echo 'border_none'; ?>">
							<a href="<?php the_permalink() ?>" class="img_product">
								<?php echo woocommerce_get_product_thumbnail(); ?>
								<div class="price_sales">-15%</div>
							</a>
							<a href="<?php the_permalink() ?>" title="Ссылка на: <?php the_title_attribute(); ?>" class="title_profuct"><?php the_title(); ?></a>
							<div class="price_old"><?php echo $product->regular_price; ?> сум</div>
							<div class="new_price"><?php echo $product->price; ?> сум</div>

							<meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>" />
							<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
							<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

							<?php
							if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
								$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" product_id="'. $product->get_id() . '" method="post" enctype="multipart/form-data">';
								$html .= '<div class="num_select">'.woocommerce_quantity_input( array(), $product, false ).'</div>';
								$html .= '<div class="but_add"><button type="submit">добавить</button></div>';
								$html .= '</form>';
								echo $html;
							}
							?>
							<div class="clear"></div>
						</div>
					</div>
				<?php endwhile;
			} else {
				echo __( 'No products found' );
			}
			wp_reset_postdata();
			?>
	<div class="clear"></div>

</div>

<script>
	jQuery(document).ready(function($) {
		var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";

		$('.price_old').parent().each(function() {
			var old_price = parseInt( $(this).find('.price_old').text() );
			var new_price = parseInt( $(this).find('.new_price').text() );
			
			var discount = '-' + parseInt(100 - new_price / old_price * 100) + '%';
			$(this).find('.price_sales').text(discount);
		});

		$('.product_item form').submit(function() {
			var data = {
				action: "add_product_to_cart_custom",
				quantity: $(this).find("input[name='quantity']").val(),
				product_id: $(this).attr("product_id")
			};
			
			jQuery.post(ajaxurl, data, function(res) {
				console.log(res);
			});
			return false;
		});
		
	});
</script>


<div class="sales_block background_none">
	<ul class="ul_cat">
		<li><img src="<?php bloginfo('template_directory'); ?>/images/shirts.png"><a href="">Текстиль и одежда<span>&#9660;</span></a>
			<ul>
				<li><a href="">Пункт меню 1</a></li>
				<li><a href="">Пункт меню 1</a></li>
				<li><a href="">Пункт меню 1</a></li>
				<li><a href="">Пункт меню 1</a></li>
			</ul>
		</li>
	</ul>
	<div class="clear"></div>
	<?php
	$args = array(
		'post_type' => 'product',
		'product_cat' => 'accessories',
		'posts_per_page' => 4
	);

	$cont_num =0;
	$loop = new WP_Query( $args );
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post();
			++$cont_num;
			?>
			<div class="product_item">
				<div class="border_right <?php if($cont_num==4) echo 'border_none'; ?>">
					<a href="<?php the_permalink() ?>" class="img_product">
						<?php echo woocommerce_get_product_thumbnail(); ?>
					</a>
					<a href="<?php the_permalink() ?>" title="Ссылка на: <?php the_title_attribute(); ?>" class="title_profuct"><?php the_title(); ?></a>
					<div class="new_price"><?php echo $product->price; ?> сум</div>

					<meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>" />
					<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
					<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

					<?php
					if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
						$html = '<form  class="cart" method="post" product_id="'. $product->get_id() .  '" enctype="multipart/form-data">';
						$html .= '<div class="num_select">'.woocommerce_quantity_input( array(), $product, false ).'</div>';
						$html .= '<div class="but_add"><button type="submit">добавить</button></div>';
						$html .= '</form>';
						echo $html;
					} elseif ( $product->is_type( 'variable' ) ) {
					?>
						<div class="but_add"><a href="<?php the_permalink() ?>">выбрать</a></div>
					<?php } ?>
					<div class="clear"></div>
				</div>
			</div>
		<?php endwhile;
	} else {
		echo __( 'No products found' );
	}
	wp_reset_postdata();
	?>
	<div class="clear"></div>

</div>


	</div>
	<?php do_action( 'storefront_sidebar' ); ?>

	<div class="clear"></div>
</div>
<?php

get_footer();
