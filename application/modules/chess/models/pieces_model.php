<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pieces_model
 *
 * @author rodrigo
 */
class pieces_model extends CI_Model{
  
  function __construct()
  {
	  parent::__construct();
  }
  
  public function retrieveGame($gameId)
  {
	$this->db->where('gameID', $gameId);
	$query = $this->db->get('pieces');
	
	$index = 0;
	$board = array();
	foreach($query->result_array() as $thisPiece)
	{
	  
	  $board[$thisPiece["row"]][$thisPiece["col"]] = getPieceCode($thisPiece["color"], $thisPiece["piece"]);
	  
	}
	
	return $board;
  }
  
  public function generateArrayOfBoard($board)
  {
	$salida = array();
	
	for ($i = 0; $i < 8; $i++)
	{
	  $aux = array();
	  for ($j = 0; $j < 8; $j++)
	  {
		if(!isset($board[$i]))
		{
		  $aux[] = 0;
		}
		else
		{
		  if(!isset($board[$i][$j]))
		  {
			$aux[] = 0;
		  }
		  else
		  {
			$aux[] = $board[$i][$j];
		  }
		}
	  }
	  
	  $salida[] = $aux;
	  if($i < 7)
	  {

	  }
	}
	return $salida;
  }
  
}

