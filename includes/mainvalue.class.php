<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */

class MainValueAvkShop{
    public $name = "AVK-Shop";
    public $version = '1.1.0';
    public $systemPage;
    protected $actMainSettings, $mainSettings;
    protected $systemPaySettings;
    protected $logInfo;
    
    const SLUG = "avkshopweb20";
    const NAME_PLUGIN = "AVKSHOP";
    
    public function __construct(){
        $this->actMainSettings = $this->mainSettings = get_option(self::SLUG . '-settings');
        $this->systemPaySettings = get_option(self::SLUG.'-table');
        $this->logInfo = get_option(self::SLUG . '_check_logs');
        $this->systemPage = array('registration'              => $this->mainSettings['start_reg'],
                                  'intermediate_registration' => $this->mainSettings['intermediate_reg'],
                                  'authorization'             => $this->mainSettings['authorization'],
                                  'error_registration'        => $this->mainSettings['error_reg'],
                                  'cart'                      => $this->mainSettings['cart_page'],
                                  'status_pay'                => $this->mainSettings['page_status_pay']);
    }
        
    public function clear_data($data, $type="str"){
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
        
    /** Транслит */
    public function sanitize_title_translit_avk($title){
        $iso = array("Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye",
             "ѓ"=>"g","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
             "Е"=>"E","Ё"=>"YO","Ж"=>"ZH","З"=>"Z","И"=>"I","Й"=>"J",
             "К"=>"K","Л"=>"L","М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
             "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"X","Ц"=>"C","Ч"=>"CH",
             "Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'","Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU",
             "Я"=>"YA","а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e",
             "ё"=>"yo","ж"=>"zh","з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
             "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u",
             "ф"=>"f","х"=>"x","ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
             "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya","—"=>"-","«"=>"","»"=>"","…"=>""," "=>"-");
             
        return strtr($title, $iso);
    }
}
?>