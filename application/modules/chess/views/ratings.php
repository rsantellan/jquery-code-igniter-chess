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

<div class="clear"></div>


<div id="chart1"></div>

<script type="text/javascript">

var players_keys = new Array();

</script>

<?php foreach($players_history as $key => $value): ?>
<div style="float:left; border-style:solid; border-width:1px; margin-right: 5px; margin-bottom: 30px;">
  <div class="usu_nick" style="text-align:center;">
	<span style="color:blue;font-weight: bold;"><?php echo $players[$key]->nick;?></span>
  </div>
  <div class="usu_results_container" style="width: 300px; height: 300px;">
	<div id="usu_results_id_<?php echo $key;?>" class="usu_results">
	</div>
  </div>
  <div style="clear: both"></div>
  <div class="usu_results_container" style="width: 300px; height: 300px; text-align:center;">
	<div id="usu_elo_results_id_<?php echo $key;?>" class="usu_elo_results">

	</div>
	
	<input type="button" value="Alejar" onclick="return resetZoom(<?php echo $key;?>)" />
  </div>
  <?php //echo $key . " - ".count($value->getGameList()); ?>
</div>

<script type="text/javascript">
  
  var data_<?php echo $key;?> = [
			  ['Ganados',<?php echo $value->getWinQuantity();?>],
			  ['Perdidos',<?php echo $value->getLooseQuantity();?>],
			  ['Empatados',<?php echo $value->getTieQuantity();?>]
			];
  
var elo_data_<?php echo $key;?> = <?php echo json_encode($value->getEloHistory()); ?>;

</script>  
    

<?php endforeach; ?>
<div style="clear: both"></div>

<script type="text/javascript">
	
$(document).ready(function(){
  
  $.jqplot.config.enablePlugins = true;
  

<?php foreach($players_history as $key => $value): ?>
	
  var container_id = 'usu_results_id_<?php echo $key;?>';
  var aux_data = [data_<?php echo $key;?>];
  var line_container_id = 'usu_elo_results_id_<?php echo $key;?>';
  var line_aux_data = [elo_data_<?php echo $key;?>];
  
  initPieGraph(container_id, aux_data);
  initAxisGraph(line_container_id, line_aux_data, <?php echo $key;?>);

<?php endforeach; ?>

});
</script>