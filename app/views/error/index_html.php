<h1><?php echo $message;?></h1>
<ul>
<?php foreach($errors as $key=>$value):?>
	<li><?php echo $value;?></li>
<?php endforeach;?>
</ul>