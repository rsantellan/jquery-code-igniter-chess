<?php echo doctype(); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta>
		<script type="text/javascript" src="<?php echo base_url() .APPPATH;?>js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url() .APPPATH;?>js/loadController.js"></script>
		
		<?php foreach($javascript as $js): ?>
		  <script type="text/javascript" src="<?php echo base_url() .APPPATH ."js/".$js; ?>"></script>
		<?php endforeach; ?>
		
		<?php foreach($stylesheet as $sheet): ?>
		  <link rel="stylesheet" type="text/css" href="<?php echo base_url() .APPPATH . "css/".$sheet;?>" />
		<?php endforeach; ?>
    </head>
    <body>
	  <!--[if !IE]>start wrapper<![endif]-->
	  <div id="wrapper">
            <?php $this->load->view('header'); ?>
		    
            
				
			 		
		<!--[if !IE]>start content<![endif]-->
		<div id="content">
		
			<!--[if !IE]>start page<![endif]-->
			<div id="page">
				<div class="inner">
					
					<?php //include('dashboard.php'); ?>
					
					
					<?php //include('brow.php'); ?>
					
					<?php //include('form.php'); ?>
					<?php if($dashboard): ?>
					  <?php 
						$enabled_modules = array();
						foreach($enabled_modules as $module):
					  ?>
						<?php echo modules::run($module.'/dashboard')?>
					  <?php endforeach; ?>
					  <?php //echo $this->load->view("dashboard"); ?>
					<?php else: ?>
					  <?php echo set_breadcrumb(); ?>
					  <?php echo $this->load->view($content) ?>
					<?php endif; ?>	
					
					<?php //include('msg.php'); ?>
									
					
					
				</div>
			</div>
			<!--[if !IE]>end page<![endif]-->
			
			<!--[if !IE]>start sidebar<![endif]-->
			<div id="sidebar">
				<div class="inner">
					
					<?php //include('quick_stats.php'); ?>
					<?php //echo modules::run('menu/menu')?>
					<?php
					  if($leftBoxOn):
					?>
					  <?php echo $this->load->view($leftBoxContent) ?>
				  <?php endif; ?>
				</div>
			</div>
			<!--[if !IE]>end sidebar<![endif]-->
		
		</div>
		<!--[if !IE]>end content<![endif]-->
	</div>
	<!--[if !IE]>end wrapper<![endif]-->	  
	  <?php $this->load->view('footer'); ?>

	<div style="display:none" class="upload_progress" id="upload_container">
	  <div class="progressWindow">Procesando, por favor espere ...</div>
	  <img src="<?php echo base_url() .APPPATH;?>images/ajax-loader.gif" alt="" />
	</div>
	<div style="display:none" class="upload_progress" id="message_container">
	  <div class="progressWindow" style="padding-top: 33px;"></div>
	</div>
    </body>
</html>