<?php
/* Template Name: Мой шаблон */
get_header(); 
?>

<!--Главные категории-->
<div class="wrap_block">
	<div class="top_category">
		<ul class="ul_cat">
			<li><img src="<?php bloginfo('template_directory'); ?>/images/shirts.png"><a href="<?php echo get_term_link( 26 ,'product_cat') ?>"><?php echo __('[:uz]To\'qimachilik va kiyim[:ru]Текстиль и одежда'); ?><span>&#9660;</span></a>
				<?php woocommerce_subcats_from_parentcat_by_ID(26); ?>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/ham.png"><a href="<?php echo get_term_link( 29 ,'product_cat') ?>"><?php echo __('[:uz]Fastfood va ichimliklar[:ru]Фасфуд и напитки'); ?><span>&#9660;</span></a>
				<?php woocommerce_subcats_from_parentcat_by_ID(29); ?>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/cake.png"><a href="<?php echo get_term_link( 27 ,'product_cat') ?>"><?php echo __('[:uz]Shirinliklar va qahva[:ru]Сладости и кофе'); ?><span>&#9660;</span></a>
				<?php woocommerce_subcats_from_parentcat_by_ID(27); ?>
			</li>
			<li><img src="<?php bloginfo('template_directory'); ?>/images/market.png"><a href="<?php echo get_term_link( 28 ,'product_cat') ?>"><?php echo __('[:uz]Market[:ru]Маркет'); ?><span>&#9660;</span></a>
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
					<h1><?php echo __('[:uz]Bepul yuk[:ru]Доставка бесплатно'); ?></h1>
					<?php echo __('[:uz]Toshkentda bepul yetkazib berish[:ru]Бесплатная доставка по Ташкенту'); ?><br>
					
				</div>
			</a>
			<a href="">
				<img src="<?php bloginfo('template_directory'); ?>/images/money.png" alt="">
				<div>
					<h1><?php echo __('[:uz]To\'lovning har qanday shakli[:ru]Форма оплаты любая'); ?></h1>
					<?php echo __('[:uz]To\'lovni Click yoki Payme orqali onlayn qilib,[:ru]Оплата онлайн через Click или Payme,'); ?><br>
					<?php echo __('[:uz]Naqd yoki terminal orqali etkazib[:ru]Наличными или через терминал'); ?><br>
					<?php echo __('[:uz]Berish vaqtida[:ru]при доставке'); ?><br>
				</div>
			</a>
			<div class="clear"></div>
		</div>

		<div class="sales_block">
			<div class="sales_title">
				<h2><span><?php echo __('[:uz]Chegirmalar[:ru]Распродажа'); ?></span></h2>
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
											echo __('[:uz]Chegirma[:ru]Скидки');
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
								$html .= '<div class="but_add"><button type="submit">' . __('[:uz]qo\'shing[:ru]добавить') . '</button></div>';
								$html .= '</form>';
								echo $html;
							} elseif ( $product->is_type( 'variable' ) ) {
							?>
								<div class="but_add"><a href="<?php the_permalink() ?>"><?php echo __('[:uz]tanlash[:ru]выбрать'); ?></a></div>
							<?php 
							} ?>
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
				<li><img src="<?php bloginfo('template_directory'); ?>/images/shirts.png"><a href=""><?php echo __('[:uz]To\'qimachilik va kiyim[:ru]Текстиль и одежда'); ?><span>&#9660;</span></a>
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
			// Функция описана в файле get_products_functions.php
			show_products_by_category_on_the_main_page( $args );
			?>
		</div>

		<div class="sales_block background_none">
			<ul class="ul_cat">
				<li><img src="<?php bloginfo('template_directory'); ?>/images/ham.png"><a href=""><?php echo __('[:uz]Fastfood va ichimliklar[:ru]Фасфуд и напитки'); ?><span>&#9660;</span></a>
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
			// Функция описана в файле get_products_functions.php
			show_products_by_category_on_the_main_page( $args );
			?>
		</div>

		<div class="sales_block background_none">
			<ul class="ul_cat">
				<li><img src="<?php bloginfo('template_directory'); ?>/images/cake.png"><a href=""><?php echo __('[:uz]Shirinliklar va qahva[:ru]Сладости и кофе'); ?><span>&#9660;</span></a>
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
			// Функция описана в файле get_products_functions.php
			show_products_by_category_on_the_main_page( $args );
			?>

		</div>

		<div class="sales_block background_none">
			<ul class="ul_cat">
				<li><img src="<?php bloginfo('template_directory'); ?>/images/market.png"><a href=""><?php echo __('[:uz]Market[:ru]Маркет'); ?><span>&#9660;</span></a>
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
			// Функция описана в файле get_products_functions.php
			show_products_by_category_on_the_main_page( $args );
			?>
		</div>
	</div>
	<?php do_action( 'storefront_sidebar' ); ?>
	<div class="clear"></div>
</div>

<script>
	jQuery(document).ready(function($) {
		var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
		
		$('.priduct_prices').each(function() {
			if( $.trim( $(this).parent().find('.price_sales').text() ) == "<?php echo __('[:uz]Chegirma[:ru]Скидки'); ?>" ) {
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
	});
</script>
<?php
get_footer();