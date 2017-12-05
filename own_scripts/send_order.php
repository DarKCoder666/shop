<?php
/**
* Plugin Name: Test plugin
* Plugin URI: http://www.mainwp.com
* Description: This plugin does some stuff with WordPress
* Version: 1.0.0
* Author: Your Name Here
* Author URI: http://www.mainwp.com
* License: GPL2
*/
    
    define('BOT_TOKEN', '385864247:AAEEMPFmBZ9KfVbt-w8vm7BxJqzFTDjxmR4');
    define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
     
        // Посылает переданное сообщение в telegram канал текстиля.
    function send_message_tekstil_tg($msg) {
        $oTelegramMessager = new soTelegramMessager();
        $oTelegramMessager->sendMessage('-1001232696769', $msg );
    }
    
    // Посылает переданное сообщение в telegram канал магазина.
    function send_message_market_tg($msg) {
        $oTelegramMessager = new soTelegramMessager();
        $oTelegramMessager->sendMessage('-1001396814948', $msg );
    }
    
    // Посылает переданное сообщение в telegram канал быстрой еды.
    function send_message_fastfood_tg($msg) {
        $oTelegramMessager = new soTelegramMessager();
        $oTelegramMessager->sendMessage('-1001291269560', $msg );
    }
    
    // Посылает переданное сообщение в telegram канал сладостей.
    function send_message_sweets_tg($msg) {
        $oTelegramMessager = new soTelegramMessager();
        $oTelegramMessager->sendMessage('-1001373526852', $msg );
    }

    // Функция для отправки письма в telegram.
    function send_message($msg, $categories_str) {
        if( stristr( $categories_str, 'market' ) !== false ) {
            send_message_market_tg( $msg );
        }

        if( stristr( $categories_str, 'textil' ) !== false ) {
            send_message_tekstil_tg( $msg );
        }

        if( stristr( $categories_str, 'fastfood' ) !== false ) {
            send_message_fastfood_tg( $msg );
        }

        if( stristr( $categories_str, 'sweets' ) !== false ) {
            send_message_sweets_tg( $msg );
        }
    }
    
    
    
    add_action( 'woocommerce_checkout_order_processed', 'my_status_pending' );
    function my_status_pending($order_id){
        global $woocommerce;
        $order = new WC_Order( $order_id );

        // Получаем данные с заполненной формы заказа.
        $my_billing = $order->data['billing'];

        // Все родительские категории товара будут записаны в нижеследующую переменную.
        $all_catigories_of_these_products = "";

        // В данную переменную попадают все товары из корзины.
        $items = $order->get_items();
        
        // Итоговая сумма за все товары.
        $total_sum = 0;

        // Содержимое данной переменной будет отослано в telegram.
        $msg_for_tg = 'Номер заказа: ' . $order->get_order_number() . "; \n" .
                'Имя:' . $my_billing['first_name'] . "; \n" .
                'Фамилия: ' . $my_billing['last_name'] . "; \n" .
                'Номер дома и название улицы: ' . $my_billing['address_1'] . "; \n" .
                'Квартира/Аппартаменты: ' . $my_billing['address_2'] . "; \n" .
                'Номер телефона: ' . $my_billing['phone'] . "; \n" .
                'Почтовый индекс: ' . $my_billing['postcode'] . "; \n" .
                'Email: ' . $my_billing['email'] . ';';
        
        foreach ($items as $key => $value) {
            $valueDecoded = json_decode($value);
            $msg_for_tg .= "\n\n\nНаименование товара: " . $value['name'] . "\n" .
            "Количество: " . $value['quantity'] . "\n";
            
            $total_sum += $value['total'];
            
            // Получение категорий, к которым относится товар.
            $terms = get_the_terms ( $value['product_id'], 'product_cat' );
            
            // Данным циклом дополняем переменную all_catigories_of_these_products
            foreach( $terms as $term ) {
                $top_category = get_category_parent_name_by_child_id( $term->term_id );
                $all_catigories_of_these_products .= $top_category . ' ';
            }
            
            // Обрабатываем и добовляем вариации товаров в письмо для отправки в telegram.
            foreach ($valueDecoded->meta_data as $key2) {  
                $variation_title = wc_attribute_label($key2->key); // Например: Цвет, Размер ...
                $msg_for_tg .= "$variation_title: $key2->value \n";
            }
            $msg_for_tg .= "Стоимость за этот товар: " . $value['total'] . "\n";
        }
        
        $msg_for_tg .= "\n\nСтоимость за все товары: " . $total_sum;
        
        
        send_message( $msg_for_tg, $all_catigories_of_these_products );
    }


    // Функция для получения имени родительской категории по id одной из дочерних категорий.
    // Вложенность дочерних категорий может быть любой.
    function get_category_parent_name_by_child_id( $child_cat_id ) {
        $parent_cat = wp_get_term_taxonomy_parent_id( $child_cat_id, 'product_cat' );
        
        if($parent_cat == 0) {
            return get_cat_name( $child_cat_id );
        }

        return get_category_parent_name_by_child_id( $parent_cat );
    }


    
    
?>