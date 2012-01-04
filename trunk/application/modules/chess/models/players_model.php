<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of players
 *
 * @author rodrigo
 */
class players_model extends CI_Model{
  
  function __construct()
  {
	  parent::__construct();
  }
  
  public function retrieveAllPlayers()
  {
	$query = $this->db->get('players');
	if($query->num_rows()>0){
	  $salida = array();
	  foreach($query->result() as $aux)
	  {
		$salida[$aux->playerID] = $aux;
	  }
	  return $salida;
	  //return $query->result_array();
	}
	return array();
  }
}


