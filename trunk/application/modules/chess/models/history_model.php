<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of history_model
 *
 * @author rodrigo
 */
class history_model extends CI_Model{
  
  function __construct()
  {
	  parent::__construct();
  }
  
  public function retrieveGameHistory($gameId)
  {
	$this->db->where('gameID', $gameId);
	$this->db->order_by("timeOfMove", "asc");
	$query = $this->db->get('history');
	
	$index = 0;
	$history = array();
	foreach($query->result_array() as $result)
	{
	  $index++;
	  $history[$index] = $result;
	}
	
	return $history;
  }
  
  public function retrieveGameHistoryArray($history)
  {
	$salida = array();
	for($i = 1; $i <= count($history); $i++)
	{
	  $aux = array();
	  $aux['curPiece'] = $history[$i]['curPiece'];
	  $aux['curColor'] = $history[$i]['curColor'];
	  $aux['fromRow'] = $history[$i]['fromRow'];
	  $aux['fromCol'] = $history[$i]['fromCol'];
	  $aux['toRow'] = $history[$i]['toRow'];
	  $aux['toCol'] = $history[$i]['toCol'];
	  if(!is_null($history[$i]['promotedTo']))
	  {
		$aux['promotedTo'] = $history[$i]['promotedTo'];
	  }
	  $aux['replaced'] = $history[$i]['replaced'];
	  $aux['isInCheck'] = $history[$i]['isInCheck'];
	  $salida[] = $aux;
	}
	return $salida;
  }
  
}


