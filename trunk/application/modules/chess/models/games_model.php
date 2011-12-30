<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of games_model
 *
 * @author rodrigo
 */
class games_model extends CI_Model{
  
  
  private $gameId;
  private $whitePlayer;
  private $blackPlayer;
  private $gameMessage;
  private $messageFrom;
  private $isOver = false;
  private $isCheckMate = false;
  private $isLoaded = false;
  
  function __construct()
  {
	  parent::__construct();
  }
  
  public function retrieveAllGames()
  {
	$query = $this->db->get('games');
	if($query->num_rows()>0){
	  // return result set as an associative array
	  return $query->result_array();
	}
	return array();
  }
  
  public function loadGame($id)
  {
	$this->db->where('gameID', $id);
	$query = $this->db->get('games');
	$results = $query->result();
	if(count($results > 0))
	{
	  $aux = $results[0];
	  $this->setBlackPlayer($aux->blackPlayer);
	  $this->setGameId($id);
	  $this->setGameMessage($aux->gameMessage);
	  $this->setMessageFrom($aux->messageFrom);
	  $this->setWhitePlayer($aux->whitePlayer);
	  $this->loadGameOver();
	}
  }
  
  private function loadGameOver()
  {
	$this->isLoaded = true;
	switch ($this->getGameMessage()) {
	  case "draw":
		$this->setIsOver(true);
		break;
	  case "playerResigned":
		$this->setIsOver(true);
		break;
	  case "checkMate":
		$this->setIsOver(true);
		$this->setIsCheckMate(true);
		break;	  
	  default:
		break;
	}
  }
  
  public function isGameOver()
  {
	if(is_null($this->getGameId()) || !$this->isLoaded)
	{
	  return false;
	}
	
	return $this->getIsOver();
	/*
	if ($this->getGameMessage() == "draw")
	{
		$statusMessage .= "Game ended in a draw";
		$isGameOver = true;
	}

	if ($tmpMessage['gameMessage'] == "playerResigned")
	{
		$statusMessage .= $tmpMessage['messageFrom']." has resigned the game";
		$isGameOver = true;
	}

	if ($tmpMessage['gameMessage'] == "checkMate")
	{
		$statusMessage .= "Checkmate! ".$tmpMessage['messageFrom']." has won the game";
		$isGameOver = true;
		$isCheckMate = true;
	}
	*/
  }
  
  public function getIsCheckMate() {
	return $this->isCheckMate;
  }

  public function setIsCheckMate($isCheckMate) {
	$this->isCheckMate = $isCheckMate;
  }

  public function getGameId() {
	return $this->gameId;
  }

  public function setGameId($gameId) {
	$this->gameId = $gameId;
  }

  public function getWhitePlayer() {
	return $this->whitePlayer;
  }

  public function setWhitePlayer($whitePlayer) {
	$this->whitePlayer = $whitePlayer;
  }

  public function getBlackPlayer() {
	return $this->blackPlayer;
  }

  public function setBlackPlayer($blackPlayer) {
	$this->blackPlayer = $blackPlayer;
  }

  public function getGameMessage() {
	return $this->gameMessage;
  }

  public function setGameMessage($gameMessage) {
	$this->gameMessage = $gameMessage;
  }

  public function getMessageFrom() {
	return $this->messageFrom;
  }

  public function setMessageFrom($messageFrom) {
	$this->messageFrom = $messageFrom;
  }

  public function getIsOver() {
	return $this->isOver;
  }

  public function setIsOver($isOver) {
	$this->isOver = $isOver;
  }


}


