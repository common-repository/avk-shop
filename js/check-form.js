(function($) {
    $(document).ready(function(){
        var checkFormAVK = {
            counter     : 0,
            errorForm   : new Array(),
            errorMsg    : '',
            backgroundColor : $('.inputregavk').css('background-color'),
            actionRegForm  : function(object){
                                    var id = object.attr('id');
                                    this.errorMsg = object.parent().prev('.errmsgrec');
                                    switch(id){
                                        case'userloginameavk' : var pattern = new RegExp(/^[a-zA-Z0-9_\-]{3,20}$/);                break;
                                        case'userpasswavk'    : var pattern = new RegExp(/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{3,15}/); break;
                                        case'usernicknameavk' : var pattern = new RegExp(/^[a-zA-Z0-9_\-]{3,20}$/);                break;
                                        case'userfirstnameavk': var pattern = new RegExp(/(^[A-ZА-Я]{1})([a-zа-я]{1,}?$)/);        break;
                                        case'userlastnameavk' : var pattern = new RegExp(/(^[A-ZА-Я]{1})([a-zа-я]{1,}$)/);         break;
                                        case'usermailavk'     : var pattern = new RegExp(/\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6}/);        break;
                                    }
                                    if(pattern.test(object.val())){
                                        if(object.parent().prev().is('.errmsgrec')){
                                            this.errorMsgAVK.hide(750);
                                        }
                                        object.css({'background-color': this.backgroundColor});                                        
                                        this.errorForm[this.counter] = false;
                                    }else{
                                        if(object.parent().prev().is('.errmsgrec')){
                                            this.errorMsg.show(750);
                                        }
                                        object.css({'background-color':'rgba(255, 0, 0, 0.5)'});
                                        this.errorForm[this.counter] = true;
                                    }
                                    ++this.counter;
                             }
        };
        
        $('.inputregavk').on('blur', function(eventObject){
            checkFormAVK.actionRegForm($(this));
        });
        $('#formregavk').submit(function(){
            checkFormAVK.counter = 0;
            $('.inputregavk').each(function(index, obj){
                checkFormAVK.actionRegForm($(obj));
            });
            if(checkFormAVK.errorForm[0] || checkFormAVK.errorForm[1] || checkFormAVK.errorForm[2] || checkFormAVK.errorForm[3] === true){
                return false;
            }
        });
    });
})(jQuery);