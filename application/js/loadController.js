var loadController = function(options){
    this._initialize(options);
}

loadController.instance = null;
loadController.getInstance = function (){
    if(loadController.instance == null)
        loadController.instance = new loadController();
    return loadController.instance;
}

loadController.prototype = {
    _initialize : function(options){
        $(window).scroll(function(){
            //$('#upload_container').css("top", $(window).height()/2 + $(window).scrollTop() + "px");
            $('#message_container').css("top", $(window).height()/2 + $(window).scrollTop() + "px");
            $('#message_container').live('click', function(){ mdHideMessage(); });
        });
    },

    show: function(){
         $('#upload_container').css("top", $(window).height()/2 + $(window).scrollTop() + "px");
         $('#upload_container_overlay').css("height", $(document).height());
         $('#upload_container_overlay').show();
         $('#upload_container').show();
    },

    hide: function(){
         $('#upload_container_overlay').hide();
         if(typeof arguments[0] != undefined){
             $('#upload_container').fadeOut('slow', arguments[0]);
         } else{
             $('#upload_container').fadeOut('slow', function() {});
         }

    }
}

//shortcuts to use this controller more easy
loadController.getInstance();

function showLoading(){ loadController.getInstance().show(); }

function hideLoading(f){ loadController.getInstance().hide(f); }

function showMessage(text){
    var timer = (arguments[1] != undefined) ? arguments[1] : 2000;
    var hide = (arguments[2] != undefined) ? arguments[2] : true;
    //console.log(hide);
    //console.log(timer);
    $('#message_container').css("top", $(window).height()/2 + $(window).scrollTop() + "px");
    //console.log($('#message_container'));
    $('#message_container .progressWindow').html(text);
    //console.log($('#message_container'));
    $('#message_container').fadeIn('slow', function() {
                 setTimeout(function(){
                    mdHideMessage();
                        }, timer);
        });


}

function hideMessage(){
    $('#message_container').fadeOut('slow', function(){});
}
