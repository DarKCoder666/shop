<?php 

function get_filtred_products($filter_params, $cat_id) {
    $cat_name = get_cat_name($cat_id);

    $args = array(
        'post_type'     => 'product',
        'product_cat'   =>  $cat_name,
        'posts_per_page'=>  8,
        'order'         => 'DESC',
        'orderby'       => 'date',
        'tax_query'     => array()  
    );

    foreach( $filter_params as $attr_name => $attr_taxonomies ) {
        $terms = array();
        foreach ($attr_taxonomies as $value) {
            array_push( $terms, $value );
        }

        $tax_query_arr = array(
            'taxonomy' => 'pa_' . $attr_name,
            'field'    => 'slug',
            'terms'    => $terms,
            'operator' => 'IN'
        );

        array_push( $args['tax_query'], $tax_query_arr );
    }
    
    $products = new WP_Query( $args );
    
    if ( $products->have_posts() ) :
        ?>

        <?php woocommerce_product_loop_start(); ?>

            <?php woocommerce_product_subcategories(); ?>

            <?php
            if ( $products->have_posts() ): while ( $products->have_posts() ):
                $products->the_post();
                global $product;
                ?>
                <div class="product_item" product_id="<?php echo $product->get_id() ?>">
                    <div class="border_right <?php if($cont_num==4) echo 'border_none'; ?>">
                        <a href="<?php the_permalink() ?>" class="img_product">
                            <?php echo woocommerce_get_product_thumbnail(); ?>
                        </a>
                        <div class="item_text_block">
                            <a href="<?php the_permalink() ?>" title="Ссылка на: <?php the_title_attribute(); ?>" class="title_profuct"><?php the_title(); ?></a>
                            <div class="new_price"><?php echo $product->get_price(); ?> сум</div>

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
            <?php
            endwhile;
                wp_reset_postdata();
            endif;

        woocommerce_product_loop_end(); ?>

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

    <?php endif;
}
?>