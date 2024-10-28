(function($) {
    $(document).ready(function(){
        $('#listinglogavkshop').on('click' , function(){
            if($(this).attr('checked') == 'checked'){
                $("input.check_log_list").each(function(indx, element){
                    $(element).attr('checked', true);
                });
            }else{
                $("input.check_log_list").each(function(indx, element){
                    $(element).attr('checked', false);
                });
            }
        });
        
        $(".fade").hide().fadeIn(1000).fadeTo(6000, 1).fadeOut(1000);
        
        $('#tabs-inside-content-avk input[type=password]').each(function(indx, elem){
            $('<div class="switch_input_field_avk"></div>').insertAfter(elem);
        });
        $('#tabs-inside-content-avk').on('click', '.switch_input_field_avk', function(){
            if(!$(this).is('.switch_input_field_click_avk')){
                $(this).addClass('switch_input_field_click_avk').prev('input[type=password]').attr('type', 'text');
            }else{
                $(this).removeClass('switch_input_field_click_avk').prev('input[type=text]').attr('type', 'password');
            }
        });
    });
})(jQuery);