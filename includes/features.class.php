<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */

class AVKShopFeatures extends AVKShopVariables{
    
    public function __construct(){
        parent::__construct();
    }
    
/** Добавляет переменную TAB к странице "Клиенты и продажи" */
    protected function initialization_avkshop(){
        if( $_GET['page'] == self::SLUG.'-table' ){
            if( !isset($_GET['tab']) ){
                header('Location: '.admin_url('admin.php?page='.self::SLUG.'-table&tab=interkassa'));
                die;
            }
        }
    }
/** Выполнения действий до отправки заголовков */
    public function add_initialization_avkshop(){
        $this->LIB = new libraryAVKShop();
        $this->HTML = new avkShopHtml();
        $this->USER = $this->LIB->USER;
        //Добавляет фильтры
        $this->add_my_filters();
        
        $this->add_avkshop_role();
        /* Осуществляет редирект от страниц регистрации и авторизации если пользователь зарегистрирован */
        if(is_user_logged_in()){
            if($this->urlHOST.$_SERVER['REQUEST_URI'] == get_permalink($this->actMainSettings['start_reg'])){
                header('Location: '.$this->urlHOST);
                die;
            }
            if($this->urlHOST . $_SERVER['REQUEST_URI'] == get_permalink($this->actMainSettings['authorization'])){
                header('Location: '.$this->urlHOST);
                die;
            }
            if($this->urlHOST . $_SERVER['REQUEST_URI'] == get_permalink($this->actMainSettings['cart_page']) && empty($this->USER->cart)){
                header('Location: '.$this->urlHOST);
                die;
            }
        }else{
            if($this->urlHOST . $_SERVER['REQUEST_URI'] == get_permalink($this->actMainSettings['cart_page'])){
                header('Location: '.$this->urlHOST);
                die;
            }
        }
        /* Сохранение данных */
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_REQUEST['actionsaveavk']) and $_REQUEST['actionsaveavk'] == 'yes'){
                $this->get_page_for_save();
            }
            if(isset($_REQUEST['dellogsavkshop']) and $_REQUEST['dellogsavkshop'] == 'yes'){
                $this->delete_log_string();
            }
            if(isset($_POST['actionvisitoravk']) and wp_verify_nonce($_POST["_avknonce"], self::SLUG.'_'.SECURE_AUTH_KEY)){
                switch($this->clear_data_avk($_POST['actionvisitoravk'])){//authorizationvisitor
                    case"visitorreg":           $this->chek_recapcha('registration');  break;
                    case"authorizationreg":     $this->chek_recapcha('authorization'); break;
                    case"authorizationvisitor": $this->login_avkshop20();              break;
                    case"logautuseravk":        $this->logout_avkshop20();             break;
                }
            }
            if(isset($_POST['avkshop_download_in_cart']) and wp_verify_nonce($_POST["_avknonce"], self::SLUG.'_'.SECURE_AUTH_KEY)){
                $this->the_act_of_loading_in_cart();
            }
            if(isset($_POST['action_del_cart_product']) and wp_verify_nonce($_POST["_avknonce"], self::SLUG.'_'.SECURE_AUTH_KEY)){
                $this->action_del_cart_product($_POST['action_del_cart_product']);
            }

            $this->LIB->check_post_new_interkassa();
            $this->LIB->check_get_post_robokassa();
            do_action('add_initialization_avkshop', '');
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            //$this->LIB->check_get_post_robokassa();
        }
    }
    
    protected function add_my_filters(){
        $this->defaultMainSettings = apply_filters('default_main_settings_avkshop', $this->defaultMainSettings);
        array_push($this->defaultMainSettings, array("type"=>"close"));
        
        $this->metaBoxValue = apply_filters('meta_box_value_avkshop', $this->metaBoxValue);
        array_push($this->metaBoxValue, array("type"=>"closemeta"));
    }
    
    protected function action_del_cart_product($id, $redirect = true){
        global $wpdb;
        $id = $this->clear_data_avk($id, 'int');
        if($id == 'error' || $id == 0) return;
        $sql="DELETE FROM " . $wpdb->prefix . $this->actMainSettings['tableshopping'] . " WHERE id = %d";
        $wpdb->query( $wpdb->prepare( $sql, $id ) );
        if( $redirect ){
            header('Location: ' . $this->urlHOST . $this->clear_data_avk( $_POST['_wp_http_referer'] ) );
            die;
        }
    }
    
    /** Выгрузка файла(оплаченного/бесплатного) */
    protected function the_act_of_loading_in_cart(){
        if($this->USER->id == 0) wp_die('<h1>' . __(' Зачем так делаешь???', self::SLUG) . '</h1>');
        $id = $this->clear_data_avk($_POST['avkshop_download_in_cart'],'int');
        $value = array_shift(get_post_meta($id, '_metaBoxValue'));
        if(empty($value) || $value['enabel_product_avk'] == 'off') return;
        switch($value['type_product_avk']){
            case'paid': if(array_key_exists($id, $this->USER->purchasedGoods)){
                            $this->read_file_avk($value['file_product_avk'], $value['new_name_product_avk']);
                            $this->write_downloaded_database($id, $value['type_product_avk']);
                        }else{
                            if(!in_array($id, $this->USER->cart)){
                                $this->add_product_to_cart($id);
                            }
                        }
                            break;
            case'free': $this->read_file_avk($value['file_product_avk'], $value['new_name_product_avk']);
                        $this->write_downloaded_database($id, $value['type_product_avk']);
                            break;
            default: apply_filters('the_act_of_loading_in_cart_avkshop', $value); break;
        }
        header('Location: '.$this->urlHOST.$this->clear_data_avk($_POST['_wp_http_referer']));
        die;
    }
    
    /** Добавление товара в корзину */
    protected function add_product_to_cart($id){
        global $wpdb;
        //$sql = "SELECT id,idthem,summa,countdown FROM {$wpdb->prefix}shopping_avk WHERE idcust=%d AND status=0 AND order_status='dn_work'";
        //$result = $wpdb->get_results($wpdb->prepare($sql, $idCust));
        $data = array( 'id_post' => $id, 'customer_id' => $this->USER->id, 'amount' => $this->actMainSettings['amount_download'], 'datetime' => time());
        $result = $wpdb->insert( $wpdb->prefix . $this->actMainSettings['tableshopping'], $data, array( '%d', '%d', '%d', '%d' ) );
        return $result;
    }
    
    /** Создание записи в базе данных при скачивании товара */
    protected function write_downloaded_database($id, $type){
        global $wpdb;
        $sql = "UPDATE " . $wpdb->prefix . $this->actMainSettings['tabledownload'] . " 
                    SET counter_downloads = counter_downloads + 1, datetime = %d 
                    WHERE id_post=%d 
                    AND customer_id=%d 
                    AND type_goods=%s";
        $result = $wpdb->query($wpdb->prepare($sql, time(), $id, $this->USER->id, $type));
        if($result === false || $result === 0){
            $data = array( 'id_post' => $id, 'customer_id' => $this->USER->id, 'counter_downloads' => 1, 'type_goods' => $type, 'datetime' => time());
            $wpdb->insert($wpdb->prefix.$this->actMainSettings['tabledownload'], $data, array( '%d', '%d', '%d', '%s', '%d' ));
            $this->counterDownloads = $data['counter_downloads'];
        }
        if($type == 'paid'){
            $data = array('counter_downloads' => $this->USER->purchasedGoods[$id]['counter'] + 1); //$this->statusData['type_system']
            $where = array('id'=>$this->USER->purchasedGoods[$id]['id']);
            $wpdb->update($wpdb->prefix.$this->actMainSettings['tableshopping'], $data, $where, array('%d'), array('%d'));
            $this->counterDownloads = $data['counter_downloads'];
        }
        $value = array_shift(get_post_meta($id, '_metaBoxValue'));
        ++$value['counter_product_avk'];
        update_post_meta($id, '_metaBoxValue', $value);
    }
    
    /** Выгрузка файла */
    protected function read_file_avk($file, $newFileName){
        $pathFile = $this->actMainSettings['main_path_up_dir'].'/'.$this->actMainSettings['name_up_dir'].'/'.$file;
        if(!file_exists($pathFile)) return;
        $extens = pathinfo($pathFile, PATHINFO_EXTENSION);
        header('Content-Description: File Transfer');
        header('Content-Type: application/.'.$extens);
        header('Content-Disposition: attachment; filename='.$newFileName.'.'.$extens);
        header('Expires: 0');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($pathFile));
        ob_clean();
        flush();
        readfile($pathFile);
    }

    /** Вход пользователя */
    protected function login_avkshop20(){
        session_start();
        if( empty($_POST) || !wp_verify_nonce($_POST["_avknonce"], self::SLUG.'_'.SECURE_AUTH_KEY) ) wp_die( __( 'Больше так не надо делать!!!', self::SLUG ) );
        $this->statusMsg = $this->chek_validate_form('authorization');
        if( $_SERVER['HTTP_REFERER'] != get_permalink($this->actMainSettings['authorization']) && $_SESSION['blockuseravkshop20'] === true ){
            wp_redirect(get_permalink($this->actMainSettings['authorization']));
            die;
        }
        if( in_array('error', $this->statusMsg) && $_SESSION['blockuseravkshop20'] === true ) return;
        if( in_array('error', $this->statusMsg) && !isset($_SESSION['blockuseravkshop20']) ){
            $_SESSION['blockuseravkshop20'] = true;
            wp_redirect(get_permalink($this->actMainSettings['authorization']));
            die;
        }
        $requestArr = array('user_login'=>$this->statusMsg['login'],'user_password'=>$_POST['userpasswavk'], true);
        $user = wp_signon( $requestArr, true );
        if(is_wp_error($user)){
            $this->statusMsg['obj'] = $user->get_error_message();
            $_SESSION['blockuseravkshop20'] = true;
            wp_redirect(get_permalink($this->actMainSettings['authorization']));
            die;
        }
        unset($_SESSION['blockuseravkshop20']);
        update_user_meta($user->ID, 'user_last_visit', time());
        header('Location: '.$this->urlHOST);
        exit;
    }
    
    /** Выход пользователя */
    protected function logout_avkshop20(){
        wp_clear_auth_cookie();
        header('Location: '.$this->urlHOST);
        exit;
    }
    
    /** Проверка reCaptcha */
    protected function chek_recapcha($nameAction){
        if( $this->actMainSettings['captchaenable'] == 'on' ){
            $this->statusMsg['recapcha']='';
            $this->statusMsg['recapchastat'] = false;
            if(!function_exists('_recaptcha_qsencode'))
                require_once(AVKSHOP_PL_PATH . '/lib/recaptchalib.php');
            $resp = recaptcha_check_answer($this->actMainSettings['captchasicretkey'], $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
            if (!$resp->is_valid) {
                $this->statusMsg = $this->chek_validate_form($nameAction);
                $this->statusMsg['recapcha'] = 'error';
            } else {
                $this->validate_create_user($nameAction);
            }
        }else{
            $this->validate_create_user($nameAction);
        }
    }
    
    /** Парсер контента */
    public function add_pars_content_avk($content){
        $postID = $this->LIB->get_post_info();
        switch($postID){
            case $this->actMainSettings['start_reg']       : $content = $this->HTML->form_register_visitor($this->statusMsg, 'visitorreg'); break;
            case $this->actMainSettings['intermediate_reg']: break;
            case $this->actMainSettings['error_reg']       : break;
            case $this->actMainSettings['cart_page']       : $content = '<div id="cart-content-avkshop">' . $this->select_content_cart($content) . '</div>'; break;
            case $this->actMainSettings['authorization']   : $content = $this->HTML->form_register_visitor($this->statusMsg, 'authorizationreg'); break;
            case $this->actMainSettings['page_status_pay'] : $content = apply_filters('show_messag_pay_system_avk', $content); break;
            default: $content = $this->HTML->get_html_meta_post($postID, $content); break;
        }
        return $content;
    }
    
    protected function select_content_cart($content){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if( $_SERVER['HTTP_REFERER'] == $this->urlHOST . $this->LIB->clear_data_avk( $_POST['_wp_http_referer'] ) ){
                if(isset($_POST['systems_pay_avkshop'])){
                    $str = $this->get_form_system_pay($_POST['systems_pay_avkshop']);
                    $str = $this->HTML->sales_receipt($this->USER->cart) . $str;
                }else{
                    $str = '<p>' . sprintf(__('Выберите %s способ оплаты', self::SLUG), '<a href="'.get_permalink($this->actMainSettings['cart_page']).'">') . '</a></p>';
                }
            }else{
                $str = '<p>' . sprintf(__('Произошла ошибка, пожалуйста, начните %s процесс оплаты заново.', self::SLUG), '<a href="'.get_permalink($this->actMainSettings['cart_page']).'">') . '</a></p>';
            }
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $str = $this->HTML->get_cart_user($content);
        }
        return $str;
    }
    
    protected function get_form_system_pay($syspemPay){
        $button = '<p class="container-button-avkshop">' . $this->HTML->get_buttons('buy', false) . '</p>';
        switch($this->LIB->clear_data_avk($syspemPay)){
            case"error": $str = '<p>' . __('Больше так не надо делать!!!', self::SLUG) . '</p>'; break;
            case"interkassa": $str = $this->HTML->get_form_interkassa($this->USER, $button); break;
            case"robokassa":  $str = $this->HTML->get_form_robokassa($this->USER, $button);  break;
            default: $str = apply_filters('get_form_system_pay_avkshop', $this->USER, $button);
        }
        return $str;
    }
    /** Добавление новой роли */
    private function add_avkshop_role(){
        if($this->actMainSettings['customerroleavkshop'] == 'buyer')
        add_role($this->actMainSettings['customerroleavkshop'], __('Покупатель', self::SLUG), array('read' => false));
    }
    
    protected function validate_create_user($nameAction){
        if ( empty($_POST) || !wp_verify_nonce($_POST["_avknonce"], self::SLUG.'_'.SECURE_AUTH_KEY) ) exit;
        switch($nameAction){
            case"registration": $this->statusMsg = $this->chek_validate_form('registration'); break;
            case"authorization": $this->statusMsg = $this->chek_validate_form('authorization'); break;
        }
        if(in_array('error',$this->statusMsg))return;
        if($nameAction == "registration"){
            $userdata = array( 'ID'              => 0,
                               'user_pass'       => $_POST['userpasswavk'],
                               'user_login'      => $this->statusMsg['login'],
                               'user_nicename'   => '',
                               'user_url'        => '',
                               'user_email'      => $this->statusMsg['email'],
                               'display_name'    => $this->statusMsg['nickname'],
                               'nickname'        => $this->statusMsg['nickname'],
                               'first_name'      => $this->statusMsg['firstname'],
                               'last_name'       => $this->statusMsg['lastname'],
                               'description'     => '',
                               'rich_editing'    => false,  // false - выключить визуальный редактор для пользователя.
                               'user_registered' => '', // дата регистрации (Y-m-d H:i:s)
                               'role'            => $this->actMainSettings['customerroleavkshop'], // (строка) роль пользвателя
                               'jabber'          => '',
                               'aim'             => '',
                               'yim'             => '' );
  
            $ID = wp_insert_user($userdata);
            if(is_wp_error($ID)){
                $this->statusMsg['obj'] = $ID->get_error_message();
                return;
            }
            update_user_meta($ID, 'show_admin_bar_front', false);
            update_user_meta($ID, 'user_status', 1);
            update_user_meta($ID, 'user_ip', $_SERVER['REMOTE_ADDR']);
            update_user_meta($ID, 'usernewsavk', $this->LIB->clear_data_avk($_POST['usernewsavk']));
            update_user_meta($ID, 'user_cart', array());
            wp_redirect(get_permalink($this->actMainSettings['intermediate_reg']));
            exit;
        }
        if($nameAction == "authorization"){
            $requestArr = array('user_login'=>$this->statusMsg['login'],'user_password'=>$_POST['userpasswavk'], true);
            $user = wp_signon( $requestArr, true );  
            if(is_wp_error($user)){
                $this->statusMsg['obj'] = ( $this->mainSettings['restore_password'] == 'on' )? $user->get_error_message() : __( 'Не правильно введено имя пользователя или пароль!', self::SLUG ); 
                return;
            }
            update_user_meta($user->ID, 'user_last_visit', time());
            session_start();
            unset($_SESSION['blockuseravkshop20']);
            header('Location: '.$this->urlHOST);
            exit;
        }
    }
    
    protected function chek_validate_form($nameAction){
        if($nameAction == "registration"){
            $status['login']     = $this->clear_data_avk($_POST['userloginameavk'], 'log');
            $status['password']  = $this->clear_data_avk($_POST['userpasswavk'], 'pas');
            $status['nickname']  = $this->clear_data_avk($_POST['usernicknameavk'], 'log');
            $status['email']     = $this->clear_data_avk($_POST['usermailavk'], 'em');
            $status['firstname'] = $this->clear_data_avk($_POST['userfirstnameavk']);
            $status['lastname']  = $this->clear_data_avk($_POST['userlastnameavk']);
        }
        if($nameAction == "authorization"){
            $status['login']     = $this->clear_data_avk($_POST['userloginameavk'],'log');
            $status['password']  = $this->clear_data_avk($_POST['userpasswavk'], 'pas');
        }
        return $status;
    }
    
    /** Сохранение данных meta box */
    public function avkshop_save_meta_box($id){
        if(isset($_POST['type_product_avk'])){
            $value = array_shift(get_post_meta($id, '_metaBoxValue'));
            if($_POST['type_product_avk'] != $value['type_product_avk']){
                $this->delete_meta_bd($id);
                if($_POST['type_product_avk'] == 'free' && $value['type_product_avk'] == 'paid'){
                    global $wpdb;
                    $sql = "UPDATE ".$wpdb->prefix.$this->actMainSettings['tableshopping']." SET counter_downloads = amount WHERE id_post = %d";
                    $wpdb->query($wpdb->prepare($sql, $id));
                }
            }
        }
        $this->save_option_plugin($this->metaBoxValue, 'metaBoxValue', $id, 'metabox');
    }
    
    /** Удаление данных в БД из таблиц плагина и удаление файла прикрепленного к записи */
    public function avkshop_delete_meta($id){
        $this->delete_meta_bd($id);
        $value = array_shift(get_post_meta($id, '_metaBoxValue'));
        $link = $this->actMainSettings['main_path_up_dir'].'/'.$this->actMainSettings['name_up_dir'].'/'.$value['file_product_avk'];
        if( file_exists($link) && is_file($link) )
            unlink($link);
    }
    
    protected function delete_meta_bd($id){
        global $wpdb;
        $sql = "DELETE FROM " . $wpdb->prefix . $this->actMainSettings['tableshopping'] . " WHERE status_purchase='in_hand' AND order_status='activ' AND id_post = %d";
        $wpdb->query($wpdb->prepare($sql, $id));
    }
    
    /** Выбор опций, которые будут сохраняться */
    protected function get_page_for_save(){
        check_admin_referer(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce");
        global $pagenow, $wpdb;
        if ($pagenow == 'admin.php'){
            $tab = $this->LIB->clear_data_avk($_GET['page']);
            switch ($tab){
                case self::SLUG.'-settings':
                        if(isset($_REQUEST['savemainsettings'])){
                            $result = $this->save_option_plugin($this->defaultMainSettings, $tab);
                            $url = ($result)?'&updated=true':'&updated=fulse';
                        }
                        if(isset($_REQUEST['ressetsettings'])){
                            $result = delete_option($tab);
                            $url = ($result)?'&delsetavk=true':'&delsetavk=fulse';
                        }
                        $page = self::SLUG.'-settings';
                            break;
                case self::SLUG.'-action-file':
                        if(isset($_REQUEST['actioncreatetableavk']) and $_REQUEST['actioncreatetableavk'] == 'yes'){
                            $wpdb->query($this->queryShopping);
                            $wpdb->query($this->queryDownload);
                        }
                        if(isset($_REQUEST['actiondeletefileserveravk']) and $_REQUEST['actiondeletefileserveravk'] == 'yes'){
                            $this->delete_file_from_dir_shop();
                            echo '<h1>YES</h1>';
                        }
                        $page = self::SLUG.'-action-file';
                            break;
                case self::SLUG.'-table':
                        if(isset($_REQUEST['_avk_save_action_sys_pay'])){
                            $result = $this->save_option_plugin($this->LIB->systemPay, $tab);
                            $url = ($result)?'&updated=true':'&updated=fulse';
                        }
                        $page = self::SLUG.'-table&tab='.$this->LIB->clear_data_avk($_REQUEST['_avk_save_action_sys_pay']);
                            break;
            }
            wp_redirect(admin_url('admin.php?page='.$page.$url));
            die;
        }
    }
    
    /** Метод сохранения настроек плагина */
    protected function save_option_plugin($options, $nameVar, $id='', $type='settings'){
        if(!is_array($options)) return false;
        $query = array();
        foreach ($options as $key => $value) {
            if(isset($_REQUEST[$value['id']])){
                if(is_array($_REQUEST[$value['id']])){
                    $query[$value['id']] = $_REQUEST[$value['id']];
                }else{
                    if($_REQUEST[$value['id']] == $this->warningMsg){
                        $query[$value['id']] = '';
                    }else{
                        switch($value['id']){
                            case"name_up_dir":   $query[$value['id']] = $this->sanitize_title_translit_avk(trim($_REQUEST[$value['id']])); break;
                            case"ownsafetysite": $query[$value['id']] = $this->sanitize_title_translit_avk(trim($_REQUEST[$value['id']])); break;
                            case"tableshopping": $query[$value['id']] = $this->sanitize_title_translit_avk(trim($_REQUEST[$value['id']])); break;
                            case"tabledownload": $query[$value['id']] = $this->sanitize_title_translit_avk(trim($_REQUEST[$value['id']])); break;
                            default: $query[$value['id']] = trim($_REQUEST[$value['id']]);
                        }
                    }
                }
            }
            if(isset($_REQUEST['_avk_save_action_sys_pay'])){
                if($key == $this->LIB->clear_data_avk($_REQUEST['_avk_save_action_sys_pay']) && is_array($value)){
                    foreach($value as $keys => $temp)
                        if(isset($_REQUEST[$keys.'_avk']))
                            $this->systemPaySettings[$key][$keys] = trim($_REQUEST[$keys.'_avk']);
                    $query = $this->systemPaySettings;
                }
            }
        }
        switch($type){
            case'settings': delete_option($nameVar);
                            $res = add_option($nameVar, $query);
                                break;
            case'metabox' : $res = update_post_meta($id, '_'.$nameVar, $query, $prev_value); break;
        }
        return $res;
    }
    
    /** Удаление записей из лог файла */
    protected function delete_log_string(){
        check_admin_referer(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce");
        if(isset($_REQUEST['listinglogavkshop'])){
            if(!file_exists(AVKSHOP_PATH_LOG)) return;
            $logs = file(AVKSHOP_PATH_LOG);
            $result = array_diff_key($logs, $_REQUEST['listinglogavkshop']);
            file_put_contents(AVKSHOP_PATH_LOG, $result, LOCK_EX);
        }
        header('Location: '.admin_url('admin.php?page='.self::SLUG.'-table&tab=logavk'));
        die;
    }
    
    /** Удаление файлов */
    protected function delete_file_from_dir_shop(){
        check_admin_referer(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce");
        $nameFile = $this->LIB->clear_data_avk($_POST['action_delete_file_avk']);
        if($nameFile == 'error') return;
        $path = $this->actMainSettings['main_path_up_dir'].'/'.$this->actMainSettings['name_up_dir'];
        $link = $path .'/'. trim(strip_tags($nameFile));
        if(file_exists($link)){
            $act = (unlink($link))? '&deletefile=true&namefileavk='.$nameFile : '&deletefile=false&namefileavk='.$nameFile;
        }else{
            $act = '&deletefile=nofile&namefileavk='.$nameFile;
        }
        wp_redirect(admin_url('admin.php?page='.$_GET['page'].$act));
        exit;
    }
    
    /** Добавляет ссылку настроек */
    public function add_avkshop_link_settings($links, $file){
			static $this_plugin;
			if (empty( $this_plugin ))
				$this_plugin = $this->baseNameFile;
			if ($file == $this_plugin){
				$settings_link = '<a href="'.admin_url('admin.php?page='.self::SLUG.'-settings').'">'.__('Настройки',self::SLUG).'</a>';
				array_unshift($links, $settings_link);
            }		
		return $links;
    }
    
    /** Добаляет страницу и подстраници плагина в админпанели */
    public function add_page_avkshop(){
        $includPage = array();
        $includPage[] = add_menu_page( 'AVK-Shop', 'AVK-Shop', 'manage_options', self::SLUG . '-settings', array( &$this, 'get_page_menu' ), AVKSHOP_PL_URL . '/images/icons.ico');
        add_submenu_page(self::SLUG.'-settings',  __('Главные настройки',self::SLUG).' '.$this->name,  __('Главные настройки',self::SLUG), 'manage_options', self::SLUG.'-settings',array (&$this, 'get_page_menu'));
        if($this->actMainSettings['pluginon'] == 'on'){
            $submenuPage = apply_filters( 'add_submenu_page_avkshop', $this->submenuPage, self::SLUG . '-settings' );
            foreach($submenuPage as $pages)
                $includPage[] = add_submenu_page($pages['parent_slug'], $pages['page_title'], $pages['menu_title'], $pages['capability'], $pages['menu_slug'], $pages['function']);
        }
        foreach($includPage as $page){
            add_action('admin_print_scripts-' . $page, array(&$this, 'engen_plugin_script_admin'));
        }
    }
    
    /** Добаляет закладки админбаре */
    public function add_admin_bar_avkshop() {
        if($this->USER->role != 'administrator') return;
        global $wp_admin_bar;
        $i = 1;
        $this->submenuBar = apply_filters('add_admin_bar_avkshop', $this->submenuBar, self::SLUG);
        if(!is_array($this->submenuBar)) return;
        $wp_admin_bar->add_menu(array('id'=>self::SLUG,'title'=>'AVK-Shop','href'=>'#'));
        foreach($this->submenuBar as $submenu){
            if($i === 1){
                $wp_admin_bar->add_menu($submenu);
            }else{
                if($this->actMainSettings['pluginon'] == 'on'){
                    $wp_admin_bar->add_menu($submenu);
                }
            }
            ++$i;
        }
    }
    
/**
 * Метод виджета для входа/корзины покупателя
 *
 * @since 0.1
 *
 * @param array - массив с HTML кодом виджета
 * @param array - массив с данными виджета
 *
 * @return string ввиде html кода
 */
    public function widget($args, $instance) {
        if($this->actMainSettings['pluginon'] == 'on'){
            if($this->actMainSettings['start_reg'] == $this->LIB->get_post_info() || $this->actMainSettings['authorization'] == $this->LIB->get_post_info()) return;

            if(is_user_logged_in()){
                $title = $this->USER->name;
            }else{
                $title = (empty($instance['title']))? $this->name : $instance['title'];
            }
            $str  = $args['before_widget'];
            $str .= $args['before_title'];
            $str .= apply_filters( 'widget_title', $title );
            $str .= $args['after_title'];
            $str .= $this->HTML->user_login_form();
            $str .= $args['after_widget'];

            return $str;
        }
    }

    /** Создание закладок на странице "Клиенты и продажи" */
    protected function get_admin_page_tabs($current='interkassa', $echo=true){
        $str = '';
        $str .= '<div id="icon-themes" class="icon32" style="background: url('. AVKSHOP_PL_URL .'/images/FolderRed.png) no-repeat;">&nbsp;</div>';
        $str .= '<h2 class="nav-tab-wrapper">';
        foreach( $this->LIB->systemPay as $tab => $value ){
            $class = ( $tab == $current ) ? ' nav-tab-active' : '';
            $str .= "<a class='nav-tab$class' href='".admin_url('admin.php?page='.self::SLUG.'-table&tab='.$tab)."'>".$value['name']."</a>";
        }
        $str .= '</h2>';
        $str .= '<div class="tab-content-avkshop">';
        switch($current){
            case'interkassa': break;
            case'robokassa': break;
            default: ;
        }
        $str .= '</div>';
        if($echo) echo $str;
        else return $str;
    }
    
    /** Получение всех пользователей */
    protected function get_all_users(){
        $args = array('blog_id' => $GLOBALS['blog_id'],
                      'role'    => $this->actMainSettings['customerroleavkshop'],
                      'orderby' => $this->actMainSettings['displaylist'],
                      'order'   => $this->actMainSettings['reverselist'],
                      'offset'  => '',
                      'number'  => '');  
        $result = get_users( $args );
        if(!is_array($result)) return;
        $str = '';
        foreach($result as $key => $user){
            $user = new User_AVKShop($user);
            if(empty($user->lastVis)) $lastVis = __('Еще не заходил',self::SLUG);
            else $lastVis = date('j.m.Y G:i',$user->lastVis);
            if($user->subsc) $subsc = __('Подписан', self::SLUG);
            else $subsc = __('Не подписан', self::SLUG);
            $str .= '<tr>';
            $str .= '<td>'.$user->id.'</td>';
            $str .= '<td>'.$user->email.'</td>';
            $str .= '<td>'.$user->firstName.'</td>';
            $str .= '<td>'.$user->lastName.'</td>';
            $str .= '<td><a href="http://ip-adress.com/ip_tracer/'.$user->ip.'" target="_blank">'.$user->ip.'</a></td>';
            $str .= '<td>'.$subsc.'</td>';
            $str .= '<td>'.date('j.m.Y G:i',$user->regTime).'</td>';
            $str .= '<td>'.$lastVis.'</td>';
            $str .= '</tr>';
        }
        return $str;
    }

    public function read_logs($array){
        if(!file_exists(AVKSHOP_PATH_LOG)) return $array;
        $num = intval(count(file(AVKSHOP_PATH_LOG)));
        $text = _n( '%s Post', '%s Posts', $num );
        if( filesize(AVKSHOP_PATH_LOG) > (int)$this->logInfo['size'] and $this->logInfo['counter'] > 0){
            $array[] = '<a href="'.admin_url('admin.php?page='.self::SLUG.'-table&tab=logavk').'">'.sprintf(__($text . ' в журнале. %s Из них %d новых.', self::SLUG), $num, '<span style="color:red;font-weight: bold;">', $this->logInfo['counter']).'</span></a>';
        }else{
            $array[] = '<a href="'.admin_url('admin.php?page='.self::SLUG.'-table&tab=logavk').'">'.sprintf(__($text . ' в журнале.', self::SLUG), $num).'</a><style>.new-logs-avkshop{color:red;}</style>';
        }
        return $array;
    }
    
    public function add_new_user_column($column) {
        global $pagenow;
        if($pagenow == 'users.php' && $_GET['role'] == $this->actMainSettings['customerroleavkshop']){
            $column['name'] = __('Имя Фамилия', self::SLUG);
            unset($column['role'], $column['posts']);
            $newColumn = array('subsc'     => __('Статус подписчика',self::SLUG),
                               'lastvis'   => __('Последний визит',self::SLUG),
                               'regtime'   => __('Дата регистрации',self::SLUG),
                               'ip'        => 'IP');
            $newColumn = apply_filters('add_new_user_column_avkshop', $newColumn);
            $column = array_merge( $column, $newColumn );
        }
        return $column;
    }

    public function output_new_user_column($value, $column_name, $id) {
        global $pagenow;
        if($pagenow == 'users.php' && $_GET['role'] == $this->actMainSettings['customerroleavkshop']){
            $user = new User_AVKShop(array('field'=>'id', 'value'=>$id));
            
            if(empty($user->lastVis)) $lastVis = __('Еще не заходил',self::SLUG);
            else $lastVis = date('j.m.Y G:i',$user->lastVis);
            
            if($user->subsc) $subsc = __('Подписан', self::SLUG);
            else $subsc = __('Не подписан', self::SLUG);
            
            switch($column_name){
                case'subsc':   $res = $subsc; break;
                case'lastvis': $res = $lastVis; break;
                case'regtime': $res = date('j.m.Y G:i',$user->regTime); break;
                case'ip': $res = '<a href="http://ip-adress.com/ip_tracer/'.$user->ip.'" target="_blank">'.$user->ip.'</a>'; break;
                default: $res = apply_filters('output_new_user_column_avkshop', $column_name, $user);
            }
            return $res;
        }
    }
    
    protected function clear_data_avk($data, $type="str"){
        if(!empty($data)){
            switch($type){
                case "str": $data = addcslashes(htmlspecialchars(trim(strip_tags($data)),ENT_QUOTES),"`");break;
                case "int": $data = abs((int)$data);break;
                case "log": $regv ="(^[a-zA-Z0-9_\-]{3,20}$)";//[a-zA-Z0-9_\-]{3,20}
                            if(preg_match($regv,$data)){
                                $data = addcslashes(htmlspecialchars(trim(strip_tags($data)),ENT_QUOTES),"`");break;
                            }else{
                                $data='error';
                            }break;
                case "pas": $regv ="(^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{3,15}$)";
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
    
    public function ajax_upload_file(){
        check_ajax_referer( self::SLUG . '_' . SECURE_AUTH_KEY, 'avk_notice_sec' );
        
        if( file_exists( AVKSHOP_PL_PATH . '/lib/file-action.class.php' ) ){
            include_once AVKSHOP_PL_PATH . '/lib/file-action.class.php';
            new FileActionAVK();    
        }else{
            echo 'choto tut';
        }
        
        die;
    }
    
    protected function ajax_action(){
        if($this->USER->id === 0){
            $this->ajaxFalse['warning'] = __('Необходимо зарегистрироваться!', self::SLUG);
            return $this->ajaxFalse;
        }
        if(!isset($_POST['avk_array_ajax']) || empty($_POST['avk_array_ajax'])) return $this->ajaxFalse;
        if(isset($_POST['avk_array_ajax']['warning'])){
            if($_POST['avk_array_ajax']['warning'] == 'no system pay'){
                $this->ajaxFalse['warning'] = $this->HTML->popup_window(__('Выберите систему оплаты!', self::SLUG));
                $result = $this->ajaxFalse;
            }
        }
        // добавление товара в корзину
        if(isset($_POST['avk_array_ajax']['add_to_cart'])){
            $result = $this->action_ajax_add_cart($_POST['avk_array_ajax']['add_to_cart']);
        }
        // получение кнопки "В корзину"
        if(isset($_POST['avk_array_ajax']['get_button_pay'])){
            $result = $this->get_button_ajax($_POST['avk_array_ajax']['get_button_pay']);
        }
        // удаление товара из корзины
        if(isset($_POST['avk_array_ajax']['delete_product_cart'])){
            $result = $this->delete_product_cart_ajax($_POST['avk_array_ajax']['delete_product_cart']);
        }
        //переход к оплате
        if(isset($_POST['avk_array_ajax']['systems_pay'])){
            $result = $this->get_form_system_pay_ajax($_POST['avk_array_ajax']['systems_pay']);
        }
        $result = json_encode($result);
        @header("Content-Type: text/html; charset=" . get_option('blog_charset'));
        @header("Cache-Control: no-store");
        exit($result);
    }
    
    protected function action_ajax_add_cart($value){
        $id = $this->clear_data_avk($value, 'int');
        if($id == 'error' || $value == 'undefined'){
            $this->ajaxFalse['error'] = $this->HTML->popup_window(__('Не удалось получить ID товара. Сообщите об этом администратору.', self::SLUG), 'err');
            return $this->ajaxFalse;
        }
        if(in_array($id, $this->USER->cart)){
            $this->ajaxFalse['warning'] = $this->HTML->popup_window(__('Данный товар уже в корзине!', self::SLUG));
            return $this->ajaxFalse;
        }
        $this->add_product_to_cart($id);
        $user = new User_AVKShop();
        $this->ajaxTrue['html'] = $this->HTML->user_basket_content($user);
        return $this->ajaxTrue;
    }
    
    protected function get_button_ajax($value){
        $id = $this->clear_data_avk($value, 'int');
        if($id == 'error' || $value == 'undefined'){
            $this->ajaxFalse['error'] = $this->HTML->popup_window(__('Не удалось получить ID товара. Сообщите об этом администратору.', self::SLUG), 'err');
            return $this->ajaxFalse;
        }
        $this->ajaxTrue['html']['button'] = $this->HTML->get_buttons('paid', false);
        $this->ajaxTrue['html']['cart'] = $this->HTML->user_basket_content($this->USER);
        return $this->ajaxTrue;
    }
    
    protected function delete_product_cart_ajax($value){
        $id = $this->clear_data_avk($value, 'int');
        if($id == 'error' || $value == 'undefined'){
            $this->ajaxFalse['error'] = $this->HTML->popup_window(__('Не удалось получить ID товара. Сообщите об этом администратору.', self::SLUG), 'err');
            return $this->ajaxFalse;
        }
        $this->action_del_cart_product($id, false);
        $user = new User_AVKShop();
        if(empty($user->cart)){
            $this->ajaxTrue['redirect'] = $this->urlHOST;
        }
        $this->ajaxTrue['html'] = $this->HTML->cart_user($user->cart);
        return $this->ajaxTrue;
    }
    
    protected function get_form_system_pay_ajax($systemPay){
        $system = $this->clear_data_avk($systemPay);
        if($system == 'error' || $systemPay == 'undefined'){
            $this->ajaxFalse['error'] = $this->HTML->popup_window(__('Не удалось получить выбранную систему для оплаты товара. Сообщите об этом администратору.', self::SLUG), 'err');
            return $this->ajaxFalse;
        }
        $str = $this->get_form_system_pay($systemPay);
        $this->ajaxTrue['html'] = $this->HTML->sales_receipt($this->USER->cart) . $str;
        return $this->ajaxTrue;
    }
}
?>