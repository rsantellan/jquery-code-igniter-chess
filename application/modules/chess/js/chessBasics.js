var CURPIECE = 0;
var CURCOLOR = 1;
var FROMROW = 2;
var FROMCOL = 3;
var TOROW = 4;
var TOCOL = 5;
var PROMOTEDTO = 6;

var knightMove = [[-1, -2], [+1, -2], [-2, -1], [-2, +1], [-1, +2], [+1, +2], [+2, -1], [+2, +1]];

function calculatePosition(col, row)
{
  var position = parseInt(col) + parseInt(row * 8);
  return position;
}

function calculateRowCol(position)
{
  var col = parseInt(position) % 8;
  var row = parseInt(position) / 8;
  row = Math.floor(row);
  var theReturn =new Array();
  theReturn[0] = col;
  theReturn[1] = row;
  return theReturn;
}

// object definition (used by isSafe)
function GamePiece()
{
  this.piece = 0;
  this.dist = 0;
}

function myGamePiece()
{
  this.col = 0;
  this.row = 0;
  this.pieceCode = 0;
}


function myGamePieceMovement(){
  this.startingCol = 0;
  this.startingRow = 0;
  this.pieceCode = 0;
  this.finishCol = 0;
  this.finishRow = 0;
}


function myCloneElement(source){
  for (i in source) {
	if (typeof source[i] == 'source') {
	  this[i] = new cloneObject(source[i]);
	}
	else{
	  this[i] = source[i];
	}
  }
}

function retrievePieceType(piece_code)
{
  if(piece_code > BLACK)
  {
	piece_code = piece_code - BLACK;
  }
  return piece_code;
}

function isInBoard(row, col)
{
  if ((row >= 0) && (row <= 7) && (col >= 0) && (col <= 7))
	return true;
  else
	return false;
}

/**
 * 
 * Lo que hace es devuelve si una pieza dada es de cierto tipo sin importar el color
 **/ 
function isPiece(piece, toBePiece)
{
  if(piece == toBePiece)
  {
	return true;
  }
  else
  {
	if((piece - BLACK) == toBePiece)
	{
	  return true;
	}
  }
  return false;
    
}
