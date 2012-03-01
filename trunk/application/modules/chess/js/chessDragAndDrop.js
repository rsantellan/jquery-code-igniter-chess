var isAccepted = false;
var myPosibleMovements = new Array();

$(document).ready(function() {
  startDraggable();
  
  $( ".droppable" ).droppable(
    {
      drop: function( event, ui ) {
        isAccepted = acceptElement(ui.draggable, this);
        return isAccepted;
      }
    }
  );
   
  $('#table_history').tableScroll({
    height:250
  });
  initMyMoves()
});
 
function startDraggable()
{
  if(can_move)
  {
    $( ".draggable" ).draggable({
      revert: function (element) {
        return !isAccepted;
      }
    });  
  }
  
}
 
function destroyDraggables()
{
  setTimeout(function() { $( ".draggable" ).draggable( "destroy" ); }, 1);
  
}

function initMyMoves()
{
  myPosibleMovements = getMovementQuantity();
  console.log(myPosibleMovements.length);
  separateMovementsByType();
}

function separateMovementsByType()
{
  var rookList = new Array();
  var bishopList = new Array();
  var knightList = new Array();
  var queenList = new Array();
  var kingList = new Array();
  var pawnList = new Array();
  var i=0;
  var aux = null;
  for(i = 0; i < myPosibleMovements.length; i++)
  {
    aux = myPosibleMovements[i];
    switch(retrievePieceType(aux.pieceCode))
    {
      case parseInt(PAWN):
//        console.log(aux);
        pawnList.push(1);
      break;
      case parseInt(KNIGHT):
        knightList.push(1);
      break;
      case parseInt(QUEEN):
        queenList.push(1);
      break;
      case parseInt(BISHOP):
        bishopList.push(1);
      break;
      case parseInt(ROOK):
        rookList.push(1);
      break;
      case parseInt(KING):
        kingList.push(1);
      break;
    }
  }
  
  console.log("Los peones son: " + pawnList.length);
  console.log("Los caballos son: " + knightList.length);
  console.log("Los queen son: " + queenList.length);
  console.log("Los alfiles son: " + bishopList.length);
  console.log("Los rook son: " + rookList.length);
  console.log("Los rey son: " + kingList.length);
}
 
function acceptElement(element, dropArea)
{
  isAccepted = false;
  
  var auxMovement = isPosibleMovement(element, dropArea);
  
  if(auxMovement == null)
  {
    return false;
  }
  else
  {
    //hago lo que tengo que hacer para mover la pieza
    //console.log(auxMovement);
//    console.log('muevo el elemento');
    swapElement(element, dropArea);
    //Me fijo si el movimiento es especial.
    //En caso de que lo sea entonces ejecuto esa accion.
    if(auxMovement.isKingRook == true)
    {
      //console.log('tengo que hacer el enroque');
      moveRookOfCastle(auxMovement.finishRow, auxMovement.finishCol, auxMovement.pieceCode);
    }
    if(auxMovement.isPawnTwoMoves == true)
    {
      console.log('tengo que comer al paso');
    }
    //Destruyo los draggables para que no mueva hasta que mueva el otro.
    //console.log('saco los draggables');
    destroyDraggables();
    //Hago la llamada ajax
    //console.log('mando el movimiento al servidor ;) ');
    sendDataAndConfirmMovement(auxMovement);
    //console.log('retorno');
    return true;
  }
  return false;
}


function showDialogOfPromotion()
{
  $( "#promoting_dialog_box" ).dialog({
    closeOnEscape: false,
    open: function(event, ui) {
       $(".ui-dialog-titlebar-close", ui.dialog).hide(); 
    },
    modal: true,
    buttons: {
				"Promover": function() {
          $.ajax({
            url: $("#promotion_form").attr('action'),
            data: $("#promotion_form").serialize(),
            type: 'post',
            dataType: 'json',
            success: function(json){
              console.log('success');
              console.log('en caso de que me devuelva bien tengo que poner el peon con la nueva forma');
            }
          });
					$( this ).dialog( "close" );
				}
    }
  });
}

function clearPromotionForm()
{
  $("#inp_player_is_white").val("");
  $("#inp_startingRow").val("");
  $("#inp_startingCol").val("");
  $("#inp_finishRow").val("");
  $("#inp_finishCol").val("");
  $("#inp_gameId").val("");
}

function sendDataAndConfirmMovement(movement)
{
  console.log(movement);
  if(!enable_ajax)
  {
    return false;
  }
  //return false;
  
  //Si esta por coronar tengo que darle las opciones de lo mismo.
  // Tengo que fijarme que es un peon.
  // Si es negro y esta por ir a la casilla 0 esta coronando.
  // Si es blanco y esta por ir a la casilla 7 esta coronando
  
  if(retrievePieceType(movement.pieceCode) == PAWN)
  {
    console.log('soy un peon');
    if((movement.pieceCode > BLACK && movement.finishCol == 0) || (movement.pieceCode < BLACK && movement.finishCol == 7))
    {
      $("#inp_player_is_white").val(player_is_white);
      $("#inp_startingRow").val(movement.startingRow);
      $("#inp_startingCol").val(movement.startingCol);
      $("#inp_finishRow").val(movement.finishRow);
      $("#inp_finishCol").val(movement.finishCol);
      $("#inp_gameId").val(gameId);
      showDialogOfPromotion();
      console.log('soy un peon que esta coronando');
      return false;
    }
  }
  
  
  
  
  $.ajax({
    url: send_movement_url,
    data: {'player_is_white' : player_is_white ,'startingRow': movement.startingRow, 'startingCol': movement.startingCol, 'finishRow': movement.finishRow, 'finishCol': movement.finishCol, 'gameId': gameId},
    type: 'post',
    dataType: 'json',
    success: function(json){
      if(json.response == "OK")
      {
        //$('#complicacion_'+json.options.id).fadeOut("slow", function(){$(this).remove();});
        //$.fancybox.close();
      }
      else
      {
        //$.fancybox.resize();                
      }
    }, 
    complete: function()
    {
      //$.fancybox.hideActivity();
    }
  });  
}

function isPosibleMovement(element, dropArea)
{
  //console.log('isPosibleMovement');
  var elementCoordinates = calculateRowCol($(element).attr("position"));
  var dropAreaCoordinates = calculateRowCol($(dropArea).attr("position"));
  var piece_code = board_js[elementCoordinates[1]][elementCoordinates[0]];
  /*
  console.log('posicion inicial');
  console.log(elementCoordinates[1]);
  console.log(elementCoordinates[0]);
  console.log('esto seria en board_js algo asi como');
  console.log(board_js[elementCoordinates[1]][elementCoordinates[0]]);
  console.log('posicion final');
  console.log(dropAreaCoordinates[1]);
  console.log(dropAreaCoordinates[0]);
  */
  var index = 0;
  var found = false;
  var auxReturn = null;
  while(index < myPosibleMovements.length && !found)
  {
    var auxMovement = myPosibleMovements[index];
    
    if(elementCoordinates[1] == auxMovement.startingCol && elementCoordinates[0] == auxMovement.startingRow)
    {
      if(dropAreaCoordinates[1] == auxMovement.finishCol && dropAreaCoordinates[0] == auxMovement.finishRow)
      {
        found = true;
        auxReturn = auxMovement;
      }
    }
    /*
    if(auxMovement.pieceCode == 160)
    {
      console.log('aca estaria buscando algo asi...:');
      console.log('la pieza es: ' + auxMovement.pieceCode);
      console.log('startingCol: ' + auxMovement.startingCol);
      console.log('startingRow: ' + auxMovement.startingRow);
      console.log('finishCol: ' + auxMovement.finishCol);
      console.log('finishRow: ' + auxMovement.finishRow);
    }
    */
    index++;
  }
  
  return auxReturn;
}


function revertMovement()
{
  console.log('revertMovement');
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
      case parseInt(PAWN):
        var pawnMovement = retrieveAllPosiblePawnMovement(myList[index].col, myList[index].row, myList[index].pieceCode);
//        console.log(pawnMovement);
        var forIndex = 0;
        for(forIndex = 0; forIndex < pawnMovement.length; forIndex++)
        {
          movementList.push(pawnMovement[forIndex]);
        }
      break;
      
      case parseInt(KNIGHT):
        var knightMovement = retrieveAllPosibleKnightMovement(myList[index].col, myList[index].row, myList[index].pieceCode);
        var forIndex = 0;
        for(forIndex = 0; forIndex < knightMovement.length; forIndex++)
        {
          movementList.push(knightMovement[forIndex]);
        }
      break;
      case parseInt(QUEEN):
        var bishopQMovement = isBishopPosibleMovement(myList[index].col, myList[index].row, myList[index].pieceCode);
        var forIndex = 0;
        for(forIndex = 0; forIndex < bishopQMovement.length; forIndex++)
        {
          movementList.push(bishopQMovement[forIndex]);
        }
        var rookQMovement = retrieveAllPosibleRookMovements(myList[index].col, myList[index].row, myList[index].pieceCode);
        forIndex = 0;
        for(forIndex = 0; forIndex < rookQMovement.length; forIndex++)
        {
          movementList.push(rookQMovement[forIndex]);
        }
      break;
      
      case parseInt(BISHOP):
        var bishopMovement = isBishopPosibleMovement(myList[index].col, myList[index].row, myList[index].pieceCode);
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
      case parseInt(KING):
        var kingMovement = retrieveAllPosibleKingMovement(myList[index].col, myList[index].row, myList[index].pieceCode);
        var forIndex = 0;
        for(forIndex = 0; forIndex < kingMovement.length; forIndex++)
        {
          movementList.push(kingMovement[forIndex]);
        }
      break;
      default:
        //console.log('se chingo todo...');
        break;
    }
  }
  
  return movementList;
  console.log(movementList);
}

function retrieveAllPosiblePawnMovement(column, row, pawnCode)
{
  var pawnMove = new Array();
  var pawnRow = 1;
  if(!player_is_white) pawnRow = 6;  
  
  var moveForward = 1;
  if(!player_is_white) moveForward = -1;
  //Chequeo el avance simple
  var auxColumn = column + moveForward;
  var auxRow = row;
  if(isInBoard(auxColumn, auxRow) && board_js[auxColumn][auxRow] == 0)
  {
    //console.log('el lugar esta vacio por lo tanto se puede mover');
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, pawnCode);
    if(aux_movement[1] != null)
    {
      pawnMove.push(aux_movement[1]);
    }
  }

  //Chequeo si puedo comer a los costados.
  //primero chequeo a la derecha
  auxColumn = column + moveForward;
  auxRow = row + moveForward;
  if(isInBoard(auxColumn, auxRow) 
      && (board_js[auxColumn][auxRow] != 0) 
      && 
      ((player_is_white && board_js[auxColumn][auxRow] > BLACK) || (!player_is_white && board_js[auxColumn][auxRow] < BLACK)))
  {
    //console.log('hay una pieza enemiga por lo tanto puedo comer');
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, pawnCode);
    if(aux_movement[1] != null)
    {
      pawnMove.push(aux_movement[1]);
    }
  }
  
  //despues chequeo a la izquierda
  auxColumn = column + moveForward;
  auxRow = row - moveForward;
  if(isInBoard(auxColumn, auxRow) 
      && (board_js[auxColumn][auxRow] != 0) 
      && ((player_is_white && board_js[auxColumn][auxRow] > BLACK) || (!player_is_white && board_js[auxColumn][auxRow] < BLACK)))
  {
    //console.log('hay una pieza enemiga por lo tanto puedo comer');
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, pawnCode);
    if(aux_movement[1] != null)
    {
      pawnMove.push(aux_movement[1]);
    }
  }
  
  //Chequeo si se puede mover dos filas, dado que nunca se movio.
  
  if(column == pawnRow)
  {
    //console.log('el peon nunca se movio');
    auxColumn = column + moveForward + moveForward;
    auxRow = row;
    if(board_js[auxColumn][auxRow] == 0)
    {
      //console.log('el lugar esta vacio por lo tanto se puede mover');
      var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, pawnCode);
      //console.log(aux_movement);
      if(aux_movement[1] != null)
      {
        pawnMove.push(aux_movement[1]);
      }
    }
  }
  
  //Ahora el ultimo chequeo seria el poder hacer que el peon coma al paso.
  //Para eso tengo que chequear que el ultimo movimiento sea de un peon.
  
  var last_movement = history_js[history_js.length -1];
  //console.log(last_movement);
  if(last_movement != undefined && last_movement.curPiece == "pawn")
  {

    console.log(last_movement);
    console.log(last_movement.fromRow - last_movement.toRow);
    console.log(last_movement.fromCol - last_movement.toCol);
    console.log(Math.abs(last_movement.fromCol - last_movement.toCol) == 2);
    console.log(Math.abs(last_movement.fromCol - last_movement.toCol) == 2);
    
    //if(Math.abs(last_movement.fromCol - last_movement.toCol) == 2)
    if(Math.abs(last_movement.fromRow - last_movement.toRow) == 2)
    {
      //Tengo que chequear que este al lado del peon que estoy moviendo.
      
      console.log(column);
      console.log(row);
      console.log('historico');
      console.log(last_movement.toCol);
      console.log(last_movement.toRow);
      
      if(last_movement.toRow == row + 1 && column == last_movement.toCol)
      {
        //Esta al lado!!!
        //Entonces puedo comer ;)
        var aux_movement = isPiecePosibleMovement(column, row, column + moveForward, row + 1, pawnCode);
        if(aux_movement[1] != null)
        {
          //console.log('estoy aca 1');
          pawnMove.push(aux_movement[1]);
        }
      }
      if(last_movement.toRow == row - 1 && column == last_movement.toCol)
      {
        //Esta al lado!!!
        //Entonces puedo comer ;)
        var aux_movement = isPiecePosibleMovement(column, row, column + moveForward, row - 1, pawnCode);
        if(aux_movement[1] != null)
        {
          //console.log('estoy aca 2');
          pawnMove.push(aux_movement[1]);
        }
      }
      
    }
  }
  
  //console.log('los movimientos del peon es:');
  //console.log(pawnMove);
  return pawnMove;
}

function retrieveAllPosibleKingMovement(column, row, kingCode)
{
  //console.log('retrieveAllPosibleKingMovement');
  var kingMovement = new Array();
  
  for(var i = 0; i < kingMove.length; i++)
  {
    var auxRow = row + kingMove[i][0];
    var auxColumn = column + kingMove[i][1];
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, kingCode);
    if(aux_movement[1] != null)
    {
      kingMovement.push(aux_movement[1]);
    }
  }
  var castleMoves = returnKingCastleMovements();
  //console.log(castleMoves);
  for(var j = 0; j < castleMoves.length; j++)
  {
    kingMovement.push(castleMoves[j]);
  } 
  //console.log('Los movimientos del rey son: ');
  //console.log(kingMovement);
  return kingMovement;
}

function retrieveAllPosibleKnightMovement(column, row, knigthCode)
{
  //console.log('retrieveAllPosibleKnightMovement');
  var knightMovement = new Array();
  
  for (var i = 0; i < 8; i++) {	// Check all eight possible knight moves
    var auxRow = row + knightMove[i][0];
    var auxColumn = column + knightMove[i][1];
    var aux_movement = isPiecePosibleMovement(column, row, auxColumn, auxRow, knigthCode);
    if(aux_movement[1] != null)
    {
      knightMovement.push(aux_movement[1]);
    }
  }
//  console.log(knightMovement);
  return knightMovement;  
}

function isBishopPosibleMovement(column, row, bishopCode)
{
  //console.log('isBishopPosibleMovement');
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
    if(aux_movement[0] == false && aux_movement[2] == false)
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
    var aux_movement1 = isPiecePosibleMovement(column, row, auxColumn, auxRow, bishopCode);
    if(aux_movement1[1] != null)
    {
      bishopMovement.push(aux_movement1[1]);
    }
    if(aux_movement1[0] == false && aux_movement[2] == false)
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
    if(aux_movement[0] == false && aux_movement[2] == false)
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
    if(aux_movement[0] == false && aux_movement[2] == false)
    {
      finish = true;
    }
    auxRow --;
    auxColumn --;
    
  }
//  console.log('la cantidad de veces que paso fue: ' + quantityPassed);
//  console.log(bishopMovement);
  return bishopMovement;  
  
  
}

/**
 * 
 * Devuelve un array.
 *  0 - Si esta comiendo
 *  1 - El movimiento que podria hacer
 *  2 - Si ese movimiento no es valido por que esta en jaque.
 * 
 **/ 
function isPiecePosibleMovement(startColumn, startRow, finishColumn, finishRow, pieceCode)
{
  var salida = new Array();
  
  if(!isInBoard(finishColumn, finishRow))
  {
    salida[0] = false;
    salida[1] = null;
    salida[2] = false;
    return salida;
  }
  
  //console.log('entre en isPiecePosibleMovement');
  var starting_piece = board_js[finishColumn][finishRow];
  if((board_js[finishColumn][finishRow] != 0) && (player_is_white && board_js[finishColumn][finishRow] < BLACK) || (!player_is_white && board_js[finishColumn][finishRow] > BLACK))
  {
    //Estoy comiendo a uno de los mios por lo no me puedo mover mas para ese lado.
    salida[0] = false;
    salida[1] = null;
    salida[2] = false;
  }
  else
  {
    //Si estoy aca entonces no estoy pasando por arriba de ninguno de los mios.
    //Lo unico que me queda es chequear que el movimiento sea valido.
    //var newObject = jQuery.extend(true, {}, board_js);
    //console.log('la copia del objecto es');
    //console.log(newObject);
    var board_aux = jQuery.extend(true, {}, board_js);// board_js;
    board_aux[startColumn][startRow] = 0;
    
    //console.log('antes de moverse la pieza era: ' + board_aux[finishColumn][finishRow]);
    
    board_aux[finishColumn][finishRow] = pieceCode;
    /*
    console.log('tendria que hacer lo mismo que para acceder como un array');
    console.log('esta seria la nueva pieza :  ' + board_aux[finishColumn][finishRow] );
    console.log('esto no deberia de ser lo mismo que lo anterior : ' + newObject[finishColumn][finishRow]);
    */ 
    var is_in_check = checkForMyKingSafety(board_aux);
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
      salida[2] = false;
    }
    else
    {
      salida[0] = false;
      salida[1] = null;
      salida[2] = true;
    }
  }
  return salida;
}

function retrieveAllPosibleRookMovements(column, row, rookCode)
{
  //console.log('retrieveAllPosibleRookMovements');
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
    if(aux_movement[0] == false && aux_movement[2] == false)
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
    if(aux_movement[0] == false && aux_movement[2] == false)
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
    if(aux_movement[0] == false && aux_movement[2] == false)
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
    if(aux_movement[0] == false && aux_movement[2] == false)
    {
      finish = true;
    }
    auxRow --;
    
  }
//  console.log('la cantidad de veces que paso fue: ' + quantityPassed);
  //console.log(rookMovement);
  return rookMovement;
}

function moveRookOfCastle(kingRow, kingColumn, pieceCode)
{
  var isWhite = true;
  var piece_name = "";
  if(pieceCode > BLACK)
  {
    piece_name = "black_";
    isWhite = false;
  }
  else
  {
    piece_name = "white_";
  }
  piece_name = piece_name + "rook";
  console.log('piece_name : ' + piece_name);
  //el rey es - kingRow : 2 kingColumn : 7 isWhite :false
  if(kingRow == 2)
  {
    if(!isWhite)
    {
      $('#droppable_59').find('div').removeClass('empty').addClass(piece_name).addClass('draggable');
      $('#droppable_56').find('div').removeClass(piece_name).removeClass('draggable').addClass('empty');
    }
    else
    {
      $('#droppable_3').find('div').removeClass('empty').addClass(piece_name).addClass('draggable');
      $('#droppable_0').find('div').removeClass(piece_name).removeClass('draggable').addClass('empty');
    }
  }
  if(kingRow == 6)
  {
    if(!isWhite)
    {
      $('#droppable_61').find('div').removeClass('empty').addClass(piece_name).addClass('draggable');
      $('#droppable_63').find('div').removeClass(piece_name).removeClass('draggable').addClass('empty');
    }
    else
    {
      $('#droppable_5').find('div').removeClass('empty').addClass(piece_name).addClass('draggable');
      $('#droppable_7').find('div').removeClass(piece_name).removeClass('draggable').addClass('empty');
    }
  }
  console.log(' el rey es - kingRow : ' + kingRow + ' kingColumn : ' +kingColumn + ' isWhite :' +isWhite);
}
 
function swapElement(element, dropArea)
{
//  console.log('swapElement');
  
//  console.log($(dropArea).find('div'));
  $(dropArea).find('div').replaceWith($(element).removeAttr("style"));
  
//  console.log(element);
//  console.log(dropArea);
  return true;
  
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
}
 
function getElementChessClass(element)
{
  console.log('getElementChessClass');
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
  console.log('checkForOtherKingSafety');
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
  
  is_check = isKingInCheckByBishopOrQueen(board_auxiliary, kingPosition.col, kingPosition.row, enemy_color);
  if(is_check)
  {
    return is_check;
  }
  
  is_check = isKingInCheckByRookOrQueen(board_auxiliary, kingPosition.col, kingPosition.row, enemy_color);
  if(is_check)
  {
    return is_check;
  }
  
  is_check = isKingInCheckByPawn(board_auxiliary, kingPosition.col, kingPosition.row, enemy_color);
  if(is_check)
  {
    return is_check;
  }
  
  is_check = isKingInCheckByKing(board_auxiliary, kingPosition.col, kingPosition.row, enemy_color);
  if(is_check)
  {
    return is_check;
  }
  
  return is_check;
  
}

function isKingInCheckByKing(board_auxiliary, col, row, enemy_color)
{
  /* Check for king */
  for (var i = 0; i < 8; i++) {	// Check all eight possible knight moves
    var fromRow = row + kingMove[i][0];
    var fromCol = col + kingMove[i][1];
    if (isInBoard(fromRow, fromCol))
    {
      //if (board_auxiliary[fromRow][fromCol] == (parseInt(KNIGHT) + parseInt(enemy_color)))
      if (board_auxiliary[fromCol][fromRow] == (parseInt(KING) + parseInt(enemy_color)))
      {
        // Enemy king found
        return true;
      }
    }
  }
  return false;
}

function isKingInCheckByPawn(board_auxiliary, col, row, enemy_color)
{
  //Los peones solo van para adelante.
  //Por lo tanto lo unico que tendria que chequear seria las dos diagonales
  //En direccion de las piezas contrarias.
  
  //Si es blanca seria.
  // [+1, +1], [-1, +1]
  //Si es Negra seria.
  // [+1, -1], [-1, -1]
  var auxColLeft = col - 1;
  var auxColRight = col + 1;
  var auxRow = row;
  if(enemy_color == BLACK)
  {
    auxRow++;
  }
  else
  {
    auxRow--;
  }
  //Chequeo a la izquierda.
  if(isInBoard(auxRow, auxColLeft))
  {
    if (board_auxiliary[auxColLeft][auxRow] == (parseInt(PAWN) + parseInt(enemy_color)))
    {
      return true;
    }
  }
  //Chequeo a la derecha.
  if(isInBoard(auxRow, auxColRight))
  {
    if (board_auxiliary[auxColRight][auxRow] == (parseInt(PAWN) + parseInt(enemy_color)))
    {
      return true;
    }
  }
  
  return false;  
  
}

function auxIsKingInCheckByRookOrQueen(board_auxiliary, col, row, enemy_color)
{
  if(isInBoard(row, col))
  {
    //console.log(" del costado es : " + board_auxiliary[col][row]);
    if (board_auxiliary[col][row] == (parseInt(ROOK) + parseInt(enemy_color)) || board_auxiliary[col][row] == (parseInt(QUEEN) + parseInt(enemy_color)))
    {
      //Enemy Rook or Queen found
      return 2;
    }
    else
    {
      if(board_auxiliary[col][row] != 0)
      {
        return 1;
      }
    }
  }
  else
  {
    return 1;
  }
  return 0;
}

/**
 * 
 * Chequeo por torres y reinas
 * 
 */
function isKingInCheckByRookOrQueen(board_auxiliary, col, row, enemy_color)
//function validateIfKingIsInCheckByRookAndQueen(myBoard, row, col, enemy_color)
{
  //console.log('isKingInCheckByRookOrQueen');
  /** 
   * La forma de chequear va a ser.
   * Chequeo:
   *  - para arriba
   *  - para abajo
   *  - a la derecha
   *  - a la izquierda
   *  
   **/

  var finish_rook_check = false;
  var aux_col_king = col;
  var aux_row_king = row;
  
  // arriba
  while(!finish_rook_check)
  {
    aux_col_king = parseInt(aux_col_king);
    aux_row_king = parseInt(aux_row_king) + 1;
  
    var result = auxIsKingInCheckByRookOrQueen(board_auxiliary, aux_col_king, aux_row_king, enemy_color);
    switch(result)
    {
      case 2:
        return true;
        break;
      case 1:
        finish_rook_check = true;
        break;
    }
  }
  
  finish_rook_check = false;
  aux_col_king = col;
  aux_row_king = row;
  
  // abajo
  while(!finish_rook_check)
  {
    aux_col_king = parseInt(aux_col_king);
    aux_row_king = parseInt(aux_row_king) - 1;
    var result = auxIsKingInCheckByRookOrQueen(board_auxiliary, aux_col_king, aux_row_king, enemy_color);
    switch(result)
    {
      case 2:
        return true;
        break;
      case 1:
        finish_rook_check = true;
        break;
    }
  }
  
  finish_rook_check = false;
  aux_col_king = col;
  aux_row_king = row;
  
  // a la derecha
  while(!finish_rook_check)
  {
    aux_col_king = parseInt(aux_col_king) + 1;
    aux_row_king = parseInt(aux_row_king);
    var result = auxIsKingInCheckByRookOrQueen(board_auxiliary, aux_col_king, aux_row_king, enemy_color);
    switch(result)
    {
      case 2:
        return true;
        break;
      case 1:
        finish_rook_check = true;
        break;
    }
  }
  
  finish_rook_check = false;
  aux_col_king = col;
  aux_row_king = row;
  
  // a la izquierda
  while(!finish_rook_check)
  {
    aux_col_king = parseInt(aux_col_king) - 1;
    aux_row_king = parseInt(aux_row_king);
    var result = auxIsKingInCheckByRookOrQueen(board_auxiliary, aux_col_king, aux_row_king, enemy_color);
    switch(result)
    {
      case 2:
        return true;
        break;
      case 1:
        finish_rook_check = true;
        break;
    }
  }
  //console.log("no estoy siendo atacado ni por la reina, ni por el alfil");
  return false;
}

function auxIsKingInCheckByBishopOrQueen(board_auxiliary, col, row, enemy_color)
{
  if(isInBoard(row, col))
  {
    //console.log(" la diagonal es : " + board_auxiliary[col][row]);
    if (board_auxiliary[col][row] == (parseInt(BISHOP) + parseInt(enemy_color)) || board_auxiliary[col][row] == (parseInt(QUEEN) + parseInt(enemy_color)))
    {
      //Enemy Bishop or Queen found
      return 2;
    }
    else
    {
      if(board_auxiliary[col][row] != 0)
      {
        return 1;
      }
    }
  }
  else
  {
    return 1;
  }
  return 0;
}

/** 
 *
 * Chequeo por alfiles o reina en verticales  
 *
 **/
function isKingInCheckByBishopOrQueen(board_auxiliary, col, row, enemy_color)
{
  /** 
   * La forma de chequear va a ser.
   * Chequeo:
   *  - arriba a la derecha
   *  - arriba a la izquierda
   *  - abajo a la derecha
   *  - abajo a la izquierda
   **/

  //console.log('el codigo del rey es : ' + parseInt(KING));
  //console.log('Yo digo que el rey es : ' + board_auxiliary[col][row]);

  var finish_bishop_check = false;
  var aux_col_king = col;
  var aux_row_king = row;
  
  // arriba a la derecha
  while(!finish_bishop_check)
  {
    aux_col_king = parseInt(aux_col_king) + 1;
    aux_row_king = parseInt(aux_row_king) + 1;
    var result = auxIsKingInCheckByBishopOrQueen(board_auxiliary, aux_col_king, aux_row_king, enemy_color);
    switch(result)
    {
      case 2:
        return true;
        break;
      case 1:
        finish_bishop_check = true;
        break;
    }
    
  }
  
  finish_bishop_check = false;
  aux_col_king = col;
  aux_row_king = row;
  
  // arriba a la izquierda 
  while(!finish_bishop_check)
  {
    aux_col_king = parseInt(aux_col_king) - 1;
    aux_row_king = parseInt(aux_row_king) + 1;
    var result = auxIsKingInCheckByBishopOrQueen(board_auxiliary, aux_col_king, aux_row_king, enemy_color);
    switch(result)
    {
      case 2:
        return true;
        break;
      case 1:
        finish_bishop_check = true;
        break;
    }
  }
  
  finish_bishop_check = false;
  aux_col_king = col;
  aux_row_king = row;
  
  // abajo a la derecha
  while(!finish_bishop_check)
  {
    aux_col_king = parseInt(aux_col_king) + 1;
    aux_row_king = parseInt(aux_row_king) - 1;
    var result = auxIsKingInCheckByBishopOrQueen(board_auxiliary, aux_col_king, aux_row_king, enemy_color);
    switch(result)
    {
      case 2:
        return true;
        break;
      case 1:
        finish_bishop_check = true;
        break;
    }
  }
  
  finish_bishop_check = false;
  aux_col_king = col;
  aux_row_king = row;
  
  // abajo a la izquierda
  while(!finish_bishop_check)
  {
    aux_col_king = parseInt(aux_col_king) - 1;
    aux_row_king = parseInt(aux_row_king) - 1;
    var result = auxIsKingInCheckByBishopOrQueen(board_auxiliary, aux_col_king, aux_row_king, enemy_color);
    switch(result)
    {
      case 2:
        return true;
        break;
      case 1:
        finish_bishop_check = true;
        break;
    }
  }
  return false;
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

function returnKingCastleMovements()
{
  var my_color = "black";
  if(player_is_white)
  {
    my_color = "white";
  }
  
  var kingCastleMovements = new Array();
  var found = false;
  var index = 0;
  var leftRookMove = false;
  var rightRookMove = false;
  while(index < history_js.length && !found)
  {
    var auxHistory = history_js[index];
    
    if(auxHistory.curColor == my_color && auxHistory.curPiece == "king")
    {
        found = true;
    }
    
    if(auxHistory.curColor == my_color && auxHistory.curPiece == "rook")
    {
      
      if(7 == parseInt(auxHistory.fromCol))
      {
        rightRookMove = true;
      }
      else
      {
        leftRookMove = true;
      }
    }
    if(rightRookMove && leftRookMove)
    {
      found = true;
    }
    index++;
  }
  
  if(found) return kingCastleMovements;
  
  var kingRow = 0;
  if(!player_is_white) kingRow = 7;
  
  if(!rightRookMove)
  {
    var isPieceInMiddle = false;
    var newIndex = 5;
    //console.log('chequeo el derecho que es el que tiende a 7');
    while(!isPieceInMiddle && newIndex < 7)
    {
      if(board_js[kingRow][newIndex] != 0)
      {
        isPieceInMiddle = true;
        //console.log('no esta vacio, no puede hacer el enroque para ese lado');
        rightRookMove = true;
      }
      //console.log('El indice es : ' + newIndex + ' la columna del rey es: ' + kingRow);
      //console.log(board_js[kingRow][newIndex])
      //console.log(board_js[newIndex][kingRow])
      newIndex++;
    }
  }
  
  if(!leftRookMove)
  {
    var isPieceInMiddle = false;
    var newIndex = 3;
    //console.log('chequeo el izquierdo que es aquel que tiende a 0');
    while(!isPieceInMiddle && newIndex > 0)
    {
      if(board_js[kingRow][newIndex] != 0)
      {
        isPieceInMiddle = true;
        //console.log('no esta vacio, no puede hacer el enroque para ese lado');
        leftRookMove = true;
      }
      //console.log('El indice es : ' + newIndex + ' la columna del rey es: ' + kingRow);
      //console.log(board_js[kingRow][newIndex])
      //console.log(board_js[newIndex][kingRow])
      newIndex--;
    }
    
  }
  if(!rightRookMove)
  {
    var board_aux = jQuery.extend(true, {}, board_js);
    //Voy llevando al rey a su posicion
    board_aux[kingRow][5] = board_js[kingRow][4];
    board_aux[kingRow][4] = 0;
    //Chequeo que no este en jaque en el camino
    var is_in_check = checkForMyKingSafety(board_aux);
    if(is_in_check)
    {
      //console.log('el rey esta en jaque en el camino por lo tanto no puede hacer enroque');
    }
    else
    {
      //Coloco el rey en su nueva posicion.
      board_aux[kingRow][6] = board_js[kingRow][4];
      //Pongo aca por que el rey ya se habia movido uno.
      board_aux[kingRow][5] = 0;
      //Coloco la torre en la nueva posicion
      board_aux[kingRow][5] = board_js[kingRow][7];
      board_aux[kingRow][7] = 0;
      //Chequeo que no este en jaque en esa posicion.
      is_in_check = checkForMyKingSafety(board_aux);
      if(!is_in_check)
      {
        //console.log('el movimiento de enroque es valido, creo el movimiento');
        var aux_movement = new myGamePieceMovement();
        //Esto va al revez??
        /*
        aux_movement.startingCol = 4;
        aux_movement.startingRow = kingRow;
        aux_movement.finishCol = 6;
        aux_movement.finishRow = kingRow;
        */
        aux_movement.startingCol = kingRow;
        aux_movement.startingRow = 4;
        aux_movement.finishCol = kingRow;
        aux_movement.finishRow = 6;
        aux_movement.isKingRook = true;
        aux_movement.pieceCode = board_js[kingRow][4];
        kingCastleMovements.push(aux_movement);
      }      
    }
    
  }
  
  if(!leftRookMove)
  {
    var board_aux = jQuery.extend(true, {}, board_js);
    //Voy llevando al rey a su posicion
    
    board_aux[kingRow][3] = board_js[kingRow][4];
    board_aux[kingRow][4] = 0;
    //Chequeo que no este en jaque
    var is_in_check = checkForMyKingSafety(board_aux);
    if(is_in_check)
    {
      //console.log('el rey esta en jaque en el camino por lo tanto no puede hacer enroque');
    }
    else
    {
      //Coloco el rey en su nueva posicion.
      board_aux[kingRow][2] = board_js[kingRow][4];
      //Pongo aca por que el rey ya se habia movido uno.
      board_aux[kingRow][3] = 0;
      //Coloco la torre en la nueva posicion
      board_aux[kingRow][3] = board_js[kingRow][0];
      board_aux[kingRow][0] = 0;
      //Chequeo que no este en jaque en esa posicion.
      is_in_check = checkForMyKingSafety(board_aux);
      if(!is_in_check)
      {
        //console.log('el movimiento de enroque es valido, creo el movimiento');
        var aux_movement = new myGamePieceMovement();
        //Esto va al revez??
        /*
        aux_movement.startingCol = 4;
        aux_movement.startingRow = kingRow;
        aux_movement.finishCol = 2;
        aux_movement.finishRow = kingRow;
        */
        aux_movement.startingCol = kingRow;
        aux_movement.startingRow = 4;
        aux_movement.finishCol = kingRow;
        aux_movement.finishRow = 2;
        aux_movement.isKingRook = true;
        aux_movement.pieceCode = board_js[kingRow][4];
        kingCastleMovements.push(aux_movement);
      }      
    }
  }
  return kingCastleMovements;
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
