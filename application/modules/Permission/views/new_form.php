<?php
// Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('welcome', $attributes);
?>

<p>
  <label for="user_name">USER_NAME <span class="required">*</span></label>
<?php echo form_error('user_name'); ?>
  <br /><input id="user_name" type="text" name="user_name" maxlength="32" value="<?php echo set_value('user_name'); ?>"  />
</p>

<p>
  <label for="password">PASSWORD <span class="required">*</span></label>
<?php echo form_error('password'); ?>
  <br /><input id="password" type="text" name="password" maxlength="64" value="<?php echo set_value('password'); ?>"  />
</p>

<p>
  <label for="profile">PROFILE <span class="required">*</span></label>
  <?php echo form_error('profile'); ?>

  <?php // Change the values in this array to populate your dropdown as required ?>
  <?php
  $options = array(
	  '' => 'Please Select',
	  'example_value1' => 'example option 1'
  );
  ?>

  <br /><?php echo form_dropdown('profile', $options, set_value('profile')) ?>
</p>                                             

<p>
  <label for="status">STATUS <span class="required">*</span></label>
  <?php echo form_error('status'); ?>

  <?php // Change the values in this array to populate your dropdown as required ?>
<?php
$options = array(
	'' => 'Please Select',
	'example_value1' => 'example option 1'
);
?>

  <br /><?php echo form_dropdown('status', $options, set_value('status')) ?>
</p>                                             

<p>
  <label for="expire_date">EXPIRE_DATE <span class="required">*</span></label>
  <?php echo form_error('expire_date'); ?>
  <br /><input id="expire_date" type="text" name="expire_date"  value="<?php echo set_value('expire_date'); ?>"  />
</p>


<p>
<?php echo form_submit('submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
