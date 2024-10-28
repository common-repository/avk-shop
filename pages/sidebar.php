<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */
?>
<div id="avk_settings_metabox_1" class="postbox">
    <h3 class="hndle hndleavk"><span class="donatecl"><?php _e('Следить за мной', self::SLUG); ?>:</span></h3>
    <div class="inside">
        <div class="sidebar-soc-content">
            <div class="sidebar-soc-buttons">
                <div class="sidebar-soc-button sidebar-soc-button-vk">
                    <a href="https://vk.com/avkprojec" target="_blank">
                        <img src="<?php echo AVKSHOP_PL_URL . '/images/soc/vk.png' ?>" alt="" />
                    </a>
                </div>
                <div class="sidebar-soc-button sidebar-soc-button-instagram">
                    <a href="https://instagram.com/smiling_hemp/" target="_blank">
                        <img src="<?php echo AVKSHOP_PL_URL . '/images/soc/instagram.png' ?>" alt="" />
                    </a>
                </div>
                <div class="sidebar-soc-button sidebar-soc-button-twitter">
                    <a href="https://twitter.com/Smiling_Hemp" target="_blank">
                        <img src="<?php echo AVKSHOP_PL_URL . '/images/soc/twitter.png' ?>" alt="" />
                    </a>
                </div>
                <div class="sidebar-soc-button sidebar-soc-button-youtube">
                    <a href="https://www.youtube.com/user/theSmilingHemp" target="_blank">
                        <img src="<?php echo AVKSHOP_PL_URL . '/images/soc/youtube.png' ?>" alt="" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="avk_settings_metabox_2" class="postbox">
    <h3 class="hndle hndleavk"><span class="donatecl"><?php _e('Помощь проекту', self::SLUG); ?>:</span></h3>
    <div class="inside">
        <div class="avk-sidebar-donate">
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                <input type="hidden" name="cmd" value="_s-xclick" />
                <input type="hidden" name="hosted_button_id" value="CWD3RZ28GX66C" />
                <input type="image" src="https://www.paypalobjects.com/<?php echo ( 'ru_RU' == get_option('WPLANG') ) ? 'ru_RU/RU' : 'en_US'; ?>/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — <?php _e('более безопасный и легкий способ оплаты через Интернет!', self::SLUG); ?>" />
                <img alt="" border="0" src="https://www.paypalobjects.com/ru_RU/i/scr/pixel.gif" width="1" height="1" />
            </form>
            <form action="https://advisor.wmtransfer.com/Spasibo.aspx" method="post" target="_blank" title="Передать $пасибо! нашему сайту">
                <input type="hidden" name="url" value="http://avkproject.ru" />
                <input type="image" src="//advisor.wmtransfer.com/img/Spasibo!.png" border="0" name="submit"/>
            </form>
        </div>
    </div>
</div>