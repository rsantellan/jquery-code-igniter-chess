(function($) {
 /**
 * Extend Tabs to add strip spacer
 **/
 $.fn.extend($.ui.draggable.prototype,{
   _original_init : $.ui.draggable.prototype._init,
   _init: function() {
       this._original_init();
   },
   
   restartOriginalPosition: function()
   {
     //console.log(this);
     //console.log('jajaja');
   }
 });
})(jQuery);
