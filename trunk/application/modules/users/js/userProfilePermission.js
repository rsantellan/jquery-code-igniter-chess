$(document).ready(function() {
  
  $("a#add_user_profile_permission").fancybox({
	'hideOnOverlayClick'		: false,
	'onClosed'					: function() {
	    window.location.reload();
	}
  });
});

function changePermissionOfUserProfile(myUrl, permissionId, userProfileId, element)
{
  
  
  showLoading();
  $.ajax({
	url: myUrl,
	type: 'get',
	dataType: 'json',
	data: {'permissionId' : permissionId, 'userProfileId' : userProfileId, 'selected' : element.checked },
	success: function(json){
		if(json.response == "OK")
		{
		  
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
  /*
  console.log(myUrl);
  console.log(permissionId);
  console.log(userProfileId);
  console.log(element.checked);
  */
}