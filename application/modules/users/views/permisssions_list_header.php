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
	
	$url = $url_base."?";
	if($filterString != "")
	{
	  $url.= $filterString."&";
	}
	$url .= "order_by=".$head["col"]."&order=".$inOrder;
  ?>
	<th><a href="<?php echo $url ?>"><?php echo lang(strtoupper($head["name"])); ?></a></th>
  <?php endforeach; ?>
</tr>