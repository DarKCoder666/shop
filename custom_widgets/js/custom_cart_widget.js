var $ = jQuery;
function update_custom_cart_total_price() {
    var total = 0;
    $('.custom_cart_product').each(function () {
        var quantity = parseInt(this.querySelector('.custom_cart_quantity').innerText);
        var price = parseInt(this.querySelector('.custom_cart_price').innerText);
        total += quantity * price;
    });

    $('.custom_cart_total_price_num').text(total);
}

function remove_item_from_cart_widget() {
    var data = {
        action: 'remove_item_from_cart_widget',
        product_key: this.getAttribute('product_key')
    };
    var that = this;

    jQuery.post(ajaxurl, data, function (res) {
        if (parseInt(res) === 1) {
            var parent_element = find_needen_parent(that, 'custom_cart_product');
            if (parent_element) {
                parent_element.remove();
                update_custom_cart_total_price();
            }
        }
    });
}

function find_needen_parent(element, parent_classname) {
    if (element == document.body) {
        return false;
    }

    var parent = element.parentNode;
    if (parent.classList.contains(parent_classname)) {
        return parent;
    } else {
        return find_needen_parent(parent, parent_classname);
    }
}

function set_add_to_cart_handler() {
    $('.product_item form').submit(function () {
        var that = this;
        var data = {
            action: "add_product_to_cart_custom",
            quantity: $(this).find("input[name='quantity']").val(),
            product_id: $(this).attr("product_id")
        };

        var product = {
            src: $(that).closest('.product_item').find('.img_product').attr('href'),
            img_src: $(that).closest('.product_item').find('.img_product img').attr('src'),
            title: $(that).closest('.product_item').find('.title_profuct').text(),
            quantity: $(that).closest('.product_item').find("input[name='quantity']").val(),
            price: parseInt($(that).closest('.product_item').find('ins .amount').text()) || parseInt($(that).closest('.product_item').find('meta[itemprop="price"]').attr('content'))
        };

        var btn_txt = $(this).find('button[type="submit"]').text();
        $(this).find('button[type="submit"]').html('<img class="dual_ring" src="<?php bloginfo('template_directory'); ?>/images/DualRing.gif">');

        jQuery.post(ajaxurl, data, function (res) {
            if (res == "Error") {
                alert('Что-то пошло не так!');
                return;
            }
            $(that).find('button[type="submit"]').html(btn_txt);
            update_custom_cart(product, res);
        });

        return false;
    });
}

function update_custom_cart(changes, product_key) {
    cart_have_been_updated = false;
    $('.custom_cart_product').each(function () {
        if ($.trim($(this).find('.custom_cart_title').text()) == $.trim(changes.title)) {
            var product_quantity = $(this).find('.custom_cart_quantity').text();
            $(this).find('.custom_cart_quantity').text(parseInt(product_quantity) + parseInt(changes.quantity));
            cart_have_been_updated = true;
            return false;
        }
    });
    if (!cart_have_been_updated) {
        var new_product_html = create_custom_cart_product_HTML(changes, product_key);
        var cart_products_html = $('.custom_cart_products').html();
        $('.custom_cart_products').html(cart_products_html + new_product_html);
    }
}

function create_custom_cart_product_HTML(product, product_key) {
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