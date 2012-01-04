var isAccepted = false;

$(document).ready(function() {
  startDraggable();
  
  $( ".droppable" ).droppable(
  {
    drop: function( event, ui ) {
      isAccepted = acceptElement(ui.draggable, this);
    }
  }
  );
   
  $('#table_history').tableScroll({
    height:250
  });
});
 
function startDraggable()
{
  $( ".draggable" ).draggable({
    revert: function (element) {
        return !isAccepted;
      }
  });
}
 
function acceptElement(element, dropArea)
{
   var isValid = isValidMoveOfElement(element, dropArea);
  //swapElement(element, dropArea);
  return isValid;
}
 
function isValidMoveOfElement(element, dropArea)
{
  var elementCoordinates = calculateRowCol($(element).attr("position"));
  var dropAreaCoordinates = calculateRowCol($(dropArea).attr("position"));
  var piece_code = board_js[elementCoordinates[1]][elementCoordinates[0]];
  var tmpDir = 1;
  var tmpColor = WHITE;
  if(piece_code > BLACK)
  {
    piece_code = piece_code - BLACK;
    tmpDir = -1;
    tmpColor = BLACK;
  }
  var isValid = false;
  switch(piece_code)
  {
	case parseInt(PAWN):
	  isValid = isValidMovePawn(elementCoordinates[1], elementCoordinates[0], dropAreaCoordinates[1], dropAreaCoordinates[0], tmpDir);
	  console.log("pawn");
	  break;
	case parseInt(KNIGHT):
	  isValid = isValidMoveKnight(elementCoordinates[1], elementCoordinates[0], dropAreaCoordinates[1], dropAreaCoordinates[0]);
	  console.log("knight");
	  break;
	case parseInt(BISHOP):
	  isValid = isValidMoveBishop(elementCoordinates[1], elementCoordinates[0], dropAreaCoordinates[1], dropAreaCoordinates[0]);
	  console.log("bishop");
	  break;
	case parseInt(ROOK):
	  isValid = isValidMoveRook(elementCoordinates[1], elementCoordinates[0], dropAreaCoordinates[1], dropAreaCoordinates[0]);
	  console.log("rook");
	  break;
	case parseInt(QUEEN):
	  isValid = isValidMoveQueen(elementCoordinates[1], elementCoordinates[0], dropAreaCoordinates[1], dropAreaCoordinates[0]);
	  console.log("queen");
	  break;
	case parseInt(KING):
	  isValid = isValidMoveKing(elementCoordinates[1], elementCoordinates[0], dropAreaCoordinates[1], dropAreaCoordinates[0], tmpColor);
	  console.log("king");
	  break;
	default:	/* ie: not implemented yet */
	  console.log('se chingo');
  }
  if(isValid != true)
  {
	console.log('estoy aca');
	$(element).animate($(element).data('startPosition'), 500);
  }
  console.log(isValid);
  return isValid;
  
}
 
function revertMovement()
{
  
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
