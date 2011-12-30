<!--[if !IE]>start section<![endif]-->
<div class="section">
	<div class="section_inner">
		<div class="title_wrapper">
			<h2><?php echo lang("form_editar formulario perfiles de usuarios");?></h2>
			
		</div>
	
		<div class="section_content">


		<!--[if !IE]>start forms<![endif]-->
		<?php // Change the css classes to suit your needs    
		  $attributes = array('class' => 'search_form general_form', 'id' => '');
		  echo form_open('users/save', $attributes); ?>
		
			<!--[if !IE]>start fieldset<![endif]-->
			<fieldset>
				<!--[if !IE]>start forms<![endif]-->
				<div class="forms">



				<!--[if !IE]>start row<![endif]-->
				<div class="row">

        
					<label for="USER_ADM_ID"><?php echo lang('form_usuario');?> <span class="required">*</span></label>
					<div class="inputs">
						<span class="input_wrapper">
						  <input id="USER_ADM_ID" readonly="readonly" class="text" type="text" name="USER_ADM_ID" maxlength="32" value="<?php echo set_value('USER_ADM_ID', $user_adm->USER_ADM_ID); ?>"  />
						</span>
						<?php echo form_error('USER_ADM_ID','<span class="system negative">','</span>'); ?>
<!--						<span class="system positive">This is a positive message</span>-->
					</div>
				</div>
				<!--[if !IE]>end row<![endif]-->

				<!--[if !IE]>start row<![endif]-->
				<div class="row">
					<label for="USER_NAME"><?php echo lang('form_nombre');?></label>
					<div class="inputs">
						<span class="input_wrapper">
						  <input id="USER_NAME" class="text" type="text" name="USER_NAME" maxlength="32" value="<?php echo set_value('USER_NAME', $user_adm->USER_NAME); ?>"  />
						</span>
						<?php echo form_error('USER_NAME'); ?>
<!--						<span class="system negative">This is a negative message</span>-->
					</div>
				</div>
				<!--[if !IE]>end row<![endif]-->

				<!--[if !IE]>start row<![endif]-->
				<div class="row">
					<label for="USER_PROFILE_ID"><?php echo lang('form_Perfil de usuario');?></label>
					<div class="inputs">
						<span class="input_wrapper blank">
							<?php // Change the values in this array to populate your dropdown as required ?>
							<?php $options = array(
                                                  ''  => 'Please Select',
                                                  'example_value1'    => 'example option 1'
                                                ); ?>

							<?php echo form_dropdown('USER_PROFILE_ID', $user_profile_list, set_value('USER_PROFILE_ID', $user_adm->USER_PROFILE_ID))?>
						</span>
					</div>
					<?php echo form_error('USER_PROFILE_ID'); ?>
				</div>
				<!--[if !IE]>end row<![endif]-->				

				<!--[if !IE]>start row<![endif]-->
				<div class="row">
					<label for="STATUS"><?php echo lang('form_status');?></label>
					<div class="inputs">
						<span class="input_wrapper blank">
							<?php echo form_dropdown('STATUS', $status_list, set_value('STATUS', $user_adm->STATUS))?>
						</span>
					</div>
					<?php echo form_error('STATUS'); ?>
				</div>
				<!--[if !IE]>end row<![endif]-->				
				
				<!--[if !IE]>start row<![endif]-->
				<div class="row">
					<label for="EXPIRE_DATE"><?php echo lang('form_EXPIRE_DATE');?></label>
					<div class="inputs">
						<span class="input_wrapper">
						  <input id="EXPIRE_DATE" class="text" type="text" name="EXPIRE_DATE"  value="<?php echo set_value('EXPIRE_DATE', $user_adm->EXPIRE_DATE); ?>"  />
						</span>
						<?php echo form_error('EXPIRE_DATE'); ?>
<!--						<span class="system positive">This is a positive message</span>-->
					</div>
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