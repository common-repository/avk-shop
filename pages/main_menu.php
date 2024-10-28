<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */
?>
<div class="wrap">
<?php
global $wpdb;

if( $this->actMainSettings['pluginon'] != 'on' ){
    foreach($this->defaultMainSettings as $field)
        if($field['type'] == 'openfieldset' and isset($field['important']))
            $link .= ' <a href="#'.$field['id'].'">'.$field['title'].'</a>,';
            
    $link = rtrim($link,',');
    echo '<div class="error" ><p class="messegadmin">'.sprintf(__('Вы активировали плагин %s, для корректной его работы заполните поля %s на данной странице используя выпадающие подсказки, и только после этого %s включите плагин %s.', self::SLUG), $this->name, $link, '<a href="#fildplon">', '</a>' ).'</p></div>';
}

    if ('true' == esc_attr($_GET['updated']))    echo '<div class="updated fade" ><p class="messegadmin">'.sprintf(__('Настройки %s сохранены.',self::SLUG),$this->name).'</p></div>';
    if ('false' == esc_attr($_GET['updated']))   echo '<div class="error fade" ><p class="messegadmin">'.sprintf(__('Настройки %s небыли сохранены!',self::SLUG),$this->name).'</p></div>';
    if ('true' == esc_attr($_GET['delsetavk']))  echo '<div class="updated fade" ><p class="messegadmin">'.sprintf(__('Настройки плагина %s установлены по умолчанию.',self::SLUG),$this->name).'</p></div>';
    if ('false' == esc_attr($_GET['delsetavk'])) echo '<div class="error fade" ><p class="messegadmin">'.sprintf(__('В процессе возникли проблемы, настройки плагина %s не сброшены!',self::SLUG),$this->name).'</p></div>';
?>
    <a href="http://avkproject.ru/">
        <div id="avkshopweb20" style="background: url(<?php echo AVKSHOP_PL_URL;?>/images/icon.png) no-repeat;" class="icon32"><br /></div>
    </a>
    <h2><span class="dashicons dashicons-admin-settings dashicons-admin-setavk"></span><?php _e('Главные настройки',self::SLUG); echo ' '.$this->name;?></h2>
<?php
if( $this->actMainSettings['pluginon'] == 'on' ){
    $sql1 = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . DB_NAME . "' AND TABLE_NAME='{$wpdb->prefix}{$this->actMainSettings['tableshopping']}'";
    $resultShopping = $wpdb->get_results($sql1, 'OBJECT');
    $sql2 = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . DB_NAME . "' AND TABLE_NAME='{$wpdb->prefix}{$this->actMainSettings['tabledownload']}'";
    $resultDownload = $wpdb->get_results($sql2, 'OBJECT');
    if( empty( $resultShopping ) && empty( $resultDownload ) ){ ?>
        <p style="color: red; font-size: 1.2em; line-height: 1.2em; font-weight: bold; text-align: center;line-height: 1.1em;">
        <?php 
        $text = __('ВНИМАНИЕ!!! %s В Вашей базе данных еще нет таблиц необходимых для работы плагина. Для того чтобы они появились, перейдите на %s страницу инструментов %s и в разделе «Менеджер таблиц БД» нажмите кнопку «Создать».', self::SLUG );
        printf( $text, '<br />', '<a href="' . admin_url( 'admin.php?page=' . self::SLUG . '-action-file' ) . '">', '</a>');
        ?>
        </p>
<?php
    }
}
?>
    <section id="middle">
		<div id="container">
			<div id="content">
                <form method="post" action="admin.php?page=<?php echo $_GET['page'];?>" id="main_settings_form">
                    <?php
                        wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce");
                        $this->HTML->get_table_settings($this->defaultMainSettings,$this->actMainSettings,$this->warningMsg);
                    ?>
                    <p class="submit"  style="text-align: center!important;">
                        <input type="hidden" name="actionsaveavk" value="yes"/>
                        <input type="submit" style="margin-left: 40px" class="button-primary" name="savemainsettings" value="<?php _e('Сохранить',self::SLUG);?>" />
                        <a href="#?w=500" rel="popup_avk" class="poplight popup_botton"><?php _e('Сбросить данные',self::SLUG);?></a>
                    </p>
                </form>
                <div id="popup_avk" class="popup_block">
                    <div style="background: url(<?php echo AVKSHOP_PL_URL;?>/images/warning.png) no-repeat;" class="icon32"><br /></div>
                    <h2><?php _e('Внимание!!!',self::SLUG)?></h2>
                        <p style="text-align: center;"><?php printf(__('Данное действие %s удалит %s все Ваши настройки плагина %s и восстановит все настройки по умолчанию!',self::SLUG), '<b>', '</b>', $this->name);?></p>
                        <p><?php _e('Вы уверенны, что хотите сделать это?',self::SLUG)?></p>
                        <p class="submit">
                            <input type="submit" class="button-primary clouse_pop_avk" value="<?php _e('Отменить',self::SLUG);?>" />
                            <input form="main_settings_form" type="submit" class="button-primary button-primary-del" name="ressetsettings" value="<?php _e('Сбросить',self::SLUG);?>" />
                        </p>
                </div>
			</div><!-- #content-->
		</div><!-- #container-->
		<aside id="sideRight">
            <?php include_once AVKSHOP_PL_PATH . "/pages/sidebar.php"; ?>
		</aside><!-- #sideRight -->
	</section><!-- #middle-->
</div>