<li>
  <span><?php echo $links["titulo"]; ?></span>
  <ul>
	  <?php foreach($links["objectos"] as $link): ?>
	  <li>
		<a href="<?php echo $link["link"];?>"><?php echo $link["texto"];?></a>
	  </li>
	  <?php endforeach; ?>
  </ul>
</li>