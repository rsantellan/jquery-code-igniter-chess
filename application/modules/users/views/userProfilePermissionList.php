<?php
echo $this->load->view("inList/inList");

?>

<a href="<?php echo site_url("users/addUserProfilePermission/".$profile_id);?>" id="add_user_profile_permission"><?php echo lang("Add User Profile Permission");?></a>