<!--[if !IE]>start section<![endif]-->
<div class="section">
	<div class="section_inner">
		<div class="title_wrapper">
			<h2><?php echo lang("form_crear formulario perfiles de usuarios");?></h2>
			
		</div>
	
		<div class="section_content">


		<!--[if !IE]>start forms<![endif]-->
		<?php // Change the css classes to suit your needs    
		  $attributes = array('class' => 'search_form general_form', 'id' => '');
		  echo form_open('users/createUserProfile', $attributes); ?>
		  <input id="USER_PROFILE_ID" type="hidden" name="USER_PROFILE_ID" maxlength="7" value="<?php echo set_value('USER_PROFILE_ID'); ?>"  />
			<!--[if !IE]>start fieldset<![endif]-->
			<fieldset>
				<!--[if !IE]>start forms<![endif]-->
				<div class="forms">

				<!--[if !IE]>start row<![endif]-->
				<div class="row">
					<label for="NAME"><?php echo lang('form_nombre');?></label>
					<div class="inputs">
						<span class="input_wrapper">
						  <input id="NAME" class="text" type="text" name="NAME" maxlength="32" value="<?php echo set_value('NAME'); ?>"  />
						</span>
						<?php echo form_error('NAME'); ?>
<!--						<span class="system negative">This is a negative message</span>-->
					</div>
				</div>
				<!--[if !IE]>end row<![endif]-->

				<!--[if !IE]>start row<![endif]-->
				<div class="row">
					<label for="DESCRIPTION"><?php echo lang('form_description');?></label>
					<div class="inputs">
						<span class="input_wrapper blank">
							<?php echo form_textarea( array( 'name' => 'DESCRIPTION', 'rows' => '5', 'cols' => '80', 'value' => set_value('DESCRIPTION') ) )?>
						</span>
					</div>
					<?php echo form_error('DESCRIPTION'); ?>
				</div>
				<!--[if !IE]>end row<![endif]-->				

				<!--[if !IE]>start row<![endif]-->
				<div class="row">
					<div class="buttons">
						<ul>
							<li><span class="button send_form_btn"><span><span><?php echo lang('form_Submit');?></span></span><input name="" type="submit" /></span></li>
						</ul>
					</div>
				</div>
				<!--[if !IE]>end row<![endif]-->



				</div>
				<!--[if !IE]>end forms<![endif]-->

			</fieldset>
			<!--[if !IE]>end fieldset<![endif]-->




		<?php echo form_close(); ?>
		<!--[if !IE]>end forms<![endif]-->	



		</div>
	</div>
</div>
<!--[if !IE]>end section<![endif]-->