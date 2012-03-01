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
  
  public function show($id, $player_id = null)
  {
	
    $this->output->enable_profiler(TRUE);
    
    $this->load->helper("chess/chess");
    
    $this->load->model("chess/history_model");
    
    $this->load->model("chess/pieces_model");
    
    $this->load->model("chess/games_model");
    
    $chessGame = new games_model();
    $chessGame->loadGame($id);
    
    $this->data["game"] = $chessGame;
    //var_dump($chessGame);
    $this->data['history'] = $this->history_model->retrieveGameHistory($id);
    
    //var_dump($this->data['history']);
    
    //var_dump(count($this->data['history']));
    
    //var_dump(count($this->data['history']) % 2);
    
    $this->data['history_js'] = $this->history_model->retrieveGameHistoryArray($this->data['history']);
    
    $this->data['board'] =  $this->pieces_model->retrieveGame($id);
    
    
    $this->data['board_js'] =  $this->pieces_model->generateArrayOfBoard($this->data['board']);
    
    $this->data['isWhite'] = true;
    
    $this->data['enableAjax'] = false;
    
    $this->data['isWhiteMove'] = false;
    
    if(count($this->data['history']) % 2 === 0)
    {
      $this->data['isWhiteMove'] = true;
    }
    
    if(!is_null($player_id))
    {
      if($player_id == $chessGame->getWhitePlayer())
      {
        $this->data['isWhite'] = true;
      }
      else
      {
        if($player_id == $chessGame->getBlackPlayer())
        {
          $this->data['isWhite'] = false;
        }
        else
        {
          throw new Exception("Estoy tratando de entrar a jugar con un jugador que no pertenece a la partida!!", 150);
        }
      }
      $this->data['enableAjax'] = true;
      
//      //Obtengo la partida.
      
      $board =  $this->pieces_model->retrieveGame($id);
      $board =  $this->pieces_model->generateArrayOfBoard($board);
      
      var_dump("Soy las blancas??");
      var_dump($this->data['isWhite']);
      echo '<hr/>';
      
      var_dump("su rey esta en jaque??");
      echo '<br/>';
      $isInCheck = $this->pieces_model->checkForOtherKingSafety($board, !$this->data['isWhite']);
      var_dump($isInCheck);
      echo '<hr/>';
      
//      var_dump($board);
//      echo '<hr/>';
//      var_dump($board[0]);
//      echo '<hr/>';
//      var_dump($board[1]);
//      echo '<hr/>';
      var_dump("mi rey esta en jaque??");
      echo '<br/>';
      $isInCheck = $this->pieces_model->checkForKingSafety($board, !$this->data['isWhite']);
      var_dump($isInCheck);
      echo '<hr/>';
      
//      //Obtengo los posibles movimientos de la partida.
//
//      $history = $this->history_model->retrieveGameHistory($id);
//
//      $history_array = $this->history_model->retrieveGameHistoryArray($history);
//
//      $movements = $this->pieces_model->calculatePosibleMovements($board, false, $this->data['isWhite'], $history_array);      
//      //var_dump(count($movements));
      
    }
    
    $this->data['myTourn'] = false;
    
    if($this->data['isWhiteMove'] && $this->data['isWhite'] || !$this->data['isWhiteMove'] && !$this->data['isWhite'])
    {
      $this->data['myTourn'] = true;
    }
    
    $this->addModuleJavascript("chess", "chessBasics.js");
    //$this->addModuleJavascript("chess", "chessReplay.js");
    
    if($chessGame->isGameOver())
    {
      $this->addModuleJavascript("chess", "chessReplay.js");
    }
    else
    {
      //$this->addModuleJavascript("chess", "myValidation.js");
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
 
 
  public function movePiece()
  {
    $this->output->enable_profiler(TRUE);
    $data = array();
    $data['startingRow'] = $this->input->get_post("startingRow", TRUE);
    $data['startingCol'] = $this->input->get_post("startingCol", TRUE);
    $data['finishRow'] = $this->input->get_post("finishRow", TRUE);
    $data['finishCol'] = $this->input->get_post("finishCol", TRUE);
    $data['gameId'] = $this->input->get_post("gameId", TRUE);
    $data['player_is_white'] = $this->input->get_post("player_is_white", TRUE);
    $data['promoting_piece'] = $this->input->get_post("piece", NULL);
    
    if($data['player_is_white'] == "false")
    {
      $data['player_is_white'] = false;
    }
    else
    {
      $data['player_is_white'] = true;
    }
    //Chequeo que yo pertenezco a ese juego.
    $response = false;
    //Hasta que no arme el logueo esto siempre tiene que dar bien.
    $response = true;
    
    //Cargo las clases requeridas
    $this->load->model("chess/pieces_model");
    $this->load->helper("chess/chess");
    $this->load->model("chess/history_model");
    
    
    //Obtengo la partida.
    $board =  $this->pieces_model->retrieveGame($data['gameId']);
    //Obtengo los posibles movimientos de la partida.
    
    $history = $this->history_model->retrieveGameHistory($data['gameId']);
    
    $history_array = $this->history_model->retrieveGameHistoryArray($history);
    
    $movements = $this->pieces_model->calculatePosibleMovements($board, false, $data['player_is_white'], $history_array);
    
    //Chequeo que el movimiento dado este contemplado
    $aux_movement = null;
    foreach($movements as $move)
    {
      
      if($move->startingCol == $data['startingCol'] && $move->startingRow == $data['startingRow'])
      {
        //var_dump($move);
        if($move->finishCol == $data['finishCol'] && $move->finishRow == $data['finishRow'])
        {
          $aux_movement = $move;
        }
      }
    }
    
    /**
     * 
     * Aca voy a interceptar el movimiento por las dudas de que este bien.
     * 
     **/
    /*
    var_dump($aux_movement);
    die;
    */
    //En caso de que lo este lo guardo.
    if(!is_null($aux_movement))
    {
      $this->pieces_model->savePieceMovement($aux_movement, $data);
      //Obtengo los datos de la fila a la que va a ir.
      //$this->pieces_model->searchPiece($data['finishCol'], $data['finishRow'], $data['gameId']);
      //$this->pieces_model->searchPiece($data['startingCol'], $data['startingRow'], $data['gameId']);
      //Guardo la nueva posicion del tablero
      
      //Guardo el historico.
    }
    else
    {
      $response = false;
    }
    
    
    
    
    echo json_encode(array('response' => $response, 'options' => $data));
    exit(0);
  }
  
  public function ratings()
  {
	
	//$this->output->enable_profiler(TRUE);
	
	$this->load->model("chess/games_model");
	
	$this->load->model("chess/players_ratings_model");
	
	$this->load->model("chess/players_model");
	
	$games = $this->games_model->retrieveAllGames();
	
	$this->data["players_ratings"] = $this->players_ratings_model->reloadAllRatings($games);
	
	$this->data["players_history"] = $this->players_ratings_model->getPlayersEloHistory();
	
	
	
	$this->data["players"] =$this->players_model->retrieveAllPlayers();
	
	//$this->addModuleJavascript('chess', 'excanvas.min.js');
	$this->addModuleJavascript('chess', 'jquery.jqplot.min.js');
	$this->addModuleJavascript('chess', 'jqplot_plugins/jqplot.pieRenderer.js');
	$this->addModuleJavascript('chess', 'jqplot_plugins/jqplot.logAxisRenderer.min.js');
	$this->addModuleJavascript('chess', 'jqplot_plugins/jqplot.cursor.min.js');
	$this->addModuleJavascript('chess', 'jqplot_plugins/jqplot.highlighter.min.js');
	$this->addModuleJavascript('chess', 'my-graphs.js');
	$this->addModuleStyleSheet('chess', 'jquery.jqplot.css');
	
	$this->data["content"] = 'ratings';
	$this->load->view('layout', $this->data);
  }
  
  /*
  public function graphicRatings()
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
   */
}

