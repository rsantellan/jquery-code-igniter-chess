<!--[if !IE]>start section<![endif]-->
<div class="section">
	<div class="section_inner">
		<div class="title_wrapper">
			<h2><?php echo $links["titulo"]; ?></h2>
		</div>
		<div class="section_content">
			<!--[if !IE]>start dashboard menu<![endif]-->
			<div class="dashboard_menu_wrapper">
			<ul class="dashboard_menu">
				<?php foreach($links["objectos"] as $link): ?>
				<li>
				  <a href="<?php echo $link["link"];?>"><?php echo $link["texto"];?></a>
				</li>
				<?php endforeach; ?>
				
<!--				<li>
				  <a href="#" class="d1"><span>User Management Tools</span></a>
				</li>
				<li>
				  <a href="#" class="d2"><span>Setup upload folders</span></a>
				</li>
				<li>
				  <a href="#" class="d3"><span>Manage photo galleries</span></a>
				</li>
				-->
			</ul>
			</div>
			<!--[if !IE]>end dashboard menu<![endif]-->	
		</div>
	</div>
</div>
<!--[if !IE]>end section<![endif]-->