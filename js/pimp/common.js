//Сохранение данных по аяксу
(function($) {
    $.fn.formAjax = function(options) {

        var defaults = {
            callback: function(){ window.location.href="/order/"; },                                                    
            defaultMessage: $("<div></div>")
        };

        var opts = $.extend(defaults, options);

        this.find( "input, textarea" ).focus(function(){
            $(this).removeClass( "error" ).attr({title: ''});
        });

        this.submit(function(){
            var iForm = jQuery( this );

            jQuery.ajax({
                url: iForm.attr( 'action' ),
                type: iForm.attr( 'method' ),
                data: iForm.serialize(),
                success: function( data, status, xhr )
                {
                    try
                    {
                        iForm.find( ".error" ).removeClass( "error" );

                        var h = jQuery.parseJSON( xhr.getResponseHeader( "actionmessage" ) );
                        if ( h.type == "error" )
                            {
                            var v = jQuery.parseJSON( xhr.getResponseHeader( "X-JSON" )  );
                            for ( var field in v  )
                                {
                                iForm.find( "#"+field ).addClass( "error" ).attr({ title: v[field] });
                            }

                            jQuery( "<p>" + h.message + "</p>" ).splash({header: "Ошибка"});
                        }
                        else
                            {
                            jQuery( "<p>" + h.message + opts.defaultMessage.html() + "</p>" ).splash({header: "Сохранено", callback: opts.callback });
                        }
                    }
                    catch( e )
                    {
                        jQuery( "<p>Во время выполнения возникла ошибка.</p>" ).splash({header: "Ошибка"});   
                    }
                    $( "form#subscribe-form .placeholder" ).placeholder();  
                }
            });

            return false;
        });
        
        return this;
    };    
})(jQuery); 

//Всплывающее окошко
(function($) {
    $.fn.splash = function(options) {
                
        var defaults = {
            header: "",
            callback: function(){},
            modal: true,
            close: 5000
        };

        var opts = $.extend(defaults, options);
        var self = this;
        if ( opts.modal )    
            var overlay = $("<div class='splash-overlay'></div>").appendTo( $("body") ).fadeIn( "200" );
                             
        var wrapper = jQuery( "<div class='splash'><p>"+opts.header+"</p><p>"+this.html()+"</p><a class='close' href='javascript:void(0)'>Закрыть</a></div>" );
        wrapper.find( "a.close" )
        .click(function(){
            jQuery(this).parents( "div.splash" ).fadeOut( "300", function(){
                $(this).remove();
                if ( opts.modal )  
                    overlay.fadeOut( "300", function(){ $(this).remove(); } );
                opts.callback(); 
            });  
        });

        wrapper.appendTo( jQuery( "body" ) ).fadeIn( "200" );
        if ( opts.close > 0 )
            window.setTimeout( function(){
                if (wrapper)
                {
                    wrapper.fadeOut( "300", function(){
                        $(this).remove();
                        if ( opts.modal )
                            overlay.fadeOut( "300", function(){ $(this).remove(); } ); 
                        opts.callback();
                    });
                }
            }, opts.close );
        
        return wrapper;

    };    
})(jQuery);

//Рыбина для тектовых полей
(function($) {
    $.fn.placeholder = function(options) {

        var defaults = {
        };

        var opts = $.extend(defaults, options);
        this.each(function(){
            var o = $(this);
            
            $(this).focus(function(){
                if( $(this).val() == $(this).attr( 'alt' ) )   
                    $(this).val('').removeClass( "inactive" );
            });

            $(this).blur(function(){
                if ( $(this).val().length == 0 )
                    $(this).val( $(this).attr( 'alt' ) ).addClass( "inactive" );    
            });

            $(this).parents("form").eq(0).submit(function(){
                if( o.val() == o.attr( 'alt' ) )   
                    o.val('');    
            });
            
            $(this).parents("form").eq(0).find(".doSubmit").click(function(){
                if( o.val() == o.attr( 'alt' ) )   
                    o.val('');    
            });

            $(this).blur();
        });

        return this;

    };    
})(jQuery);  


(function($) {
    $.fn.zebra = function(options) {

        var defaults = {
            odd: "#fff",
            even: "#efefef"
        };

        var opts = $.extend(defaults, options);

        this.each(function(){
            var row = 1;
            var color;
            
            $(this).find("tr").each(function(){
                color = ( row % 2 ) ? opts.odd : opts.even;
                $(this).css({ background : color });
                ++row;
            })
        });
        
        return this;

    };    
})(jQuery); 