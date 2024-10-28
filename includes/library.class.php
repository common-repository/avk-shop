<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */

class libraryAVKShop extends MainValueAvkShop{
    public $USER;
    public $systemPay;
    public $statusData;
    public function __construct(){
        parent::__construct();
        $this->USER = new User_AVKShop();
        $this->systemPay = $this->add_systems_pay();
    }    
    
    public function get_all_price($cartUser){
        $price = 0;
        if(!is_array($cartUser)) return;
        foreach($cartUser as $idCart => $idPost){
            $value = array_shift(get_post_meta($idPost, '_metaBoxValue'));
            if($value['enabel_product_avk'] == 'on'){
                $price = $price + $value['price_product_avk'];
            }
        }
        return $this->clear_data_avk($price, 'int');
    }
    
    protected function add_systems_pay(){
        $systemPay = array( 'logavk'    => array('name' => __('Журнал', self::SLUG),
                                                 'title' => __('Просмотр и управления записями в журнале оплат', self::SLUG)),
                            'interkassa' => array('name'   => 'InterKassa',
                                                  'title' => __('Настройка платежного шлюза', self::SLUG) . ' InterKassa',
                                                  'imgUrl' => AVKSHOP_PL_URL .'/images/ik_88x31.gif',
                                                  'avk_sys_enabel' => '',
                                                  'ik_shop_id' => '',
                                                  'ik_secret_key' => '',
                                                  'ik_test_key' => '',
                                                  'ik_name_val' => '',
                                                  'ik_stus_res' => '',
                                                  'ik_text_res' => '',
                                                  'ik_stus_sus' => '',
                                                  'ik_text_sus' => '',
                                                  'ik_stus_fai' => '',
                                                  'ik_text_fai' => ''),
                            'robokassa'  => array('name'   => 'RoboKassa',
                                                  'title' => __('Настройка платежного шлюза', self::SLUG) . ' RoboKassa',
                                                  'imgUrl' => AVKSHOP_PL_URL .'/images/Robokassa88x31.png',
                                                  'avk_sys_enabel' => '',
                                                  'rb_shop_id' => '',
                                                  'rb_secret_key_1' => '',
                                                  'rb_secret_key_2' => '',
                                                  'rb_name_val' => '',
                                                  'rb_stus_res' => '',
                                                  'rb_stus_sus' => '',
                                                  'rb_stus_fai' => '',
                                                  'rb_text_sus' => '',
                                                  'rb_text_fai' => '')
                            );
        $systemPay = apply_filters('add_systems_pay_avkshop', $systemPay);
        return $systemPay;
    }
    
/** Фильтрация  данных, возвращаемые данные $data или error */
    public function clear_data_avk($data, $type="str"){
        if(!empty($data)){
            switch($type){
                case "str": $data = addcslashes(htmlspecialchars(trim(strip_tags($data)),ENT_QUOTES),"`");break;
                case "int": $data = abs((int)$data);break;
                case "log": $regv ="(^[a-zA-Z0-9_\-]{3,10}$)";
                            if(preg_match($regv,$data)){
                                $data = addcslashes(htmlspecialchars(trim(strip_tags($data)),ENT_QUOTES),"`");break;
                            }else{
                                $data='error';
                            }break;
                case "pas": $regv ="(^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{7,14}$)";
                            if(preg_match($regv,$data)){
                                $data = md5(addcslashes(htmlspecialchars(trim(strip_tags($data)),ENT_QUOTES),"`"));
                            }else{
                                $data='error';
                            }break;
                case "em":  $regv = '^[^@]+@([a-z\-]+\.)+[a-z]{2,5}$';
                            if(!ereg($regv,$data)){
                                $data= 'error';
                            }else{
                                $data = addcslashes(htmlspecialchars(trim(strip_tags($data)),ENT_QUOTES),"`");
                            }break;
            }
        }else{
            $data ='error';
        }
            return $data;
    }
    
/** Для получения случайного значения */
    public function avk_random_url($number){
        $array = array("A","B","C","D","E","F","G",
                       "H","I","J","K","L","M","N",
                       "O","P","Q","R","S","T","U",
                       "V","W","X","Y","Z","a","b",
                       "c","d","e","f","g","h","i",
                       "j","k","l","m","n","o","p",
                       "q","r","s","t","u","v","w",
                       "x","y","z","0","1","2","3",
                       "4","5","6","7","8","9");
        $outstring = '';
        for($i=0;$i<$number;$i++){
            $index = rand(0, count($array)-1);
            $outstring .= $array[$index];
        }
        return md5($outstring);
    }
    
    public function write_logs($email, $masseg, $die = true, $massDie = false){
        delete_option(self::SLUG . '_check_logs');
        $this->logInfo['size'] = filesize(AVKSHOP_PATH_LOG);
        
        $masseg = $this->clear_data_avk($email) . '|' . $this->clear_data_avk($masseg) . '|' . time() . "\n";
        file_put_contents(AVKSHOP_PATH_LOG, $masseg, FILE_APPEND | LOCK_EX);
        
        $this->logInfo['counter'] = $this->logInfo['counter'] + 1;
        add_option(self::SLUG . '_check_logs', $this->logInfo);
        if($die)
            if($massDie) die('<h1>' . $massDie . '</h1>');
            else die;
        else return;
    }
    
    public function get_post_to_cart($id){
        global $wpdb;
        $sql = "SELECT id FROM {$wpdb->prefix}{$this->mainSettings['tableshopping']} WHERE status_purchase='in_hand' AND order_status='activ' AND id_post=%d";
        $result = $wpdb->get_results($wpdb->prepare($sql, $id));
        if(empty($result)){
            return false;
        }else{
            return $result;
        }
    }
    
/** Проверка запроса от новой InterKassa */
    public function check_post_new_interkassa(){
        if(!isset($_POST['ik_co_id'], $_POST['ik_inv_st'])) return;
        $this->statusData['type_system'] = 'InterKassa';
        $this->statusData['status_messag'] = true;
        $text = __('В запросе от InterKassa не хватило данных, %s', self::SLUG);
        $chopID = $this->clear_data($_POST['ik_co_id']);
        $email = $this->clear_data($_POST['ik_x_user']);
        // проверяет существует или зарегистрированный пользователь с таким E-mail
        if(false == email_exists($email))
            $this->write_logs($email, sprintf($text, '«ik_x_user»'));
        // существует ли в запросе подпись
        if(!isset($_POST['ik_x_sing']))
            $this->write_logs($email, sprintf($text, '«ik_x_sing»'), true, __('Не балуйся!!!', self::SLUG));
        // проверка ID магазина
        if($this->systemPaySettings['interkassa']['ik_shop_id'] != $chopID)
            $this->write_logs($email, sprintf($text, '«ik_co_id» ==>> '.$chopID));
        // проверка подписи
        $data = array('ik_co_id'  => $_POST['ik_co_id'],
                      'ik_pm_no'  => $_POST['ik_pm_no'],
                      'ik_am'     => $_POST['ik_am'],
                      'ik_cur'    => $this->mainSettings['currency'],
                      'ik_desc'   => $_POST['ik_desc'],
                      'ik_x_user' => $_POST['ik_x_user']);
        ksort($data, SORT_STRING);
        $stringSing = implode(':', $data);
        $sing = base64_encode(md5($stringSing . SECURE_AUTH_KEY, true));
        if($_POST['ik_x_sing'] != $sing)
            $this->write_logs($email, __('В запросе от InterKassa неправильная электронная подпись', self::SLUG));
        //процесс обновления БД
        if('success' == $this->clear_data($_REQUEST['ik_inv_st']))
            $this->process_update_db($email);
    }
    
    private function process_update_db($email){
        $arrayID = $this->create_array_id($_POST['ik_pm_no']);
        if($this->statusData['status_messag'] == false)
            $this->write_logs($email, $this->statusData['text_messag']);
        
        $this->update_db($arrayID, $email, 'SUCCESS');
    }
    
    public function check_get_post_robokassa(){
        if(isset($_GET[$this->systemPaySettings['robokassa']['rb_name_val']])){
            $this->statusData['type_system'] = 'RoboKassa';
            if($_GET[$this->systemPaySettings['robokassa']['rb_name_val']]==$this->systemPaySettings['robokassa']['rb_stus_res']){
                $this->result_robokassa();
            }
            if($_GET[$this->systemPaySettings['robokassa']['rb_name_val']]==$this->systemPaySettings['robokassa']['rb_stus_sus']){
                $this->success_robokassa();
            }
            if($_GET[$this->systemPaySettings['robokassa']['rb_name_val']]==$this->systemPaySettings['robokassa']['rb_stus_fai']){
                $this->fail_robokassa();
            }
        }
    }
    
/** Отказ от оплаты RoboKassa */
    private function fail_robokassa(){
        $inv_id = $_REQUEST["InvId"];
        $this->write_logs($_REQUEST["Shp_item"], __('Отказ клиента от оплаты RoboKassa. Заказ #', self::SLUG).$this->clear_data_avk($_REQUEST['Shp_cart_id']), false);
        header('Location: '.get_bloginfo('url'));
        die;
    }
    
/** Получение уведомления об исполнении операции RoboKassa */
    private function result_robokassa(){
        $mrhPass2 = $this->systemPaySettings['robokassa']['rb_secret_key_2'];// регистрационная информация (пароль #2)
        $this->hash_str($mrhPass2, 'RESULT');
        if($this->statusData['status_messag']){
            if($this->check_robokassa('RESULT')){
                echo "OK".$_REQUEST["InvId"]."\n";
            }else{
                echo "BAD".$_REQUEST["InvId"]."\n";
            }
        }
        die;
    }
    
/** Проверка параметров в скрипте завершения операции RoboKassa */
    private function success_robokassa(){
        $mrhPass1 = $this->systemPaySettings['robokassa']['rb_secret_key_1'];// регистрационная информация (пароль #1)
        $this->hash_str($mrhPass1, 'SUCCESS');
        if($this->statusData['status_messag']){
            $temp = get_option('permalink_structure');
            $pref = (!empty($temp))?'?':'&';
            if($this->check_robokassa('SUCCESS')){
                header('Location: '.get_permalink($this->mainSettings['page_status_pay']).$pref.'robsuccess=payok');
            }else{
                header('Location: '.get_permalink($this->mainSettings['page_status_pay']).$pref.'robsuccess=paybad');
            }
            die;
        }
    }
    
    private function check_robokassa($type){
        $userId = email_exists($_REQUEST["Shp_item"]);
        if($userId === false) $this->write_logs($_REQUEST["Shp_item"], sprintf(__('В запросе «%s» от RoboKassa был указан не существующий E-mail', self::SLUG), $type), true, true);// проверка корректности подписи
        $user = new User_AVKShop(array('field'=>'id', 'value'=>$userId));
        $arrayShop = $this->create_array_id($_REQUEST['Shp_cart_id']);
        if(!$this->statusData['status_messag']) $this->write_logs($_REQUEST["Shp_item"], $this->statusData['text_messag']); // может прийти сообщение о неправильном массиве
        
        if($this->clear_data_avk($_REQUEST["OutSum"], 'int') != $this->get_all_price($arrayShop))
            $this->write_logs($_REQUEST["Shp_item"], sprintf(__('В запросе «%s» от RoboKassa, была передана неправильная сумма. Ожидалось «%d», пришло «%d».', self::SLUG), $type, $this->get_all_price($arrayShop), $this->clear_data_avk($_REQUEST["OutSum"], 'int'))); //создание сообщение о том, что сумма не соотвествует действительной
        
        $result = $this->update_db($arrayShop, $this->clear_data_avk($_REQUEST["Shp_item"]),$type);
        return $result;
    }
    
    private function update_db($array, $email, $type){
        global $wpdb;
        $invId = $this->create_id($array);
        foreach($array as $idShop => $idPost){
            $data = array('payment_system' => $this->statusData['type_system'],
                          'order_status' => 'paid',
                          'amount' => (int) $this->mainSettings['amount_download'],
                          'datetime' => time());
            $where = array('id'=>$idShop, 'order_status'=>'activ');
            $result = $wpdb->update($wpdb->prefix . $this->mainSettings['tableshopping'], $data, $where, array('%s','%s','%d','%d'), array('%d','%s'));
            $value = array_shift(get_post_meta($idPost, '_metaBoxValue'));
            $msg = sprintf(__('Товар «%s» был оплачен. ID заказа «%d». Цена товара: «%s». Платежная система: «%s». Тип запроса: «%s».', self::SLUG), $value['name_product_avk'], $invId, $value['price_product_avk'].' '.$this->mainSettings['currency'], $this->statusData['type_system'], $type);
            if($result != false){
                $this->write_logs($email, $msg, false);
                $info[$idShop] = true;
            }else{
                $sql = "SELECT order_status FROM {$wpdb->prefix}{$this->mainSettings['tableshopping']} WHERE id=%d";
                $result2 = $wpdb->get_row($wpdb->prepare($sql, $idShop));
                
                if($result2->order_status == 'paid'){
                    $this->write_logs($email, $msg, false);
                    $info[$idShop] = true;
                }
                if($result2->order_status == 'activ'){
                    $info[$idShop] = $wpdb->update($wpdb->prefix.$this->mainSettings['tableshopping'], $data, $where, array('%s','%s','%d','%d'), array('%d','%s'));
                    if($info[$idShop] != false){
                        $this->write_logs($email, $msg, false);
                    }else{
                        $this->write_logs($email, sprintf(__('Товар «%s» был оплачен, но запись в БД не обновилась. ID заказа «%d». Цена товара: «%s». Тип запроса: «%s».', self::SLUG), $value['name_product_avk'].' '.$this->mainSettings['currency'], $invId, $value['price_product_avk'], $type), false);
                    }
                }
            }
        }
        if(in_array(false, $info, true)){
            return false;
        }else{
            return true;
        }
    }
    
    private function hash_str($password, $type){
        if(isset($_REQUEST["OutSum"], $_REQUEST["InvId"], $_REQUEST["Shp_item"], $_REQUEST["SignatureValue"], $_REQUEST['Shp_cart_id'])){
            $outSumm = $_REQUEST["OutSum"];
            $invId = $_REQUEST["InvId"];
            $shpItem = $_REQUEST["Shp_item"];
            $ShpCartId = $_REQUEST['Shp_cart_id'];
            $crc = $_REQUEST["SignatureValue"];
            $crc = strtoupper($crc);
            $my_crc = strtoupper(md5("{$outSumm}:{$invId}:{$password}:Shp_cart_id={$ShpCartId}:Shp_item={$shpItem}"));
            $this->statusData['status_messag'] = false;
            if($my_crc == $crc){
                $this->statusData['status_messag'] = true;
            }else{
                $this->write_logs($_REQUEST["Shp_item"], __('В запросе от RoboKassa, кеш запроса от ' . $type . ' не прошел проверку.', self::SLUG), true, true);
            }
        }else{
            $this->write_logs($_REQUEST["Shp_item"], __('В запросе от RoboKassa не хватало данных!', self::SLUG), true);
        }
    }
    
/** Создание ID из массива */
    public function create_id($array){
        $invId = 0;
        foreach($array as $idShop => $idPost){
            $invId += $idShop + $idPost;
        }
        return $invId;
    }
    
/** Создание массива из ID */
    private function create_array_id($str, $check = true){
        $temp = explode('and', $str);
        $payId = explode('_', $temp[0]);
        $themId = explode('_', $temp[1]);
        if($check){
            if(count($payId) != count($themId)){
                $this->statusData['status_messag'] = false;
                $this->statusData['text_messag']   = sprintf(__('Не одинаковое количество значений в массивах с ID заказов и ID постов в сообщении от «%s». Сообщение имело следующий текст «%s».', self::SLUG), $this->statusData['type_system'], $this->clear_data_avk($str));
                return;
            }
        }
        $arrayStr = array_combine($payId, $themId);
        if(empty($arrayStr) || !is_array($arrayStr)){
            $this->statusData['status_messag'] = false;
            $this->statusData['text_messag']   = sprintf(__('Не удалось получить массив с ID заказов и тем из сообщения от %s. Сообщение имело следующий текст «%s».', self::SLUG), $this->statusData['type_system'], $this->clear_data_avk($str));
            return;
        }
        $arrayNum = array();
        foreach($arrayStr as $key => $value){
            $arrayNum[$this->clear_data_avk($key, 'int')] = $this->clear_data_avk($value, 'int');
        }
        return $arrayNum;
    }
    
    /** Получения ID или типа */
    public function get_post_info($var = 'id'){
        global $post;
        switch($var){
            case 'id'  : $postInfo = $post;
                         if (is_object($postInfo)) $postInfo = $postInfo->ID;
                         break;
            case 'type': $postInfo = $post->post_type; break;
        }
        return $postInfo;
    }
    
    public function chars_reference( $url ){
        //$pref = get_option( 'permalink_structure' );
        $pos = strpos( $url, '?' );
        $chars = ( $pos === false ) ? '?' : '&';
        return $chars;
    }
    
/**
 * Метод создает тег с нужными атрибутами и контентом
 *
 * @since 0.1
 *
 * @param string - $tag, название тега
 * @param array  - атрибуты тега, где ключи массива это атрибут
 * @param string - контент атрибута
 * @return string ввиде html кода
 */
    public function html( $tag ) {
    	static $SELF_CLOSING_TAGS = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' );
    
    	$args = func_get_args();
    
    	$tag = array_shift( $args );
    
    	if ( is_array( $args[0] ) ) {
    		$closing = $tag;
    		$attributes = array_shift( $args );
    		foreach ( $attributes as $key => $value ) {
    			if ( false === $value )
    				continue;
    
    			if ( true === $value )
    				$value = $key;
    
    			$tag .= ' ' . $key . '="' . esc_attr( $value ) . '"';
    		}
    	} else {
    		list( $closing ) = explode( ' ', $tag, 2 );
    	}
    
    	if ( in_array( $closing, $SELF_CLOSING_TAGS ) ) {
    		return "<{$tag} />";
    	}
    
    	$content = implode( '', $args );
    
    	return "<{$tag}>{$content}</{$closing}>";
    }

/**
 * Метод создает тег PRE с контентом из информации о переменной
 *
 * @since 0.1
 *
 * @param string - $tag, название тега. Обязателен.
 * @param boolean  - если установлен в false то вернет тег pre, если true то выведет. По умолчанию: true
 * 
 * @return string ввиде html кода или выводит строку
 */
    public function show_war_info( $var, $echo = true){
        ob_start();
        print_r($var);
        
        if($echo){
            echo $this->html('pre', array('class'=>'pre'), ob_get_clean());
        }else{
            return $this->html('pre', array('class'=>'pre'), ob_get_clean());
        }
    }
}
?>