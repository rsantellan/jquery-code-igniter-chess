$(document).ready(function() {
   // put all your jQuery goodness in here.
   initUserAdmId("user_adm_id",$("#user_adm_id_ajax").val());
 });


function initUserAdmId(inputId, searchUrl)
{
  $("#" + inputId).autocomplete({
	source: searchUrl,
	minLength: 1
  });
  /*
  $("#" + inputId).autoSuggest(searchUrl, {
        startText: "",
        emptyText: "",
        limitText: "",
        minChars: 2,
        selectionLimit: 1
    });
	
	*/
}