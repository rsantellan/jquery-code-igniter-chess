<!--[if !IE]>start section<![endif]-->
<div class="section">
	<div class="section_inner">
		<div class="title_wrapper">
			<h2><?php echo lang("permisos_filter"); ?></h2>
		</div>
		<div class="section_content">
			<!--[if !IE]>start stats<![endif]-->
			<div class="stats">
				<!--[if !IE]>start stats odd<![endif]-->
				<div class="stats_odd centered">
				  <?php
				  $attributes = array();
				  echo form_open('users/permisosList', $attributes); ?>
				  
				  
				  <label><?php echo lang("permisos_filter grupos de permisos"); ?></label>
				  <br/>
				  
				  <select name="permission_group_id">
					<?php 
					foreach($filterDataPermission as $key => $value):
					?>
					<option value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php
					endforeach;
					?>
					
				  </select>
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