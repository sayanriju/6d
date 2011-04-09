<h1>Media Library</h1>
<dl>
<?php foreach($media as $key=>$value):?>
	<dd>
		<?php echo $value->src;?>
	</dd>
<?php endforeach;?>
</dl>