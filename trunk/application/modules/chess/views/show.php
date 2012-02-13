<?php 
$squareSize = 50;
$classWSquare = 'light_enabled';
$classBSquare = 'dark_enabled';
$classHeader = 'header_enabled';
$sqBackground = array($classWSquare, $classBSquare);
$borderWidth = $squareSize / 2;
$rank = 8;
$rankLabel = $rank;
$files = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
//var_dump($isWhite);
//var_dump($enableAjax);
?>

<div class="clear"></div>
<table id="theBoard" cellpadding="0" style="float: left; border:1px solid #888; padding:0; border-collapse: collapse;border-spacing:0; margin-bottom:5px;">
  <tr id="bordertop" style="height: <?php echo $borderWidth;?>px;"><td colspan="10" class="list_textos <?php echo $classHeader;?>">&nbsp;</td></tr>
  <tr><td id="rank<?php echo $rank--;?>" class="list_textos <?php echo $classHeader;?>" width="<?php echo $borderWidth;?>"><?php echo $rankLabel;?></td>
  
	
<?php
$row = 0;
$col = 0;
$j = 1;
$i = 0;

for($k = 63; $k >= 0; $k--):

  if(($k+1) % 8 == 0)
  {
    $i = $k - 7;
    if(!$isWhite)
    {
      $i = 63 - $i;
    }
    
  }
  else
  {
    if($isWhite)
    {
      $i++;
    }
    else
    {
      $i--;
    }
  }
?>
	<td id="tsq<?php echo $i;?>" class="list_textos <?php echo $sqBackground[$j];?>" width="<?php echo $squareSize;?>" height="<?php echo $squareSize;?>">
	  
	  <?php 
		$piece = '';
		$row = (int)($i / 8);
		$col = $i % 8;
		$piece_color = getPieceColor($board_js[$row][$col]);
		$piece_figure = getPieceName($board_js[$row][$col]);
		$piece =  $piece_color . '_' .$piece_figure;
	  ?>
	  
	  <div id="droppable_<?php echo $i;?>" class="droppable" position="<?php echo $i;?>">
		<?php 
		if($piece != "white_empty")
		{
      if($piece_color == 'white' && $isWhite || $piece_color == 'black' && !$isWhite)
      {
        $piece .= " draggable";
      }
		}
		else
		{
		  $piece .= " empty";
		}
		?>
		<div class="<?php echo $piece;?>" position="<?php echo $i;?>"></div>
	  </div>
	  
	  
	</td>  
<?php  
  if($k % 8 === 0):
?>
	<td id="rbrd<?php echo $rank++;?>" class="list_textos <?php echo $classHeader;?>" width="<?php echo $borderWidth;?>">
	  &nbsp;
	</td>
  </tr>
<?php	
  if($k != 0):
	$rank = $rank - 1;
	$rankLabel = $rank;
  ?>
	<tr><td id="rank<?php echo $rank--;?>" class="list_textos <?php echo $classHeader;?>" width="<?php echo $borderWidth;?>"><?php echo $rankLabel;?></td>
  <?php
  endif;

  else:
	$j = 1 - $j;
  endif;
  
  
endfor;  
?>
  <tr id="borderbottom" class="list_textos <?php echo $classHeader;?>" height="<?php echo $borderWidth;?>">
	<td width="<?php echo $borderWidth;?>">&nbsp;</td>
  <?php
  $file_label = "";
  $l = 0;
  while($l < 8):
	
	$file_label = $files[$l];
?>
  <td id="file<?php echo $l;?>" class="list_textos <?php echo $classHeader;?>">  <?php echo $file_label;?> </td>
	
<?php
  $l++;
  endwhile;
  
  ?>
  <td id="rbrd0" class="list_textos <?php echo $classHeader;?>">&nbsp;</td></tr>
</table>

<div class="botones">
  
  
</div>

<div class="messages">
  <?php if($myTourn): ?>
  
    <span>Tu turno</span>
  <?php else: ?>
    <span>Turno del contricante</span>
  <?php endif; ?>
</div>

<div class="history_table">
  <div class="history">
	<?php
	  $moves = 1;
	?>
	<table id="table_history" class="table_history">
	  <thead>
		<tr>
		  <td>Num</td>
		  <td>Blancas</td>
		  <td>Negras</td>
		</tr>
	  </thead>
	  <tbody>
	  <?php 
	  $index = 0;
	  $row_index = 0;
	  $ul_is_open = false;
	  $row_counts = 1;
	  while($index < count($history_js)):
		$aux = $history_js[$index];
	  ?>
	  <?php if($row_index == 0): ?>
	
	  <tr>
	  <td> <?php echo $row_counts;?></td>
	  <?php $row_counts++;?>
	  <?php  endif;?>
	  <td id="movement_<?php echo $index;?>" movement="<?php echo $index;?>" class="moveToPosition">
		<?php //var_dump($aux);?>
		<?php echo getHistoryMove($aux);?>
      </td>

	  <?php if($row_index == 1): ?>
	  </tr>
		<?php $row_index = 0; ?>
	<?php  
	  else:
		$row_index++;
	endif;?>	  
	  
		
	<?php
	  $index++;
	  endwhile;
	  ?>
	  
	  <?php if($row_index == 1): ?>
	  <td></td></tr>
	  <?php endif; ?>
	  </tbody>
	</table>
	
  </div>
</div>

<script type="text/javascript">

  var gameId = <?php echo $game->getGameId(); ?>;

  var history_js = <?php echo json_encode($history_js); ?>;
  
  var board_js = <?php echo json_encode($board_js); ?>;
  
  var PAWN = "<?php echo PAWN;?>";
  var KNIGHT = "<?php echo KNIGHT?>";
  var BISHOP = "<?php echo BISHOP?>";
  var ROOK = "<?php echo ROOK?>";
  var QUEEN = "<?php echo QUEEN?>";
  var KING = "<?php echo KING?>";
  var BLACK = "<?php echo BLACK?>";
  var WHITE = "<?php echo WHITE?>";
  var player_is_white = true;
  var enable_ajax = false;
  <?php if(!$isWhite): ?>
	 player_is_white = false;
  <?php endif; ?>
  <?php if($enableAjax): ?>
	 enable_ajax = true;
  <?php endif; ?>  

  var can_move = false;
  <?php if($myTourn): ?>
    can_move = true;
  <?php endif; ?>    
    var send_movement_url = '<?php echo site_url('chess/movePiece');?>';
</script>


<div class="clear"></div>
