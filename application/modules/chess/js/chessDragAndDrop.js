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
  isAccepted = false;
  // Valido que la pieza pueda hacer el movimiento
  var isValid = isValidMoveOfElement(element, dropArea);
  if(isValid)
  {
	//Si la pieza puede hacer el movimiento me fijo si no existe
	//una pieza del mismo color en la casilla.
	var checking_place = isPlaceOcupied(element, dropArea);
	if(checking_place == 1)
	{
	  //El movimiento es invalido por que no puedo auto comerme
	  return false;
	}
	else
	{
	  //El movimiento es valido.
	  //Ahora tengo que chequear que no estoy dejando al rey expuesto.
	  var is_king_exposed = checkForCheckToTheKing(element, dropArea);
	}
	console.log(checking_place);
  //Si no es del mismo color entonces la come :)
	
  }
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
	
  //$(element).animate($(element).data('startPosition'), 500);
  }
  
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

/**
 * 
 * Este metodo va a retornar:
 *	  - 0 en caso de que el lugar este vacio.
 *	  - 1 en caso de que las dos piezas sean del mismo color.
 *	  - 2 en caso de que una pieza este comiendo a la otra.
 * 
 * 
 */
function isPlaceOcupied(element, dropArea)
{
  var elementCoordinates = calculateRowCol($(element).attr("position"));
  var dropAreaCoordinates = calculateRowCol($(dropArea).attr("position"));
  var piece_code = board_js[elementCoordinates[1]][elementCoordinates[0]];  
  
  var position_element = board_js[dropAreaCoordinates[1]][dropAreaCoordinates[0]];
  if(position_element == 0)
  {
	return 0;
  }
  
  if((piece_code < BLACK && position_element < BLACK) || (piece_code > BLACK && position_element > BLACK) )
  {
	return 1;
  }
  else
  {
	return 2;
  }
}

/**
 *
 * Devuelve true si el rey esta en peligro
 * Devuelve false si el rey esta a salvo
 *
 **/
function checkForCheckToTheKing(element, dropArea)
{
  var board_aux = board_js;
  var elementCoordinates = calculateRowCol($(element).attr("position"));
  var dropAreaCoordinates = calculateRowCol($(dropArea).attr("position"));
  board_aux[dropAreaCoordinates[1]][dropAreaCoordinates[0]] = board_aux[elementCoordinates[1]][elementCoordinates[0]];
  board_aux[elementCoordinates[1]][elementCoordinates[0]] = 0;
  var used_color = WHITE;
  var enemy_color = BLACK;
  if(board_aux[dropAreaCoordinates[1]][dropAreaCoordinates[0]] > BLACK)
  {
	used_color = BLACK;
	enemy_color = WHITE;
  }
  var found = false;
  var i = 0;
  var j = 0;
  var king_value = parseInt(KING) + parseInt(used_color);
  var king_col = 0;
  var king_row = 0;
  console.log(king_value);
  while(!found)
  {
	j = 0;
	while(j < 8 && !found)
	{
	  if(parseInt(board_aux[i][j]) == king_value)
	  {
		king_col = j;
		king_row = i;
		found = true;
	  }
	  else
	  {
		j = j+1;
	  }
	  
	}
	if(!found)
	{
	  i = i + 1;
	}
  }
  
  /* check for knights first */
  
  var is_check = validateIfKingIsInCheckByKnight(board_aux, king_row, king_col, enemy_color);
  
  if(is_check)
  {
	return is_check;
  }
  
  is_check = validateIfKingIsInCheckByBishopAndQueen(board_aux, king_row, king_col, enemy_color);
  if(is_check)
  {
	return is_check;
  }
  
  
  /* tactic: start at test pos and check all 8 directions for an attacking piece */
  /* directions:
    0 1 2
    7 * 3
    6 5 4
  */
  var pieceFound = new Array();
  for (var i = 0; i < 8; i++)
	pieceFound[i] = new GamePiece();
  var DEBUG = false;
  
  for (var i = 1; i < 8; i++)
  {
	if (((king_row - i) >= 0) && ((king_col - i) >= 0))
	  if ((pieceFound[0].piece == 0) && (board_aux[king_row - i][king_col - i] != 0))
	  {
		if (DEBUG)
		  alert("isSafe -> pieceFound[0] = " + board_aux[king_row - i][king_col - i] + "\ndist = " + i);

		pieceFound[0].piece = board_aux[king_row - i][king_col - i];
		pieceFound[0].dist = i;
	  }

	if ((king_row - i) >= 0)
	  if ((pieceFound[1].piece == 0) && (board_aux[king_row - i][king_col] != 0))
	  {
		if (DEBUG)
		  alert("isSafe -> pieceFound[1] = " + board_aux[king_row - i][king_col] + "\ndist = " + i);

		pieceFound[1].piece = board_aux[king_row - i][king_col];
		pieceFound[1].dist = i;
	  }

	if (((king_row - i) >= 0) && ((king_col + i) < 8))
	  if ((pieceFound[2].piece == 0) && (board_aux[king_row - i][king_col + i] != 0))
	  {
		if (DEBUG)
		  alert("isSafe -> pieceFound[2] = " + board_aux[king_row - i][king_col + i] + "\ndist = " + i);

		pieceFound[2].piece = board_aux[king_row - i][king_col + i];
		pieceFound[2].dist = i;
	  }

	if ((king_col + i) < 8)
	  if ((pieceFound[3].piece == 0) && (board_aux[king_row][king_col + i] != 0))
	  {
		if (DEBUG)
		  alert("isSafe -> pieceFound[3] = " + board_aux[king_row][king_col + i] + "\ndist = " + i);

		pieceFound[3].piece = board_aux[king_row][king_col + i];
		pieceFound[3].dist = i;
	  }

	if (((king_row + i) < 8) && ((king_col + i) < 8))
	  if ((pieceFound[4].piece == 0) && (board_aux[king_row + i][king_col + i] != 0))
	  {
		if (DEBUG)
		  alert("isSafe -> pieceFound[4] = " + board_aux[king_row + i][king_col + i] + "\ndist = " + i);

		pieceFound[4].piece = board_aux[king_row + i][king_col + i];
		pieceFound[4].dist = i;
	  }

	if ((king_row + i) < 8)
	  if ((pieceFound[5].piece == 0) && (board_aux[king_row + i][king_col] != 0))
	  {
		if (DEBUG)
		  alert("isSafe -> pieceFound[5] = " + board_aux[king_row + i][king_col] + "\ndist = " + i);

		pieceFound[5].piece = board_aux[king_row + i][king_col];
		pieceFound[5].dist = i;
	  }

	if (((king_row + i) < 8) && ((king_col - i) >= 0))
	  if ((pieceFound[6].piece == 0) && (board_aux[king_row + i][king_col - i] != 0))
	  {
		if (DEBUG)
		  alert("isSafe -> pieceFound[6] = " + board_aux[king_row + i][king_col - i] + "\ndist = " + i);

		pieceFound[6].piece = board_aux[king_row + i][king_col - i];
		pieceFound[6].dist = i;
	  }

	if ((king_col - i) >= 0)
	  if ((pieceFound[7].piece == 0) && (board_aux[king_row][king_col - i] != 0))
	  {
		if (DEBUG)
		  alert("isSafe -> pieceFound[7] = " + board_aux[king_row][king_col - i] + "\ndist = " + i);

		pieceFound[7].piece = board_aux[king_row][king_col - i];
		pieceFound[7].dist = i;
	  }
  }
  
  //console.log(pieceFound);
  
  console.log(king_col);
  console.log(king_row);
//locateKingInBoard(board_aux, used_color);
  
}