<!--[if !IE]>start head<![endif]-->
<div id="head">
	<!--[if !IE]>start logo and user details<![endif]-->
	<div id="logo_user_details">
		<h1 id="logo"><a href="#"><strong>websitename</strong> Administration Panel</a></h1>
		<div id="user_details">
			<ul id="user_loged">
				<li>
				  <?php echo lang("header_logued_as");?>
				  <strong>
					<?php //echo $username; ?>
				  </strong>
				</li>
				<li class="last">
				  <?php echo anchor('login/logout', 'Logout'); ?>
				</li>
			</ul>
		</div>
	</div>
	<!--[if !IE]>end logo and user details<![endif]-->
</div>
<!--[if !IE]>end head<![endif]-->