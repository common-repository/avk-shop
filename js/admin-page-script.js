(function($) {
    $(document).ready(function(){
        avkVars = {
            ChekId: ''
        };
        
        $('.helpimg').click(function(){
            if(avkVars.ChekId == ''){
                descAvk = $(this).parent().next('dd.descavk');
                avkVars.ChekId = descAvk.attr('id');
                descAvk.stop().slideDown(500);
            }else{
                descAvk = $(this).parent().next('dd.descavk');
                if(avkVars.ChekId == descAvk.attr('id')){
                    descAvk.stop().slideUp(150);
                    avkVars.ChekId = '';
                }else{
                    $('#'+avkVars.ChekId).stop().slideUp(150);
                    descAvk.stop().slideDown(500);
                    avkVars.ChekId = descAvk.attr('id');
                }
            }
        });
        
        //$('.avk_fieldset').slideToggle("slow");
        //$('.helpimg').tooltip({ show:{effect:"blind", duration:200} });// hide:{effect:"explode", duration:800},

        $(".fade").hide().fadeIn(1000).fadeTo(6000, 1).fadeOut(1000);
        
        $('a.poplight[href^=#]').click(function(){
            var popID = $(this).attr('rel');
            var popURL = $(this).attr('href');
            var query= popURL.split('?');
            var dim= query[1].split('&');
            var popWidth = dim[0].split('=')[1];
            $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) });
            var popMargTop = ($('#' + popID).height() + 80) / 2;
            var popMargLeft = ($('#' + popID).width() + 80) / 2;
            $('#' + popID).css({'margin-top' : -popMargTop, 'margin-left' : -popMargLeft});
            $('body').append('<div id="fade"></div>');
            $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
         
            return false;
        });
        
        $('a.close, #fade, .clouse_pop_avk').live('click', function() {
            $('#fade, .popup_block').fadeOut(function(){
                $('#fade, a.close').remove();
            });
            return false;
        });
        
    });
})(jQuery); 