

<?php
  foreach($games as $game):
?>
  <div id ="<?php echo $game["gameID"]; ?>">
  
	<ul>
	  <li>
		<a href="<?php echo site_url("chess/show/".$game["gameID"]);?>">
		  Ver juego
		</a>
	  </li>
	  <li><?php echo $game["whitePlayer"]; ?></li>
	  <li><?php echo $game["blackPlayer"]; ?></li>
	</ul>
	
  </div>
<?php endforeach; ?>
