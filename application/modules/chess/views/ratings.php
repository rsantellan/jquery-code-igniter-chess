<?php
//var_dump($players);
?>
<?php
//var_dump($players_ratings);
?>

<table>
  <thead>
	<tr>
	  <th>Nick</th>
	  <th>Ganados</th>
	  <th>Perdidos</th>
	  <th>Empatados</th>
	  <th>Cantidad de partidos</th>
	  <th>Puntos</th>
	</tr>
  </thead>
  <tbody>
	<?php foreach($players_ratings as $pr): ?>
	
	
	<tr>
	  <td><?php echo $players[$pr->getPlayerId()]->nick; ?></td>
	  <td><?php echo $pr->getWin(); ?></td>
	  <td><?php echo $pr->getLose(); ?></td>
	  <td><?php echo $pr->getDraw(); ?></td>
	  <td><?php echo $pr->getTotalGames();?></td>
	  <td><?php echo $pr->getPoints(); ?></td>
	</tr>
	<?php endforeach; ?>
  </tbody>
</table>
  
  