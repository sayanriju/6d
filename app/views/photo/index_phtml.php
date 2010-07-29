<?php for($i=0; $i < count($photos); $i++):?>
	<?php $image = $photos[$i];?>
	<?php if(strpos($image->src, 'http://') === false):?>
		<img class="manipulate" src="<?php echo PhotoResource::getLittleSrc($image->src);?>" width="<?php echo PhotoResource::getThumbnailWidth($image->src);?>" />
	<?php endif;?>
<?php endfor;?>