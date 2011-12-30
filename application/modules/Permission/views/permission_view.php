<?php
// Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('Permission', $attributes);
?>

<p>
  <label for="name">NAME <span class="required">*</span></label>
<?php echo form_error('name'); ?>
  <br /><input id="name" type="text" name="name" maxlength="32" value="<?php echo set_value('name'); ?>"  />
</p>

<p>
  <label for="permission_groups_id">PERMISSION_GROUPS_ID <span class="required">*</span></label>
  <?php echo form_error('permission_groups_id'); ?>

  <?php // Change the values in this array to populate your dropdown as required ?>
  <?php
  $options = array(
	  '' => 'Please Select',
	  '1' => '1'
  );
  ?>

  <br /><?php echo form_dropdown('permission_groups_id', $options, set_value('permission_groups_id')) ?>
</p>                                             

<p>
  <label for="description">DESCRIPTION <span class="required">*</span></label>
<?php echo form_error('description'); ?>
  <br /><input id="description" type="text" name="description" maxlength="32" value="<?php echo set_value('description'); ?>"  />
</p>


<p>
<?php echo form_submit('submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
