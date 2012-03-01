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

if(!function_exists('getSimplePieceCode'))
{
  function getSimplePieceCode($code)
  {
    if($code > BLACK)
    {
      return $code - BLACK;
    }
    return $code;
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
	$piece = $history["curPiece"];
	$row_from = $history["fromRow"];
	$row_from = ((int)$row_from) + 1;
	$col_from = $history["fromCol"];
	$col_from = $files[$col_from];
	
	$row_to = $history["toRow"];
	$row_to = ((int)$row_to) + 1;
	$col_to = $history["toCol"];
	$col_to = $files[$col_to];
	$return = "";
	
	
	if (($piece == "king") && (abs($history["toCol"] - $history["fromCol"]) == 2))
	{
//      var_dump($history["curColor"]);
//      var_dump(($history["curColor"] == "white" && $history["toCol"] > 4));
//      var_dump(($history["curColor"] == "black" && $history["toCol"] < 4));
      if(($history["curColor"] == "white" && $history["toCol"] > 4) || ($history["curColor"] == "black" && $history["toCol"] > 4) )
      {
        $return .= "0 - 0";
      }
      else
      {
        $return .= "0 - 0 - 0";
      }
	  
	}
	else
	{
	  $return .= $col_from.$row_from;
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
	}
	return $return;
  }
  
}

if ( ! function_exists('pieceIsInBoard'))
{
  function pieceIsInBoard($row, $col)
  {
    if (($row >= 0) && ($row <= 7) && ($col >= 0) && ($col <= 7))
      return true;
    else
      return false;
  }
}

if ( ! function_exists('array_copy'))
{
  /**
  * make a recursive copy of an array 
  *
  * @param array $aSource
  * @return array    copy of source array
  */
  function array_copy ($aSource) {
     // check if input is really an array
     if (!is_array($aSource)) {
         throw new Exception("Input is not an Array");
     }

     // initialize return array
     $aRetAr = array();

     // get array keys
     $aKeys = array_keys($aSource);
     // get array values
     $aVals = array_values($aSource);

     // loop through array and assign keys+values to new return array
     for ($x=0;$x<count($aKeys);$x++) {
         // clone if object
         if (is_object($aVals[$x])) {
             $aRetAr[$aKeys[$x]]=clone $aVals[$x];
         // recursively add array
         } elseif (is_array($aVals[$x])) {
             $aRetAr[$aKeys[$x]]=array_copy ($aVals[$x]);
         // assign just a plain scalar value
         } else {
             $aRetAr[$aKeys[$x]]=$aVals[$x];
         }
     }

     return $aRetAr;
  }
}