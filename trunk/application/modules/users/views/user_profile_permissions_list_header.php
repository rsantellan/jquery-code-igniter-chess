<tr>
  <?php 
	$url_base = current_url();
  ?>
  <?php foreach($headers as $head):?>
  <?php
	$inOrder = "DESC";
	if($head["col"] == $order_by)
	{
	  if($order == "DESC")
	  {
		$inOrder = "ASC";
	  }
	}
  ?>
	<th><a href="<?php echo $url_base."?order_by=".$head["col"]."&order=".$inOrder; ?>"><?php echo lang(strtoupper($head["name"])); ?></a></th>
  <?php endforeach; ?>
</tr>