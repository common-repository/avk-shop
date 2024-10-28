<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */

class avkShopHtml extends MainValueAvkShop{
    private $USER;
    private $LIB;
    private static $count = 0;
    public function __construct(){
        parent::__construct();
        $this->LIB = new libraryAVKShop();
        $this->USER = $this->LIB->USER;
    }
    /** HTML генератор форм */
    public function get_table_settings($defParametrs,$actParametrs,$sms,$echo=true){
        if(!is_array($defParametrs)) return;
        $str = "";
        foreach($defParametrs as $content){
            if(is_array($actParametrs) and isset($actParametrs[$content['id']])){
                $value = $actParametrs[$content['id']];
            }elseif(isset($content['std'])){
                $value = $content['std'];
            }else{
                $value = '';
            }
            if(is_array($value))$value = $value[$iteration];
            switch($content['type']){
                case"open": $str.='<div id="avk_settings_metabox" class="postbox">
                                        <h3 class="hndle hndleavk"><span>'.$content['title'].'</span></h3>
                                        <div class="inside"><dl>';
                                break;
                case"openmeta": $str.='<dl class="avkmetabox">'; break;
                case"openfieldset": $str.='<fieldset id="'.$content['id'].'" class="avk_fieldset"><legend class="avk_legend">'.$content['title'].'</legend>'; break;
                case"text": $str.='<dt id="'.$content['id'].'_title" class="dttitleavk">
                                        <div class="divlabel"><label class="regular-text" for="'.$content['id'].'">'.$content['label'].'</label></div>
                                        <input id="'.$content['id'].'" type="text" class="inputavk '.$content['class'].'" name="'.$content['id'].'" value="'.stripslashes($value).'" placeholder="' . $sms . '"/>
                                        <img class="helpimg" title="'.__('Нажмите, чтобы прочитать подробнее о настройке',self::SLUG).'" src="' . AVKSHOP_PL_URL . '/images/question.png"/>
                                   </dt>
                                   <dd id="'.$content['id'].'_desc" class="descavk">'.$content['desc'].'</dd>';
                                break;
                case"textarea": $str.='<dt id="'.$content['id'].'_title" class="dttitleavk">
                                            <div class="divlabel"><label class="regular-text" for="'.$content['id'].'">'.$content['label'].'</label></div>
                                            <textarea id="'.$content['id'].'" class="inputavk '.$content['class'].'" name="'.$content['id'].'" cols="40" rows="5"  placeholder="' . $sms . '">'.stripslashes($value).'</textarea>
                                            <img class="helpimg" title="'.__('Нажмите, чтобы прочитать подробнее о настройке',self::SLUG).'" src="' . AVKSHOP_PL_URL . '/images/question.png"/>
                                       </dt>
                                       <dd id="'.$content['id'].'_desc" class="descavk">'.$content['desc'].'</dd>';
                                break;
                case"select": $str.='<dt id="'.$content['id'].'_title" class="dttitleavk">
                                        <div class="divlabel"><label class="labelavk '.$content['class'].'" for="'.$content['id'].'">'.$content['label'].'</label></div>
                                        <select id="'.$content['id'].'" name="'.$content['id'].'" class="inputavk">'.$this->get_option_select($content['option'], $value).'</select>
                                        <img class="helpimg" title="'.__('Нажмите, чтобы прочитать подробнее о настройке',self::SLUG).'" src="' . AVKSHOP_PL_URL . '/images/question.png"/>
                                   </dt>
                                   <dd id="'.$content['id'].'_desc" class="descavk">'.$content['desc'].'</dd>';
                                break;
                case"readonly": $str.='<dt id="'.$content['id'].'_title" class="dttitleavk">
                                        <div class="divlabel"><label class="regular-text" for="'.$content['id'].'">'.$content['label'].'</label></div>
                                        <input id="'.$content['id'].'" readonly="readonly" class="inputavk '.$content['class'].'" name="'.$content['id'].'" value="'.stripslashes($value).'" />
                                        <img class="helpimg" title="'.__('Нажмите, чтобы прочитать подробнее о настройке',self::SLUG).'" src="' . AVKSHOP_PL_URL . '/images/question.png"/>
                                   </dt>
                                   <dd id="'.$content['id'].'_desc" class="descavk">'.$content['desc'].'</dd>';
                                break;
                case"file": if($content['std'] == stripslashes($value)){
                                $text = __('Загрузить',self::SLUG); 
                                $class = 'button-primary-act';
                                $act = 'addavkshop';
                                $rel = 'popup_download_file_avk';
                            }else{
                                $text = __('Удалить',self::SLUG); 
                                $class = 'button-primary-del';
                                $act = 'delavkshop';
                                $rel = 'popup_delete_file_avk';
                            }
                            $str.='<dt id="'.$content['id'].'_title" class="dttitleavk fileavk">
                                        <div class="divlabel"><label class="regular-text" for="'.$content['id'].'">'.$content['label'].'</label></div>
                                        <input id="'.$content['id'].'" readonly="readonly" class="inputavk '.$content['class'].'" name="'.$content['id'].'" value="'.stripslashes($value).'" onfocus="if(this.value == \''.$sms.'\'){this.value = \'\';this.style.color = \'#000000\';}" onblur="if(this.value == \'\'){this.value = \''.$sms.'\';this.style.color = \'#fc0000\';}"/>
                                        <span id="actbuttonavk">
                                            <button class="downloadavk button button-primary button-primary-avk '.$class.'" href="#?w=500" rel="'.$rel.'" act="'.$act.'">'.$text.'</button>
                                        </span>
                                        <img class="helpimg" title="'.__('Нажмите, чтобы прочитать подробнее о настройке',self::SLUG).'" src="' . AVKSHOP_PL_URL . '/images/question.png"/>
                                   </dt>
                                   <dd id="'.$content['id'].'_desc" class="descavk">'.$content['desc'].'</dd>';
                                break;
                case"closefieldset": $str.='</fieldset>'; break;
                case"closemeta": $str.='</dl></div>'; break;
                case"close": $str.='</dl></div></div>';
                                break;
            }
            
        }
        if($echo)echo $str;
        else return $str;
    }
    
    /** опции селекта */
    private function get_option_select($array, $actValue){
        if(!is_array($array)) return;
        $str = "";
        foreach($array as $key => $value){
            $str .= '<option value="'.$key.'" '.selected( $actValue, $key, false ).' >'.$value.'</option>';
        }
        return $str;
    }
    
    /** Форма добавления товара */
    public function get_meta_box($post, $arg){
        extract($arg['args'], EXTR_PREFIX_SAME, "avkshop");
        $actValue = array_shift(get_post_meta($post->ID, '_metaBoxValue'));
        $attrH2 = array('class' => 'infomsg');
        $queryStr = '';
        
        if(!empty($_SERVER['QUERY_STRING'])) $queryStr = '?' . $_SERVER['QUERY_STRING'];
        $stringHtml  = '<div class="avkshop_meta_middle">';
        $stringHtml .= '<div id="title_input_avk" class="">';
        $stringHtml .= '<input type="hidden" value="avkshop_edit" name="'.self::SLUG.'_edit" />';
        $stringHtml .= '<label for="id_post_avk" id="lb_id_post_avk">ID ' . $nameTypePost . ' = </label>';
        $stringHtml .= '<input readonly="readonly" id="id_post_avk" name="idpostavk" value="'.$post->ID.'"/>';
        $stringHtml .= '</div>';
        switch($post->ID){
            case $this->mainSettings['start_reg']        : $stringHtml .= $this->LIB->html( 'h2', $attrH2, __('Данная страница выбрана для регистрации посетителей.',self::SLUG) ) . '</div>'; break;
            case $this->mainSettings['intermediate_reg'] : $stringHtml .= $this->LIB->html( 'h2', $attrH2, __('Данная страница выбрана финальной для регистрации. Введите текст, который должен отображаться только, что зарегистрированному посетителю.',self::SLUG) ) . '</div>';break;
            case $this->mainSettings['authorization']    : $stringHtml .= $this->LIB->html( 'h2', $attrH2, __('Эта страница выбрана для авторизации с reCaptcha',self::SLUG) ) . '</div>'; break;
            case $this->mainSettings['cart_page']        : $stringHtml .= $this->LIB->html( 'h2', $attrH2, __('Страница для вывода корзины покупателя.',self::SLUG) ) . '</div>'; break;
            case $this->mainSettings['page_status_pay']  : $stringHtml .= '<h2 class="infomsg">'.__('Страница для вывода статуса платежа. Для дальнейшей настройки перейдите по ссылкам',self::SLUG);
                                                               foreach($this->LIB->systemPay as $nameSysPay => $valueSysPay)
                                                                   $tempStr .= ($nameSysPay != 'logavk')?' <a href="'.admin_url('admin.php?page='.self::SLUG.'-table&tab='.$nameSysPay).'">'.$valueSysPay['name'].'</a>,':'';
                                                           $stringHtml .= rtrim($tempStr, ',');
                                                           $stringHtml .= '.</h2></div>';break;
            default: $stringHtml .= '<input type="hidden" id="avk_notice_sec" name="avk_notice_sec" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$queryStr .'" />';
                     $stringHtml .= $this->get_table_settings($metaBoxValue, $actValue, $warningMsg, false);
                     $stringHtml .= $this->get_popup_download_file();
                     $stringHtml .= $this->get_popup_delete_file();
                     $stringHtml  = apply_filters( 'get_meta_box_avk', $stringHtml, $post, $arg, $actValue );
        }
        echo $stringHtml;
    }
    
    public function message_an_incomplete_application(){
        global $post;
        if($this->LIB->get_post_to_cart($post->ID))
            echo '<div class="misc-pub-section"><var id="massegtoadminnopaycart">' . __('У данной записи есть неоплаченные заявки!!!', self::SLUG) . '</var></div>';
    }
    
    /** Форма удаления файла */
    public function get_popup_download_file(){
        $str = '';      
        $str .= '<div id="popup_download_file_avk" class="popup_block">';
        $str .= '<div style="background-image: url('.AVKSHOP_PL_URL.'/images/add_files.png);" class="avkicon32"><br /></div>';
        $str .= '<h2>'.__('Добавляем файл',self::SLUG).'</h2>';
        $str .= '<div id="avk_error_upload" class="avk_error"></div>';
        $str .= '<dl>';
        $str .= '<dt>
                    <label for="uploadButton" class="lab_new_file lab_upload_file">'.__('Выберите файл: ',self::SLUG).'</label>
                    <div id="uploadButton" class="button button-primary">
                        <font>
                            '.__('Загрузить файл',self::SLUG).'
                        </font>
                    </div>
                    <div class="avk_loading"></div>
                 </dt>';
        $str .= '<dd>' . sprintf( __('Выберите файл для загрузки на сервер. %s Внимание: %s После выбора файла, загрузка начнется автоматически.',self::SLUG), '<br /><b>', '</b>').'</dd>';
        $str .= '</dl>';
        $str .= '<p class="submit">';
        $str .= '<input type="submit" class="button-primary button-primary-del clouse_pop_avk" value="&nbsp;&nbsp;&nbsp;'. __('Отмена',self::SLUG).'&nbsp;&nbsp;&nbsp;" />';
        $str .= '</p>';
        $str .= '</div>';
        return $str;
    }
    
    /** Форма загрузки файла */
    public function get_popup_delete_file(){
        $str = '';      
        $str .= '<div id="popup_delete_file_avk" class="popup_block">';
        $str .= '<div style="background: url('.AVKSHOP_PL_URL.'/images/warning.png) no-repeat;" class="avkicon32"><br /></div>';
        $str .= '<h2>'.__('Внимание!',self::SLUG).'</h2>';
        $str .= '<div id="avk_error_delete" class="avk_error"></div>';
        $str .= '<p class="mesdelavk">'. sprintf( __('Это действие приведет к удалению файла на сервере! %s Вы уверены, что хотите сделать это?',self::SLUG), '<br />') . '</p>';
        $str .= '</dl>';
        $str .= '<p class="submit submitavk">';
        $str .= '<input type="submit" class="button-primary button-primary clouse_pop_avk" value="&nbsp;&nbsp;&nbsp;'. __('Отмена',self::SLUG).'&nbsp;&nbsp;&nbsp;" />';
        $str .= '<input type="submit" id="delete_file_avk" class="button-primary button-primary-del" value="&nbsp;&nbsp;&nbsp;'. __('Удалить',self::SLUG).'&nbsp;&nbsp;&nbsp;" />';
        $str .= '</p>';
        $str .= '</div>';
        return $str;
    }
    
    /** Форма регистрации посетителя */
    public function form_register_visitor($messag, $type){
        $i = false;
        $str  = '<form id="formregavk" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
        $str .= '<div class="avkmiddle"><div class="avkcontentform"><dl class="dlavk">';
        if(function_exists('wp_nonce_field'))
            $str .= wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce", true, false);
        $str .= '<input type="hidden" name="actionvisitoravk" value="'.$type.'"/>';
        $str .= (isset($messag['obj']))?'<div class="errmsgrec errmsgrecavk"><p>'.$messag['obj'].'</p></div>':'';
        $str .= ($messag['login'] == 'error')?'<div class="errmsgrec"><p>'.__('Введите Ваш логин!',self::SLUG).'</p></div>':'';
        $str .= '<dt class="dttitleavk"><div class="divlabel"><label for="userloginameavk">'.__('Логин',self::SLUG).': </label></div>';
        $str .= '<input type="text" id="userloginameavk" autocomplete="off" class="inputregavk titleiformer" name="userloginameavk" value="'.$_POST['userloginameavk'].'" tabindex="1"/></dt>';
        $str .= '<dd class="ddescavk">' . sprintf(__('Введите логин, которое будет использоваться для входа. Пример: %s Возможные символы',self::SLUG),'<b>Smiling_Hemp</b><br />') . '<br />A-Z, a-z, 0-9, _, –</dd>';
        $str .= ($messag['password'] == 'error')?'<div class="errmsgrec"><p>'.__('Не правильный пароль!',self::SLUG).'</p></div>':'';
        $str .= '<dt class="dttitleavk"><div class="divlabel"><label for="userpasswavk">'.__('Пароль',self::SLUG).': </label></div>';
        $str .= '<input type="password" id="userpasswavk" autocomplete="off" class="inputregavk titleiformer" name="userpasswavk" value="'.$_POST['userpasswavk'].'" tabindex="2" /></dt>';
        $str .= '<dd class="ddescavk">' . sprintf(__('Пароль должен обязательно содержать как минимум одну заглавную букву, одну строчную букву и цифру. Пример: %s',self::SLUG),'<b>Abcd123</b>') . '</dd>';
        $tabindex = 3;
        if( $type == 'visitorreg' ){
            $i = true;
            $str .= ($messag['nickname'] == 'error')?'<div class="errmsgrec"><p>'.__('Не правильный Nickname',self::SLUG).'</p></div>':'';
            $str .= '<dt class="dttitleavk"><div class="divlabel"><label for="usermailavk">'.__('Nickname',self::SLUG).': </label></div>';
            $str .= '<input type="text" id="usernicknameavk" class="inputregavk titleiformer" name="usernicknameavk" value="'.$_POST['usernicknameavk'].'" tabindex="3"/></dt>';
            $str .= '<dd class="ddescavk">' . sprintf(__('Введите желаемое имя, которое будет отображаться на сайте. Пример: %s Возможные символы',self::SLUG),'<b>Smiling_Hemp</b><br />') . '<br />A-Z, a-z, 0-9, _, –</dd>';            
            $str .= ($messag['email'] == 'error')?'<div class="errmsgrec"><p>'.__('Не правильный E-mail адрес!',self::SLUG).'</p></div>':'';
            $str .= '<dt class="dttitleavk"><div class="divlabel"><label for="usermailavk">'.__('E-mail',self::SLUG).': </label></div>';
            $str .= '<input type="text" id="usermailavk" class="inputregavk titleiformer" name="usermailavk" value="'.$_POST['usermailavk'].'" tabindex="3"/></dt>';
            $str .= '<dd class="ddescavk">'.sprintf(__('Введите E-mail. Пример: %s',self::SLUG),'<b>ivan@mail.ru</b>').'</dd>';
            $str .= ($messag['firstname'] == 'error')?'<div class="errmsgrec"><p>'.__('Вы не ввели имя!',self::SLUG).'</p></div>':'';
            $str .= '<dt class="dttitleavk"><div class="divlabel"><label for="userfirstnameavk">'.__('Имя',self::SLUG).': </label></div>';
            $str .= '<input type="text" id="userfirstnameavk" class="inputregavk titleiformer" name="userfirstnameavk" value="'.$_POST['userfirstnameavk'].'" tabindex="4"/></dt>';
            $str .= '<dd class="ddescavk">'.sprintf(__('Введите своё имя. Пример: %s',self::SLUG),'<b>Иван</b>').'</dd>';
            $str .= ($messag['lastname'] == 'error')?'<div class="errmsgrec"><p>'.__('Вы не ввели фамилию!',self::SLUG).'</p></div>':'';
            $str .= '<dt class="dttitleavk"><div class="divlabel"><label for="userlastnameavk">'.__('Фамилия',self::SLUG).': </label></div>';
            $str .= '<input type="text" id="userlastnameavk" class="inputregavk titleiformer" name="userlastnameavk" value="'.$_POST['userlastnameavk'].'" tabindex="5"/></dt>';
            $str .= '<dd class="ddescavk">'.sprintf(__('Введите свою фамилию. Пример: %s',self::SLUG),'<b>Василовский</b>').'</dd>';
            
            $str .= '<dt class="dttitleavk"><div class="divlabel"><label for="usernewsavk">'.__('Быть в курсе',self::SLUG).': </label></div>';
            $str .= '<input type="checkbox" id="usernewsavk" name="usernewsavk" checked="checked" class="checkregavk titleiformer" tabindex="5"/>';
            $str .= '<dd class="ddescavk">'.__('Оставьте галочку в чекбоксе если хотите подписаться на рассылку новостей',self::SLUG).'</dd>';
            $tabindex = 6;
            if($this->mainSettings['chekregform'] == 'on')
                $str  .= '<script type="text/javascript" src="'.AVKSHOP_PL_URL.'/js/check-form.js"></script>';
            if($this->mainSettings['displayhelp'] == 'on')
                $str  .= '<script type="text/javascript" src="'.AVKSHOP_PL_URL.'/js/disphelp.js"></script>';
        }
        
        $str .= ($messag['recapcha'] == 'error')?'<div class="errmsgrec"><p>'.__('reCaptcha была введена не верно!',self::SLUG).'</p></div>':'';
        $str .= $this->get_recaptcha($tabindex);
        if($i){
            $str .= '<p class="botsubavk"><button tabindex="'. ++$tabindex .'">'.__('Регистрироваться',self::SLUG).'</button></p>';
        }else{
            $msg = '';
            if( $this->mainSettings['restore_password'] == 'on' ){
                $msgt = __('Забыли пароль?', self::SLUG);
                $msg = (isset($messag['obj']))?'':'<a class="remeb" href="'.wp_lostpassword_url(get_bloginfo('url')).'" title="'.$msgt.'">'.$msgt.'</a>';
            }else{
                $msgt = __('Регистрироваться', self::SLUG);
                $msg = '<a class="avkreglink" href="'.get_permalink($this->mainSettings['start_reg']).'" title="' . $msgt . '">' . $msgt . '</a>';
            }
            $str .= '<p class="botwidavk">'.$this->get_buttons('login',false).$msg.'</p>';
        }
        $str .= '</dl></div></div>';
        $str .= '</form>';
        return $str;
    }
    
    public function user_login_form($echo=false){
        if(is_user_logged_in()){
            $str = $this->user_basket(false);
        }else{
            $str = $this->get_reg_form(false);
        }
        if($echo)echo $str;
        else   return $str;
    }
    
    private function get_recaptcha($tabindex){
        if($this->mainSettings['captchaenable'] != 'on') return;
        if(!function_exists('recaptcha_get_html'))
            require_once(AVKSHOP_PL_PATH . '/lib/recaptchalib.php');
        if($this->mainSettings['themsrecaptcha'] == 'custom'){
            $strCaptcha  = '<script type="text/javascript"> var RecaptchaOptions = { theme : "custom", custom_theme_widget: "recaptcha_widget_avk", tabindex: '.$tabindex.' };</script>';
            $strCaptcha .= '<div id="recaptcha_widget_avk" style="display:none">';
            $strCaptcha .= '<div id="recaptcha_image"></div>';
            $strCaptcha .= '<div class="recaptcha_only_if_incorrect_sol" style="color:red">Не правильно пожалуйста попробывать снова</div>';
            $strCaptcha .= '<span class="recaptcha_only_if_image">Введите слова с картинки:</span>';
            $strCaptcha .= '<span class="recaptcha_only_if_audio">Введите цифры, которые слышите:</span>';
            $strCaptcha .= '<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />';
            $strCaptcha .= '<div class="reload_only_captcha"><a href="javascript:Recaptcha.reload()">Обновить CAPTCHA</a></div>';
            $strCaptcha .= '<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type(\'audio\')">Режим аудио CAPTCHA</a></div>';
            $strCaptcha .= '<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type(\'image\')">Визуальный режим CAPTCHA</a></div>';
            $strCaptcha .= '<div class="recaptcha_helper"><a href="javascript:Recaptcha.showhelp()">Помощь</a></div>';
            $strCaptcha .= '</div><script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k='.$this->mainSettings['captchapublickey'].'">';
            $strCaptcha .= '</script>';
            $strCaptcha .= '<noscript>';
            $strCaptcha .= '<iframe src="http://www.google.com/recaptcha/api/noscript?k='.$this->mainSettings['captchapublickey'].'" height="300" width="500" frameborder="0"></iframe><br />';
            $strCaptcha .= '<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>';
            $strCaptcha .= '<input type="hidden" name="recaptcha_response_field" value="manual_challenge" />';
            $strCaptcha .= '</noscript>';
        }else{
            $strCaptcha  = '<script type="text/javascript"> var RecaptchaOptions = { theme : "'.$this->mainSettings['themsrecaptcha'].'", tabindex : "6",  custom_translations : { instructions_visual : "'.$this->mainSettings['textinputrecaptcha'].'" }};</script>';
            $strCaptcha .= '<div class="standartrecaptcha">'.recaptcha_get_html($this->mainSettings['captchapublickey']).'</div>';
        }
        return $strCaptcha;
    }
    
    /** Виджет корзины */
    private function user_basket($echo = true){
        if(function_exists('wp_nonce_field'))
            $tempStr = wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce", true, false);
        $str  = '<div id="wid-'.self::SLUG.'" class="'.self::SLUG.'-cart'.$class.'">';
        $str .= '<ul id="widget_cart_html" class="widget_cart_html">';
        $str .= $this->user_basket_content($this->USER);
        $str .= '</ul>';
        $str .= '<form method="post" action="">';
        $str .= $tempStr;
        $str .= '<input type="hidden" name="actionvisitoravk" value="logautuseravk" />';
        $str .= '<p class="botwidavk">'.$this->get_buttons('logout', false).'</p>';
        $str .= '</form>';
        $str .= '</div>';
        
        if($echo) echo $str;
        else    return $str;
    }
    
    public function user_basket_content($user){
        $cartPermalink = $cartPrice = $li = '';
        $class = (empty($user->cart))? ' '.self::SLUG.'-cart-empty' : ' '.self::SLUG.'-cart-full';
        $count = count($user->cart);
        if(!empty($user->purchasedGoods)){
            $li = $this->LIB->html( 'li', array('class' => 'paid_goods_title'), '<b>' . __('Оплаченное', self::SLUG) . ':</b>' );
            foreach($user->purchasedGoods as $idPost => $shopInfo){
                $value = array_shift(get_post_meta($idPost, '_metaBoxValue'));
                global $wpdb;
                if(empty($value)) echo $wpdb->posts;
                $a = $this->LIB->html( 'a', array('href' => get_permalink($idPost), 'title' => $value['desc_product_avk']), $value['name_product_avk'] );
                $li .= $this->LIB->html( 'li', array('class' => 'paid_goods_li'), '&nbsp;' . $a);
            }
            $li .= $this->LIB->html( 'li', array('class' => 'paid_goods_br'), '');
        }
        if($count > 0){
            if('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] != get_permalink($this->mainSettings['cart_page'])){
                $goodsCart = $this->LIB->html( 'li', array('class' => 'cart_goods_li'), sprintf( __('В корзине: %d&nbsp;шт.', self::SLUG), $count ) );
                $cartPrice = $this->LIB->html( 'li', array('class' => 'cart_goods_price'), __('На сумму', self::SLUG).' : '.$this->LIB->get_all_price($user->cart).'&nbsp;'.$this->mainSettings['currency']);
                $temp = $this->LIB->html( 'a', array( 'href' => get_permalink($this->mainSettings['cart_page']), 'rel' => 'nofollow'), __('Перейти в корзину', self::SLUG) );
                $temp = $this->LIB->html( 'noindex', $temp);
                $cartPermalink = $this->LIB->html( 'li', array('class' => 'cart_goods_link'), $temp );
            }
        }else{
            $goodsCart = $this->LIB->html( 'li', array('class' => 'in_cart_goods'), sprintf( __( 'В корзине: %d&nbsp;шт.', self::SLUG ), 0) );
        }
        $str = $li . $goodsCart . $cartPrice . $cartPermalink;
        $widgetCartHtml = apply_filters('html_view_cart_widget_avkshop', $str, $this->USER, get_permalink($this->mainSettings['cart_page']));
        return $widgetCartHtml;
    }
    
    public function get_reg_form($echo = true){
        $html = '';
        if(function_exists('wp_nonce_field'))
            $html .= wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce", true, false);
        $html .= $this->LIB->html( 'input', array('type' => "hidden", 'name' => "actionvisitoravk", 'value' => "authorizationvisitor") );
        $html .= $this->LIB->html( 'dt', $this->LIB->html( 'label', array('class' => "avtorezfor", 'for' => "userloginameavk1"), __('Логин',self::SLUG) . ':' ) );
        $html .= $this->LIB->html( 'dd', $this->LIB->html( 'input', array('autocomplete' => "off", 'type'=>"text", 'id'=>"userloginameavk1", 'name'=>"userloginameavk", 'value'=>"") ) );
        $html .= $this->LIB->html( 'dt', $this->LIB->html( 'label', array('class' => "avtorezfor", 'for' => "userpasswavk1"), __('Пароль',self::SLUG) . ':' ) );
        $html .= $this->LIB->html( 'dd', $this->LIB->html( 'input', array('autocomplete' => "off", 'type'=>"password", 'id'=>"userpasswavk1", 'name'=>"userpasswavk", 'value'=>"") ) );
        
        $html  = $this->LIB->html( 'dl', $html );
        $html .= '<p class="botwidavk">'.$this->get_buttons('login',false);
        $html .= '<a class="avkreglink" href="'.get_permalink($this->mainSettings['start_reg']).'">'.__('Регистрироваться',self::SLUG).'</a></p>';
        
        $form = $this->LIB->html( 'form', array('method' => 'post', 'action' => '/'), $html );
        
        if($echo)echo $form;
        else   return $form;
    }
    
    public function get_buttons($type, $echo = true, $idPost = NULL, $text=''){
        $tempText = $text;
        $ID = (isset($_POST['avk_array_ajax']['get_button_pay'])) ? $this->LIB->clear_data_avk($_POST['avk_array_ajax']['get_button_pay'], 'int') : $this->LIB->get_post_info();
        $i = self::$count++;
        $value;
        switch($type){
            case"login" : $id = 'loginavkshop20';
                          $class = ' avk_buttons_'.$this->mainSettings['typebutton'];
                          $text = __('Войти',self::SLUG);
                          switch($this->mainSettings['typebutton']){
                              case'css':      $img = $src = ''; break;
                              case'customer': $style = 'background: url(\''.AVKSHOP_PL_URL.'/images/button/'.$type.'_'.$this->mainSettings['typebutton'].'.png\') 50% 50% no-repeat;';
                                              $text = '';
                                                  break;
                              default:        $img = $this->LIB->html('img', array('src' => AVKSHOP_PL_URL . '/images/button/login.png', 'class' => 'button-' . $type)); break;
                          }
                              break;
            case"logout": $id = 'logoutavkshop20';
                          $class = ' avk_buttons_'.$this->mainSettings['typebutton'];
                          $text = sprintf(__('Выйти %s',self::SLUG), '[' . $this->USER->name . ']');
                          switch($this->mainSettings['typebutton']){
                              case'css':      $img = $src = ''; break;
                              case'customer': $style = 'background: url(\''.AVKSHOP_PL_URL.'/images/button/'.$type.'_'.$this->mainSettings['typebutton'].'.png\') 50% 50% no-repeat;';
                                              $text = '';
                                                  break;
                              default:        $img = $this->LIB->html('img', array('src' => AVKSHOP_PL_URL . '/images/button/logout.png', 'class' => 'button-' . $type)); break;
                          }
                              break;
            case"basket": $id = 'basketavkshop20';
                          $class = ' avk_buttons_'.$this->mainSettings['typebutton'];
                          $text = __('Перейти в корзину',self::SLUG);
                          switch($this->mainSettings['typebutton']){
                              case'css':      $img = $src = ''; break;
                              case'customer': $style = 'background: url(\''.AVKSHOP_PL_URL . '/images/button/' . $type . '_' . $this->mainSettings['typebutton'] . '.png\') 50% 50% no-repeat;';
                                              $text = '';
                                                  break;
                              default:        $basket = (!empty($this->USER->cart))?'full':'empty';
                                              $img = $this->LIB->html('img', array( 'src' => AVKSHOP_PL_URL . '/images/button/basket_' . $basket . '.png', 'class' => 'button-' . $type ) ); break;
                          }
                              break;
            case"paid"  : $id = 'action-shop-avk-'.$ID;
                          $class = ' avk_buttons_'.$this->mainSettings['typebutton']. ' avk_buttons_paid';
                          $text = __('В корзину',self::SLUG);
                          switch($this->mainSettings['typebutton']){
                              case'css':      $img = $src = ''; break;
                              case'customer': $style = 'background: url(\''.AVKSHOP_PL_URL.'/images/button/'.$type.'_'.$this->mainSettings['typebutton'].'.png\') 50% 50% no-repeat;';
                                              $text = '';
                                                  break;
                              default:        $img = ($tempText == '')? $this->LIB->html('img', array( 'src' => AVKSHOP_PL_URL . '/images/button/cart_' . $this->mainSettings['typebutton'] . '.png', 'class' => 'button-' . $type ) ):''; break;
                          }
                              break;
            case"buy"   : $id = 'action-shop-avk-buy';
                          $class = ' avk_buttons_'.$this->mainSettings['typebutton'];
                          $text = __('Оплатить',self::SLUG);
                          switch($this->mainSettings['typebutton']){
                              case'css':      $img = $src = ''; break;
                              case'customer': $style = 'background: url(\''.AVKSHOP_PL_URL.'/images/button/'.$type.'_'.$this->mainSettings['typebutton'].'.png\') 50% 50% no-repeat;';
                                              $text = '';
                                                  break;
                              default:        $img = $this->LIB->html('img', array( 'src' => AVKSHOP_PL_URL . '/images/button/bay.png', 'class' => 'button-' . $type ) ); break;
                          }
                              break;
            case"free"  : $id = 'action-shop-avk-'.$ID;                
                          $class = ' avk_buttons_'.$this->mainSettings['typebutton'].' avk_buttons_free';
                          $prefex = '';
                          $text  = __('Скачать',self::SLUG);
                          if($idPost != NULL){
                              if(array_key_exists($idPost, $this->USER->purchasedGoods)){
                                  $prefex  = '&nbsp;<span class="counter">'.$this->USER->purchasedGoods[$idPost]['counter'].'</span>&nbsp;&frasl;&nbsp;<span class="amount">'.$this->USER->purchasedGoods[$idPost]['amount'].'</span>';
                                  $text  = __('Скачано',self::SLUG) . $prefex;
                                  $class .= ' avk_buttons_process';
                              }
                          }
                          switch($this->mainSettings['typebutton']){
                              case'css':      $img = $src = ''; break;
                              case'customer': $style = 'background: url(\''.AVKSHOP_PL_URL.'/images/button/download_'.$this->mainSettings['typebutton'].'.png\') 50% 50% no-repeat;';
                                              $text = '';
                                                  break;
                              default:        $img = ($tempText == '')? $this->LIB->html('img', array( 'src' => AVKSHOP_PL_URL . '/images/button/download_' . $this->mainSettings['typebutton'] . '.png', 'class' => 'button-' . $type ) ):''; break;
                          }
                              break;
            case"further": $id = 'action-shop-avk-further';                
                           $class = ' avk_buttons_'.$this->mainSettings['typebutton'];
                           $text  = __('Продолжить',self::SLUG);
                           switch($this->mainSettings['typebutton']){
                               case'css':      $img = $src = ''; break;
                               case'customer': $style = 'background: url(\''.AVKSHOP_PL_URL.'/images/button/further_'.$this->mainSettings['typebutton'].'.png\') 50% 50% no-repeat;';
                                               $text = '';
                                                   break;
                               default:        $img = $this->LIB->html('img' , array( 'src' => AVKSHOP_PL_URL . '/images/button/further.png', 'class' => 'button-' . $type ) ); break;
                           }
                               break;
            case"text":    $id = 'avkshop-button-'.$i;
                           $class = ' avk_buttons_text avk_buttons_'.$this->mainSettings['typebutton'] . ' ' . $idPost;
        }
        
        $span = $this->LIB->html( 'span', array( 'class' => 'textavk' ), $text );
        
        $array = (isset($style) && !empty($style))? array( 'id' => $id, 'class' => 'avk_buttons' . $class, 'style' => $style ) : array( 'id' => $id, 'class' => 'avk_buttons' . $class );
        
        $button = $this->LIB->html( 'button', $array, $span . $img );
        
        if($echo)echo $button;
        else   return $button;
    }
    
    public function get_html_meta_post($id, $content){
        $value = array_shift(get_post_meta($id, '_metaBoxValue'));
        $counter = ($value['enabel_counter_product_avk'] == 'on')?$value['counter_product_avk']:'';
        
        if($value['type_product_avk'] == 'paid' || $value['type_product_avk'] == 'free')
            $form = $this->get_form_meta_post($id, $value);
        
        $form = apply_filters('get_html_meta_post_avkshop', $form, $id, $value);
        
        $search = array('<!--%buttonavk%-->', '<!--%coudownloadavk%-->');
        $replace = array($form, $counter);
        
        $content = apply_filters('avkshop_content', $content);
        
        return str_replace($search, $replace, $content);
    }
    
    /** Корзина покупателя */
    public function get_cart_user($content){
        if($this->USER->id == 0) return $content;
        if(!is_array($this->USER->cart) || empty($this->USER->cart)) return;
        $str  = '<div id="all_product_cart">'.$this->cart_user($this->USER->cart).'</div>';
        $str  = apply_filters('avkshop_cart_user', $str);
        $str .= '<fieldset class="fieldset-systems-pay-avk"><legend class="legend-systems-pay-avk">'.__('Способ оплаты:', self::SLUG).'</legend>';
        $str .= $this->get_systems_pay($this->LIB->systemPay);
        $str .= '</fieldset>';
        return $str;
    }
    
    public function cart_user($shoppingCart){
        $i=0;
        if(!is_array($shoppingCart)) return;
        foreach($shoppingCart as $idCart => $idPost){
            $value = array_shift(get_post_meta($idPost, '_metaBoxValue'));
            if($value['enabel_product_avk'] == 'on'){
                if(function_exists('wp_nonce_field'))
                    $nonce = wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce", true, false);
                $str .= '<form class="del-form-avk" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
                $str .= '<ul class="cart-list-avk">';
                $str .= '<li class="cart-list-number-avk">№ '.++$i.'</li>';
                $str .= '<li class="cart-list-name-avk"><a href="'.get_permalink($idPost).'" title="'.$value['desc_product_avk'].'">'.$value['name_product_avk'].'</a></li>';
                $str .= '<li>' . $nonce . '<input type="hidden" name="action_del_cart_product" value="'.$idCart.'"/></li>';
                $str .= '<li class="cart-list-price-avk"><span>'.$value['price_product_avk'].'&nbsp;'.$this->mainSettings['currency'].'</span></li>';
                $str .= '<li class="cart-list-buttom-avk">
                             <button class="delbuttonavk avk_buttons"><img src="'.AVKSHOP_PL_URL.'/images/delete.png" title="'.__('Удалить из корзины', self::SLUG).'" /></button>
                         </li>';
                $str .= '</ul>';
                $str .= '</form>';
            }
        }
        $str .= '<div id="all_price_avk">'.__('Итого', self::SLUG).':&nbsp;<span>'.$this->LIB->get_all_price($shoppingCart).'</span>&nbsp;'.$this->mainSettings['currency'].'</div>';
        return $str;
    }
    
    /** Вывод информации о покупке */
    public function sales_receipt($arrayCart){
        if(!is_array($arrayCart)) return;
        reset($arrayCart);
        $counter = 1;
        $price = 0;
        $str = '<dl id="sales_receipt_avkshop">';
        foreach($arrayCart as $id){
            $value = array_shift(get_post_meta($id, '_metaBoxValue'));
            if($value['enabel_product_avk'] == 'on'){
                $price = $price + $value['price_product_avk'];
            }
            $str .= '<dt>№' . $counter . '. <span>' . $value['name_product_avk'].'</span></dt>';
            $str .= '<dd><var>(' . $value['desc_product_avk'] . ')</var></dd>';
            $counter++;
        }
        $str .= '</dl>';
        $str .= '<dl>';
        $str .= '<dt>'.__('Итого', self::SLUG).'</dt>';
        $str .= '<dd>'.$price.'&nbsp;'.$this->mainSettings['currency'].'</dd>';
        $str .= '<dl>';
        reset($arrayCart);
        $str = apply_filters('sales_receipt_avkshop', $str, $arrayCart);
        return $str;
    }
    
    protected function get_systems_pay($systemPay){
        if(function_exists('wp_nonce_field'))
            $nonce = wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce", true, false);
        $str  = '<div id="systems-pay-avkshop" class="systems-pay-avkshop">';
        $str .= '<form id="form-systems-pay-avkshop" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
        $str .= $nonce;
        $str .= '<ul class="list-systems-pay-avk">';
        if(is_array($systemPay)){
            array_shift($systemPay);
            foreach($systemPay as $system => $value){
                if(($this->systemPaySettings[$system]['avk_sys_enabel'] == 'on') || ($this->USER->role == 'administrator' and ($this->systemPaySettings[$system]['avk_sys_enabel'] == 'test'))){
                    $str .= '<li>';
                    $str .= '<label for="'.$system.'_avkshop">';
                    $str .= '<input id="'.$system.'_avkshop" type="radio" name="systems_pay_avkshop" value="'.$system.'" />';
                    $str .= '<span style="background: url('.$value['imgUrl'].') no-repeat;"></span>';
                    $str .= '</label>';
                    $str .= '</li>';
                }
            }
        }
        $str .= '</ul>';
        $str .= '<p class="container-button-avkshop">'.$this->get_buttons('further', false).'</p>';
        $str .= '</form>';
        $str .= '</div>';
        return $str;
    }
    
    public function get_form_meta_post($id, $value, $img=''){
        if(empty($value) || $value['enabel_product_avk'] == 'off') return;
        if($this->USER->id !== 0){
            $i = self::$count++;
            $str = '';
            if($value['type_product_avk'] == 'free'){//array_key_exists($id, $this->USER->purchasedGoods)
                $typeProduct = 'free';
                $tempFree = NULL;
            }
            if($value['type_product_avk'] == 'paid'){
                if(array_key_exists($id, $this->USER->purchasedGoods)){
                    $typeProduct = 'free';
                }else{
                    $typeProduct = 'paid';
                }
                $tempFree = $id;
            }
            $str .= '<form method="post" name="forma" class="avk-product avk-product-'.$id.' avk-product-'.$value['type_product_avk'].'" action="'.$_SERVER['REQUEST_URI'].'">';
            if(function_exists('wp_nonce_field'))
                $str .= wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce", true, false);
            $str .= '<input type="hidden" name="avkshop_download_in_cart" value="'.$id.'" />';
            $str .= $this->get_buttons($typeProduct, false, $tempFree, $img);
            $str .= '</form>';
        }else{
            switch($value['type_product_avk']){
                case'paid': $text = __('Чтобы купить авторизуйтесь',self::SLUG); break;
                case'free': $text = __('Чтобы cкачать авторизуйтесь',self::SLUG); break;
            }
            $attr = array( 'class' => 'avkreglink-post avk-typebutton avk-typebutton-'.$this->mainSettings['typebutton'], 'href' => get_permalink($this->mainSettings['authorization']) );
            $str = $this->LIB->html('a', $attr, $text);
        }
        return $str;
    }
    
    protected function set_button($textButton, $action, $class = ''){
        $class = (empty($class))? 'button-primary' : $class;
        
        $html  = $this->LIB->html( 'input', array('type' => "hidden", 'name' => $action, 'value' => "yes") );
        $html .= $this->LIB->html( 'input', array('type' => "submit", 'class' => (empty($class))? 'button-primary' : $class, 'value' => $textButton) );
        
        $p = $this->LIB->html( 'p', array('class' => 'submit', 'style' => 'text-align: center;'), $html );
        
        return $p;
    }
    
    public function page_interkassa($tabs){
        $text1 = __( 'Итог' , self::SLUG );
        $url = get_permalink( $this->mainSettings['page_status_pay'] );
        $pref = $this->LIB->chars_reference( $url );

        $str  = '<label for="ik_enabel_avk">'.sprintf(__('Включить %s Выключить',self::SLUG),'/').'</label>';
        $str .= '<select id="ik_enabel_avk" name="avk_sys_enabel_avk">';
        $str .= '<option value="on" '.selected( $this->systemPaySettings['interkassa']['avk_sys_enabel'], 'on', false ).'>'.__('Включить', self::SLUG).'</option>';
        $str .= '<option value="off" '.selected( $this->systemPaySettings['interkassa']['avk_sys_enabel'], 'off', false ).'>'.__('Выключить', self::SLUG).'</option>';
        $str .= '<option value="test" '.selected( $this->systemPaySettings['interkassa']['avk_sys_enabel'], 'test', false ).'>'.__('Тестовый режим', self::SLUG).'</option>';
        $str .= '</select>';
        $str .= '<br />';
        $str .= '<label for="ik_shop_id_avk">'.__('ID Магазина',self::SLUG).'</label><input id="ik_shop_id_avk" type="text" name="ik_shop_id_avk" value="'.$this->systemPaySettings['interkassa']['ik_shop_id'].'"/><br />';
        $str .= '<label for="ik_secret_key_avk">'.__('Секретный ключ',self::SLUG).'</label><input id="ik_secret_key_avk" type="password" name="ik_secret_key_avk" value="'.$this->systemPaySettings['interkassa']['ik_secret_key'].'"/>';
        $str .= '<label for="ik_test_key_avk">'.__('Тестовый ключ ',self::SLUG).'</label><input id="ik_test_key_avk" type="password" name="ik_test_key_avk" value="'.$this->systemPaySettings['interkassa']['ik_test_key'].'"/>';
        
        $site = ( !empty( $this->mainSettings['page_status_pay'] ) ) ? $url : __('Вы не выбрали страницу «Статус платежа»', self::SLUG);
        $nameVar = ( !empty( $this->systemPaySettings['interkassa']['ik_name_val'])) ? $site . $pref . $this->systemPaySettings['interkassa']['ik_name_val'].'=': $site . $pref . 'xxxxx=';
        $str .= '<label for="ik_name_val_avk">' . __('Название переменной',self::SLUG) .' </label><input id="ik_name_val_avk" type="text" name="ik_name_val_avk" value="'.$this->systemPaySettings['interkassa']['ik_name_val'].'"/><br />';
        
        $valUrl = ( !empty( $this->systemPaySettings['interkassa']['ik_stus_sus'])) ? $nameVar . $this->systemPaySettings['interkassa']['ik_stus_sus'] : $nameVar . 'xxxx';
        $str .= '<label for="ik_stus_sus_avk">Url ' . __('успешной оплаты',self::SLUG) . '</label><input id="ik_stus_sus_avk" type="text" name="ik_stus_sus_avk" value="'.$this->systemPaySettings['interkassa']['ik_stus_sus'].'"/><br />';
        $str .= '<span class="stat-url-robo"><b>' . $text1.'</b>: <input class="readonlyavk" type="text" readonly="readonly" value="'.$valUrl.'" /></span><br />';
        $str .= '<label for="ik_text_sus_avk">' . __('Сообщение при совершённой оплаты товара',self::SLUG) . '</label><textarea id="ik_text_sus_avk" rows="5" name="ik_text_sus_avk">'.$this->systemPaySettings['interkassa']['ik_text_sus'].'</textarea><br />';
        
        $valUrl = ( !empty( $this->systemPaySettings['interkassa']['ik_stus_fai'])) ? $nameVar . $this->systemPaySettings['interkassa']['ik_stus_fai'] : $nameVar . 'xxxx';
        $str .= '<label for="ik_stus_fai_avk">Url '.__('при отказе оплаты',self::SLUG).'</label><input id="ik_stus_fai_avk" type="text" name="ik_stus_fai_avk" value="'.$this->systemPaySettings['interkassa']['ik_stus_fai'].'"/><br />';
        $str .= '<span class="stat-url-robo"><b>'.$text1.'</b>: <input class="readonlyavk" type="text" readonly="readonly" value="'.$valUrl.'" /></span><br />';
        $str .= '<label for="ik_text_fai_avk">'.__('Сообщение при отказе оплаты товара',self::SLUG).'</label><textarea id="ik_text_fai_avk" rows="5" name="ik_text_fai_avk">'.$this->systemPaySettings['interkassa']['ik_text_fai'].'</textarea><br />';
        
        $valUrl = ( !empty( $this->systemPaySettings['interkassa']['ik_stus_res'] ) ) ? $nameVar . $this->systemPaySettings['interkassa']['ik_stus_res'] : $nameVar . 'xxxx';
        $str .= '<label for="ik_stus_res_avk">Url '.__('ожидания проведения платежа',self::SLUG).'</label><input id="ik_stus_res_avk" type="text" name="ik_stus_res_avk" value="'.$this->systemPaySettings['interkassa']['ik_stus_res'].'"/><br />';
        $str .= '<span class="stat-url-robo"><b>'.$text1.'</b>: <input class="readonlyavk" type="text" readonly="readonly" value="'.$valUrl.'" /></span><br />';
        $str .= '<label for="ik_text_res_avk">'.__('Сообщение при ожидании проведения платежа',self::SLUG).'</label><textarea id="ik_text_res_avk" rows="5" name="ik_text_res_avk">'.$this->systemPaySettings['interkassa']['ik_text_res'].'</textarea><br />';
        
        $str .= $this->set_button(__('Сохранить', self::SLUG), 'actionsaveavk');
        return $str;
    }
    
    public function page_robokassa($tabs){
        $text = __('Значение',self::SLUG);
        $text1 = __('Итог',self::SLUG);
        $text2 = __('Пароль',self::SLUG);
        $pref = get_option('permalink_structure');
        $pref = (!empty($pref))?'?':'&';
        $str  = '<label for="rb_enabel_avk">'.sprintf(__('Включить %s Выключить',self::SLUG),'/').'</label>';
        $str .= '<select id="rb_enabel_avk" name="avk_sys_enabel_avk">';
        $str .= '<option value="on" '.selected( $this->systemPaySettings['robokassa']['avk_sys_enabel'], 'on', false ).'>'.__('Включить', self::SLUG).'</option>';
        $str .= '<option value="off" '.selected( $this->systemPaySettings['robokassa']['avk_sys_enabel'], 'off', false ).'>'.__('Выключить', self::SLUG).'</option>';
        $str .= '<option value="test" '.selected( $this->systemPaySettings['robokassa']['avk_sys_enabel'], 'test', false ).'>'.__('Тестовый режим', self::SLUG).'</option>';
        $str .= '</select>';
        $str .= '<br />';
        $str .= '<label for="rb_shop_id_avk">'.__('Идентификатор магазина',self::SLUG).'</label><input id="rb_shop_id_avk" type="text" name="rb_shop_id_avk" value="'.$this->systemPaySettings['robokassa']['rb_shop_id'].'"/><br />';
        $str .= '<label for="rb_secret_key_1_avk">'.$text2.' №1</label><input id="rb_secret_key_1_avk" type="password" name="rb_secret_key_1_avk" value="'.$this->systemPaySettings['robokassa']['rb_secret_key_1'].'"/><br />';
        $str .= '<label for="rb_secret_key_2_avk">'.$text2.' №2</label><input id="rb_secret_key_2_avk" type="password" name="rb_secret_key_2_avk" value="'.$this->systemPaySettings['robokassa']['rb_secret_key_2'].'"/><br />';
        
        $site = (!empty($this->mainSettings['page_status_pay']))?get_permalink($this->mainSettings['page_status_pay']):'<span style="color:red;">'.__('Вы не выбрали страницу «Статус платежа»').'</span>';
        $nameVar = (!empty($this->systemPaySettings['robokassa']['rb_name_val']))?$site.$pref.$this->systemPaySettings['robokassa']['rb_name_val'].'=':$site.$pref.'xxxxx=';
        $str .= '<label for="rb_name_val_avk">'.__('Название переменной',self::SLUG).'</label><input id="rb_name_val_avk" type="text" name="rb_name_val_avk" value="'.$this->systemPaySettings['robokassa']['rb_name_val'].'"/><br />';
        
        $valUrl = (!empty($this->systemPaySettings['robokassa']['rb_stus_res']))?$nameVar.$this->systemPaySettings['robokassa']['rb_stus_res']:$nameVar.'xxxx';
        $str .= '<label for="rb_stus_res_avk">'.$text.' Result Url</label><input id="rb_stus_res_avk" type="text" name="rb_stus_res_avk" value="'.$this->systemPaySettings['robokassa']['rb_stus_res'].'"/><br />';
        $str .= '<span class="stat-url-robo"><b>'.$text1.'</b>: <input class="readonlyavk" type="text" readonly="readonly" value="'.$valUrl.'" /></span><br />';
        
        $valUrl = (!empty($this->systemPaySettings['robokassa']['rb_stus_sus']))?$nameVar.$this->systemPaySettings['robokassa']['rb_stus_sus']:$nameVar.'xxxx';
        $str .= '<label for="rb_stus_sus_avk">'.$text.' Success Url</label><input id="rb_stus_sus_avk" type="text" name="rb_stus_sus_avk" value="'.$this->systemPaySettings['robokassa']['rb_stus_sus'].'"/><br />';
        $str .= '<span class="stat-url-robo"><b>'.$text1.'</b>: <input class="readonlyavk" type="text" readonly="readonly" value="'.$valUrl.'" /></span><br />';
        $str .= '<label for="rb_text_sus_avk">'.__('Сообщение при совершённой оплаты товара',self::SLUG).'</label><textarea id="rb_text_sus_avk" rows="5" name="rb_text_sus_avk">'.$this->systemPaySettings['robokassa']['rb_text_sus'].'</textarea><br />';
        
        $valUrl = (!empty($this->systemPaySettings['robokassa']['rb_stus_fai']))?$nameVar.$this->systemPaySettings['robokassa']['rb_stus_fai']:$nameVar.'xxxx';
        $str .= '<label for="rb_stus_fai_avk">'.$text.' Fail Url</label><input id="rb_stus_fai_avk" type="text" name="rb_stus_fai_avk" value="'.$this->systemPaySettings['robokassa']['rb_stus_fai'].'"/><br />';
        $str .= '<span class="stat-url-robo"><b>'.$text1.'</b>: <input class="readonlyavk" type="text" readonly="readonly" value="'.$valUrl.'" /></span><br />';
        $str .= '<label for="rb_text_fai_avk">'.__('Сообщение при отказе оплаты товара',self::SLUG).'</label><textarea id="rb_text_fai_avk" rows="5" name="rb_text_fai_avk">'.$this->systemPaySettings['robokassa']['rb_text_fai'].'</textarea><br />';

        $str .= $this->set_button(__('Сохранить', self::SLUG), 'actionsaveavk');//rb_messag_sus
        return $str;
    }
    
    public function page_log(){
        $messag = '<p style="text-align:center;">' . __('Нет записей в журнале',self::SLUG) . '</p>';
        if(!file_exists(AVKSHOP_PATH_LOG)) return $messag;
        $this->logInfo['counter'] = 0;
        $this->logInfo['size'] = filesize(AVKSHOP_PATH_LOG);
        update_option(self::SLUG . '_check_logs', $this->logInfo);
        $logs = file(AVKSHOP_PATH_LOG);
        if(!is_array($logs)) return '<p style="text-align:center;">' . __('Не удалось зачитать файл',self::SLUG) . '</p>';
        if(empty($logs)) return $messag;
        $str  = '<table id="table-logs-avkshop">';
        $str .= '<tr><th><input id="listinglogavkshop" type="checkbox" /></th><th>E-mail</th><th>'.__('Сообщение',self::SLUG).'</th><th>'.__('Дата',self::SLUG).'</th></tr>';
        foreach($logs as $key => $string){
            list($email, $text, $time) = explode('|', $string);
            $str .= '<tr><td><input class="check_log_list" type="checkbox" name="listinglogavkshop['.$key.']" value="'.$email.'" /></td><td>'.$email.'</td><td>'.$text.'</td><td>'.date('d:m:Y H:i:s', $time).'</td></tr>';
        }
        $str .= '</table>';
        $str .= $this->set_button(__('Удалить', self::SLUG), 'dellogsavkshop', 'button-primary button-primary-del');
        return $str;
    }
    
    public function get_form_interkassa($user, $button){
        $str = $payId = $themId = $ikPayDesc = '';
        if(($this->systemPaySettings['interkassa']['avk_sys_enabel'] == 'on') || ($user->role == 'administrator' and ($this->systemPaySettings['interkassa']['avk_sys_enabel'] == 'test'))){//
            foreach($user->cart as $paysId => $themeId){
                $value = array_shift(get_post_meta($themeId, '_metaBoxValue'));
                if($value['enabel_product_avk'] == 'on'){
                    $payId .= $paysId . '_';
                    $themId .= $themeId . '_';
                    $ikPayDesc .=  $this->sanitize_title_translit_avk($value['name_product_avk']) . ', ';
                }
            }
            $ikPayId = rtrim($payId, '_') . 'and' . rtrim($themId, '_');
            $data = array('ik_co_id' => $this->systemPaySettings['interkassa']['ik_shop_id'],
                          'ik_pm_no' => $ikPayId,
                          'ik_am'    => $this->LIB->get_all_price($user->cart).'.00',
                          'ik_cur'   => $this->mainSettings['currency'],
                          'ik_desc'  => rtrim($ikPayDesc, ', '),
                          'ik_x_user'=> $user->email);
            ksort($data, SORT_STRING);
            $stringSing = implode(':', $data);
            $data['ik_x_sing'] = base64_encode(md5($stringSing . SECURE_AUTH_KEY, true));
            switch($this->systemPaySettings['interkassa']['avk_sys_enabel']){
                case"on": 
                          $addField = '';
                              break;
                case"test": 
                            $data['ik_pw_via'] = "test_interkassa_test_xts";
                            $addField = '<input type="hidden" name="ik_pw_via" value="test_interkassa_test_xts" />';
                                break;
            }           
            ksort($data, SORT_STRING);
            array_push($data, $this->systemPaySettings['interkassa']['ik_secret_key']);//secret
            $sing = base64_encode(md5(implode(':', $data), true));
            
            $str  = '<form name="payment" action="https://sci.interkassa.com/" accept-charset="UTF-8" method="post" target="_blank">';
            $str .= '<input type="hidden" name="ik_co_id" value="'.$data['ik_co_id'].'" />';
            $str .= '<input type="hidden" name="ik_pm_no" value="'.$data['ik_pm_no'].'" />';
            $str .= '<input type="hidden" name="ik_am" value="'.$data['ik_am'].'" />';
            $str .= '<input type="hidden" name="ik_cur" value="'.$data['ik_cur'].'" />';
            $str .= '<input type="hidden" name="ik_desc" value="'.$data['ik_desc'].'" />';
            $str .= '<input type="hidden" name="ik_x_user" value="'.$data['ik_x_user'].'" />';
            $str .= '<input type="hidden" name="ik_x_sing" value="'.$data['ik_x_sing'].'" />';
            $str .= $addField;
            $str .= '<input type="hidden" name="ik_sign" value="'.$sing.'" />';
            $str .= $button;
            $str .= '</form>';
        }
        return $str;
    }
    
    public function get_form_robokassa($user, $button){
        $str = $payId = $themId = $invDesc = '';
        if(($this->systemPaySettings['robokassa']['avk_sys_enabel'] == 'on') || ($user->role == 'administrator' and ($this->systemPaySettings['robokassa']['avk_sys_enabel'] == 'test'))){
            $mrhLogin = $this->systemPaySettings['robokassa']['rb_shop_id'];
            $mrhPass1 = $this->systemPaySettings['robokassa']['rb_secret_key_1'];
            $invId = $this->LIB->create_id($user->cart);
            foreach($user->cart as $idShop => $themeId){
                $value = array_shift(get_post_meta($themeId, '_metaBoxValue'));
                if($value['enabel_product_avk'] == 'on'){
                    $payId .= $idShop . '_';
                    $themId .= $themeId . '_';
                    $invDesc .= '&laquo;' . $value['name_product_avk'] . '&raquo;, '; // описание заказа
                }
            }
            $ShpCartId = rtrim($payId, '_') . 'and' . rtrim($themId, '_');
            $invDesc = rtrim($invDesc, ', ');
            $outSumm = $this->LIB->get_all_price($user->cart) . '.00'; 
            $shpItem = $user->email;
            $crc  = md5("$mrhLogin:$outSumm:$invId:$mrhPass1:Shp_cart_id=$ShpCartId:Shp_item=$shpItem"); // формирование подписи
            switch($this->systemPaySettings['robokassa']['avk_sys_enabel']){
                case'on': $action = 'https://merchant.roboxchange.com/Index.aspx'; break;
                case'test': $action = 'http://test.robokassa.ru/Index.aspx'; break;
            }
            $str  = '<form action="' . $action . '" method="POST">';
            $str .= '<input type="hidden" name="MrchLogin" value="' . $mrhLogin . '">';
            $str .= '<input type="hidden" name="OutSum" value="' . $outSumm . '">';
            $str .= '<input type="hidden" name="InvId" value="' . $invId . '">';
            $str .= '<input type="hidden" name="Desc" value="' . $invDesc . '">';
            $str .= '<input type="hidden" name="SignatureValue" value="' . $crc . '">';
            $str .= '<input type="hidden" name="Shp_item" value="' . $shpItem . '">';
            $str .= '<input type="hidden" name="Shp_cart_id" value="' . $ShpCartId . '">';
            $str .= '<input type="hidden" name="IncCurrLabel" value="">'; // предлагаемая валюта платежа
            $str .= '<input type="hidden" name="Culture" value="ru">'; // язык
            $str .= $button;
            $str .= '</form>';
        }
        return $str;
    }
    
    public function show_messag_pay_system($content){
        if(isset($_GET['robsuccess'])){
            if($_GET['robsuccess'] == 'payok'){
                $newContent  = $this->systemPaySettings['robokassa']['rb_text_sus'];
            }
            if($_GET['robsuccess'] == 'paybad'){
                $newContent  = $this->systemPaySettings['robokassa']['rb_text_fai'];
            }
            return $newContent;
        }
        if(isset($_GET[$this->systemPaySettings['interkassa']['ik_name_val']])){
            if($_GET[$this->systemPaySettings['interkassa']['ik_name_val']] == $this->systemPaySettings['interkassa']['ik_stus_sus']){
                $newContent = $this->systemPaySettings['interkassa']['ik_text_sus'];
            }
            if($_GET[$this->systemPaySettings['interkassa']['ik_name_val']] == $this->systemPaySettings['interkassa']['ik_stus_fai']){
                $newContent = $this->systemPaySettings['interkassa']['ik_text_fai'];
            }
            if($_GET[$this->systemPaySettings['interkassa']['ik_name_val']] == $this->systemPaySettings['interkassa']['ik_stus_res']){
                $newContent = $this->systemPaySettings['interkassa']['ik_text_res'];
            }
            return $newContent;
        }
        return $content;
    }
    
    public function popup_window($text, $type = 'war'){
        switch($type){
            case"war": $title = __('Внимание', self::SLUG); break;
            case"err": $title = __('Ошибка', self::SLUG); break;
        }
        $str  = '<div class="popup_window_mes_avkshop">';
        $str .= '</div>';
        $str .= '<div class="popup_message_avkshop">';
        $str .= '<h3>' . $title . '</h3>';
        $str .= '<div class="popup_message_content_avkshop"><p>' . $text . '</p></div>';
        $str .= '<p class="button-avkshop">' . $this->get_buttons('text', false, 'close', __('Закрыть', self::SLUG)) . '</p>';
        $str .= '</div>';
        return $str;
    }
}
?>