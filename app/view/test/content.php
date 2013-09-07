<div id="maincontent">
	<h1>Hello World!</h1>
	<?php foreach ($this->args["items"] as $item) : ?>
		<h1><?php echo $item;?></h1>
	<?php endforeach;?>
</div>