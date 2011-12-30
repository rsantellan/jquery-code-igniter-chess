<div class="section">
	<div class="section_inner">
		<div class="title_wrapper">
			<h2>Menu Options</h2>
		</div>
		<div class="section_content">
			<ul id="main_menu">
				<?php 
				  foreach($enabled_modules as $module):
				?>
				  <?php echo modules::run($module.'/menu')?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
