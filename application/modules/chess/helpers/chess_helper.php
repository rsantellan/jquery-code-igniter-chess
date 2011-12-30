<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('getPieceCode'))
{
  function getPieceCode($color, $piece)
  {
	switch($piece)
	{
		case "pawn":
			$code = PAWN;
			break;
		case "knight":
			$code = KNIGHT;
			break;
		case "bishop":
			$code = BISHOP;
			break;
		case "rook":
			$code = ROOK;
			break;
		case "queen":
			$code = QUEEN;
			break;
		case "king":
			$code = KING;
			break;
	}

	if ($color == "black")
		$code = BLACK + $code;
	
	return $code;
  }
}


if ( ! function_exists('getPieceColor'))
{
  function getPieceColor($piece)
  {
	if($piece > BLACK)
	{
	  return "black";
	}
	else
	{
	  return "white";
	}
  }
}

if ( ! function_exists('getPieceName'))
{
  function getPieceName($piece)
  {
	$pieceName = array();
	$pieceName[PAWN] = "pawn";
	$pieceName[ROOK] = "rook";
	$pieceName[KNIGHT] = "knight";
	$pieceName[BISHOP] = "bishop";
	$pieceName[QUEEN] = "queen";
	$pieceName[KING] = "king";
	$empty = constant("EMPTY");
	$pieceName[$empty] = "empty";
	$aux = "empty";
	if($piece > BLACK)
	{
	  $aux = $pieceName[$piece - BLACK];
	}
	else
	{
	  $aux = $pieceName[$piece];
	}
	return $aux;
  }
}

if ( ! function_exists('getHistoryMove'))
{
  function getHistoryMove($history)
  {
	$files = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
	
	$row_from = $history["fromRow"];
	$row_from = ((int)$row_from) + 1;
	$col_from = $history["fromCol"];
	$col_from = $files[$col_from];
	
	$row_to = $history["toRow"];
	$row_to = ((int)$row_to) + 1;
	$col_to = $history["toCol"];
	$col_to = $files[$col_to];
	$return = $col_from.$row_from;
	if(!is_null($history["replaced"]))
	{
	  $return .= " x ";
	}
	else
	{
	  $return .= " - ";
	}
	
	$return .= $col_to.$row_to;
	
	if($history["isInCheck"] == "1")
	{
	  $return .= " +";
	}
	return $return;
  }
  
}