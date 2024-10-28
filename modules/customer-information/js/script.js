( function( $ ) {
    $( document ).ready( function(){
        
        $( ".tabs" ).tabs();
        
        var tabUserAVKShop = {
            tbInlineWidth : 750,
            tbInlineHeight : 400,
            tempWidth: 0,
            resizeWindow: function(){
                            if( $( window ).outerWidth( true ) > 550 ){
                                this.tempWidth = $( window ).outerWidth( true ) - 400;
                                if( this.tempWidth > 550 ){
                                    this.tbInlineWidth = this.tempWidth; 
                                }else{
                                    this.tbInlineWidth = 550;
                                }
                            }else{
                                this.tbInlineWidth = 550;
                            }
                            if( $( window ).outerHeight( true ) > 200 ){
                                this.tempWidth = $( window ).outerHeight( true ) - 300;
                                if( this.tempWidth > 200 ){
                                    this.tbInlineHeight = this.tempWidth; 
                                }else{
                                    this.tbInlineHeight = 200;
                                }
                            }else{
                                this.tbInlineHeight = 200;
                            }
                          },
        };
        
        tabUserAVKShop.resizeWindow();
        
        $( ".thickbox-click" ).each( function( indx, element ){
            $( element ).attr( 'alt', $( element ).attr( 'alt' )
                        .replace( "400", tabUserAVKShop.tbInlineHeight )
                        .replace( "750", tabUserAVKShop.tbInlineWidth ) );
        });

    });
})( jQuery );