<?php
/* Template Name: Мой шаблон */

get_header(); ?>


<?php while ( have_posts() ) : the_post(); ?>

	<h2><a href="<?php the_permalink() ?>" title="Ссылка на: <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

	<?php

	function get_images($product)
	{
		$images = array();
		$attachment_ids = array();
		// Add featured image.
		if (has_post_thumbnail($product->get_id())) {
			$attachment_ids[] = $product->get_image_id();
		}
		// Add gallery images.
		$attachment_ids = array_merge($attachment_ids, $product->get_gallery_image_ids());

		// Build image data.
		foreach ($attachment_ids as $position => $attachment_id) {
			$attachment_post = get_post($attachment_id);
			if (is_null($attachment_post)) {
				continue;
			}
			$attachment = wp_get_attachment_image_src($attachment_id, 'full');
			if (!is_array($attachment)) {
				continue;
			}
			$images[] = array('id' => (int) $attachment_id, 'date_created' => wc_rest_prepare_date_response($attachment_post->post_date_gmt), 'date_modified' => wc_rest_prepare_date_response($attachment_post->post_modified_gmt), 'src' => current($attachment), 'name' => get_the_title($attachment_id), 'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true), 'position' => (int) $position);
		}
		// Set a placeholder image if the product has no images set.
		if (empty($images)) {
			$images[] = array('id' => 0, 'date_created' => wc_rest_prepare_date_response(current_time('mysql')), 'date_modified' => wc_rest_prepare_date_response(current_time('mysql')), 'src' => wc_placeholder_img_src(), 'name' => __('Placeholder', 'woocommerce'), 'alt' => __('Placeholder', 'woocommerce'), 'position' => 0);
		}


		foreach ($images as $value) {
			echo "<img src='".$value[src]."'/>";
		}
	}

	wc_print_r(get_images($product));




	$attributes = $product->get_attributes();
	?>

	<?php foreach ( $attributes as $attribute ) : ?>
		<tr>
			<th><?php echo wc_attribute_label( $attribute->get_name() ); ?></th>
			<td><?php
				$values = array();
				if ( $attribute->is_taxonomy() ) {
					$attribute_taxonomy = $attribute->get_taxonomy_object();
					$attribute_values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );
					foreach ( $attribute_values as $attribute_value ) {
						$value_name = esc_html( $attribute_value->name );
						if ( $attribute_taxonomy->attribute_public ) {
							$values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
						} else {
							$values[] = $value_name;
						}
					}
				} else {
					$values = $attribute->get_options();
					foreach ( $values as &$value ) {
						$value = make_clickable( esc_html( $value ) );
					}
				}
				echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
				?></td>
		</tr>
	<?php endforeach; ?>

	<?php the_content(); ?>
	<?php
	if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
		$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
		$html .= woocommerce_quantity_input( array(), $product, false );
		$html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
		$html .= '</form>';
		echo $html;
	}
	?>

	<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

		<p class="price"><?php echo $product->get_price_html(); ?></p>

		<meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>" />
		<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
		<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

	</div>

<?php endwhile; // end of the loop. ?>

<?php
get_footer();
