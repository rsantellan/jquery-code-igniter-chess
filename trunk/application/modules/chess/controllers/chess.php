<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of chess
 *
 * @author rodrigo
 */
class chess extends MY_Controller {
  
  
  public function __construct() {
    parent::__construct();
    $this->loadI18n(get_class($this), $this->lenguage, FALSE, TRUE, '', strtolower(get_class($this)));
  }
  
  public function index()
  {
    //$this->output->enable_profiler(TRUE);
    
    $this->load->model("chess/games_model");
    
    $this->load->model("chess/players_model");
    
    $this->data['games'] = $this->games_model->retrieveAllGames();
    
    $this->data['players'] = $this->players_model->retrieveAllPlayers();
    $this->data["content"] = 'index';
    
    $this->load->view('layout', $this->data);
  }
  
  public function show($id)
  {
	
    $this->output->enable_profiler(TRUE);
    
    $this->load->helper("chess/chess");
    
    $this->load->model("chess/history_model");
    
    $this->load->model("chess/pieces_model");
    
    $this->load->model("chess/games_model");
    
    $chessGame = new games_model();
    $chessGame->loadGame($id);
    
    $this->data["game"] = $chessGame;
    
    $this->data['history'] = $this->history_model->retrieveGameHistory($id);
    
    $this->data['history_js'] = $this->history_model->retrieveGameHistoryArray($this->data['history']);
    
    $this->data['board'] =  $this->pieces_model->retrieveGame($id);
    
    
    $this->data['board_js'] =  $this->pieces_model->generateArrayOfBoard($this->data['board']);
    
    
    $this->data['isWhite'] = true;
    
    $this->addModuleJavascript("chess", "chessBasics.js");
    if($chessGame->isGameOver())
    {
      $this->addModuleJavascript("chess", "chessReplay.js");
    }
    else
    {
      $this->addModuleJavascript("chess", "myValidation.js");
    }
    
    $this->addModuleStyleSheet("chess", "chess.css");
    
    $this->addJavascript("jquery-ui-1.8.16.custom.min.js");
    $this->addStyleSheet("le-frog/jquery-ui-1.8.16.custom.css");
    $this->addModuleJavascript("chess", "chessDragAndDrop.js");
    $this->addModuleStyleSheet("chess", "basic/basic.css");
    
    //$this->addJavascript("myJqueryUIDrag.js");
    
    $this->addModuleStyleSheet("chess", "jquery.tablescroll.css");
    $this->addModuleJavascript("chess", "jquery.tablescroll.js");
    $this->data["content"] = 'show';
    
    $this->load->view('layout', $this->data);
  }
 
  
  public function ratings()
  {
	
	$this->output->enable_profiler(TRUE);
	
	$this->load->model("chess/games_model");
	
	$this->load->model("chess/players_ratings_model");
	
	$this->load->model("chess/players_model");
	
	$games = $this->games_model->retrieveAllGames();
	
	$this->data["players_ratings"] = $this->players_ratings_model->reloadAllRatings($games);
	
	
	$this->data["players"] =$this->players_model->retrieveAllPlayers();
	
	$this->data["content"] = 'ratings';
	$this->load->view('layout', $this->data);
  }
}

