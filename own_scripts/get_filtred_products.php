<?php 

if( wp_doing_ajax() ) {
    add_action('wp_ajax_nopriv_load_products', 'load_products');
    add_action('wp_ajax_load_products', 'load_products');
}

function load_products() {
    $data = $_POST;

    if(  $data['loaded_products'] == null ||  $data['filter_data'] == null  ||  $data['cat_name'] == null  ) {
        echo 'Error: Не верно переданны данные!';
        wp_die();
    }

    $loaded_products = trim( $data['loaded_products'] );
    $filter_data_json = trim( $data['filter_data'] );
    $cat_name = trim( $data['cat_name'] );
    $filter_data = json_decode( stripcslashes ( $filter_data_json ), true );

    $products = get_products_by_filter( $filter_data, $cat_name, $loaded_products );

    if( $products == false ) {
        echo 'no more';
        wp_die();
    }

    show_products($products);

    wp_die();
}

function get_products_by_filter($filter_params, $cat_name, $offset) {
    
    $args = array(
        'post_type'     =>  'product',
        'product_cat'   =>  $cat_name,
        'posts_per_page'=>  4,
        'offset'        =>  $offset,
        'order'         =>  'DESC',
        'orderby'       =>  'date',
        'meta_query'    =>  array(),
        'tax_query'     =>  array()
    );


    // Если параметры max_price и min_price переданы, то
    if( isset( $filter_params['max_price'] ) && isset( $filter_params['min_price'] ) ) {

        $prices = get_prices_from_filter_params( $filter_params );

        foreach( $prices as $price ) {
            // Проверяем а корректны ли переданные данные, т.е. min_price не может быть больше чем max_price, иначе приведет к ошибки при запросе
            if( (int) $price['min_price'] <= (int)$price['max_price'] ) {
    
                $price_query_arr = array(
                    'key'     => '_price',
                    'value'   => array( (int) $price['min_price'], (int)$price['max_price'] ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN'
                );
                
                array_push($args['meta_query'], $price_query_arr);
            }
        }

        
    }

    foreach( $filter_params as $attr_name => $attr_taxonomies ) {
        if( $attr_name == 'min_price' || $attr_name == 'max_price' ) {
            continue;   
        }

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


    if( $products->have_posts() ) {
        return $products;
    }

    return false;
}

function show_products($products) {
    if ( $products->have_posts() ): while ( $products->have_posts() ):
        $products->the_post();
        global $product;
        ?>
        <div class="product_item" product_id="<?php echo $product->get_id() ?>">
            <div class="border_right">
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
}

function get_filtred_products($filter_params, $cat_id) {
    $cat_name = get_cat_name($cat_id);
    $products = get_products_by_filter( $filter_params, $cat_name, 0);
    if( $products !== false ) {
        if ( $products->have_posts() ) :
            ?>
            <div class="products_list_wrapper">
                <?php woocommerce_product_loop_start(); ?>

                    <?php woocommerce_product_subcategories(); ?>
                    
                    <?php
                    show_products( $products );

                woocommerce_product_loop_end(); ?>
                
                <div class="products_loading_ring">
                    <img src="<?php bloginfo('template_directory'); ?>/images/DualRing.gif" alt="Ring" style="display: none">
                </div>

                <script>
                    var loading_products = false;     
                    var have_other_products = true;
                    var there_is_not_more_items = false; // Принимает значение true, если запрос на получение продуктов вернет значение 'no more'. Сбрасывается на false при выборе фильтров.

                    jQuery(document).ready(function($) {
                        var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";

                        scrollHandler();

                        $(window).scroll( scrollHandler );

                        function scrollHandler() {
                            if(loading_products) { return }
                            // Учитывается и высота окна браузера.
                            var scrollTop = window.scrollY + document.documentElement.clientHeight;
                            var lastProductScrollTop = $('.products .product_item:last-child').offset().top;

                            if(scrollTop > lastProductScrollTop) {
                                loading_products = true;
                                load_products();
                            }
                        }

                        function load_products() {
                            if(there_is_not_more_items) {
                                return;
                            }

                            var category_name = $('.woocommerce-products-header .woocommerce-products-header__title').text();
                            var filter_data = getFilterData(true);
                            var data = {
                                action: 'load_products',
                                loaded_products: $('.products .product_item').length,
                                filter_data: filter_data,
                                cat_name: category_name
                            };

                            $('.products_loading_ring img').css('display', 'inline-block');
                            
                            jQuery.post( ajaxurl, data, function(res) {
                                if( res == 'no more' ) {
                                    there_is_not_more_items = true;
                                    $('.products_loading_ring img').css('display', 'none');
                                    return;
                                }

                                if( res.indexOf('Error:') == 0 ) {
                                    alert( res );
                                }

                                $('.products').append( res );
                                $('.products_loading_ring img').css('display', 'none');
                                loading_products = false;
                            });
                        }
                    });
                </script>

            </div>
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
}

function get_prices_from_filter_params($filter_params) {
    $prices = array();
    if( isset( $filter_params['min_price'] ) &&  isset( $filter_params['max_price'] ) ) {

        for( $i = 0; $i < count( $filter_params['min_price'] ); $i++ ) {
            array_push( $prices, array(
                'min_price' => $filter_params['min_price'][$i],
                'max_price' => $filter_params['max_price'][$i]
            ));
        }
    }
    return $prices;
}

function check_price_in_prices( $prices, $min_price, $max_price ) {
    foreach( $prices as $price ) {
        if( $min_price == $price['min_price'] && $max_price == $price['max_price'] ) {
            return true;
        }
    }
    return false;
}
?>