$(document).ready(function() {
  startDraggable();
  
  $( ".droppable" ).droppable(
  {
	drop: function( event, ui ) {
	  acceptElement(ui.draggable, this);
	}
  }
  );
   
  $('#table_history').tableScroll({
	height:150
  });
});
 
function startDraggable()
{
  $( ".draggable" ).draggable({
	revert: "invalid"
  });
}
 
function acceptElement(element, dropArea)
{
  //console.log(element);
  //console.log(dropArea);
   
  swapElement(element, dropArea);
  return true;
}
 
function swapElement(element, dropArea)
{
  
  //Lo primero seria poner en vacio de donde salio
  var parent_container_id = "#droppable_" + $(element).attr("position");
  $(parent_container_id).addClass("empty");
  var element_class = getElementChessClass(element);
  console.log(element_class);
  console.log(dropArea);
  var inner_div = $(dropArea).children('div');
  console.log(inner_div);
  //$(element).appendTo($(dropArea));
  var dropArea_class = getElementChessClass(inner_div);
  if(dropArea_class != "white_empty")
  {
	//$(inner_div).removeClass(dropArea_class);
	
	//Esta yendo a un lugar que no es vacio.
	//Por lo tanto tiene que comer lo que esta ahi.
	//Saco el dragable de ese lugar, y lo pongo vacio.
	
	//$(inner_div).draggable( "option", "disabled", true );
	//$(inner_div).remove();
	
  }
  /*
  console.log(dropArea_class);
  var elementCoordinates = calculateRowCol($(element).attr("position"));
   
  console.log(elementCoordinates);
  console.log($(dropArea).attr("position"));
  */
}
 
function getElementChessClass(element)
{
  var classes = $(element).attr('class').split(/\s+/);
  var match = "";
  
  for(var i = 0; i < classes.length; i++){
	var className = classes[i];
	if(className != "draggable" && className != "ui-draggable" && className != "ui-draggable-dragging" && className != "empty")
	{
	  match = className;
	}
  }
  return match;
}