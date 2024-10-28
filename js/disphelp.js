(function($) {
    $(document).ready(function(){
        var blokkDescAVK;
        $('.titleiformer').focus(function(){
            var inputWidth = $(this).outerWidth();
            var posLeft = inputWidth + $(this).offset().left + 20;
            blokkDescAVK = $(this).parents('.dttitleavk').next('dd.ddescavk');
            blokkDescAVK.stop().css({position: 'absolute', width: '200px', display:'block'})
                           .offset({top: $(this).offset().top, left: posLeft})
                           .hide().slideDown(150);
        }).blur(function(){
            blokkDescAVK.stop().css({display:'none'});
        });
    });
})(jQuery);