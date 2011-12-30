$(document).ready(function() {
  $(".moveToPosition").each(function(index, element){
	var movement = $(element).attr("movement");
	$(element).click(function() {
	  moveToPosition(movement);
	});
  });
});



function moveToPosition(position)
{
  $("#table_history td").each(function(index, element){
	if($(element).hasClass("move_replay_selected"))
	{
	  
	  $(element).removeClass("move_replay_selected");
	}
  });
  //console.log(history_js[position]);
  moveHistoryToPosition(position);
}

function moveHistoryToPosition(finalPosition)
{
  var movement = 0;
  resetBoard();
  finalPosition = parseInt(finalPosition);
  while(movement <= finalPosition)
  {
	$("#movement_" + movement).addClass("move_replay_selected");
	var history_aux = history_js[movement];
	var position = calculatePosition(history_aux.fromCol, history_aux.fromRow);
	$("#tsq"+position +" > div > div").removeClass().addClass("white_empty");
	var aux_piece = history_aux.curColor + "_" + history_aux.curPiece;
	position = calculatePosition(history_aux.toCol, history_aux.toRow);
	$("#tsq"+position +" > div > div").removeClass().addClass(aux_piece);
	movement = parseInt(movement) + 1;
  }
}

function resetBoard()
{
  cleanBoard();
  resetPawns();
  resetWhiteFigures();
  resetBlackFigures();
}

function cleanBoard()
{
  for(var i = 0; i < 64; i++)
  {
	$("#tsq"+i+" > div > div").removeClass().addClass("white_empty");
  }
}

function resetPawns()
{
  for(var i = 8; i < 16; i++)
  {
	$("#tsq"+i+" > div > div").removeClass().addClass("white_pawn");
  }
  
  for(var j = 48; j < 56; j++)
  {
	$("#tsq"+j+" > div > div").removeClass().addClass("black_pawn");
  }
  
}

function resetWhiteFigures()
{

  
  //Torres
  $("#tsq7 > div > div").removeClass().addClass("white_rook");
  $("#tsq0 > div > div").removeClass().addClass("white_rook");
  //Caballos
  $("#tsq6 > div > div").removeClass().addClass("white_knight");
  $("#tsq1 > div > div").removeClass().addClass("white_knight");
  //Alfiles
  $("#tsq5 > div > div").removeClass().addClass("white_bishop");
  $("#tsq2 > div > div").removeClass().addClass("white_bishop");
  
  //Reina
  $("#tsq3 > div > div").removeClass().addClass("white_queen");
  //Rey
  $("#tsq4 > div > div").removeClass().addClass("white_king");
  
}

function resetBlackFigures()
{
  //Torres
  $("#tsq63 > div > div").removeClass().addClass("black_rook");
  $("#tsq56 > div > div").removeClass().addClass("black_rook");
  //Caballos
  $("#tsq62 > div > div").removeClass().addClass("black_knight");
  $("#tsq57 > div > div").removeClass().addClass("black_knight");
  //Alfiles
  $("#tsq61 > div > div").removeClass().addClass("black_bishop");
  $("#tsq58 > div > div").removeClass().addClass("black_bishop");
  
  //Reina
  $("#tsq59 > div > div").removeClass().addClass("black_queen");
  //Rey
  $("#tsq60 > div > div").removeClass().addClass("black_king");
}