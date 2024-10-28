(function($){
    $(document).ready(function(){
        if(typeof(QTags) !== 'undefined') {
            QTags.addButton( avkShopVarAdm.buttonTextAVK.download, avkShopVarAdm.buttonTextAVK.download, '<!--%buttonavk%-->','','que-marks-key',avkShopVarAdm.buttonTextAVK.download);
            QTags.addButton( avkShopVarAdm.buttonTextAVK.counter, avkShopVarAdm.buttonTextAVK.counter, '<!--%coudownloadavk%-->','','que-marks-key',avkShopVarAdm.buttonTextAVK.counter);
        }
        
        $('.avkshop_meta_middle').find("select").each(function(indx, element){
            $(element).css('text-align', 'center');
        });
        avkVars = { defValInp: avkShopVarAdm.defValInp,
                    delValInp: avkShopVarAdm.delValInp,
                    chekValue: '',
                    publishingAction: $('#publishing-action').html(),
                    chekId: '',
                /** Проверка валидности формы AVK-Shop */
                    chek_valid_input: function(object){
                                            var i = true;
                                            if('on' == object.eq(0).attr('value')){
                                                for(r = 0; r < object.length; r++){
                                                    // если выбран типтовара "бесплатный" не проверяет поле с ценой
                                                    if(object.eq(r).attr('name') == 'price_product_avk' && 'free' == $('#type_product_avk').attr('value')){
                                                        object.eq(r).attr('value','');
                                                        continue;
                                                    }
                                                    if(object.eq(r).attr('value') == avkVars.defValInp || object.eq(r).attr('value') == '' || object.eq(r).attr('value') == avkVars.delValInp){
                                                        $('#publishing-action').html('<botton class="button button-primary button-large" style="margin: 5px -7px auto auto;">' + avkShopVarAdm.textButton + '</botton>');
                                                        i = false;
                                                    }
                                                    if(i){
                                                        $('#publishing-action').html(avkVars.publishingAction);
                                                    }
                                                }
                                            }else{
                                                $('#publishing-action').html(avkVars.publishingAction);
                                            }
                                        }
                  };
        
        $('.helpimg').on('click', function(){
            if(avkVars.chekId == ''){
                descAvk = $(this).parent().next('dd.descavk');
                avkVars.chekId = descAvk.attr('id');
                descAvk.stop().slideDown(500);
            }else{
                descAvk = $(this).parent().next('dd.descavk');
                if(avkVars.chekId == descAvk.attr('id')){
                    descAvk.stop().slideUp(150);
                    avkVars.chekId = '';
                }else{
                    $('#'+avkVars.chekId).stop().slideUp(150);
                    descAvk.stop().slideDown(500);
                    avkVars.chekId = descAvk.attr('id');
                }
            }
        });
/*
        avkVars.chek_valid_input($('.inputavk'));
        
        $('.inputavk').on('blur focus', function(){
            avkVars.chek_valid_input($('.inputavk'));
        });
*/
        
        /** Окно для загрузки файла */
        $('#file_product_avk_title').on('click', 'button.button-primary-avk[href^=#]', function(){
            avkVars.popID = $(this).attr('rel');
            avkVars.popURL = $(this).attr('href');
            avkVars.query= avkVars.popURL.split('?');
            avkVars.dim= avkVars.query[1].split('&');
            avkVars.popWidth = avkVars.dim[0].split('=')[1];
            //$('#' + avkVars.popID).fadeIn().css({ 'width': Number( avkVars.popWidth ) }).prepend('<a href="#" class="close"><img src="' + avkShopVarAdm.urlImgClouse + '" class="btn_close" title="Close Window" alt="Close" /></a>');
            $('#' + avkVars.popID).fadeIn().css({ 'width': Number( avkVars.popWidth ) });
            avkVars.popMargTop = ($('#' + avkVars.popID).height() + 80) / 2;
            avkVars.popMargLeft = ($('#' + avkVars.popID).width() + 80) / 2;
            $('#' + avkVars.popID).css({'margin-top' : -avkVars.popMargTop, 'margin-left' : -avkVars.popMargLeft});
            $('body').append('<div id="fadebgavk"></div>');
            $('#fadebgavk').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); 
            return false;
        });
                        
        $('#file_product_avk_title').on('click', 'button.button-primary-avk[act^=add]', function(){
            avkVars.ajaxThis = $(this);
            avkVars.loading = $('.avk_loading');
            var button = $('#uploadButton'), interval;
            $.ajax_upload(button, {
                action : ajaxurl,
                name : "avk_file",
                data : {actionavk: 'addfile',
                        action: 'avkshop_upload',
                        avk_notice_sec: avkShopVarAdm.safety},
                onSubmit : function(file, ext){
                                avkVars.loading.css({'visibility':'visible'});
                                this.disable();
                          },
                onComplete : function(file, response) {
                                this.enable();
                                avkVars.loading.css({'visibility':'hidden'});
                                $.ajaxSetup({cache: false});
                                console.log(response);
                                // преобразуем строку в массив
                                response = JSON.parse(response);
                                // вывод данных
                                switch(response.type){
                                    case'error': $('#avk_error_upload').text(response.messeg).fadeIn(1000, function(){
                                                     $(this).fadeTo(3000,1).fadeOut(1000);
                                                 }); 
                                                 break;
                                    case'finish': $('#fadebgavk, .popup_block').fadeOut(function() {
                                                      $('#fadebgavk, a.close').remove();
                                                  });
                                                  $("#file_product_avk").attr('value',response.name);
                                                  if( $( "#new_name_product_avk" ).attr( 'value' ) === "" ){
                                                      $( "#new_name_product_avk" ).attr( 'value', response.newname );
                                                  }
                                                  avkVars.ajaxThis.remove();
                                                  $('<button></button>').attr({'class':'button-primary-del downloadavk button button-primary button-primary-avk',
                                                                               'act':'delavkshop',
                                                                               'href':'#?w=500',
                                                                               'rel':'popup_delete_file_avk'})
                                                                        .text(avkShopVarAdm.buttonTextAVK.deleteavk)
                                                                        .appendTo('#actbuttonavk');
                                                  break;
                                }
                          }
            });
        });
        
        //Закрыть всплывающие окна и затемненный фон
        $('.submit').on('click', 'input.clouse_pop_avk', function(){
            $('#fadebgavk, .popup_block').fadeOut(function() {
                $('#fadebgavk, a.close').remove();
            });
            return false;
        });
        
        $('#popup_delete_file_avk').on('click', '#delete_file_avk', function(){
            var data = {
            		action: 'avkshop_upload',
                    actionavk: 'delfile',
                    namefiledel: $('#file_product_avk').attr('value'),
                    avk_notice_sec: avkShopVarAdm.safety
            	};
            	$.ajaxSetup({cache: false});
                
            	$.post(ajaxurl, data, function(response){
                    response = JSON.parse(response);
                    switch(response.type){
                        case 'finish': $("#file_product_avk").attr('value',avkVars.delValInp);
                                       $("#actbuttonavk").empty();
                                       $('<button></button>').attr({'class':'downloadavk button button-primary button-primary-avk button-primary-act', 
                                                                    'href':'#?w=500',
                                                                    'rel':'popup_download_file_avk',
                                                                    'act':'addavkshop'})
                                                             .text(avkShopVarAdm.buttonTextAVK.upload).appendTo('#actbuttonavk');
                                       $('#fadebgavk, .popup_block').fadeOut(function(){ $('#fadebgavk, a.close').remove(); });
                                           break;
                        case 'error': $('#avk_error_delete').text(response.messeg).fadeIn(1000, function(){
                                                     $(this).fadeTo(3000,1).fadeOut(1000);
                                      });
                                          break;
                    }
            	});
            return false;
        });
    });
})(jQuery);