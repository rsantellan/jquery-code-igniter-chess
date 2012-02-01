<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of players_ratings_model
 *
 * @author rodrigo
 */
class players_ratings_model extends CI_Model{
  
  private $playerId = 0;
  private $win = 0;
  private $lose = 0;
  private $draw = 0;
  private $points = 1500;
  
  private $playersEloHistory = array();
  
  function __construct()
  {
	  parent::__construct();
  }
  
  public function reloadAllRatings($games, $accessDatabase = true)
  {
	if($accessDatabase)
	{
	  $this->db->where('playerID >', "0");
	  $this->db->delete('players_ratings');
	}
	
	
	$players_ratings = array();
	foreach($games as $game)
	{
	  $white = $game->getWhitePlayer();
	  $black = $game->getBlackPlayer();
		
	  if($game->getIsOver())
	  {
		
		
		$rWhite = null;
		$hrWhite = null;
		if(isset($players_ratings[$white]))
		{
		  $rWhite = $players_ratings[$white];
		  $hrWhite = $this->playersEloHistory[$white];
		}
		else
		{
		  $rWhite = new players_ratings_model();
		  $rWhite->setPlayerId($white);
		  $hrWhite = new EloRatingHistory($white);
		  
		}
		
		$rBlack = null;
		$hrBlack = null;
		if(isset($players_ratings[$black]))
		{
		  $rBlack = $players_ratings[$black];
		  $hrBlack = $this->playersEloHistory[$black];
		}
		else
		{
		  $rBlack = new players_ratings_model();
		  $rBlack->setPlayerId($black);
		  $hrBlack = new EloRatingHistory($black);
		}
		
		switch ($game->getGameMessage()) {
		  case "draw":
			
			$elo = new EloRating($rWhite->getPoints(), $rBlack->getPoints(), 0.5, 0.5, $rWhite->getTotalGames(), $rBlack->getTotalGames());
			$rBlack->setDraw(1);
			$rWhite->setDraw(1);
			$aux_ratings = $elo->getNewRatings();
			$rWhite->setPoints($aux_ratings['a']);
			$rBlack->setPoints($aux_ratings['b']);
			$hrWhite->addGame(EloRatingHistory::TIE, $aux_ratings['a']);
			$hrBlack->addGame(EloRatingHistory::TIE, $aux_ratings['b']);
			break;
		  case "playerResigned":
			$statusA = 0;
			$statusB = 0;
			if($game->getMessageFrom() == "white")
			{
			  $statusB = 1;
			  $rBlack->setWin(1);
			  $rWhite->setLose(1);
			}
			else
			{
			  $statusA = 1;
			  $rWhite->setWin(1);
			  $rBlack->setLose(1);
			}
			$elo = new EloRating($rWhite->getPoints(), $rBlack->getPoints(), $statusA, $statusB, $rWhite->getTotalGames(), $rBlack->getTotalGames());
			$aux_ratings = $elo->getNewRatings();
			
			if($game->getMessageFrom() == "white")
			{
			  $hrWhite->addGame(EloRatingHistory::LOOSE, $aux_ratings['a']);
			  $hrBlack->addGame(EloRatingHistory::WON, $aux_ratings['b']);
			}
			else
			{
			  $hrWhite->addGame(EloRatingHistory::WON, $aux_ratings['a']);
			  $hrBlack->addGame(EloRatingHistory::LOOSE, $aux_ratings['b']);
			}
			
			
			$rWhite->setPoints($aux_ratings['a']);
			$rBlack->setPoints($aux_ratings['b']);
			break;
		  case "checkMate":
			$statusA = 0;
			$statusB = 0;
			if($game->getMessageFrom() == "black")
			{
			  $statusB = 1;
			  $rBlack->setWin(1);
			  $rWhite->setLose(1);
			}
			else
			{
			  $statusA = 1;
			  $rWhite->setWin(1);
			  $rBlack->setLose(1);
			}
			$elo = new EloRating($rWhite->getPoints(), $rBlack->getPoints(), $statusA, $statusB, $rWhite->getTotalGames(), $rBlack->getTotalGames());
			$aux_ratings = $elo->getNewRatings();
			
			if($game->getMessageFrom() == "black")
			{
			  $hrWhite->addGame(EloRatingHistory::LOOSE, $aux_ratings['a']);
			  $hrBlack->addGame(EloRatingHistory::WON, $aux_ratings['b']);
			}
			else
			{
			  $hrWhite->addGame(EloRatingHistory::WON, $aux_ratings['a']);
			  $hrBlack->addGame(EloRatingHistory::LOOSE, $aux_ratings['b']);
			}
			$rWhite->setPoints($aux_ratings['a']);
			$rBlack->setPoints($aux_ratings['b']);
			break;	  
		  default:
			break;
		}
    //var_dump($elo);
		$players_ratings[$white] = $rWhite;
		$players_ratings[$black] = $rBlack;
		$this->playersEloHistory[$white] = $hrWhite;
		$this->playersEloHistory[$black] = $hrBlack;
	  }
	}
	if($accessDatabase)
	{
	  foreach($players_ratings as $p_rating)
	  {
		$p_rating->save();
	  }
	}
	
	usort($players_ratings, "players_ratings_model::sortPlayersRatings");
	//var_dump(count($this->playersEloHistory));
	return $players_ratings;
  }
  

  public function sortPlayersRatings($playerRatingA, $playerRatingB)
  {
	if($playerRatingA->getPoints() == $playerRatingB->getPoints())
	{
	  return 0;
	}
	return ($playerRatingA->getPoints() > $playerRatingB->getPoints()) ? -1 : 1;
  }
  
  
  public function save()
  {
	$form_data = array(
		  'playerID' => $this->getPlayerId(),
		  'win' => $this->getWin(),
		  'lose' => $this->getLose(),
		  'draw' => $this->getDraw(),
		  'points' => $this->getPoints()
	  );
	$this->db->insert('players_ratings', $form_data);

  }
  
  public function getTotalGames()
  {
	return $this->getDraw() + $this->getWin() + $this->getLose();
  }
  
  public function getPlayerId() {
	return $this->playerId;
  }

  public function setPlayerId($playerId) {
	$this->playerId = $playerId;
  }

  public function getWin() {
	return $this->win;
  }

  public function setWin($win) {
	
	$this->win = $this->win + $win;
  }

  public function getLose() {
	return $this->lose;
  }

  public function setLose($lose) {
	$this->lose = $this->lose + $lose;
  }

  public function getDraw() {
	return $this->draw;
  }

  public function setDraw($draw) {
	$this->draw = $this->draw + $draw;
  }

  public function getPoints() {
	return $this->points;
  }

  public function setPoints($points) {
	$this->points = $points;
  }


  public function getPlayersEloHistory()
  {
	return $this->playersEloHistory;
  }
}
