<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */
?>
<div class="wrap">
<?php 
    if ('true' == esc_attr($_GET['updated'])) echo '<div class="updated fade" ><p class="messegadmin">'.sprintf(__('Настройки %s сохранены.',self::SLUG), '<b>'.strtoupper($this->clear_data_avk($_GET['tab'])).'</b>').'</p></div>'; 
        
    $tab = $this->LIB->clear_data_avk($_GET['tab']);
    if($tab == 'error') return;
    
    switch($tab){
        case"logavk": $title = '<span class="dashicons dashicons-cart dashicons-admin-setavk"></span>' . __('Информация о Ваших клиентах и продажах', self::SLUG); break;
        case"interkassa": $title = '<span class="dashicons dashicons-admin-generic dashicons-admin-setavk"></span>' . __('Настройка платежной системы', self::SLUG) . ' InterKassa'; break;
        case"robokassa": $title = '<span class="dashicons dashicons-admin-generic dashicons-admin-setavk"></span>' . __('Настройка платежной системы', self::SLUG) . ' RoboKassa'; break;
        default: do_action('title_for_page_settings_system_pay_avkshop', $tab);
    }
?>
    <h2><?php echo $title . ' - ' . $this->name; ?></h2>
    <?php $this->get_admin_page_tabs($tab); ?>
    <section id="middle">
		<div id="container">
			<div id="content">
                <div id="avk_settings_metabox" class="postbox">
                    <h3 class="hndle hndleavk">
                        <span>
                        <?php echo $this->LIB->systemPay[$this->clear_data_avk($_GET['tab'])]['title']?>
                        </span>
                    </h3>
                    <div class="inside">
                        <div id="tabs-inside-content-avk" class="inside-content-avk" <?php if($tab == 'logavk') echo 'style="width: 99%!important;"'; ?>>
                            <form method="post" action="admin.php?<?php echo $_SERVER['QUERY_STRING']; ?>" >
                                <?php wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce"); ?>
                                <input type="hidden" name="_avk_ref" value="<?php echo admin_url('admin.php?'.$_SERVER['QUERY_STRING']);?>"/>
                                <input type="hidden" name="_avk_save_action_sys_pay" value="<?php echo $tab;?>"/>
                                <?php
                                    switch($tab){
                                        case"logavk": echo $this->HTML->page_log(); break;
                                        case"interkassa": echo $this->HTML->page_interkassa($tab); break;
                                        case"robokassa": echo $this->HTML->page_robokassa($tab); break;
                                        default: do_action('input_for_page_settings_system_pay_avkshop', $tab);
                                    }
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
			</div><!-- #content-->
		</div><!-- #container-->
		<aside id="sideRight">
            <?php include_once AVKSHOP_PL_PATH . "/pages/sidebar.php"; ?>
		</aside><!-- #sideRight -->
	</section><!-- #middle-->
</div>