<?php    
    define('BOT_TOKEN', '385864247:AAEEMPFmBZ9KfVbt-w8vm7BxJqzFTDjxmR4');
    define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
     
    
    class soTelegramMessager
    { 
        /**
        * Делает запрос к серверу
        * 
        * @param resource $handle
        * 
        * @return boolean
        */
        protected function _exec_curl_request($handle)
        {
            $response = curl_exec($handle);
            if ($response === false)
            {
                $errno = curl_errno($handle);
                $error = curl_error($handle);
                error_log("Curl returned error $errno: $error\n");
                curl_close($handle);
                return false;
            }
        
            $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
            curl_close($handle);
            if ($http_code >= 500)
            {
                // do not wat to DDOS server if something goes wrong
                sleep(10);
                return false;
            }
            else if ($http_code != 200)
            {
                $response = json_decode($response, true);
                error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
                if ($http_code == 401)
                {
                throw new Exception('Invalid access token provided');
                }
                return false;
            }
            else
            {
                $response = json_decode($response, true);
                if (isset($response['description']))
                {
                error_log("Request was successfull: {$response['description']}\n");
                }
                $response = $response['result'];
            }
        
            return $response;
        }
    
        /**
        * Подготовка запроса
        * 
        * @param string $method
        * @param array $parameters
        * 
        * @return boolean
        */
        protected function _apiRequest($method, $parameters)
        {
            if (!is_string($method))
            {
                error_log("Method name must be a string\n");
                return false;
            }
        
            if (!$parameters)
            {
                $parameters = array();
            }
            else if (!is_array($parameters))
            {
                error_log("Parameters must be an array\n");
                return false;
            }
        
            foreach($parameters as $key => & $val)
            {
                // encoding to JSON array parameters, for example reply_markup
                if (!is_numeric($val) && !is_string($val))
                {
                $val = json_encode($val);
                }
            }
        
            $url = API_URL . $method . '?' . http_build_query($parameters);
        
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($handle, CURLOPT_TIMEOUT, 60);
            return $this->_exec_curl_request($handle);
        }
    
        /**
        * Отправка сообщения 
        * 
        * @param int $id_chat
        * @param string $sMessage
        * 
        * @return void
        */
        public function sendMessage($id_chat, $sMessage)
        {
        //https://api.telegram.org/botID:HASH/sendMessage?chat_id=111&text=Nice+to+meet+you
    
            $this->_apiRequest('sendMessage', array(
                'chat_id' => $id_chat,
                'text' => $sMessage,
            ));
        }  
    }

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