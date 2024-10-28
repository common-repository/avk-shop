//encodeURIComponent()
(function($) {
    var SiteTempVarAVKShop = {
            get_scroll: function(){
                            return $(document).scrollTop();
                        },
            get_height: function(){
                            return $(window).outerHeight(true) - ($(window).outerHeight(true) / 100) * 35;
                        },
            get_width : function(){
                            return $(document).outerWidth(true)- ($(document).outerWidth(true) / 100) * 35;
                        },
            get_top   : function(element){
                            return ($(window).outerHeight(true) - $(element).height()) / 2;
                        },
            get_left  : function(element){
                            return ($(document).outerWidth(true) - $(element).width()) / 2;
                        },
                        
            AJAX_Query_AVKShop: function(data, element, callAction, obj){
                                    obj.attr('disabled', true);
                                    //.next('.avk_loading_visibl').fadeIn();
                                    datas = {
                                             action: 'avkshop_system_web20',
                                             avk_nonce: avkShopDataAJAX.avk_nonce,
                                             avk_array_ajax: data
                                            };
                                        
                                    $.ajaxSetup({cache: false});
                                    $.post(avkShopDataAJAX.ajaxUrl, datas, function(response){
                                        obj.attr('disabled', false); 
                                        obj.next('.avk_loading_visibl').fadeOut();
                                        console.log(response);
                                        response = JSON.parse(response);
                                        if(response.type == false){
                                            if(typeof response.error != 'undefined'){
                                                alert(response.error);
                                            }
                                            if(typeof response.warning != 'undefined'){
                                                SiteTempVarAVKShop.show_message_from_server('message', '', response.warning)
                                            }
                                        }
                                        if(response.type == true){
                                            if(typeof response.redirect != 'undefined'){
                                                document.location.href = response.redirect;
                                                return;
                                            }
                                            if(typeof response.html != 'undefined'){
                                                SiteTempVarAVKShop.show_message_from_server(callAction, element, response.html);
                                            }
                                        }
                                    });
                                },
                                
            show_message_from_server: function(action, element, content){
                                          //console.log(action, element, content);
                                          switch(action){
                                              case"refresh": $(element).empty().html(content);
                                                                break;
                                              case"remuve": element.prev('[name = avkshop_download_in_cart]').after(content.button);
                                                            element.remove();
                                                            $('#widget_cart_html').empty().html(content.cart);
                                                            //alert(element);
                                                                break;
                                              case"message": $('body').append(content);
                                                             $('.popup_message_avkshop').css({'top': this.get_top('.popup_message_avkshop'), 'left': this.get_left('.popup_message_avkshop')});
                                                                 break;
                                          }
                                      }
    };

    $(document).ready(function(){
            //Добавления товара в корзину
        $('.avk-product').on('click', '.avk_buttons_paid', function(){
            SiteTempVarAVKShop.elements = $(this);
            SiteTempVarAVKShop.query = {add_to_cart: SiteTempVarAVKShop.elements.prev('[name = avkshop_download_in_cart]').attr('value')};
            SiteTempVarAVKShop.AJAX_Query_AVKShop(SiteTempVarAVKShop.query, '#widget_cart_html', 'refresh', SiteTempVarAVKShop.elements);
            return false;
        });
            //Счетчик скачивания и замена кнопки
        $('.avk-product-paid').on('click', '.avk_buttons_process', function(e){
            SiteTempVarAVKShop.counter = $(this).find('.counter').text();
            SiteTempVarAVKShop.amount =  $(this).find('.amount').text();
            if(++SiteTempVarAVKShop.counter <= SiteTempVarAVKShop.amount){
                $(this).find('.counter').text(SiteTempVarAVKShop.counter);
            }
            if(SiteTempVarAVKShop.counter == SiteTempVarAVKShop.amount){
                SiteTempVarAVKShop.elements = $(this);
                SiteTempVarAVKShop.query = {get_button_pay: $(this).prev('[name = avkshop_download_in_cart]').attr('value')};
                setTimeout(function(){
                    SiteTempVarAVKShop.AJAX_Query_AVKShop(SiteTempVarAVKShop.query, SiteTempVarAVKShop.elements, 'remuve', SiteTempVarAVKShop.elements);
                }, 2000);
            }
            return true;
        });
        
        $('#all_product_cart').on('click', '.delbuttonavk', function(){
            SiteTempVarAVKShop.elements = $(this);
            SiteTempVarAVKShop.query = {delete_product_cart: SiteTempVarAVKShop.elements.parents('form.del-form-avk').find('[name = action_del_cart_product]').attr('value')};
            SiteTempVarAVKShop.AJAX_Query_AVKShop(SiteTempVarAVKShop.query, '#all_product_cart', 'refresh', SiteTempVarAVKShop.elements);
            return false;
        });
        
        $('#cart-content-avkshop').on('click', '#action-shop-avk-further', function(){
            SiteTempVarAVKShop.elements = $(this);
            SiteTempVarAVKShop.value = $('[name = systems_pay_avkshop]:checked').attr('value');
            if(typeof SiteTempVarAVKShop.value == 'undefined'){
                SiteTempVarAVKShop.AJAX_Query_AVKShop({warning: 'no system pay'}, '', '');
            }else{
                SiteTempVarAVKShop.AJAX_Query_AVKShop({systems_pay: SiteTempVarAVKShop.value}, '#cart-content-avkshop', 'refresh', SiteTempVarAVKShop.elements);
            }
            return false;
        });
        
        $(this).on('click', '.popup_message_avkshop button', function(){
            $('.popup_window_mes_avkshop, .popup_message_avkshop').remove();
        });
    });
})(jQuery);