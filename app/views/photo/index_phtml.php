<dl>
<?php for($i=0; $i < count($photos); $i++):?>
	<?php $image = $photos[$i];?>
	<?php if(strpos($image->src, 'http://') === false):?>
		<dt><?php echo $image->title;?></dt>
		<dd>
			<img class="manipulate" src="<?php echo PhotoResource::getLittleSrc($image->src);?>" width="<?php echo PhotoResource::getThumbnailWidth($image->src);?>" />
		<form method="post" action="<?php echo FrontController::urlFor('photo');?>">
			<input type="hidden" value="delete" name="_method" />
			<input type="hidden" value="<?php echo $image->src;?>" name="src" />
			<button type="submit"><span>Delete</span></button>
		</form>
		</dd>
	<?php endif;?>
<?php endfor;?>
</dl>