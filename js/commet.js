function Commet( o ){
    var
        $ = jQuery,
        xhr = null,
        time = null,
        resend = false;

    var
        self = {
            pullUrl: '/commet/pull',
            pushUrl: '/commet/push',
            room: 'default',
            successTimeout: 1000,
            errorTimeout: 1000,
            debug: false,
            pull: function(){
                xhr = $.ajax({
                    url: self.pullUrl,
                    async: true,
                    data: { room: self.room, time: time },
                    dataType: 'json',
                    global: false,
                    success: function( data ){
                        if( data.time )
                            time = data.time;

                        if( data.status ){
                            if( console && console.log && self.debug )
                                console.log( 'Received message:', data );

                            if( data.message ){
                                self.handle( data.message, data.mySession );
                            }
                        }

                        if( resend )
                            setTimeout(self.pull, self.successTimeout);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        if( resend )
                            setTimeout(self.pull, self.errorTimeout);

                        self.error(jqXHR, textStatus, errorThrown);
                    }
                });
            },
            push: function( message ){
                return $.ajax({
                    url: self.pushUrl,
                    async: true,
                    global: false,
                    data: { room: self.room, message: message }
                });
            },
            start: function(){
                resend = true;
                self.pull();
            },
            stop: function(){
                if( xhr )
                    xhr.abort();

                xhr = null;
                resend = false;
            },

            handle: function( message, mySession ){
                // noop
            },
            error: function( jqXHR, textStatus, errorThrown ){
                // noop
            },

            autoStart: true
        }

    self = jQuery.extend( self, o );

    if( self.autoStart )
        self.start();

    return self;
}