
<a href="<?php echo site_url("chess/ratings");?>" >
  
  Ratings
  
</a>

<?php
  foreach($games as $game):
?>
  <div id ="<?php echo $game->getGameId(); ?>">
  
	<ul>
	  <li>
		<a href="<?php echo site_url("chess/show/".$game->getGameId());?>">
		  Ver juego
		</a>
	  </li>
	  <li>
		Blancas:
		<?php 
		  $white = $players[$game->getWhitePlayer()];
		  echo $white->nick; 
		?>
	  </li>
	  <li>
		Negras:
		<?php 
		  $black = $players[$game->getBlackPlayer()];
		  echo $black->nick; 
		?>
	  </li>
	  <li>
		<?php echo $game->getGameMessage(); ?>
	  </li>
	  <li>
		<?php if($game->getIsOver()):
		  echo "El juego termino";
		endif; ?>
	  </li>
	</ul>
	
  </div>
<?php endforeach; ?>
