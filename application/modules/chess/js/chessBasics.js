var CURPIECE = 0;
var CURCOLOR = 1;
var FROMROW = 2;
var FROMCOL = 3;
var TOROW = 4;
var TOCOL = 5;
var PROMOTEDTO = 6;

function calculatePosition(col, row)
{
  console.log(col);
  console.log(row);
  var position = parseInt(col) + parseInt(row * 8);
  console.log(position);
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