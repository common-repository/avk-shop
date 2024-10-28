<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */
?>
<div class="wrap">
    <?php 
        $nameFile = $this->LIB->clear_data_avk($_GET['namefileavk']);
        if($nameFile != 'error'){
            switch($_GET['deletefile']){
                case'true': echo '<div class="updated fade" ><p class="messegadmin">'.sprintf(__('Файл %s удален.',self::SLUG), '<b>'.$nameFile.'</b>').'</p></div>'; break;
                case'false': echo '<div class="error fade" ><p class="messegadmin">'.sprintf(__('Файл %s не удален!',self::SLUG), '<b>'.$nameFile.'</b>').'</p></div>'; break;
                case'nofile': if($nameFile == 'nonefile'){
                                  $str = __('Не выбран файл!',self::SLUG);
                              }else{
                                  $str = sprintf(__('Файл %s не существует!',self::SLUG), '<b>'.$nameFile.'</b>');
                              }
                              echo '<div class="error fade" ><p class="messegadmin">'.$str.'</p></div>'; break;
            }
        }
        /** Проверка таблиц */
        global $wpdb;
        $sql1 = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . DB_NAME . "' AND TABLE_NAME='{$wpdb->prefix}{$this->actMainSettings['tableshopping']}'";
        $resultShopping = $wpdb->get_results($sql1, 'OBJECT');
        $sql2 = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . DB_NAME . "' AND TABLE_NAME='{$wpdb->prefix}{$this->actMainSettings['tabledownload']}'";
        $resultDownload = $wpdb->get_results($sql2, 'OBJECT');
        $button = '<p class="submit" style="text-align: center;">
                        <input type="hidden" name="actioncreatetableavk" value="yes"/>
                        <input type="hidden" name="actionsaveavk" value="yes"/>
                        <input type="submit" class="button-primary" value="'. __('Создать',self::SLUG). '" />
                   </p>';
        $strQuery = __('запрос',self::SLUG);
    ?>
    
    <h2><span class="dashicons dashicons-admin-tools dashicons-admin-setavk"></span><?php _e('Инструменты',self::SLUG); echo ' '.$this->name;?></h2>
    <section id="middle">
		<div id="container">
			<div id="content">
                <div id="avk_settings_metabox" class="postbox">
                    <h3 class="hndle hndleavk"><span><?php _e('Менеджер файлов',self::SLUG);?></span></h3>
                    <div class="inside">
                        <form method="post" action="admin.php?page=<?php echo $_GET['page'];?>" id="action_file_form">
                            <?php wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce"); ?>
                            <dl>
                                <dt id="action_delete_file_avk_title" class="dttitleavk">
                                    <div class="divlabel"><label class="labelavk select" for="action_delete_file_avk"><?php _e('Выберите файл', self::SLUG)?>:</label></div>
                                    <select id="action_delete_file_avk"  size="1" name="action_delete_file_avk" class="inputavk">
    <?php 
        $path = $this->actMainSettings['main_path_up_dir'].'/'.$this->actMainSettings['name_up_dir'];
        if(is_dir($path)){
            $d = dir($path);
            while (false !== ($entry = $d->read())){
                if($entry == '.' || $entry == '..' || $entry == '.htaccess' || $entry == 'index.php') continue;
                $optionArray[] = $entry;
            }
            $d->close();
            if(is_array($optionArray)){
                sort($optionArray);
                $option = '<option value="nonefile" selected ></option>';
                foreach($optionArray as $valueOption){
                    $option .= '<option value="'.$valueOption.'" >'.$valueOption.'</option>';
                }
            }
        }
        echo $option;
    ?>
                                    </select>
                                    <img class="helpimg" title="<?php _e('Нажмите, чтобы прочитать подробнее о настройке', self::SLUG);?>" src="<?php echo AVKSHOP_PL_URL;?>/images/question.png"/>
                                </dt>
                                <dd id="action_delete_file_avk_desc" class="descavk"><?php _e('Из выпадающего списка выберите файл, который необходимо удалить с Вашего сервера, и нажмите кнопку «Удалить файл».',self::SLUG);?></dd>
                            </dl>
                            <p class="submit" style="text-align: center;">
                                <input type="hidden" name="actiondeletefileserveravk" value="yes"/>
                                <input type="hidden" name="actionsaveavk" value="yes"/>
                                <a href="#?w=300" rel="action_file_pop" class="poplight popup_botton"><?php _e('Удалить файл',self::SLUG);?></a>
                            </p>
                        </form>
                    </div>
                </div>
                <div id="avk_settings_metabox-1" class="postbox">
                    <h3 class="hndle hndleavk"><span><?php _e('Менеджер таблиц БД',self::SLUG);?></span></h3>
                    <div class="inside">
                    <?php if($this->actMainSettings['tableshopping'] == '' || $this->actMainSettings['tabledownload'] == ''):?>
                    <p style="color: red; font-size: 2em; font-weight: bold; text-align: center;line-height: 1.1em;">
                    <?php printf(__('ВНИМАНИЕ!!! Введены не все данные. Прейдите на страницу %s настроек %s, введите название таблиц и нажмите кнопку «Сохранить».',self::SLUG),'<a href="'.admin_url('admin.php?page='.self::SLUG.'-settings#fildplsec').'" target="_blank">','</a>');?>
                    </p>
                    <?php else: ?>
                        <?php if(empty($resultShopping) && empty($resultDownload)):?>
                            <p style="color: red; font-size: 1.5em; font-weight: bold; text-align: center;line-height: 1.1em;">
                            <?php printf(__('ВНИМАНИЕ!!! %s В Вашей базе данных еще нет таблиц необходимых для работы плагина. Для того чтобы они появились, нажмите кнопу «Создать» или можете создать таблицы при помощи SQL запроса приведенного ниже:',self::SLUG),'<br />');?>
                            </p>
                            <form method="post" action="admin.php?page=<?php echo $_GET['page'];?>" id="checktable-avk">
                                <?php wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce"); ?>
                                <?php echo $button; ?>
                            </form>
                            <fieldset class="avk_fieldset" style="font-size: 1.2em;text-align: center; padding-left: 10px; padding-right: 10px;">
                                <legend class="avk_legend">SQL-<?php echo $strQuery;?></legend>
                                <?php echo $this->queryShopping; ?>
                                <br />
                                <?php echo $this->queryDownload; ?>
                            </fieldset>
                        <?php elseif(empty($resultShopping)):?>
                            <p style="color: red; font-size: 1.5em; font-weight: bold; text-align: center;line-height: 1.1em;">
                            <?php printf(__('ВНИМАНИЕ!!! %s По каким-то причинам таблица для учёта покупок не была создана. Попробуйте еще раз создать таблицу, нажав на кнопку «Создать».',self::SLUG),'<br />');?>
                            </p>
                            <form method="post" action="admin.php?page=<?php echo $_GET['page'];?>" id="checktable-avk">
                                <?php wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce"); ?>
                                <?php echo $button; ?>
                            </form>
                            <p style="color: red; font-size: 1.5em; font-weight: bold; text-align: center;line-height: 1.1em;">
                            <?php _e('Если после повторной попытки создания таблицы, вы вновь увидите данное сообщение, Вам необходимо будет создать таблицу в ручную используя SQL запрос приведенный ниже:',self::SLUG);?>
                            </p>
                            <fieldset class="avk_fieldset" style="font-size: 1.2em;text-align: center; padding-left: 10px; padding-right: 10px;">
                                <legend class="avk_legend">SQL-<?php echo $strQuery;?></legend>
                                <?php echo $this->queryShopping; ?>
                            </fieldset>
                        <?php elseif(empty($resultDownload)):?>
                            <p style="color: red; font-size: 1.5em; font-weight: bold; text-align: center;line-height: 1.1em;">
                            <?php printf(__('ВНИМАНИЕ!!! %s По каким-то причинам таблица для учёта загрузок не была создана. Попробуйте еще раз создать таблицу, нажав на кнопку «Создать».',self::SLUG),'<br />');?>
                            </p>
                            <form method="post" action="admin.php?page=<?php echo $_GET['page'];?>" id="checktable-avk">
                                <?php wp_nonce_field(self::SLUG.'_'.SECURE_AUTH_KEY, "_avknonce"); ?>
                                <?php echo $button; ?>
                            </form>
                            <p style="color: red; font-size: 1.5em; font-weight: bold; text-align: center;line-height: 1.1em;">
                            <?php _e('Если после повторной попытки создания таблицы, вы вновь увидите данное сообщение, Вам необходимо создать таблицу в ручную используя SQL запрос приведенный ниже:',self::SLUG);?>
                            </p>
                            <fieldset class="avk_fieldset" style="font-size: 1.2em;text-align: center; padding-left: 10px; padding-right: 10px;">
                                <legend class="avk_legend">SQL-<?php echo $strQuery;?></legend>
                                <?php echo $this->queryDownload; ?>
                            </fieldset>
                        <?php else:?>
                            <p style="color: blue; font-size: 1.5em; font-weight: bold; text-align: center;line-height: 1.1em;">
                            <?php printf(__('Все в порядке, таблицы %s и %s созданы.',self::SLUG), '<i> &laquo;'.$wpdb->prefix.$this->actMainSettings['tableshopping'].'&raquo; </i>', '<i> &laquo;'.$wpdb->prefix.$this->actMainSettings['tabledownload'].'&raquo; </i>');?>
                            </p>
                        <?php endif;?>
                    <?php endif;?>
                    </div>
                </div>
                <div id="action_file_pop" class="popup_block">
                    <div style="background: url(<?php echo AVKSHOP_PL_URL;?>/images/warning.png) no-repeat;" class="avkicon32"><br /></div>
                    <h2><?php _e('Внимание!!!',self::SLUG)?></h2>
                        <p style="text-align: center;"><?php _e('Данное действие удалит файл <b id=”name_del_file_avk”></b>.',self::SLUG);?></p>
                        <p><?php _e('Вы уверенны, что хотите сделать это?',self::SLUG)?></p>
                        <p class="submit">
                            <input type="submit" class="button-primary clouse_pop_avk" value="<?php _e('Отменить',self::SLUG);?>" />
                            <input form="action_file_form" type="submit" class="button-primary button-primary-del action-file-button-primary-del" name="" value="<?php _e('Удалить',self::SLUG);?>" />
                        </p>
                </div>
			</div><!-- #content-->
		</div><!-- #container-->
		<aside id="sideRight">
            <?php include_once AVKSHOP_PL_PATH . "/pages/sidebar.php"; ?>
		</aside><!-- #sideRight -->
	</section><!-- #middle-->
</div>