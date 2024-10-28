<?php 
/**
 * @author Smiling_Hemp
 * @copyright 2015
 */

class ModuleCustomerInformationAVKShop extends libraryAVKShop{
    
    protected $mainSettings;
    private $plPath;
    private $plUrl;
    private $contPath;
    private $contUrl;
    
    const THIS_SLUG = "avkshopweb20modulecustomerinformation";
    
    public function __construct(){
        $this->mainSettings = get_option( self::SLUG . '-settings' );
        
        $this->plPath   = rtrim(plugin_dir_path(__FILE__),'/');
        $this->plUrl    = rtrim(plugin_dir_url(__FILE__),'/');
        $this->contPath = ABSPATH . array_pop(explode('/',content_url()));
        $this->contUrl  = content_url();
        
        add_filter('add_new_user_column_avkshop', array (&$this, 'add_column'), 10, 1);
        add_filter('output_new_user_column_avkshop', array (&$this, 'add_column_user'), 10, 2);
        add_action('admin_print_scripts-users.php', array(&$this, 'engen_script_page_user'));
    }

    public function add_column($column){
        $newColumn = array('infobutton'=> __('Активность', self::SLUG));
        $column = array_merge($column, $newColumn);
        return $column;
    }
    
    public function add_column_user($nameColumn, $user){
        if($nameColumn == 'infobutton'){
            $text  = '(';
            $text .= $this->html( 'span', array( 'class' => 'info_user_act', 'title' => __('Скачано бесплатного товара', self::SLUG) ), $user->counterDownloads );
            $text .= ' / ';
            $text .= $this->html( 'span', array( 'class' => 'info_user_act', 'title' => __('Куплено товара', self::SLUG) ), $user->counterPurchases );
            $text .= ')';
            $div   = $this->html( 'div', array( 'class' => 'info_user_avk_div' ), $text );
            $input = $this->html( 'input', array( 'alt'   => '#TB_inline?height=400&width=750&inlineId=infobutton-user-' . $user->id,
                                                  'type'  => 'button',
                                                  'class' => 'thickbox button thickbox-click',
                                                  'title' => __( 'Информация', self::SLUG ),
                                                  'value' => __( 'Показать', self::SLUG ) ) );
            $div  .= $this->html( 'div', array( 'class' => 'info_user_avk_div' ), $input );
            $div  .= $this->html( 'div', array( 'id' => 'infobutton-user-' . $user->id, 'style' => 'display:none;' ), $this->tabs_user_info( $user->id ) );
                        
            return $div;
        }
    }
    
    public function engen_script_page_user(){                
        if( isset( $_GET['role'] ) && $_GET['role'] == $this->mainSettings['customerroleavkshop'] ){
            add_thickbox();
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-core', null, array( 'jquery' ) );
            wp_enqueue_script( 'jquery-ui-tabs', null, array( 'jquery', 'jquery-ui-core' ) );
            wp_enqueue_script( self::THIS_SLUG . '-script', $this->plUrl . '/js/script.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs' ), $this->version );
            wp_enqueue_style ( self::THIS_SLUG . '-style-ui', $this->plUrl . '/css/style-ui.css', array(), '1.10.3' );
            wp_enqueue_style ( self::THIS_SLUG . '-style-table-user', $this->plUrl . '/css/style.css', array(), $this->version );
        }
    }
    
    protected function set_bookmark_tabs($num, $text, $type){
        switch($type){
            case"li":  $str = $this->html( 'li', $this->html( 'a', array( 'href' => '#tabs-' . $num ), $text ) ); break;
            case"div": $str = $this->html( 'div', array( 'id' => 'tabs-' . $num ), $text ); break;
        }
        return $str;
    }
    
    protected function tabs_user_info($id){
        if( class_exists( 'User_AVKShop' ) ) $user = new User_AVKShop( array( 'field' => 'id', 'value' => $id ) );
        $str  = '<div class="tabs">';
        $str .= '<ul>';
        $str .= $this->set_bookmark_tabs( 1, __( 'Скачал(a)', self::SLUG ), 'li' );
        $str .= $this->set_bookmark_tabs( 2, __( 'Купил(a)', self::SLUG ), 'li' );
        $str .= '</ul>';
        $str .= $this->set_bookmark_tabs( 1, $this->get_table_download( $user->downloadTable ), 'div' );
        $str .= $this->set_bookmark_tabs( 2, $this->get_table_pay( $user->purchasesTable ), 'div' );
        $str .= '</div>';
        return $str;
    }
    
    protected function get_table_download($object){
        if(empty($object)) return '<h1 class="no-action-avk">' . __('Нет действий', self::SLUG) . '</h1>';
        $str  = '<table class="avkshop_user avkshop_download" cellspacing="0">';
        $str .= '<tr>
                     <th>№</th>
                     <th>'.__('Название', self::SLUG).'</th>
                     <th>'.__('Описание', self::SLUG).'</th>
                     <th>'.__('Кол-во скачиваний', self::SLUG).'</th>
                     <th>'.__('Тип товара', self::SLUG).'</th>
                     <th>'.__('Последний раз', self::SLUG).'</th>
                 <tr>';
        foreach($object as $key => $value){
            global $locali;
            $meta = array_shift(get_post_meta($value->id_post, '_metaBoxValue'));
            if(empty($meta)) continue;
            $str .= '<tr>';
            $str .= '<td>'.++$key.'</td>';
            $str .= '<td><h4><a href="'.get_permalink($value->id_post).'" target="_blank" title="'.get_the_title($value->id_post).'">'.$meta['name_product_avk'].'</a></h4></td>';
            $str .= '<td>'.$meta['desc_product_avk'].'</td>';
            $str .= '<td>'.$value->counter_downloads.'</td>';
            $str .= '<td>'.$this->type_product_lang($value->type_goods).'</td>';
            $str .= '<td>'.date('j.n.Y G:i:s',$value->datetime + (get_option('gmt_offset') * HOUR_IN_SECONDS)).'</td>';
            $str .= '</tr>';
        }
        $str .= '</table>';
        return $str;
    }
    
    protected function get_table_pay($object){
        if(empty($object)) return '<h1 class="no-action-avk">' . __('Нет действий', self::SLUG) . '</h1>';
        $str  = '<table class="avkshop_user avkshop_download" cellspacing="0">';
        $str .= '<tr>
                     <th>№</th>
                     <th>'.__('Название', self::SLUG).'</th>
                     <th>'.__('Описание', self::SLUG).'</th>
                     <th>'.__('Кол-во скачиваний', self::SLUG).'</th>
                     <th>'.__('Доступное кол-во', self::SLUG).'</th>
                     <th>'.__('Система оплаты', self::SLUG).'</th>
                     <th>'.__('Дата покупки', self::SLUG).'</th>
                 <tr>';
        foreach($object as $key => $value){
            if($value->order_status != 'paid') continue;
            $meta = array_shift(get_post_meta($value->id_post, '_metaBoxValue'));
            if(empty($meta)) continue;
            $str .= '<tr>';
            $str .= '<td>'.++$key.'</td>';
            $str .= '<td><h4><a href="'.get_permalink($value->id_post).'" target="_blank" title="'.get_the_title($value->id_post).'">'.$meta['name_product_avk'].'</a></h4></td>';
            $str .= '<td>'.$meta['desc_product_avk'].'</td>';
            $str .= '<td>'.$value->counter_downloads.'</td>';
            $str .= '<td>'.$value->amount.'</td>';
            $str .= '<td>'.$value->payment_system.'</td>';
            $str .= '<td>'.date('j.n.Y G:i:s',$value->datetime + (get_option('gmt_offset') * HOUR_IN_SECONDS)).'</td>';
            $str .= '</tr>';
        }
        $str .= '</table>';
        return $str;
    }
    
    protected function type_product_lang($type){
        switch($type){
            case'free': $type = __('Бесплатный', self::SLUG); break;
            case'paid': $type = __('Платный', self::SLUG); break;
        }
        return $type;
    }
}
new ModuleCustomerInformationAVKShop();
?>