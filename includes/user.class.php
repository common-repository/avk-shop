<?php
class User_AVKShop{
    public $id;
    public $login;
    public $email;
    public $regTime;
    public $name;
    public $role;
    public $lastVis;
    public $subsc;
    public $ip;
    public $firstName;
    public $lastName;
    public $cart;
    public $counterPurchases;
    public $counterDownloads;
    public $purchasedGoods;
    public $downloadTable;
    public $purchasesTable;
    private $mainSettings;
    
    public function __construct($id = ''){
        $this->mainSettings = get_option('avkshopweb20-settings');        
        if(!empty($id) && is_object($id)){
            $temp = $id;
        }elseif(!empty($id) && is_array($id)){
            $temp = get_user_by($id['field'], $id['value']);
        }else{
            $temp = wp_get_current_user();
        }

        if($temp->ID == 0){
            $this->id = 0;
            unset($this->login,
                  $this->email,
                  $this->regTime,
                  $this->name,
                  $this->role,
                  $this->lastVis,
                  $this->subsc,
                  $this->ip,
                  $this->firstName,
                  $this->lastName,
                  $this->cart,
                  $this->purchasedGoods,
                  $this->counterPurchases,
                  $this->counterDownloads);
            return;
        }
        $this->get_user_info($temp);
        
    }
    private function get_user_info($objUser){
        $this->id               = $objUser->ID;
        $this->login            = $objUser->data->user_login;
        $this->email            = $objUser->data->user_email;
        $this->regTime          = $this->get_time_unix($objUser->data->user_registered);
        $this->name             = $objUser->data->display_name;
        $this->role             = $objUser->roles[0];
        $this->lastVis          = array_shift( get_user_meta( $objUser->ID, 'user_last_visit' ) );
        $this->ip               = array_shift( get_user_meta( $objUser->ID, 'user_ip' ) );
        $this->firstName        = array_shift( get_user_meta( $objUser->ID, 'first_name' ) );
        $this->lastName         = array_shift( get_user_meta( $objUser->ID, 'last_name' ) );
        $this->cart             = $this->get_user_cart($objUser->ID);
        $this->purchasedGoods   = $this->get_purchased_goods($objUser->ID);
        
        $this->downloadTable    = $this->downloads_table($objUser->ID);
        $this->purchasesTable   = $this->purchases_table($objUser->ID);
        
        $this->counterDownloads = $this->counter($this->downloadTable, 'type_goods', 'free');
        $this->counterPurchases = $this->counter($this->purchasesTable, 'order_status', 'paid');
        if( 'on' == array_shift( get_user_meta( $objUser->ID, 'usernewsavk' ) ) )
            $this->subsc = true;
        else
            $this->subsc = false;
    }
    
    protected function counter($array, $nameVar, $type){
        if(empty($array)) return 0;
        $counter = 0;
        foreach($array as $obj){
            if($obj->$nameVar == $type){
                ++$counter;
            }
        }
        return $counter;
    }
    
    protected function downloads_table($userId){
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}{$this->mainSettings['tabledownload']} WHERE customer_id=%d";
        $count = $wpdb->get_results($wpdb->prepare($sql, $userId));
        return $count;
    }
    
    protected function purchases_table($userId){
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}{$this->mainSettings['tableshopping']} WHERE customer_id=%d";
        $count = $wpdb->get_results($wpdb->prepare($sql, $userId));
        return $count;
    }
    
    protected function get_user_cart($userId){
        global $wpdb;
        $sql = "SELECT shop.id, shop.id_post 
                    FROM {$wpdb->prefix}{$this->mainSettings['tableshopping']} shop
                    INNER JOIN {$wpdb->posts} post ON shop.id_post = post.ID 
                        WHERE shop.status_purchase='in_hand' 
                        AND shop.order_status='activ' 
                        AND shop.customer_id=%d
                        AND post.post_status='publish'";
        $result = $wpdb->get_results($wpdb->prepare($sql, $userId));
        $newArray = array();
        if(!empty($result)){
            foreach($result as $obj){
                $newArray[$obj->id] = $obj->id_post;
            }
        }
        return $newArray;
    }

    protected function get_purchased_goods($userId){
        global $wpdb;
        $sql = "SELECT shop.id, shop.id_post, shop.counter_downloads, shop.amount 
                    FROM {$wpdb->prefix}{$this->mainSettings['tableshopping']} shop 
                    INNER JOIN {$wpdb->posts} post ON shop.id_post = post.ID 
                        WHERE shop.status_purchase='in_hand' 
                            AND shop.order_status='paid' 
                            AND shop.customer_id=%d 
                            AND shop.counter_downloads < shop.amount
                            AND post.post_status='publish'";
        $result = $wpdb->get_results($wpdb->prepare($sql, $userId));
        $newArray = array();
        if(!empty($result)){
            foreach($result as $obj){
                $newArray[$obj->id_post] = array('id' => $obj->id, 'counter' => $obj->counter_downloads, 'amount' => $obj->amount);
            }
        }
        return $newArray;
    }
    
    private function get_time_unix($time, $format='U'){
        $date = new DateTime($time);
        return $date->format($format);
    }
}
?>