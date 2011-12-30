<tr class="tr_row_inlist <?php echo ($first) ? "first" : "second"; ?>">
  <?php
  foreach ($row as $key => $value):
	?>

    <td>
	  <?php echo $value; ?>
    </td>
	<?php
  endforeach;
  ?>
    <td style="width: 120px;">
  	<div class="actions_menu">
  	  <ul>

  		<li><a class="edit" href="<?php echo site_url($obj_module . "/edit/" . $row[$table_key]); ?>">edit</a></li>
  		<li><a class="delete" onclick="basicFunctions.getInstance().deleteRowAndReturnSystemMessage('<?php echo site_url($obj_module . "/delete/" . $row[$table_key]); ?>', this)" href="javascript:void(0)">delete</a></li>
  	  </ul>
  	</div>
    </td>
</tr>