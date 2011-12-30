basicFunctions = function(options){
	this._initialize();

}

basicFunctions.instance = null;

basicFunctions.getInstance = function (){
	if(basicFunctions.instance == null)
		basicFunctions.instance = new basicFunctions();
	return basicFunctions.instance;
}

basicFunctions.prototype = {
    _initialize: function(){
        
    },
	
	deleteRowAndReturnSystemMessage: function(myUrl, obj)
	{
	  var row = $(obj).parents('.tr_row_inlist');
	  showLoading();
	  $.ajax({
		url: myUrl,
		type: 'get',
		dataType: 'json',
		success: function(json){
			if(json.response == "OK")
			{
			  $(row).fadeOut('slow', function() {
				$(this).remove();
			  });
			  $("#yellow_message_title").html(json.options.ok_message);
			  $("#yellow_message_subtitle").html(json.options.ok_message_description);
			  $("#yellow_message_li_container").fadeIn('fast', function () {
				$(this).fadeOut(3000);
			  });
			}
			else 
			{

			}
          },
		complete: function()
		{
		  hideLoading();
		}
	  });
	}
}