<?php
/* Template Name: Мой шаблон */


get_header(); 
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
								<div class="price_sales">
									<?php
										if( $product->is_type('variable') ) {
											echo "Скидки";
										}
									?>
								</div>
							</a>
							<a href="<?php the_permalink() ?>" title="Ссылка на: <?php the_title_attribute(); ?>" class="title_profuct"><?php the_title(); ?></a>
						
							<div class="priduct_prices">
								<?php echo $product->get_price_html(); ?>
							</div>

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

<script>
	jQuery(document).ready(function($) {
		var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
		
		$('.priduct_prices').each(function() {
			if( $.trim( $(this).parent().find('.price_sales').text() ) == "Скидки" ) {
				return;
			}

			// Обработка строк в числовой вид. Пример: '99.999.000 сум' в 99999000
			var old_price = $(this).find('del .amount').text();
			old_price = $.map( old_price.split('.'), function(el, i) {
				return parseInt( el );
			});
			old_price = old_price.join('');

			// Обработка строк в числовой вид. Пример: '99.999.000 сум' в 99999000
			var new_price = $(this).find('ins .amount').text();
			new_price = $.map( new_price.split('.'), function(el, i) {
				return parseInt( el );
			});
			new_price = new_price.join('');

			// Расчёт скидки и отображение в блоке для скидки.
			var discount = '-' + parseInt(100 - new_price / old_price * 100) + '%';
			$(this).parent().find('.price_sales').text(discount);
		});

		$('.product_item form').submit(function() {
			var that = this;
			var data = {
				action: "add_product_to_cart_custom",
				quantity: $(this).find("input[name='quantity']").val(),
				product_id: $(this).attr("product_id")
			};

			var product = {
				src: $(this).parent().find('.img_product').attr('href'),
				img_src: $(this).parent().find('.img_product img').attr('src'),
				title: $(this).parent().find('.title_profuct').text(),
				quantity: $(this).parent().find("input[name='quantity']").val(),
				price: parseInt( $(this).parent().find('ins .amount').text() )
			};

			var btn_txt = $(this).find('button[type="submit"]').text();
			$(this).find('button[type="submit"]').html('<img class="dual_ring" src="<?php bloginfo('template_directory'); ?>/images/DualRing.gif">');

			jQuery.post(ajaxurl, data, function(res) {
				if( res == "Error" ) {
					alert('Что-то пошло не так!');
					return;
				}
				$(that).find('button[type="submit"]').html( btn_txt );
				update_custom_cart(product, res);
			});



			return false;
		});

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
		
	});
</script>


<div class="sales_block background_none">
	<ul class="ul_cat">
		<li><img src="<?php bloginfo('template_directory'); ?>/images/shirts.png"><a href="">Текстиль и одежда<span>&#9660;</span></a>
			<?php woocommerce_subcats_from_parentcat_by_ID(26); ?>
		</li>
	</ul>
	<div class="clear"></div>
	<?php
	$args = array(
		'post_type' => 'product',
		'product_cat' => 'tekstil',
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
					<div class="priduct_prices">
						<?php echo $product->get_price_html(); ?>
					</div>

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

<div class="sales_block background_none">
	<ul class="ul_cat">
		<li><img src="<?php bloginfo('template_directory'); ?>/images/ham.png"><a href="">Фастфуд и напитки<span>&#9660;</span></a>
			<?php woocommerce_subcats_from_parentcat_by_ID(29); ?>
		</li>
	</ul>
	<div class="clear"></div>
	<?php
	$args = array(
		'post_type' => 'product',
		'product_cat' => 'fastfood',
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
					<div class="priduct_prices">
						<?php echo $product->get_price_html(); ?>
					</div>
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

<div class="sales_block background_none">
	<ul class="ul_cat">
		<li><img src="<?php bloginfo('template_directory'); ?>/images/cake.png"><a href="">Сладости и кофе<span>&#9660;</span></a>
			<?php woocommerce_subcats_from_parentcat_by_ID(27); ?>
		</li>
	</ul>
	<div class="clear"></div>
	<?php
	$args = array(
		'post_type' => 'product',
		'product_cat' => 'sweets',
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
					<div class="priduct_prices">
						<?php echo $product->get_price_html(); ?>
					</div>
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

<div class="sales_block background_none">
	<ul class="ul_cat">
		<li><img src="<?php bloginfo('template_directory'); ?>/images/market.png"><a href="">Маркет<span>&#9660;</span></a>
			<?php woocommerce_subcats_from_parentcat_by_ID(28); ?>
		</li>
	</ul>
	<div class="clear"></div>
	<?php
	$args = array(
		'post_type' => 'product',
		'product_cat' => 'shop',
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
					<div class="priduct_prices">
						<?php echo $product->get_price_html(); ?>
					</div>
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
