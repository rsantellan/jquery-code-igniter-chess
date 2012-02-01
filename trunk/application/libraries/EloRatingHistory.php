<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EloRatingHistory
 *
 * @author rodrigo
 */
class EloRatingHistory 
{
  
  const WON = "W";
  const LOOSE = "L";
  const TIE = "T";
  
  private $playerId;
  
  private $gameList = array();
  private $win = NULL;
  private $tie = NULL;
  private $loose = NULL;
  
  private $elo_history = null;
  
  function __construct($playerId) {
	$this->playerId = $playerId;
  }
  
  public function addGame($game_result, $actualElo)
  {
	$aux = array();
	$aux['result'] = $game_result;
	$aux['elo'] = $actualElo;
	$this->gameList[] = $aux;
  }

  public function getGameList()
  {
	return $this->gameList;
  }
  
  private function initilizeCounters()
  {
	$this->win = 0;
	$this->tie = 0;
	$this->loose = 0;
	$this->elo_history = array();
	$this->elo_history[] = 1500;
	foreach($this->gameList as $values)
	{
	  switch ($values['result']) {
		case self::WON:
		  $this->win = $this->win + 1;
		  break;
		case self::TIE:
		  $this->tie = $this->tie + 1;
		  break;
		case self::LOOSE:
		  $this->loose = $this->loose + 1;
		  break;
		default:
		  break;
	  }
	  $this->elo_history[] = $values['elo'];
	}
  }
  
  public function getEloHistory()
  {
	if(is_null($this->elo_history))
	{
	  $this->initilizeCounters();
	}
	return $this->elo_history;
  }
  
  
  public function getWinQuantity()
  {
	if(is_null($this->win))
	{
	  $this->initilizeCounters();
	}
	return $this->win;
  }
  
  public function getTieQuantity()
  {
	if(is_null($this->tie))
	{
	  $this->initilizeCounters();
	}
	return $this->tie;
  }  
  
  public function getLooseQuantity()
  {
	if(is_null($this->loose))
	{
	  $this->initilizeCounters();
	}
	return $this->loose;
  }  
}


