<tr class="tr_row_inlist <?php echo ($first) ? "first" : "second"; ?>">
  <?php
  $quantity = 0;
  foreach ($row as $key => $value):
?>
	  <td>
		<?php echo $value; ?>
	  </td>
<?php		
  endforeach;
  ?>
</tr>