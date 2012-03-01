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

  
  //Los 8 movimientos del caballo.
  
  private $knightMove = array(
        array(-1, -2),
        array(1, -2),
        array(-2, -1),
        array(-2, 1),
        array(-1, 2),
        array(1, 2),
        array(2, -1),
        array(2, 1),
  ); 
  
  private $kingMove = array(
      array(-1, -1),
      array(-1, 0),
      array(-1, 1),
      array(0, 1), //
      array(0, -1),
      array(1, 1),
      array(1, 0),
      array(1, -1),
  );
  
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
  
  public function searchPiece($col, $row, $gameId)
  {
    //Tengo que invertir la columna y la fila por como lo ve la base de datos.
    $this->db->where('gameID', $gameId);
    $this->db->where('col', $row);
    $this->db->where('row', $col);
    $query = $this->db->get('pieces', 1);
    return array_pop($query->result_array());
  }

  private function deletePiece($col, $row, $gameId)
  {
    $this->db->where('gameID', $gameId);
    $this->db->where('col', $row);
    $this->db->where('row', $col);
    $this->db->delete('pieces');
    var_dump($this->db->last_query());
  }
  /**
   *
   * @param stdClass $pieceMovement
   *            -sdtClass con los siguientes parametros
   *              - emptySpace
   *              - noMove
   *              - startingCol
   *              - startingRow
   *              - finishCol
   *              - finishRow
   *              - pieceCode
   * @param array $post_data 
   *            Es un array que contiene:
   *              -startingRow
   *              -startingCol
   *              -finishRow
   *              -finishCol
   *              -gameId
   *              -player_is_white
   * 
   */
  public function savePieceMovement($pieceMovement, $post_data)
  {
    if(isset($pieceMovement->pawnEnPassant))
    {
      var_dump('estoy comiendo al paso.');
      $this->savePawnEnPassant($pieceMovement, $post_data);
      return true;
    }
    if(isset($pieceMovement->isKingRook))
    {
      var_dump('estoy haciendo un enroque');
      $this->saveCastling($pieceMovement, $post_data);
      return true;
    }
    
    
    
    //var_dump($pieceMovement);
    $toPlace = $this->searchPiece($post_data['finishCol'], $post_data['finishRow'], $post_data['gameId']);
    var_dump('to place');
    var_dump($toPlace);
    $fromPlace = $this->searchPiece($post_data['startingCol'], $post_data['startingRow'], $post_data['gameId']);
    var_dump('from place');
    var_dump($fromPlace);
    
    $piece_to_put_on_board = $fromPlace['piece'];
    $promotedTo = NULL;
    
    if(isset($post_data['promoting_piece']) && !is_null($post_data['promoting_piece'])) 
    {
      if($post_data['promoting_piece'])
      {
        switch ($post_data['promoting_piece']) {
          case QUEEN:
              $piece_to_put_on_board = "queen";
            break;
          case ROOK:
              $piece_to_put_on_board = "rook";
            break;
          case KNIGHT:
              $piece_to_put_on_board = "knight";
            break;
          case BISHOP:
              $piece_to_put_on_board = "bishop";
            break;
          default:
            break;
        }
        $promotedTo = $piece_to_put_on_board;
      }
    }

    /**
     * Si esta nulo el lugar de toPlace entonces lo tengo que insertar.
     * En caso contrario tengo que hacer un update.
     */
    if(is_null($toPlace))
    {
      $data = array(
          'gameID' => $fromPlace['gameID'] ,
          'color' => $fromPlace['color'] ,
          'piece' => $piece_to_put_on_board,
          'col' => $post_data['finishRow'],
          'row' => $post_data['finishCol']
       );
      $this->db->insert("pieces", $data);
      $this->deletePiece($post_data['startingCol'], $post_data['startingRow'], $post_data['gameId']);
      
      //Ahora lo tengo que agregar al historico.
      //Esto tengo que ver bien como paso los parametros que me faltan.
      //Tendria que fijarme si es un peon y se esta coronando.
      //Tendria que fijarme el jaque.
      
      //Obtengo la partida con el ultimo cambio.
      $board =  $this->retrieveGame($fromPlace['gameID']);
      $board =  $this->generateArrayOfBoard($board);
      //Obtengo si esta chequeado.
      //var_dump('soy las blancas?');
      //var_dump($post_data['player_is_white']);
      $isInCheck = $this->checkForOtherKingSafety($board, !$post_data['player_is_white']);
      //var_dump($isInCheck);
      
      $this->addMovementToHistory($fromPlace['gameID'], $fromPlace['piece'], $fromPlace['color'], $post_data['startingRow'], $post_data['startingCol'], $post_data['finishRow'], $post_data['finishCol'], null, $promotedTo, $isInCheck);
    }
    else
    {
      var_dump(' Le tengo que hacer es un update');
      $data = array(
          'color' => $fromPlace['color'] ,
          'piece' => $piece_to_put_on_board,
          
       );
      $where = array(
          'gameID' => $fromPlace['gameID'] ,
          'col' => $post_data['finishRow'],
          'row' => $post_data['finishCol']
      );
      //$this->db->where('gameID', $fromPlace['gameID']);
      $this->db->update("pieces", $data, $where);
      $this->deletePiece($post_data['startingCol'], $post_data['startingRow'], $post_data['gameId']);
      
      $board =  $this->retrieveGame($fromPlace['gameID']);
      $board =  $this->generateArrayOfBoard($board);
      $isInCheck = $this->checkForOtherKingSafety($board, !$post_data['player_is_white']);
      $this->addMovementToHistory($fromPlace['gameID'], $fromPlace['piece'], $fromPlace['color'], $post_data['startingRow'], $post_data['startingCol'], $post_data['finishRow'], $post_data['finishCol'], $toPlace['piece'], $promotedTo, $isInCheck);
      
    }
  }
  
  private function saveCastling($pieceMovement, $post_data)
  {
    $kingRow = 0;
    if(!$post_data['player_is_white']){
      $kingRow = 7;
    }
    
    var_dump(" Finish col ". $post_data['finishCol']);
    var_dump(" Finish row ". $post_data['finishRow']);
    var_dump(" La fila del rey es :". $kingRow);
    
    $fromPlace = $this->searchPiece($post_data['startingCol'], $post_data['startingRow'], $post_data['gameId']);
    $data = array(
          'gameID' => $fromPlace['gameID'] ,
          'color' => $fromPlace['color'] ,
          'piece' => $fromPlace['piece'],
          'col' => $post_data['finishRow'],
          'row' => $post_data['finishCol']
       );
    //Muevo el rey
    $this->db->insert("pieces", $data);
    $this->deletePiece($post_data['startingCol'], $post_data['startingRow'], $post_data['gameId']);
    //Elimino donde estaba el rey
    //Muevo la torre
    
    //Si el rey se mueve a la casilla 2 el otro se mueve a la casilla 3
    $rookCol = 3; 
    $rookStartCol = 0;
    if($post_data['finishRow'] != 2)
    {
      $rookCol = 5; 
      $rookStartCol = 7;
    }
    $data = array(
          'gameID' => $fromPlace['gameID'] ,
          'color' => $fromPlace['color'] ,
          'piece' => "rook",
          'col' => $rookCol,
          'row' => $kingRow
       );
    $this->db->insert("pieces", $data);
    //Elimino donde estaba la torre
    $this->deletePiece($kingRow, $rookStartCol, $post_data['gameId']);
    
    $board =  $this->retrieveGame($fromPlace['gameID']);
    $board =  $this->generateArrayOfBoard($board);
    $isInCheck = $this->checkForOtherKingSafety($board, !$post_data['player_is_white']);
    $this->addMovementToHistory($fromPlace['gameID'], $fromPlace['piece'], $fromPlace['color'], $post_data['startingRow'], $post_data['startingCol'], $post_data['finishRow'], $post_data['finishCol'], null, null, $isInCheck);
  }
  
  private function savePawnEnPassant($pieceMovement, $post_data)
  {
    $moveBackwards = -1;
    if(!$post_data['player_is_white'])
    {
      $moveBackwards = 1;
    }
    
    $toPlace = $this->searchPiece($post_data['finishCol'], $post_data['finishRow'], $post_data['gameId']);
    $fromPlace = $this->searchPiece($post_data['startingCol'], $post_data['startingRow'], $post_data['gameId']);
    $data = array(
          'gameID' => $fromPlace['gameID'] ,
          'color' => $fromPlace['color'] ,
          'piece' => $fromPlace['piece'],
          'col' => $post_data['finishRow'],
          'row' => $post_data['finishCol']
       );
    
    $this->db->insert("pieces", $data);
    $this->deletePiece($post_data['startingCol'], $post_data['startingRow'], $post_data['gameId']);
    $back_position_col = $post_data['startingCol'] + $moveBackwards;
    $this->deletePiece($post_data['startingRow'], $back_position_col, $post_data['gameId']);
    $board =  $this->retrieveGame($fromPlace['gameID']);
    $board =  $this->generateArrayOfBoard($board);
    
    $isInCheck = $this->checkForOtherKingSafety($board, !$post_data['player_is_white']);
    $this->addMovementToHistory($fromPlace['gameID'], $fromPlace['piece'], $fromPlace['color'], $post_data['startingRow'], $post_data['startingCol'], $post_data['finishRow'], $post_data['finishCol'], $fromPlace['piece'], null, $isInCheck);
    /*
    var_dump($post_data['finishCol']);
    var_dump($post_data['finishCol'] + $moveBackwards);
    var_dump($board[$post_data['finishCol']]);
    var_dump($board[$post_data['finishCol'] + $moveBackwards]);
    var_dump($board[$post_data['finishCol']][$post_data['finishRow']]);
    */
  }
  
  private function addMovementToHistory($gameId, $currentPiece, $currentColor, $fromRow, $fromColumn, $toRow, $toColumn, $replace = null, $promotedTo = null, $isInCheck = false)
  {
    $data = array(
          'timeOfMove' => date('Y-m-d H:i:s'),
          'gameID' => $gameId ,
          'curColor' => $currentColor ,
          'curPiece' => $currentPiece,
          'fromCol' => $fromRow,
          'fromRow' => $fromColumn,
          'toCol' => $toRow,
          'toRow' => $toColumn,
          'replaced' => $replace,
          'promotedTo' => $promotedTo,
          'isInCheck' => $isInCheck
       );
    
    $this->db->insert("history", $data);
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
	}
	return $salida;
  }
  
  
  public function calculatePosibleMovements($board, $isArray = false, $isWhite = true, $history_array = array())
  {
    //var_dump($isWhite);
    $board_array = $board;
    if(!$isArray)
    {
      $board_array = $this->generateArrayOfBoard($board);
    }
    
    $array_piezas = array();
    for($i=0; $i <8; $i++)
    {
      for($j=0; $j < 8; $j++)
      {
        if($board_array[$i][$j] != 0)
        {
          if($isWhite && $board_array[$i][$j] < BLACK || (!$isWhite && $board_array[$i][$j] > BLACK))
          {
            $aux = new stdClass();
            $aux->col = $i;
            $aux->row = $j;
            $aux->pieceCode = $board_array[$i][$j];
            array_push($array_piezas, $aux);
          }
        }
      }
    }
    
    $lista_movimientos = array();
    foreach($array_piezas as $pieza)
    {
      switch (getSimplePieceCode($pieza->pieceCode)) {
        case PAWN:
          $auxMovements = $this->retrieveAllPosiblePawnMovement($pieza->col, $pieza->row, $pieza->pieceCode, $isWhite, $board_array, $history_array);
          foreach($auxMovements as $mov)
          {
            array_push($lista_movimientos, $mov);
          }
//          var_dump("pawn");
//          var_dump(count($lista_movimientos));
          break;
        case KNIGHT:
          $auxMovements = $this->retrieveAllPosibleKnightMovement($pieza->col, $pieza->row, $pieza->pieceCode, $isWhite, $board_array);
          foreach($auxMovements as $mov)
          {
            array_push($lista_movimientos, $mov);
          }
//          var_dump("knight");
//          var_dump(count($lista_movimientos));
          break;
        case QUEEN:
          $auxBishopQMovements = $this->retrieveAllPosibleBishopMovement($pieza->col, $pieza->row, $pieza->pieceCode, $isWhite, $board_array);
          foreach($auxBishopQMovements as $mov)
          {
            array_push($lista_movimientos, $mov);
          }
//          var_dump("bishop queen");
//          var_dump(count($lista_movimientos));
          $auxRookQMovements = $this->retrieveAllPosibleRookMovement($pieza->col, $pieza->row, $pieza->pieceCode, $isWhite, $board_array);
          foreach($auxRookQMovements as $mov)
          {
            array_push($lista_movimientos, $mov);
          }
//          var_dump("rook queen");
//          var_dump(count($lista_movimientos));
          break;
        case BISHOP:
          $auxBishopMovements = $this->retrieveAllPosibleBishopMovement($pieza->col, $pieza->row, $pieza->pieceCode, $isWhite, $board_array);
          foreach($auxBishopMovements as $mov)
          {
            array_push($lista_movimientos, $mov);
          }
//          var_dump("bishop");
//          var_dump(count($lista_movimientos));
          break;
        case ROOK:
          $auxRookMovements = $this->retrieveAllPosibleRookMovement($pieza->col, $pieza->row, $pieza->pieceCode, $isWhite, $board_array);
          foreach($auxRookMovements as $mov)
          {
            array_push($lista_movimientos, $mov);
          }
//          var_dump("rook");
//          var_dump(count($lista_movimientos));
          break;
        case KING:
          $auxKingMovements = $this->retrieveAllPosibleKingMovement($pieza->col, $pieza->row, $pieza->pieceCode, $isWhite, $board_array, $history_array);
          foreach($auxKingMovements as $mov)
          {
            array_push($lista_movimientos, $mov);
          }
//          var_dump("king");
//          var_dump(count($lista_movimientos));
          break;
        default:
          break;
      }
    }
    
    return $lista_movimientos;
    //DEBUG
    $salida = array();
    foreach($lista_movimientos as $key => $mov_aux)
    {
//      if(!isset($mov_aux->pieceCode))
//      {
//        var_dump($key);
//        var_dump($mov_aux);
//      }
      if(!isset($salida[$mov_aux->pieceCode]))
      {
        $salida[$mov_aux->pieceCode] = 0;
      }
      $salida[$mov_aux->pieceCode] = $salida[$mov_aux->pieceCode] + 1;
      if(getSimplePieceCode($mov_aux->pieceCode) == PAWN)
      {
        //var_dump($mov_aux);
      }
    }
    
    foreach($salida as $key => $value)
    {
      switch (getSimplePieceCode($key)) {
        case PAWN:
          var_dump('los peones son '. $value);
          break;
        case KNIGHT:
          var_dump('los knight son '. $value);
          break;
        case QUEEN:
          var_dump('los queen son '. $value);
          break;      
        case BISHOP:
          var_dump('los bishop son '. $value);
          break;
        case ROOK:
          var_dump('los rook son '. $value);
          break;
        case KING:
          var_dump('los king son '. $value);
          break;
        default:
          break;
      }
    }
    
    return $lista_movimientos;
  }

  private function retrieveAllPosibleKingMovement($column, $row, $kingCode, $isWhite, $board_array, $history_array)
  {
    $king_movements = array();
    for($i = 0; $i < count($this->kingMove); $i++)
    {
      $auxRow = $row + $this->kingMove[$i][0];
      $auxColumn = $column + $this->kingMove[$i][1];
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $kingCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($king_movements, $auxMovement);
      }
    }
    
    $castle_moves = $this->returnKingCastleMovements($isWhite, $board_array, $history_array);
    foreach($castle_moves as $move)
    {
      array_push($king_movements, $move);
    }
    return $king_movements;
  }
  
  private function returnKingCastleMovements($isWhite, $board_array, $history_array)
  {
    $king_castle_movements = array();
    
    $my_color = "black";
    if($isWhite)
    {
      $my_color = "white";
    }
    $found = false;
    $index = 0;
    $leftRookMove = false;
    $rightRookMove = false;
    while(!$found && $index < count($history_array))
    {
      $aux_history = $history_array[$index];
      //var_dump($aux_history);
      if($aux_history["curColor"] == $my_color && $aux_history["curPiece"] == "king")
      {
        $found = true;
      }
      if($aux_history["curColor"] == $my_color && $aux_history["curPiece"] == "rook")
      {
        if((int)$aux_history["fromCol"] == 7)
        {
          $rightRookMove = true;
        }
        else
        {
          $leftRookMove = true;
        }
      }
      
      if($rightRookMove && $leftRookMove)
      {
        $found = true;
      }
      $index++;
    }
    if($found) return $king_castle_movements;
    
    //var_dump('por el historico los mismos se pueden mover');
    $kingRow = 0;
    if(!$isWhite) $kingRow = 7;
    
    if(!$rightRookMove)
    {
      $isPieceInMiddle = false;
      $newIndex = 5;
      while(!$isPieceInMiddle && $newIndex < 7)
      {
        if($board_array[$kingRow][$newIndex] != 0)
        {
          $isPieceInMiddle = true;
          $rightRookMove = true;
        }
        $newIndex++;
      }
    }
    
    if(!$leftRookMove)
    {
      $isPieceInMiddle = false;
      $newIndex = 3;
      while(!$isPieceInMiddle && $newIndex > 0)
      {
        if($board_array[$kingRow][$newIndex] != 0)
        {
          $isPieceInMiddle = true;
          $leftRookMove = true;
        }
        $newIndex--;
      }
    }
    
    if(!$rightRookMove)
    {
      //Chequeo que no este en jaque en el camino
      $copy_board_array = array_copy($board_array);
      //Voy llevando al rey a su posicion
      $copy_board_array[$kingRow][5] = $board_array[$kingRow][4];
      $copy_board_array[$kingRow][4] = 0;
      $is_in_check = $this->checkForKingSafety($copy_board_array, !$isWhite);
      if(!$is_in_check)
      {
        //Coloco el rey en su nueva posicion.
        $copy_board_array[$kingRow][6] = $board_array[$kingRow][4];
        //Pongo aca por que el rey ya se habia movido uno.
        $copy_board_array[$kingRow][5] = 0;
        //Coloco la torre en la nueva posicion
        $copy_board_array[$kingRow][5] = $board_array[$kingRow][7];
        //Vacio el lugar de la torre
        $copy_board_array[$kingRow][7] = 0;
        $is_in_check = $this->checkForKingSafety($copy_board_array, !$isWhite);
        if(!$is_in_check)
        {
          $movimiento = new stdClass();
          $movimiento->noMove = false;
          $movimiento->startingCol = $kingRow;
          $movimiento->startingRow = 4;
          $movimiento->finishCol = $kingRow;
          $movimiento->finishRow = 6;
          $movimiento->pieceCode = $board_array[$kingRow][4];
          $movimiento->isKingRook = true;
          array_push($king_castle_movements, $movimiento);
        }
      }
    }
    if(!$leftRookMove)
    {
      $copy_board_array = array_copy($board_array);
      //Voy llevando al rey a su posicion
      $copy_board_array[$kingRow][5] = $board_array[$kingRow][4];
      $copy_board_array[$kingRow][4] = 0;
      //Chequeo que no este en jaque
      $is_in_check = $this->checkForKingSafety($copy_board_array, !$isWhite);
      if(!$is_in_check)
      {
        //Coloco el rey en su nueva posicion.
        $copy_board_array[$kingRow][2] = $board_array[$kingRow][4];
        //Pongo aca por que el rey ya se habia movido uno.
        $copy_board_array[$kingRow][3] = 0;
        //Coloco la torre en la nueva posicion
        $copy_board_array[$kingRow][3] = $board_array[$kingRow][7];
        //Vacio el lugar de la torre
        $copy_board_array[$kingRow][0] = 0;
        $is_in_check = $this->checkForKingSafety($copy_board_array, !$isWhite);
        if(!$is_in_check)
        {
          $movimiento = new stdClass();
          $movimiento->noMove = false;
          $movimiento->startingCol = $kingRow;
          $movimiento->startingRow = 4;
          $movimiento->finishCol = $kingRow;
          $movimiento->finishRow = 2;
          $movimiento->pieceCode = $board_array[$kingRow][4];
          $movimiento->isKingRook = true;
          array_push($king_castle_movements, $movimiento);
        }
      }
    }
    return $king_castle_movements;
  }

  private function retrieveAllPosibleRookMovement($column, $row, $rookCode, $isWhite, $board_array)
  {
    $rook_movements = array();
    
    //Primero chequeo los movimientos de las columna menores a 8
    $auxColumn = $column;
    $auxRow = $row;
    $finish = false;
    $auxColumn++;
    while(!$finish && $auxColumn < 8)
    {
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $rookCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($rook_movements, $auxMovement);
        if(!$auxMovement->emptySpace)
        {
          //Comio, por lo tanto tiene que parar.
          $finish = true;
        }
      }
      else
      {
        if(!$auxMovement->inCheck)
        {
          $finish = true;
        }
      }
      $auxColumn ++;
    }
    
    //Segundo chequeo los movimientos de las columna mayores a 0
    $auxColumn = $column;
    $auxRow = $row;
    $finish = false;
    $auxColumn --;
    while(!$finish && $auxColumn >= 0)
    {
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $rookCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($rook_movements, $auxMovement);
        if(!$auxMovement->emptySpace)
        {
          //Comio, por lo tanto tiene que parar.
          $finish = true;
        }
      }
      else
      {
        if(!$auxMovement->inCheck)
        {
          $finish = true;
        }
        //$finish = true;
      }
      $auxColumn --;
    }
    
    //Tercero chequeo los movimientos de las filas menores a 8
    $auxColumn = $column;
    $auxRow = $row;
    $finish = false;
    $auxRow ++;
    while(!$finish && $auxRow < 8)
    {
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $rookCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($rook_movements, $auxMovement);
        if(!$auxMovement->emptySpace)
        {
          //Comio, por lo tanto tiene que parar.
          $finish = true;
        }
      }
      else
      {
        if(!$auxMovement->inCheck)
        {
          $finish = true;
        }
        //$finish = true;
      }
      $auxRow ++;
    }
    
    //Cuarto chequeo los movimientos de las filas mayores a 0
    $auxColumn = $column;
    $auxRow = $row;
    $finish = false;
    $auxRow --;
    while(!$finish && $auxRow >= 0)
    {
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $rookCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($rook_movements, $auxMovement);
        if(!$auxMovement->emptySpace)
        {
          //Comio, por lo tanto tiene que parar.
          $finish = true;
        }
      }
      else
      {
        if(!$auxMovement->inCheck)
        {
          $finish = true;
        }
        //$finish = true;
      }
      $auxRow --;
    }
    return $rook_movements;
  }

  private function retrieveAllPosibleBishopMovement($column, $row, $bishopCode, $isWhite, $board_array)
  {
    $bishop_movements = array();

    //Primero chequeo los movimientos en diagonal arriba a la derecha.
    $auxColumn = $column;
    $auxRow = $row;
    $finish = false;
    $auxColumn ++;
    $auxRow ++;
    
    while(!$finish && $auxColumn < 8 && $auxRow < 8)
    {
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $bishopCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($bishop_movements, $auxMovement);
        if(!$auxMovement->emptySpace)
        {
          //Comio, por lo tanto tiene que parar.
          $finish = true;
        }
      }
      else
      {
        if(!$auxMovement->inCheck)
        {
          $finish = true;
        }
        //$finish = true;
      }
      $auxColumn ++;
      $auxRow++;
    }
    
    //Segundo chequeo los movimientos en diagonal abajo a la derecha.
    $auxColumn = $column;
    $auxRow = $row;
    $finish = false;
    $auxColumn ++;
    $auxRow --;
    
    while(!$finish && $auxColumn < 8 && $auxRow >= 0)
    {
      
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $bishopCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($bishop_movements, $auxMovement);
        if(!$auxMovement->emptySpace)
        {
          //Comio, por lo tanto tiene que parar.
          $finish = true;
        }
      }
      else
      {
        if(!$auxMovement->inCheck)
        {
          $finish = true;
        }
        //$finish = true;
      }
      $auxColumn ++;
      $auxRow --;
    }
    
    //Tercero chequeo los movimientos en diagonal arriba a la izquierda.
    $auxColumn = $column;
    $auxRow = $row;
    $finish = false;
    $auxColumn --;
    $auxRow ++;
    
    while(!$finish && $auxColumn >= 0 && $auxRow < 8)
    {
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $bishopCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($bishop_movements, $auxMovement);
        if(!$auxMovement->emptySpace)
        {
          //Comio, por lo tanto tiene que parar.
          $finish = true;
        }
      }
      else
      {
        if(!$auxMovement->inCheck)
        {
          $finish = true;
        }
        //$finish = true;
      }
      $auxColumn --;
      $auxRow ++;
    }

    //Cuarto chequeo los movimientos en diagonal abajo a la izquierda.
    $auxColumn = $column;
    $auxRow = $row;
    $finish = false;
    $auxColumn --;
    $auxRow --;
    
    while(!$finish && $auxColumn >= 0 && $auxRow >= 0)
    {
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $bishopCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($bishop_movements, $auxMovement);
        if(!$auxMovement->emptySpace)
        {
          //Comio, por lo tanto tiene que parar.
          $finish = true;
        }
      }
      else
      {
        if(!$auxMovement->inCheck)
        {
          $finish = true;
        }
        //$finish = true;
      }
      $auxColumn --;
      $auxRow --;
    }
    return $bishop_movements;
  }
  
  
  
  private function retrieveAllPosibleKnightMovement($column, $row, $knigthCode, $isWhite, $board_array)
  {
    $knigth_moves = array();
    for($i=0; $i < 8; $i++)
    {
      $auxRow = $row + $this->knightMove[$i][0];
      $auxColumn = $column + $this->knightMove[$i][1];
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $knigthCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($knigth_moves, $auxMovement);
      }
    }
    return $knigth_moves;
  }
  
  private function retrieveAllPosiblePawnMovement($column, $row, $pawnCode, $isWhite, $board_array, $history_array)
  {
//    var_dump($column);
//    var_dump($row);
    $pawn_moves = array();
    //Defino el movimiento basico del peon y la fila de inicio.
    $pawnRow = 1;
    $moveForward = 1;
    if(!$isWhite)
    {
      $pawnRow = 6;
      $moveForward = -1;
    }
    //Chequeo el avance simple
    $auxColumn = $column + $moveForward;
    $auxRow = $row;
    
    if(pieceIsInBoard($auxColumn, $auxColumn) && $board_array[$auxColumn][$auxRow] == 0)
    {
      //El lugar esta vacio por lo tanto se puede mover.
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $pawnCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($pawn_moves, $auxMovement);
      }
    }
    
    //Chequeo si puedo comer a los costados.
    //primero chequeo a la derecha
    $auxColumn = $column + $moveForward;
    $auxRow = $row + $moveForward;
    if( (pieceIsInBoard($auxColumn, $auxRow)) 
            && ($board_array[$auxColumn][$auxRow] != 0) 
            && (($isWhite && $board_array[$auxColumn][$auxRow] > BLACK) 
            || (!$isWhite && $board_array[$auxColumn][$auxRow] < BLACK)))
    {
      //El lugar ocupado por una pieza enemiga por lo tanto se puede comer.
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $pawnCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($pawn_moves, $auxMovement);
      }
    }
    
    //despues chequeo a la izquierda
    $auxColumn = $column + $moveForward;
    $auxRow = $row - $moveForward;
    if( (pieceIsInBoard($auxColumn, $auxRow)) 
            && ($board_array[$auxColumn][$auxRow] != 0) 
            && (($isWhite && $board_array[$auxColumn][$auxRow] > BLACK) 
            || (!$isWhite && $board_array[$auxColumn][$auxRow] < BLACK)))
    {
      //El lugar ocupado por una pieza enemiga por lo tanto se puede comer.
      $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $pawnCode, $isWhite, $board_array);
      if(!$auxMovement->noMove)
      {
        array_push($pawn_moves, $auxMovement);
      }
    }
    
    //Chequeo si se puede mover dos filas, dado que nunca se movio.
    if($pawnRow == $column)
    {
      //El peon nunca se movio.
      $auxColumn = $column + $moveForward + $moveForward;
      $auxRow = $row;
      if(pieceIsInBoard($auxColumn, $auxRow) && $board_array[$auxColumn][$auxRow] == 0)
      {
        //El lugar esta vacio por lo tanto se puede mover.
        $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $pawnCode, $isWhite, $board_array);
        if(!$auxMovement->noMove)
        {
          array_push($pawn_moves, $auxMovement);
        }
      }
    }
    
    //Ahora el ultimo chequeo seria el poder hacer que el peon coma al paso.
    //Para eso tengo que chequear que el ultimo movimiento sea de un peon.
    $aux_history = null;
    if(count($history_array)> 0)
    {
      $aux_history = $history_array[count($history_array) - 1];
    }
    if(!is_null($aux_history) && $aux_history["curPiece"] == "pawn")
    {
      /*
      var_dump($aux_history);
      var_dump(abs($aux_history["fromRow"] - $aux_history["toRow"]) == 2);
      var_dump(abs($aux_history["fromCol"] - $aux_history["toCol"]) == 2);
      */
      if(abs($aux_history["fromCol"] - $aux_history["toCol"]) == 2)
      {
//        var_dump('movio dos posiciones');
//        var_dump('row + 1');
//        var_dump($aux_history["toRow"] == $row + 1);
//        var_dump($aux_history["fromCol"] == $column);
//        var_dump('row - 1');
//        var_dump($aux_history["toRow"] == $row - 1);
//        var_dump('la columna es :'.$column);
//        var_dump('la columna historica es :'.$aux_history["toCol"]);
        //Movio dos posiciones. 
        //Tengo que chequear que este al lado del peon que estoy moviendo.
        if($aux_history["toRow"] == $row + 1 && $aux_history["toCol"] == $column)
        {
          //Esta al lado!!!
          //Entonces puedo comer ;)
          $auxColumn = $column + $moveForward;
          $auxRow = $row + 1;
          $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $pawnCode, $isWhite, $board_array);
          if(!$auxMovement->noMove)
          {
            $auxMovement->pawnEnPassant = true;
            array_push($pawn_moves, $auxMovement);
          }
        }
        
        //Tengo que chequear que este al lado del peon que estoy moviendo.
        //Chequeo para el otro lado
        if($aux_history["toRow"] == $row - 1 && $aux_history["toCol"] == $column)
        {
          //Esta al lado!!!
          //Entonces puedo comer ;)
          $auxColumn = $column + $moveForward;
          $auxRow = $row - 1;
          $auxMovement = $this->isPiecePosibleMovement($column, $row, $auxColumn, $auxRow, $pawnCode, $isWhite, $board_array);
          if(!$auxMovement->noMove)
          {
            $auxMovement->pawnEnPassant = true;
            array_push($pawn_moves, $auxMovement);
          }
        }
      }
    }
    return $pawn_moves;
  }
  
  /**
   *
   * @param int $startColumn
   * @param int $startRow
   * @param int $finishColumn
   * @param int $finishRow
   * @param int $pieceCode
   * @param boolean $isWhite
   * @param multidimension array $board_array
   * @return stdClass 
   */
  private function isPiecePosibleMovement($startColumn, $startRow, $finishColumn, $finishRow, $pieceCode, $isWhite, $board_array)
  {
    $salida = new stdClass();
    //Chequeo que este adentro del tablero
    if(!pieceIsInBoard($finishRow, $finishColumn))
    {
      //Esta por afuera del tablero por lo tanto no es posible.
      $salida->noMove = true;
      $salida->inCheck = false;
      return $salida;
    }
    
    //Chequeo que no caiga en una pieza mia.
    //if(($board_array[$finishColumn][$finishRow] != 0) && ($isWhite && $board_array[$finishColumn][$finishRow] < BLACK) || (!$isWhite && $board_array[$finishColumn][$finishRow] > BLACK))
    if( (pieceIsInBoard($finishColumn, $finishRow)) 
        && ($board_array[$finishColumn][$finishRow] != 0) 
        && (($isWhite && $board_array[$finishColumn][$finishRow] < BLACK) 
        || (!$isWhite && $board_array[$finishColumn][$finishRow] > BLACK)))
    {
      //Estoy cayendo en una pieza mia por lo tanto no es valido.
      $salida->noMove = true;
      $salida->inCheck = false;
      return $salida;
    }
    else
    {
      $copy_board_array = array_copy($board_array);
      //Muevo las piezas como deberia de quedar.
      $startingPiece = $copy_board_array[$finishColumn][$finishRow];
      $copy_board_array[$startColumn][$startRow] = 0;
      $copy_board_array[$finishColumn][$finishRow] = $pieceCode;
      //Con el nuevo tablero chequeo que el rey no este en jaque.
      $isCheck = $this->checkForKingSafety($copy_board_array, !$isWhite);
      if(!$isCheck)
      {
        if($startingPiece == 0)
        {
          $salida->emptySpace = true;
        }
        else
        {
          $salida->emptySpace = false;
        }
        $salida->noMove = false;
        $salida->startingCol = $startColumn;
        $salida->startingRow = $startRow;
        $salida->finishCol = $finishColumn;
        $salida->finishRow = $finishRow;
        $salida->pieceCode = $pieceCode;
        $salida->inCheck = false;
        return $salida;
      }
      else
      {
        $salida->noMove = true;
        $salida->inCheck = true;
        return $salida;
      }
    }
  }
  
  
  public function checkForOtherKingSafety($board_aux_array, $enemy_is_white)
  {
    $kingPosition = $this->retrieveKingPosition($board_aux_array, $enemy_is_white);
    
    //var_dump($kingPosition);
    $isCheck = $this->isKingInCheckByKnight($board_aux_array, $kingPosition->col, $kingPosition->row, !$enemy_is_white);
    //var_dump('isKingInCheckByKnight');
    //var_dump($isCheck);
    if($isCheck)
    {
      return $isCheck;
    }
    
    $isCheck = $this->isKingInCheckByBishopOrQueen($board_aux_array, $kingPosition->col, $kingPosition->row, !$enemy_is_white);
    //var_dump('isKingInCheckByBishopOrQueen');
    //var_dump($isCheck);
    if($isCheck)
    {
      return $isCheck;
    }
    
    $isCheck = $this->isKingInCheckByRookOrQueen($board_aux_array, $kingPosition->col, $kingPosition->row, !$enemy_is_white);
    //var_dump('isKingInCheckByRookOrQueen');
    //var_dump($isCheck);
    if($isCheck)
    {
      return $isCheck;
    }
    
    $isCheck = $this->isKingInCheckByPawn($board_aux_array, $kingPosition->col, $kingPosition->row, !$enemy_is_white);
    //var_dump('isKingInCheckByPawn');
    //var_dump($isCheck);
    if($isCheck)
    {
      return $isCheck;
    }
    
    $isCheck = $this->isKingInCheckByKing($board_aux_array, $kingPosition->col, $kingPosition->row, !$enemy_is_white);
    //var_dump('isKingInCheckByKing');
    //var_dump($isCheck);
    if($isCheck)
    {
      return $isCheck;
    }
    
    return $isCheck;
  }
  
  public function checkForKingSafety($board_aux_array, $enemy_is_white)
  {
    $kingPosition = $this->retrieveKingPosition($board_aux_array, !$enemy_is_white);
    
    //var_dump($kingPosition);
    $isCheck = $this->isKingInCheckByKnight($board_aux_array, $kingPosition->col, $kingPosition->row, $enemy_is_white);
    if($isCheck)
    {
      return $isCheck;
    }
    
    $isCheck = $this->isKingInCheckByBishopOrQueen($board_aux_array, $kingPosition->col, $kingPosition->row, $enemy_is_white);
    
    if($isCheck)
    {
      return $isCheck;
    }
    
    $isCheck = $this->isKingInCheckByRookOrQueen($board_aux_array, $kingPosition->col, $kingPosition->row, $enemy_is_white);
    if($isCheck)
    {
      return $isCheck;
    }
    
    $isCheck = $this->isKingInCheckByPawn($board_aux_array, $kingPosition->col, $kingPosition->row, $enemy_is_white);
    if($isCheck)
    {
      return $isCheck;
    }
    
    $isCheck = $this->isKingInCheckByKing($board_aux_array, $kingPosition->col, $kingPosition->row, $enemy_is_white);
    if($isCheck)
    {
      return $isCheck;
    }
    
    return $isCheck;
  }
  

  /**
   *
   * Chequeo que el rey no este chequeado por el otro rey.
   * 
   * @param array $board_aux_array
   * @param int $col
   * @param int $row
   * @param boolean $enemy_is_white
   * @return boolean
   * @author Rodrigo Santellan 
   */
  private function isKingInCheckByKing($board_aux_array, $col, $row, $enemy_is_white)
  {
    //Obtengo el codigo del caballo enemigo.
    $king = KING;
    if(!$enemy_is_white)
    {
      $king = KING + BLACK;
    }
    //Recorro los 8 posibles movimientos del caballo.
    for($i = 0; $i < 8; $i++)
    {
      $fromRow = $row + $this->kingMove[$i][0];
      $fromCol = $col + $this->kingMove[$i][1];
      if(pieceIsInBoard($fromRow, $fromCol))
      {
        if($board_aux_array[$fromCol][$fromRow] == $king)
        {
          return true;
        }
      }
    }
    return false;
  }  
  
  /**
   *
   * Los peones solo van para adelante.
   * Por lo tanto lo unico que tendria que chequear seria las dos diagonales
   * En direccion de las piezas contrarias.
   * Si es blanca seria.
   *  [+1, +1], [-1, +1]
   * Si es Negra seria.
   *  [+1, -1], [-1, -1]
   * @param array $board_aux_array
   * @param int $col
   * @param int $row
   * @param boolean $enemy_is_white
   * @return boolean
   * @author Rodrigo Santellan 
   */
  private function isKingInCheckByPawn($board_aux_array, $col, $row, $enemy_is_white)
  {

    $auxColLeft = $col - 1;
    $auxColRight = $col + 1;
    $auxRow = $row;
    $pawnCode = PAWN;
    if(!$enemy_is_white)
    {
      $auxRow++;
      $pawnCode = PAWN + BLACK;
    }
    else
    {
      $auxRow--;
    }
    //Chequeo a la izquierda.
    if(pieceIsInBoard($auxRow, $auxColLeft))
    {
      if ($board_aux_array[$auxColLeft][$auxRow] == $pawnCode)
      {
        return true;
      }
    }
    //Chequeo a la derecha.
    if(pieceIsInBoard($auxRow, $auxColRight))
    {
      if ($board_aux_array[$auxColRight][$auxRow] == $pawnCode)
      {
        return true;
      }
    }
    return false;  
  }

  
  /**
   *
   * La forma de chequear va a ser.
   * Chequeo:
   *  - para arriba
   *  - para abajo
   *  - a la derecha
   *  - a la izquierda
   * @param array $board_aux_array
   * @param int $col
   * @param int $row
   * @param boolean $enemy_is_white
   * @return boolean
   * @author Rodrigo Santellan 
   */
  private function isKingInCheckByRookOrQueen($board_aux_array, $col, $row, $enemy_is_white)
  {
    $finish_rook_check = false;
    $aux_col_king = $col;
    $aux_row_king = $row;

    // arriba
    while(!$finish_rook_check)
    {
      $aux_col_king = $aux_col_king;
      $aux_row_king = $aux_row_king + 1;

      $result = $this->auxIsKingInCheckByRookOrQueen($board_aux_array, $aux_col_king, $aux_row_king, $enemy_is_white);
      switch($result)
      {
        case 2:
          return true;
          break;
        case 1:
          $finish_rook_check = true;
          break;
      }
    }

    $finish_rook_check = false;
    $aux_col_king = $col;
    $aux_row_king = $row;

    // abajo
    while(!$finish_rook_check)
    {
      $aux_col_king = $aux_col_king;
      $aux_row_king = $aux_row_king - 1;
      $result = $this->auxIsKingInCheckByRookOrQueen($board_aux_array, $aux_col_king, $aux_row_king, $enemy_is_white);
      switch($result)
      {
        case 2:
          return true;
          break;
        case 1:
          $finish_rook_check = true;
          break;
      }
    }

    $finish_rook_check = false;
    $aux_col_king = $col;
    $aux_row_king = $row;

    // a la derecha
    while(!$finish_rook_check)
    {
      $aux_col_king = $aux_col_king + 1;
      $aux_row_king = $aux_row_king;
      $result = $this->auxIsKingInCheckByRookOrQueen($board_aux_array, $aux_col_king, $aux_row_king, $enemy_is_white);
      switch($result)
      {
        case 2:
          return true;
          break;
        case 1:
          $finish_rook_check = true;
          break;
      }
    }

    $finish_rook_check = false;
    $aux_col_king = $col;
    $aux_row_king = $row;

    // a la izquierda
    while(!$finish_rook_check)
    {
      $aux_col_king = $aux_col_king - 1;
      $aux_row_king = $aux_row_king;
      $result = $this->auxIsKingInCheckByRookOrQueen($board_aux_array, $aux_col_king, $aux_row_king, $enemy_is_white);
      switch($result)
      {
        case 2:
          return true;
          break;
        case 1:
          $finish_rook_check = true;
          break;
      }
    }
    return false;
  }


  /**
   *
   * Funcion auxiliar para determinar los chequeos de la reina o la torre
   * 
   * @param array $board_aux_array
   * @param int $col
   * @param int $row
   * @param boolean $enemy_is_white
   * @return int en caso de que se encuentra uno devuelve 2, en caso de que este vacio devuelve 0 y en caso de que sea otra pieza devuelve 1
   * @author Rodrigo Santellan 
   */
  private function auxIsKingInCheckByRookOrQueen($board_aux_array, $col, $row, $enemy_is_white)
  {
    $rook = ROOK;
    $queen = QUEEN;
    if(!$enemy_is_white)
    {
      $rook = ROOK + BLACK;
      $queen = QUEEN + BLACK;
    }
/*
    var_dump("auxIsKingInCheckByRookOrQueen");
    var_dump($rook);
    var_dump($queen);
  */  
    if(pieceIsInBoard($row, $col))
    {
      //var_dump($board_aux_array[$col][$row]);
      if ($board_aux_array[$col][$row] == $rook || $board_aux_array[$col][$row] == $queen)
      {
        //Enemy Rook or Queen found
        return 2;
      }
      else
      {
        if($board_aux_array[$col][$row] != 0)
        {
          return 1;
        }
      }
    }
    else
    {
      return 1;
    }
    return 0;
  }  

  
  /**
   *
   * La forma de chequear va a ser.
   * Chequeo:
   *  - arriba a la derecha
   *  - arriba a la izquierda
   *  - abajo a la derecha
   *  - abajo a la izquierda
   * 
   * @param array $board_aux_array
   * @param int $col
   * @param int $row
   * @param boolean $enemy_is_white
   * @return boolean
   * @author Rodrigo Santellan 
   */
  private function isKingInCheckByBishopOrQueen($board_aux_array, $col, $row, $enemy_is_white)
  {
//    echo 'isKingInCheckByBishopOrQueen <br/>';
//    var_dump($col);
//    var_dump($row);
//    var_dump($enemy_is_white);
//    echo '<br/>';
    $finish_bishop_check = false;
    $aux_col_king = $col;
    $aux_row_king = $row;
    // arriba a la derecha
    while(!$finish_bishop_check)
    {
      $aux_col_king = $aux_col_king + 1;
      $aux_row_king = $aux_row_king + 1;
//      echo '<hr/>';
//      echo 'columna '.$aux_col_king.'<br/>';
//      echo 'row '.$aux_row_king.'<br/>';
      
      $result = $this->auxIsKingInCheckByBishopOrQueen($board_aux_array, $aux_col_king, $aux_row_king, $enemy_is_white);
//      echo '<br/>devolvio '.$result.'<br/>';
      //var_dump($result);
      switch ($result) {
        case 2:
          return true;
          break;
        case 1:
          $finish_bishop_check = true;
          break;
      }
    }
    
    $finish_bishop_check = false;
    $aux_col_king = $col;
    $aux_row_king = $row;
    // arriba a la izquierda 
    while(!$finish_bishop_check)
    {
      $aux_col_king = $aux_col_king - 1;
      $aux_row_king = $aux_row_king + 1;
//      echo '<hr/>';
//      echo 'columna '.$aux_col_king.'<br/>';
//      echo 'row '.$aux_row_king.'<br/>';
      $result = $this->auxIsKingInCheckByBishopOrQueen($board_aux_array, $aux_col_king, $aux_row_king, $enemy_is_white);
      switch ($result) {
        case 2:
          return true;
          break;
        case 1:
          $finish_bishop_check = true;
          break;
      }
    }
    
    $finish_bishop_check = false;
    $aux_col_king = $col;
    $aux_row_king = $row;
    // abajo a la derecha
    while(!$finish_bishop_check)
    {
      $aux_col_king = $aux_col_king + 1;
      $aux_row_king = $aux_row_king - 1;
//      echo '<hr/>';
//      echo 'columna '.$aux_col_king.'<br/>';
//      echo 'row '.$aux_row_king.'<br/>';
      $result = $this->auxIsKingInCheckByBishopOrQueen($board_aux_array, $aux_col_king, $aux_row_king, $enemy_is_white);
      switch ($result) {
        case 2:
          return true;
          break;
        case 1:
          $finish_bishop_check = true;
          break;
      }
    }
    
    $finish_bishop_check = false;
    $aux_col_king = $col;
    $aux_row_king = $row;
    // abajo a la izquierda
    while(!$finish_bishop_check)
    {
      $aux_col_king = $aux_col_king - 1;
      $aux_row_king = $aux_row_king - 1;
//      echo '<hr/>';
//      echo 'columna '.$aux_col_king.'<br/>';
//      echo 'row '.$aux_row_king.'<br/>';
      $result = $this->auxIsKingInCheckByBishopOrQueen($board_aux_array, $aux_col_king, $aux_row_king, $enemy_is_white);
      switch ($result) {
        case 2:
          return true;
          break;
        case 1:
          $finish_bishop_check = true;
          break;
      }
    }
    return false;
  }
  
  /**
   *
   * Funcion auxiliar para determinar los chequeos de la reina o el alfil
   * 
   * @param array $board_aux_array
   * @param int $col
   * @param int $row
   * @param boolean $enemy_is_white
   * @return int en caso de que se encuentra uno devuelve 2, en caso de que este vacio devuelve 0 y en caso de que sea otra pieza devuelve 1
   * @author Rodrigo Santellan 
   */
  private function auxIsKingInCheckByBishopOrQueen($board_aux_array, $col, $row, $enemy_is_white)
  {
    $bishop = BISHOP;
    $queen = QUEEN;
    if(!$enemy_is_white)
    {
      $bishop = BISHOP + BLACK;
      $queen = QUEEN + BLACK;
    }
    /*
    echo '<hr/>';
    
    var_dump("auxIsKingInCheckByBishopOrQueen - bishop");
    var_dump($bishop);
    var_dump("auxIsKingInCheckByBishopOrQueen - queen");
    var_dump($queen);
    
    echo '<hr/>';
    var_dump($col);
    var_dump($row);
    echo '<hr/>';
    */
    if(pieceIsInBoard($row, $col))
    {
//      var_dump($board_aux_array[$col]);
//      var_dump($board_aux_array[$col][$row]);
//      echo '<hr/>';
      //console.log(" la diagonal es : " + board_auxiliary[col][row]);
      if ($board_aux_array[$col][$row] == $bishop || $board_aux_array[$col][$row] == $queen)
      {
        //Enemy Bishop or Queen found
        return 2;
      }
      else
      {
        if($board_aux_array[$col][$row] != 0)
        {
          
//          var_dump('estoy aca??');
          return 1;
        }
      }
    }
    else
    {
//      var_dump('estoy aca 1??');
      return 1;
    }
    return 0;
  }
  
  
  /**
   *
   * Chequeo que el rey no este chequeado por un caballo.
   * 
   * @param array $board_aux_array
   * @param int $col
   * @param int $row
   * @param boolean $enemy_is_white
   * @return boolean
   * @author Rodrigo Santellan 
   */
  private function isKingInCheckByKnight($board_aux_array, $col, $row, $enemy_is_white)
  {
    //Obtengo el codigo del caballo enemigo.
    $knight = KNIGHT;
    if(!$enemy_is_white)
    {
      $knight = KNIGHT + BLACK;
    }
    /*
    var_dump('el caballo enemigo es:');
    var_dump($knight);
    echo '<br/>';
    */
    //Recorro los 8 posibles movimientos del caballo.
    for($i = 0; $i < 8; $i++)
    {
      $fromRow = $row + $this->knightMove[$i][0];
      $fromCol = $col + $this->knightMove[$i][1];
      if(pieceIsInBoard($fromRow, $fromCol))
      {
        /*
        echo '<br/>';
        echo 'la pieza es:';
        echo '<br/>';
        var_dump($board_aux_array[$fromCol][$fromRow]);
        echo '<br/>';
        */
        if($board_aux_array[$fromCol][$fromRow] == $knight)
        {
          return true;
        }
      }
    }
    return false;
  }
  
  private function retrieveKingPosition($board_aux_array, $is_white)
  {
    $kingPosition = new stdClass();
    $found = false;
    $king_value = KING;
    if(!$is_white)
    {
      $king_value = $king_value + BLACK;
    }
    $i = 0;
    while(!$found)
    {
      $j = 0;
      while($j < 8 && !$found)
      {
        if($board_aux_array[$i][$j] == $king_value)
        {
          $kingPosition->col = $i;
          $kingPosition->row = $j;
          $found = true;
        }
        else
        {
          $j++;
        }
      }
      if(!$found)
      {
        $i++;
      }
    }
    return $kingPosition;
  }
  
}

