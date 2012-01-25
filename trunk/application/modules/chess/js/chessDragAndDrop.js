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

function getMovementQuantity()
{
  var myList = new Array();
  var i = 0;
  //Recorro todas las columnas
  for(i=0; i < 8; i++)
  {
    //Para cada columna recorro las filas buscando por color.
    var j = 0;
    for(j=0; j < 8; j++)
    {
        if(board_js[i][j] != 0)
        {
          //Si el casillero no es 0 (vacio) y es del color mio.
          if((player_is_white && board_js[i][j] < BLACK) || (!player_is_white && board_js[i][j] > BLACK))
          {
            var aux = new myGamePiece();
            aux.col = i;
            aux.row = j;
            aux.pieceCode = board_js[i][j];
            myList.push(aux);  
          }
        }
    }
  }
  var movementList = new Array();
  var index = 0;
  for(index = 0; index < myList.length; index++)
  {
    switch(retrievePieceType(myList[index].pieceCode))
    {
      
      case parseInt(BISHOP):
        var bishopMovement = isBishopPosibleMovement(myList[index].col, myList[index].row, myList[index].pieceCode);
        console.log('soy el alfil');
        console.log(bishopMovement);
        var forIndex = 0;
        for(forIndex = 0; forIndex < bishopMovement.length; forIndex++)
        {
          movementList.push(bishopMovement[forIndex]);
        }
      break;
      case parseInt(ROOK):
        var rookMovement = retrieveAllPosibleRookMovements(myList[index].col, myList[index].row, myList[index].pieceCode);
        var forIndex = 0;
        for(forIndex = 0; forIndex < rookMovement.length; forIndex++)
        {
          movementList.push(rookMovement[forIndex]);
        }
        //console.log("rook");
      break;
      default:
        //console.log('se chingo todo...');
        break;
    }
  }
  console.log(movementList);
}


function isBishopPosibleMovement(column, row, bishopCode)
{
  var bishopMovement = new Array();
  
  //Esta es la primera vez que defino las variables.
  var auxColumn = column;
  var finish = false;
  var auxRow = row;
  var quantityPassed = 0;
  //Primero chequeo los movimientos en diagonal arriba a la derecha.
  auxColumn ++;
  auxRow ++;
  while(!finish && auxColumn < 8 && auxRow < 8)
  {
    quantityPassed++;
    
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, bishopCode);
    
    if(aux_movement[1] != null)
    {
      bishopMovement.push(aux_movement[1]);
    }
    if(aux_movement[0] == false)
    {
      finish = true;
    }
    auxRow ++;
    auxColumn ++;
  }
  
  quantityPassed = 0;
  auxColumn = column;
  auxRow = row;
  finish = false;
  //Segundo chequeo los movimientos en diagonal abajo a la derecha.
  auxColumn ++;
  auxRow --;
  while(!finish && auxColumn < 8 && auxRow >= 0)
  {
    quantityPassed++;
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, bishopCode);
    if(aux_movement[1] != null)
    {
      bishopMovement.push(aux_movement[1]);
    }
    if(aux_movement[0] == false)
    {
      finish = true;
    }
    auxColumn ++;
    auxRow --;
  }
  
  quantityPassed = 0;
  
  auxRow = row;
  auxColumn = column;
  finish = false;
  //Tercero chequeo los movimientos en diagonal arriba a la izquierda.
  auxRow ++;
  auxColumn --;
  while(!finish && auxColumn >= 0 && auxRow < 8)
  {
    quantityPassed++;
    
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, bishopCode);
    if(aux_movement[1] != null)
    {
      bishopMovement.push(aux_movement[1]);
    }
    if(aux_movement[0] == false)
    {
      finish = true;
    }
    auxRow ++;
    auxColumn --;
  }
  
//  console.log('la cantidad de veces que paso fue: ' + quantityPassed);
  
  quantityPassed = 0;
  
  auxRow = row;
  auxColumn = column;
  finish = false;
  //Cuarto chequeo los movimientos en diagonal abajo a la izquierda.
  auxRow --;
  auxColumn --;
  while(!finish && auxColumn >= 0 && auxRow >= 0)
  {
    
    quantityPassed++;
    
    
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, bishopCode);
    if(aux_movement[1] != null)
    {
      bishopMovement.push(aux_movement[1]);
    }
    if(aux_movement[0] == false)
    {
      finish = true;
    }
    auxRow --;
    auxColumn --;
    
  }
//  console.log('la cantidad de veces que paso fue: ' + quantityPassed);
  //console.log(rookMovement);
  return bishopMovement;  
  
  
}

function isPiecePosibleMovement(startColumn, startRow, finishColumn, finishRow, pieceCode)
{
  var salida = new Array();
  //console.log('entre en isRookPosibleMovement');
  var starting_piece = board_js[finishColumn][finishRow];
  if((board_js[finishColumn][finishRow] != 0) && (player_is_white && board_js[finishColumn][finishRow] < BLACK) || (!player_is_white && board_js[finishColumn][finishRow] > BLACK))
  {
    //Estoy comiendo a uno de los mios por lo no me puedo mover mas para ese lado.
    if(pieceCode == 4)
    {
      console.log('estoy pasando por arriba de uno de los mios');
    }
    salida[0] = false;
    salida[1] = null;
  }
  else
  {
    //Si estoy aca entonces no estoy pasando por arriba de ninguno de los mios.
    //Lo unico que me queda es chequear que el movimiento sea valido.
    var board_aux = board_js;
    board_aux[startColumn][startRow] = 0;
    board_aux[finishColumn][finishRow] = pieceCode;
    var is_in_check = checkForMyKingSafety(board_aux);
    if(pieceCode == 4)
    {
      console.log('Estoy en jaque? : ' + is_in_check);
    }
    if(!is_in_check)
    {
      //No esta chequeado
      var auxBoardPos = board_js[finishColumn][finishRow];
      if(parseInt(starting_piece) == 0)
      {
        salida[0] = true;
      }
      else
      {
        salida[0] = false;
      }
      //El movimiento es valido. Por lo tanto lo puedo hacer :)
      var aux_movement = new myGamePieceMovement();
      aux_movement.startingCol = startColumn;
      aux_movement.startingRow = startRow;
      aux_movement.finishCol = finishColumn;
      aux_movement.finishRow = finishRow;
      aux_movement.pieceCode = pieceCode;
      salida[1] = aux_movement;
    }
    else
    {
      salida[0] = false;
      salida[1] = null;
    }
  }
  return salida;
}

function retrieveAllPosibleRookMovements(column, row, rookCode)
{
  var rookMovement = new Array();
  
  //Esta es la primera vez que defino las variables.
  var auxColumn = column;
  var finish = false;
  var auxRow = row;
  var quantityPassed = 0;
  //Primero chequeo los movimientos de las columna menores a 8
  auxColumn ++;
  while(!finish && auxColumn < 8)
  {
    quantityPassed++;
    
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, rookCode);
    
    if(aux_movement[1] != null)
    {
      rookMovement.push(aux_movement[1]);
    }
    if(aux_movement[0] == false)
    {
      finish = true;
    }
    auxColumn ++;
  }
  
  quantityPassed = 0;
  auxColumn = column;
  finish = false;
  //Segundo chequeo los movimientos de las columna mayores a 0
  auxColumn --;
  while(!finish && auxColumn >= 0)
  {
    quantityPassed++;
    auxRow = row;
    
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, rookCode);
    if(aux_movement[1] != null)
    {
      rookMovement.push(aux_movement[1]);
    }
    if(aux_movement[0] == false)
    {
      finish = true;
    }
    auxColumn --;
    
  }
  
  quantityPassed = 0;
  
  auxRow = row;
  finish = false;
  //Tercero chequeo los movimientos de las filas menores a 8
  auxRow ++;
  while(!finish && auxRow < 8)
  {
    quantityPassed++;
    auxColumn = column;
    
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, rookCode);
    if(aux_movement[1] != null)
    {
      rookMovement.push(aux_movement[1]);
    }
    if(aux_movement[0] == false)
    {
      finish = true;
    }
    auxRow ++;
  }
  
//  console.log('la cantidad de veces que paso fue: ' + quantityPassed);
  
  quantityPassed = 0;
  
  auxRow = row;
  finish = false;
  //Cuarto chequeo los movimientos de las filas mayores a 0
  auxRow --;
  while(!finish && auxRow >= 0)
  {
    
    quantityPassed++;
    auxColumn = column;
    
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, rookCode);
    if(aux_movement[1] != null)
    {
      rookMovement.push(aux_movement[1]);
    }
    if(aux_movement[0] == false)
    {
      finish = true;
    }
    auxRow --;
    
  }
//  console.log('la cantidad de veces que paso fue: ' + quantityPassed);
  //console.log(rookMovement);
  return rookMovement;
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
 * Devuelve false si el rey esta a salvo.
 * 
 **/ 
function checkForMyKingSafety(board_auxiliary)
{
//  console.log('estoy llamando a checkForMyKingSafety');
  var enemy_color = WHITE;
  if(player_is_white)
  {
    enemy_color = BLACK;
  }
  var is_check = checkForKingSafety(board_auxiliary, enemy_color);
//  console.log('El rey esta en jaque? ');
//  console.log(is_check);
  return is_check;
}

function checkForOtherKingSafety()
{
  var enemy_color = BLACK;
  if(player_is_white)
  {
    enemy_color = WHITE;
  }
  return checkForMyKingSafety(board_auxiliary, enemy_color);
}

function checkForKingSafety(board_auxiliary, enemy_color)
{
  var player_color = WHITE;
  if(enemy_color == player_color)
  {
    player_color = BLACK;
  }

  var kingPosition = retrieveKingPosition(player_color, board_auxiliary);
  
  
  var is_check = isKingInCheckByKnight(board_auxiliary, kingPosition.col, kingPosition.row, enemy_color);
  
  if(is_check)
  {
    return is_check;
  }
  
  is_check = validateIfKingIsInCheckByBishopAndQueen(board_auxiliary, kingPosition.row, kingPosition.col, enemy_color);
  if(is_check)
  {
    return is_check;
  }
  
  is_check = validateIfKingIsInCheckByRookAndQueen(board_auxiliary, kingPosition.row, kingPosition.col, enemy_color);
  if(is_check)
  {
    return is_check;
  }
  
  return is_check;
  
}

function isKingInCheckByKnight(board_auxiliary, col, row, enemy_color)
{
  /*
  console.log('el codigo del rey es : ' + parseInt(KING));
  console.log('Yo digo que el rey es : ' + board_auxiliary[col][row]);
  */
  /* check for knights first */
  for (var i = 0; i < 8; i++) {	// Check all eight possible knight moves
    var fromRow = row + knightMove[i][0];
    var fromCol = col + knightMove[i][1];
    if (isInBoard(fromRow, fromCol))
    {
      //console.log('estoy mirando los posibles caballos: ' + board_auxiliary[fromRow][fromCol]);
      //console.log('fromrow : ' + fromRow + ' fromCol : ' +fromCol); 
      //console.log('2 - estoy mirando los posibles caballos: ' + board_auxiliary[fromCol][fromRow]);
      //if (board_auxiliary[fromRow][fromCol] == (parseInt(KNIGHT) + parseInt(enemy_color)))
      if (board_auxiliary[fromCol][fromRow] == (parseInt(KNIGHT) + parseInt(enemy_color)))
      {
        // Enemy knight found
        return true;
      }
    }
  }
  return false;
  
  
} 


function retrieveKingPosition(search_color, board_auxiliary)
{
  var found = false;
  var i = 0;
  var kingPosition = new myGamePiece();
  var king_value = parseInt(KING) + parseInt(search_color);
  while(!found)
  {
    var j = 0;
    while(j < 8 && !found)
    {
      if(parseInt(board_auxiliary[i][j]) == king_value)
      {
        kingPosition.col = i;
        kingPosition.row = j;
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
  return kingPosition;
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
  
  is_check = validateIfKingIsInCheckByRookAndQueen(board_aux, king_row, king_col, enemy_color);
  console.log('El rey esta en jaque? ');
  console.log(is_check);
  if(is_check)
  {
	return is_check;
  }  
  
  
  
  return is_check;
  
  
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
