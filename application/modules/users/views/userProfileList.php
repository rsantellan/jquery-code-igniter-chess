<?php
echo $this->load->view("inList/inList");
?>

<a href="<?php echo site_url("users/addUserProfile");?>">
  <?php echo lang('usuario_agregar nuevo perfil de usuario');?>
</a>