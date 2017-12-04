<?php
if( wp_doing_ajax() ) {
    add_action('wp_ajax_nopriv_find_products', 'find_products');
    add_action('wp_ajax_find_products', 'find_products');
}

function find_products() {
    $posts_per_page = 10;
    $data = $_POST;

    if(  $data['category_name'] == null ||  $data['filter_data'] == null ) {
        echo 'Error: Не верно переданны данные!';
        wp_die();
    }

    $category_name = $_POST['category_name'];
    $filter_data_json = $_POST['filter_data'];
    $filter_data = json_decode( stripcslashes ( $filter_data_json ), true );
    
    $category = get_term_by( 'slug', $category_name, 'product_cat' );
    $cat_id = $category->term_id;

    get_filtred_products($filter_data, $cat_id);
    wp_die();
}

//////////////////////////////////////////////////////////////////////

add_action( 'widgets_init', 'register_filter_widget' );

function custom_filter_widget_frontend_js() {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
            $('.custom_filter_widget input[type="checkbox"').change(function () {
                var category_name = $('.woocommerce-products-header .woocommerce-products-header__title').text();
                var filter_name = $(this).text();
                var attr_tax = $(this).parent().data('attr-tax');
                if (!category_name) {
                    return;
                }

                there_is_not_more_items = false; // Сбрасывет флаг на наличие товаров. Переменная описана и используется в файле get_filtred_products.php
                
                var filter_data = getFilterData(true);

                var data = {
                    action: 'find_products',
                    category_name: category_name,
                    filter_data: filter_data
                };
                
                jQuery.post(ajaxurl, data, function (res) {
                    $('.products_list_wrapper').replaceWith(res);

                    var url_params = get_GET_url( getFilterData() );
                    var path = window.location.href;

                    if( path.indexOf('?') !== -1 ) {
                        path = path.slice( window.location.origin.length, path.indexOf('?') );
                    }

                    history.pushState(null, '', path + url_params);
                });
            });

            // Функция получает все данные из форм фильтрации и если указан параметр returnJson равный true, возвращает данные в json формате, иначе в виде обычного объекта.


            // Дополняет jQuery методом $.parseParams(url), принимает параметры из url (после символа '?') и возвращает объект.
            (function($) {
                var re = /([^&=]+)=?([^&]*)/g;
                var decodeRE = /\+/g;  // Regex for replacing addition symbol with a space
                var decode = function (str) {return decodeURIComponent( str.replace(decodeRE, " ") );};
                $.parseParams = function(query) {
                    var params = {}, e;
                    while ( e = re.exec(query) ) { 
                        var k = decode( e[1] ), v = decode( e[2] );
                        if (k.substring(k.length - 2) === '[]') {
                            k = k.substring(0, k.length - 2);
                            (params[k] || (params[k] = [])).push(v);
                        }
                        else params[k] = v;
                    }
                    return params;
                };
            })(jQuery);


        });
        function getFilterData( returnJson ) {
            var filter_data = {};
            jQuery('.custom_filter_widget input[type="checkbox"').each(function() {
                if( jQuery(this).attr('checked') ) {
                    var attr_name = jQuery(this).closest('[data-attr-tax]').data('attr-tax');
                    var attr_tax = jQuery(this).attr('name');
                    if ( filter_data[attr_name] ) {
                        filter_data[attr_name].push( attr_tax );
                    } else {
                        filter_data[attr_name] = [ attr_tax ];
                    }
                }
            });
            if( returnJson ) {
                return JSON.stringify( filter_data );
            }
            return filter_data;
        }

        function get_GET_url(params) {
            if( typeof params !== 'object' ) { return false; }
            var result = '?';

            for( var param in params ) {
                result += 'filter_' + param + '=';
                for (var i = 0; i < params[param].length; i++) {
                    result += params[param][i];
                    if( i !== params[param].length - 1 ) {
                        result += ',';
                    }
                }
                result += "&";
            }
            return result;
        }
        </script>
    <?php
}

function register_filter_widget() {
    register_widget( 'Cusstom_Filters_Widget' );
    add_action('wp_footer', 'custom_filter_widget_frontend_js');
}

class Cusstom_Filters_Widget extends WP_Widget { 

    function __construct() {
		$widget_ops = array( 'classname' => 'Filters', 'description' => __('A widget that displays the authors name ', 'Cusstom_Filters_Widget') );
		

		parent::__construct( 'Cusstom_Filters_Widget', __('Cusstom_Filters_Widget', 'Cusstom_Filters_Widget'), $widget_ops );
	}

    function widget( $args, $instance ) {
        extract($args, EXTR_SKIP);
      
        // В данном блоке получаем данные с get запроса, преобразуем в валидный для сравнения вид, и записываем в переменную $filter_params
        $filter_params = array();
        foreach ($_GET as $filter_name => $filter_values) {
            if( stristr( $filter_name, 'filter_' ) ) {
                $filter_params[ substr( $filter_name, 7 ) ] = explode(  ',', $filter_values );
            }
        }
        //////////////////////////////////////////////////////////
        
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
        $filter_type = empty($instance['filter_type']) ? ' ' : apply_filters('widget_filter_type', $instance['filter_type']);
        
        if($filter_type == "") {
            wp_die();
        }
        
        $terms = get_attribute_terms($filter_type);
        ?>
        <div class="custom_filter_widget" data-attr-tax="<?php echo $filter_type; ?>">
            <h1> <?php echo $filter_type; ?> </h1>
            <?php foreach($terms as $name => $slug): 
                $has_checked = false;
                if( isset( $filter_params[$filter_type] ) ) {
                    foreach($filter_params[$filter_type] as $param) {
                        if( $param == $name ) { $has_checked = true; }
                    }
                }
                ?> 
                <p>
                    <input type="checkbox" name="<?php echo $name ?>" id="custom_filter_widget_checkbox_<?php echo $name ?>" <?php echo $has_checked ? ' checked' : ''  ?>>
                    <label for="custom_filter_widget_checkbox_<?php echo $name ?>"><?php echo $slug ?></label>
                </p>
            <?php endforeach; ?>
        </div>
        <?php
    }

    function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['filter_type'] = $new_instance['filter_type'];
		return $instance;
    }

    function form($instance) {
        $title = " ";
        $filter_type = " ";
        if( !empty($instance) ) {
            $title = $instance['title'];
            $filter_type = $instance['filter_type'];
        }

        $title_id = $this->get_field_id('title');
        $title_name = $this->get_field_name('title');

        $filter_type_id = $this->get_field_id('filter_type');
        $filter_type_name = $this->get_field_name('filter_type');

        $attribute_names = get_all_attributes_names();
        
        
		?>
        
        <p>
            <label for="<?php echo $title_id ?>">Заголовок:</label>
            <input class="widefat" id="<?php echo $title_id ?>" value="<?php echo $title ?>" name="<?php echo $title_name ?>" type="text" />
        </p>
        <p>
            <select name="<?php echo $filter_type_name ?>" id="<?php echo $filter_type_id ?>">
                <option value="">Выбрать...</option>
                <?php foreach( $attribute_names as $the_name => $the_value ): ?>
                    <option value="<?php echo $the_name ?>" <?php echo ($filter_type == $the_name) ? 'selected': ''; ?> ><?php echo $the_value ?></option>
                <?php endforeach; ?>
            </select>
        </p>

		<?php
    }
}

function get_all_attributes_names() {
    $names = array();
    $attr_tax = wc_get_attribute_taxonomies();
    foreach ($attr_tax as $value) {
        $names[ $value->attribute_name ] = $value->attribute_label;
    }
    return $names;
}

function get_attribute_terms( $attr_name ) {
    $result = array();
    $terms = get_terms('pa_' . $attr_name);

    if( !empty($terms) ) {
        foreach ($terms as $term) {
            $result[$term->slug] = $term->name;
        }
        return $result;
    }

    return false;
}
?>