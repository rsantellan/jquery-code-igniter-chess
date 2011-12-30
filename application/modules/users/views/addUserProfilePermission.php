<?php 
foreach($permission_group_list as $permissions_group): 
?>
<h4><?php echo $permissions_group["NAME"];?></h4>
<ul>
<?php
  foreach($permissions_group["DATA"] as $permission):
?>  
  <li>
	<input type="checkbox" name="permission" value="<?php echo $permission["PERMISSION_ID"] ?>" onclick="changePermissionOfUserProfile('<?php echo site_url("users/changePermissionOfUserProfile");?>', <?php echo $permission["PERMISSION_ID"] ?>, <?php echo $user_profile_id;?>, this)" <?php if (array_key_exists((int)$permission["PERMISSION_ID"],$permission_used_list)) echo "checked"?>/>
	<?php echo $permission["NAME"]; //var_dump($permission);?>
  </li>

<?php  
  endforeach;
?>
</ul>
<?php
endforeach; ?>
