<!--[if !IE]>start section<![endif]-->
<div class="section">
	<div class="section_inner">
		<div class="title_wrapper">
			<h2><?php echo lang("usuario_filter"); ?></h2>
		</div>
		<div class="section_content">
			<!--[if !IE]>start stats<![endif]-->
			<div class="stats">
				<!--[if !IE]>start stats odd<![endif]-->
				<div class="stats_odd centered">
				  
				  <input type="hidden" id="user_adm_id_ajax" value="<?php echo site_url("users/bringUsersDataUserAdmIdAutosuggest"); ?>" />
				  
				  <?php
				  $attributes = array();
				  echo form_open('users/userList', $attributes); ?>
				  
				  
				  <label><?php echo lang("usuario_filter usuario"); ?></label>
				  <br/>
				  
				  <input type="text" name="user_adm_id" id="user_adm_id" value="<?php echo set_get_value("user_adm_id");?>"/>
				  
				  <input name="" type="submit" value="<?php echo lang("filtrar"); ?>"/>
				  <?php echo form_close(); ?>
				</div>
				<!--[if !IE]>end stats odd<![endif]-->
			</div>
			<!--[if !IE]>end stats<![endif]-->
		</div>
	</div>
</div>
<!--[if !IE]>end section<![endif]-->