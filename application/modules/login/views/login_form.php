<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="imagetoolbar" content="no" />
<title>Titulo</title>
<link media="screen" rel="stylesheet" type="text/css" href="<?php echo base_url() .APPPATH;?>css/login.css"  />
<!-- blue theme is default -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url() .APPPATH;?>css/green-theme-login.css" />
<!--[if lte IE 6]><link media="screen" rel="stylesheet" type="text/css" href="<?php echo base_url() .APPPATH;?>css/login-ie.css" /><![endif]-->
</head>

<body>
	
	<!--[if !IE]>start wrapper<![endif]-->
	<div id="wrapper">
		<div id="login_wrapper">
		<h1><?php echo lang('lg_titulo')?><span class="ico"></span></h1>
			<?php echo form_open('login/validate_credentials'); ?>
				<fieldset>
						<label>
							<strong><?php echo lang('lg_login')?></strong>
							<span class="input_wrapper">
								<input type="text" name="username"/>
							</span>
						</label>
						<label>
							<strong><?php echo lang('lg_password')?></strong>
							<span class="input_wrapper">
								<input type="password" name="password"/>
							</span>
						</label>
						<p>
							<span class="button send_form_btn">
							  <span><span><?php echo lang('lg_log_in')?></span></span>
							  <input type="submit" name=""/>
							</span>
						</p>
				</fieldset>
			<?php echo form_close(); ?>
			<?php if($errores): ?>
			<div class="error">
				<div class="error_inner">
					<strong><?php echo lang('lg_error')?></strong>
					<?php echo form_error('username'); ?>
					<?php echo form_error('password'); ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<!--[if !IE]>end wrapper<![endif]-->
</body>
</html>
