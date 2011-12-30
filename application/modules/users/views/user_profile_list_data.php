<tr class="tr_row_inlist <?php echo ($first) ? "first" : "second"; ?>">
  <?php
  $quantity = 0;
  foreach ($row as $key => $value):
	
	switch ($key):
	  case "USER_COUNT":
		$quantity = $value;
?>
	  <td>
		<?php echo $value; ?>
	  </td>
<?php
		break;
	  case "PERMISSION_COUNT":
?>
	  <td>
		<a href="<?php echo site_url($obj_module . "/viewUserProfilePermissions/" . $row[$table_key]); ?>"><?php echo lang("Ver");?></a>
		<?php echo $value; ?>
		
	  </td>
<?php
		break;
	  default:
?>
	  <td>
		<?php echo $value; ?>
	  </td>
<?php		
		break;
	endswitch;
  endforeach;
  ?>
    <td style="width: 120px;">
  	<div class="actions_menu">
  	  <ul>

  		<li><a class="edit" href="<?php echo site_url($obj_module . "/editUserProfile/" . $row[$table_key]); ?>">edit</a></li>
		<?php if($quantity == 0): ?>
  		<li><a class="delete" onclick="basicFunctions.getInstance().deleteRowAndReturnSystemMessage('<?php echo site_url($obj_module . "/deleteUserProfile/" . $row[$table_key]); ?>', this)" href="javascript:void(0)">delete</a></li>
		<?php endif; ?>
  	  </ul>
  	</div>
    </td>
</tr>