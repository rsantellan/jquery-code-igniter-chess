<?php
  //$headers = array();
  $data = array();
  foreach($listContent as $row)
  {
	$auxRow = array();
	foreach($row as $key => $value)
	{
	  if(!is_int($key))
	  {
		
		/*if(!in_array($key, $headers))
		{
		  //array_push($headers, $key);
		}*/
		$auxRow[$key] = $value;
	  }
	  
	}
	$data[] = $auxRow;
  }
  
  $view_edit = true;
  if(isset($edit_disable) && $edit_disable)
  {
	$view_edit = false;
  }
?>

<!--[if !IE]>start system messages<![endif]-->
<ul class="system_messages">

	<li class="red_message ajax_system_messages">
	  <div class="red system_inner"><span class="ico"></span><strong class="system_title" id="red_message_title"></strong><label id="red_message_subtitle"></label></div>
	</li>
	<li id="yellow_message_li_container" class="yellow_message ajax_system_messages">
	  <div class="yellow system_inner"><span class="ico"></span><strong class="system_title" id="yellow_message_title"></strong><label id="yellow_message_subtitle"></label></div>
	</li>
	<li class="blue_message ajax_system_messages">
	  <div class="blue system_inner"><span class="ico"></span><strong class="system_title" id="blue_message_title"></strong><label id="blue_message_subtitle"></label></div>
	</li>

</ul>
<!--[if !IE]>end system messages<![endif]-->


<!--[if !IE]>start system messages<![endif]-->
<ul class="system_messages">
<?php 
  $error = $this->session->flashdata('error_message'); 
  
  if($error != false):
?>	
	<li class="red_message"><div class="red system_inner"><span class="ico"></span><strong class="system_title">Error or negative message </strong> - Subtitle can be added. </div></li>
<?php	
  endif;
  ?>

<?php 
  $ok = $this->session->flashdata('ok_message'); 
  if($ok != false):
?>	
	<li class="yellow_message"><div class="yellow system_inner"><span class="ico"></span><strong class="system_title"><?php echo $ok; ?></strong><?php echo $this->session->flashdata('ok_message_description'); ?> </div></li>
<?php	
  endif;
  ?>	
	
<?php 
  $info = $this->session->flashdata('info_message'); 
  if($info != false):
?>	
	<li class="blue_message"><div class="blue system_inner"><span class="ico"></span><strong class="system_title">Other Messages</strong> - Subtitle can be added.</div></li>
<?php	
  endif;
  ?>	
	

</ul>
<!--[if !IE]>end system messages<![endif]-->

<!--[if !IE]>start section<![endif]-->
<div class="section table_section">
  <div class="section_inner">
	<div class="title_wrapper">
	  <h2><?php echo $listName; ?></h2>
	</div>
	<div class="section_content">

	  <div  id="product_list">
		<!--[if !IE]>start table_wrapper<![endif]-->
		<div class="table_wrapper">
		  <div class="table_wrapper_inner">
			<table cellpadding="0" cellspacing="0" width="100%">
			  <tbody>
				<?php echo $this->load->view($obj_module."/".$obj_model."_list_header"); ?>
				<?php 
				$first = true; 
				foreach($data as $row):
				?>
				
				<?php echo $this->load->view($obj_module."/".$obj_model."_list_data", array('first' => $first, 'row' => $row)); ?>
				
				
				<?php
				$first = !$first;
				endforeach; ?>
			  </tbody></table>
		  </div>
		</div>
		<!--[if !IE]>end table_wrapper<![endif]-->
	  </div>



	  <!--[if !IE]>start pagination<![endif]-->
	  <div class="pagination">
		<span class="page_no">Page 1 of <?php echo $pages; ?></span>
		<?php if($resultQuantity > 0 && $pages > 1): ?>
		<?php
			$url_base = current_url();
			$url_base .= "?";
			if(isset($filterString) && $filterString != "")
			{
			  $url_base .= $filterString."&";
			}
		?>
		<ul class="pag_list">
		  <?php if($page > 1): ?>
		  <li>
			<a href="<?php echo $url_base."page=".($page - 1); ?>" class="button small_button pag_nav"><span><span>Previous</span></span></a> 
		  </li>
		  <?php endif; ?>
		  <?php if($page - 3 > 1): ?>
		  <li>
			<a href="<?php echo $url_base."page=1"; ?>">1</a>
		  </li>
		  <?php endif; ?>
		  <?php
			$index = 1;
			$no_more_finish = false;
			
			while($index <= $pages):
			 if($index == 1):
			   if($page - 3 > $index):
		  ?> 
				<li>[...]</li>
		<?php	   
			  endif;
			 endif;
		  ?> 
				
		<?php
			if($page - 3 <= $index && $page + 3 >= $index):
		  ?> 
			<li>
			  <?php 
			  if($page == $index): 
			?>
			  <a href="<?php echo $url_base."page=".$index; ?>" class="current_page"><span><span><?php echo $index; ?></span></span></a>
			<?php
			  else:
			?>
			  <a href="<?php echo $url_base."page=".$index; ?>"><?php echo $index; ?></a>	
			<?php
			  endif;
			  ?>
			</li>
		<?php	  
			endif;
			if($index > $page + 3 && !$no_more_finish):
			  $no_more_finish = true;
			?> 
				<li>[...]</li>
		<?php  
			endif;
			
			$index++;
			endwhile;
		  ?>
		  <?php if($page + 3 < $pages): ?>
		  <li>
			<a href="<?php echo $url_base."page=".$pages; ?>"><?php echo $pages; ?></a>
		  </li>
		  <?php endif; ?>
		  <?php if($page < $pages): ?>
		  <li>
			<a href="<?php echo $url_base."page=".($page + 1); ?>" class="button small_button pag_nav"><span><span>Next</span></span></a> 
		  </li>
		   <?php endif; ?>
		</ul>
		<?php endif; ?>

	  </div>
	  <!--[if !IE]>end pagination<![endif]-->


	</div>
  </div>
</div>
<!--[if !IE]>end section<![endif]-->