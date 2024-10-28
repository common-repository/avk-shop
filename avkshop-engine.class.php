<?php 
/** 
 *  Plugin Name: AVK-Shop
 * 	Plugin URI: http://avkproject.ru/avkshop-web20
 *  Description: Плагин интерент магазина для продажи файлов любого формата.
 * 	Author: Smiling_Hemp
 * 	Version: 1.1.1
 * 	Author URI: https://profiles.wordpress.org/smiling_hemp#content-plugins
 */

/**
Copyright (C) 20013-2015 Smiling_Hemp, avkproject.ru (support AT avkproject DOT ru)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include_once('includes/mainvalue.class.php');
include_once('includes/variables.class.php');
include_once('includes/features.class.php');
include_once('includes/html.class.php');
include_once('includes/user.class.php');
include_once('includes/library.class.php');

class AVKShopEngine extends AVKShopFeatures{
    
    
    public function __construct(){
        $this->initialization_avkshop();
        $this->load_language_plugin_avkshop();
        
        if( !defined( 'AVKSHOP_PL_PATH' ) )   define( 'AVKSHOP_PL_PATH', rtrim( plugin_dir_path( __FILE__), '/' ) );
        if( !defined( 'AVKSHOP_PL_URL' ) )    define( 'AVKSHOP_PL_URL', rtrim( plugin_dir_url( __FILE__),'/' ) );
        if( !defined( 'AVKSHOP_CONT_PATH' ) ) define( 'AVKSHOP_CONT_PATH', ABSPATH . array_pop( explode( '/', content_url() ) ) );
        if( !defined( 'AVKSHOP_CONT_URL' ) )  define( 'AVKSHOP_CONT_URL', content_url() );
        if( !defined( 'AVKSHOP_PATH_LOG' ) )  define( 'AVKSHOP_PATH_LOG', AVKSHOP_PL_PATH . '/temp/.ht' . self::SLUG . '_logs' );
        if( !defined( 'AVKSHOP_PATH_ROB' ) )  define( 'AVKSHOP_PATH_ROB', AVKSHOP_PL_PATH . '/temp/.htorderrobokassa' );

        add_action( 'init', array( &$this, 'add_initialization_avkshop' ), 1 );
        
        parent::__construct();

        add_filter( 'plugin_action_links', array( &$this, 'add_avkshop_link_settings' ), 10, 2 );
        add_action( 'admin_menu', array( &$this, 'add_page_avkshop' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'add_scripts_site_avkshop' ) );
        add_action( 'wp_before_admin_bar_render', array( &$this, 'add_admin_bar_avkshop' ) );
        
        //add_action('admin_footer-post-new.php',array(&$this, 'get_footer_html'));
        //add_action('admin_footer-post.php',array(&$this, 'get_footer_html'));
        add_action( 'wp_ajax_avkshop_upload', array( &$this, 'ajax_upload_file' ) );
        add_action( 'wp_ajax_avkshop_system_web20', array( &$this, 'ajax_system_web20' ) );
        add_action( 'wp_ajax_nopriv_avkshop_system_web20', array( &$this, 'ajax_system_web20' ) );
        if( $this->actMainSettings['pluginon'] == 'on' ){
            add_action( 'admin_menu', array( &$this, 'add_avkshop20_meta_box' ) );
            add_action( 'post_updated', array( &$this, 'avkshop_save_meta_box' ) );
            //add_action( 'edit_post', array( &$this, 'avkshop_save_meta_box' ) );
            //add_action( 'publish_post', array( &$this, 'avkshop_save_meta_box' ) );
            //add_action( 'save_post', array( &$this, 'avkshop_save_meta_box' ) );
            //add_action( 'edit_page_form', array( &$this, 'avkshop_save_meta_box' ) );
            add_action( 'wp_trash_post', array( &$this, 'avkshop_delete_meta' ) );
            add_action( 'admin_print_scripts-post-new.php', array(&$this, 'engen_post_edit_script' ) );
            add_action( 'admin_print_scripts-post.php', array( &$this, 'engen_post_edit_script' ) );
            add_action( 'admin_print_scripts-users.php', array( &$this, 'engen_script_page_user' ) );
            add_filter( 'the_content', array( &$this, 'add_pars_content_avk' ), 10, 1 );
            add_filter( 'the_excerpt', array( &$this, 'add_pars_content_avk' ), 10, 1 );
            add_action( 'manage_users_columns', array( &$this, 'add_new_user_column' ), 1, 1 );
            add_action( 'manage_users_custom_column', array( &$this, 'output_new_user_column' ), 1, 3 );
            add_action( 'dashboard_glance_items', array( &$this, 'read_logs' ), 1 );
            add_action( 'show_messag_pay_system_avk', array( &$this->HTML, 'show_messag_pay_system' ), 10, 1 );
            add_action( 'post_submitbox_misc_actions', array( &$this->HTML, 'message_an_incomplete_application' ) );
            add_action( 'widgets_init', array( &$this, 'register_widget' ) );
        }
        register_deactivation_hook( __FILE__, array( &$this, 'del_sett_avkshop' ) );
        
        if( $this->actMainSettings['translit'] == 'on' ){
            add_action( 'sanitize_title', array( &$this, 'sanitize_title_translit_avk' ), 0 );
        }
        
        add_filter( 'plugin_row_meta', array( &$this, 'add_link_dashplugins'), 10, 2);
        add_filter( 'login_headerurl', create_function('', 'return get_home_url();') );
        add_filter( 'login_headertitle', create_function('','return false;') );
        add_action( 'login_head', array( &$this, 'my_login_logo' ) );
        
        include_once( AVKSHOP_PL_PATH . '/modules/customer-information/module-customer-information.class.php' );
        
    }
    
    public function del_sett_avkshop(){
        
    }
    
    public function engen_script_page_user(){
        wp_register_style   ( self::SLUG . '-style-table-user', AVKSHOP_PL_URL.'/css/table-user.css', array(), $this->version );
        wp_enqueue_script   ( 'jquery' );
        wp_enqueue_style    ( self::SLUG . '-style-table-user' );
    }

    /** Инициализация JS и CSS скриптов для внешней части сайта */
    protected function engen_plugin_script_site(){
        wp_register_script  ( self::SLUG . '-script',        AVKSHOP_PL_URL . '/js/avkshop-script.js',  array('jquery'), $this->version );
        wp_register_style   ( self::SLUG . '-style-content', AVKSHOP_PL_URL . '/css/avkshop-style.css', array(),         $this->version );
        wp_register_script  ( self::SLUG . '-modal-avk',     AVKSHOP_PL_URL . '/js/simplemodal.js',     array('jquery'), '1.1.1' );
        wp_register_script  ( self::SLUG . '-script-ajax',   AVKSHOP_PL_URL . '/js/ajax-script.js',     array('jquery'), $this->version );
        wp_enqueue_script   ( 'jquery' );
        wp_enqueue_script   ( self::SLUG . '-script' );
        wp_enqueue_style    ( self::SLUG . '-style-content' );
        if($this->actMainSettings['ajax_system'] == 'on'){
            wp_enqueue_script( self::SLUG . '-script-ajax' );
            wp_enqueue_script( self::SLUG . '-modal-avk' );
            wp_localize_script(self::SLUG . '-script-ajax', 'avkShopDataAJAX', array('ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                                                                                     'avk_nonce' => wp_create_nonce(self::SLUG.'_'.SECURE_AUTH_KEY)));
        }
    }
    
    /** Инициализация JS и CSS скриптов для страниц в админпанели */
    public function engen_plugin_script_admin(){
        wp_register_script( self::SLUG . '-script-page',    AVKSHOP_PL_URL . '/js/admin-page-script.js',  array('jquery'), $this->version);
        wp_register_script( self::SLUG . '-modal-avk',      AVKSHOP_PL_URL . '/js/simplemodal.js',        array('jquery'), '1.1.1');
        wp_register_script( self::SLUG . '-script-tabs',    AVKSHOP_PL_URL . '/js/tabs.js',               array('jquery'), $this->version);
        wp_register_script( self::SLUG . '-script-sidebar', AVKSHOP_PL_URL . '/js/admin-sidebar.js',      array('jquery'), $this->version);
        wp_register_style ( self::SLUG . '-style-page',     AVKSHOP_PL_URL . '/css/admin-page-style.css', array(),         $this->version);
        wp_register_style ( self::SLUG . '-style-tabs',     AVKSHOP_PL_URL . '/css/admin-tabs-style.css', array(),         $this->version);
        wp_register_style ( self::SLUG . '-style-sidebar',  AVKSHOP_PL_URL . '/css/admin-sidebar.css',    array(),         $this->version);
        
        switch($_GET['page']){
            case self::SLUG.'-settings': 
            case self::SLUG.'-action-file': wp_enqueue_script ( self::SLUG . '-script-page' );
                                            wp_enqueue_script ( self::SLUG . '-modal-avk' );
                                            wp_enqueue_script ( self::SLUG . '-script-sidebar' );
                                            wp_localize_script( self::SLUG . '-script-page', 'avkShopVarAdm', array( 'urlImgClouse' => AVKSHOP_PL_URL . '/images/close_box_min.png' ) );
                                            wp_enqueue_style  ( self::SLUG . '-style-page' );
                                            wp_enqueue_style  ( self::SLUG . '-style-sidebar' );
                                                                                                break;
            case self::SLUG.'-table': wp_enqueue_script( self::SLUG . '-script-tabs');
                                      wp_enqueue_style ( self::SLUG . '-style-tabs');
                                      wp_enqueue_script ( self::SLUG . '-script-sidebar');
                                      wp_enqueue_style  ( self::SLUG . '-style-sidebar');
                                                                                        break;
            default: apply_filters( 'engen_script_admin_avkshop', $_GET['page'] ); break;
        }
    }
    
    /** Инициализация JS и CSS скриптов при создании/редактировании постов */
    public function engen_post_edit_script(){
        wp_enqueue_script ( 'jquery' );
        wp_enqueue_script ( self::SLUG . '-modal-avk', AVKSHOP_PL_URL . '/js/simplemodal.js', array('jquery'), '1.1.1' );
        wp_enqueue_script ( self::SLUG . '-ajax-upload-avk', AVKSHOP_PL_URL . '/js/ajaxupload.js', array('jquery'), '1.0.0' );
        wp_enqueue_script ( self::SLUG . '-script-edit', AVKSHOP_PL_URL . '/js/admin-post-edit.js', array( 'jquery', self::SLUG . '-modal-avk' ), $this->version );
        wp_localize_script( self::SLUG . '-script-edit', 'avkShopVarAdm', array( 'defValInp' => $this->warningMsg,
                                                                                 'delValInp' => __('Загрузить файл ...',self::SLUG),
                                                                                 'buttonTextAVK' => array('upload'    => __('Загрузить',self::SLUG),
                                                                                                          'download'  => __('Скачать/Купить',self::SLUG),
                                                                                                          'deleteavk' => __('Удалить',self::SLUG),
                                                                                                          'counter'   => __('Счетчик',self::SLUG)),
                                                                                 'textButton'=> sprintf( __( 'Введены не все данные в %s', self::SLUG ), $this->name ),
                                                                                 'safety' => wp_create_nonce( self::SLUG . '_' . SECURE_AUTH_KEY )
                                                                                ) );
        wp_enqueue_script ( self::SLUG . '-script-edit' );
        wp_enqueue_style  ( self::SLUG . '-style-edit', AVKSHOP_PL_URL . '/css/admin-post-edit.css', array(), $this->version );
    }
    
    /** Подключение файлов с переводом */
    public function load_language_plugin_avkshop(){
        load_plugin_textdomain( self::SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/lang');
    }
    
    /** Метод выводит форму AVKShop2.0 в редакторе */
    public function add_avkshop20_meta_box(){
        $arrayDataPost = get_post_types( array('public' => true, '_builtin' => false), 'objects', 'or' );
        
		foreach ($arrayDataPost as $postType => $object){
            if($postType == 'link' || $postType == 'attachment') continue;
            
            $arrValue = array( 'warningMsg' => $this->warningMsg, 'metaBoxValue' => $this->metaBoxValue, 'nameTypePost' => $object->labels->name );
            $name = $this->name . '&nbsp;' . __('- форма добавления товара',self::SLUG);
            add_meta_box( 'id-meta-box-' . self::SLUG, $name, array(&$this->HTML, 'get_meta_box'), $postType, 'normal', 'high', $arrValue );
		}
	}
    
    /** Подключает страницу */
    public function get_page_menu() {
  		switch ($_GET['page']){
            case self::SLUG.'-secondary-settings' : 
            case self::SLUG.'-table' : 
                        include_once (AVKSHOP_PL_PATH.'/pages/tables.php');
                                                                        break;
            case self::SLUG.'-action-file': 
                        include_once (AVKSHOP_PL_PATH.'/pages/action_file.php');
                                                                        break;            
            case self::SLUG.'-settings' : 
                        include_once (AVKSHOP_PL_PATH.'/pages/main_menu.php'); 
                                                                        break;
		}
	}
    
    /** Подключение скриптов на внешней части сайта */
    public function add_scripts_site_avkshop(){
        $this->engen_plugin_script_site();
    }
    
    public function my_login_logo(){
        if($this->actMainSettings['maylogo'] == 'on')
            echo '<style type="text/css">
                    #login h1 {margin-bottom: 7px}
                    #login h1 a { background: url(' . $this->actMainSettings['maylogourl'] . ') no-repeat center center !important;
                                  display: block;
                                  width: 100%; }
                  </style>';
    }
    
    public function ajax_system_web20(){
        check_ajax_referer(self::SLUG.'_'.SECURE_AUTH_KEY, 'avk_nonce');
        $this->ajax_action();
        die;
    }

/** Регистрация виджета корзины */    
    public function register_widget(){
        include_once( 'includes/widget.class.php' );
        register_widget( 'WidgetBasketAVK' );
    }
    
/** Добавляет ссылки на странице плагинов */
    public static function add_link_dashplugins( $links, $file ){        
		if( $file == plugin_basename( __FILE__ ) ) {
			$links[] = '<a href="http://goo.gl/3Ux5gD">' . __('Поблагодарить', self::SLUG) . '</a>';
            //$links[] = '<a href="#" target="_blank">F.A.Q.</a>';
		}
		return $links;
	}
}

$GLOBALS['AVKShopEngine'] = new AVKShopEngine();
?>