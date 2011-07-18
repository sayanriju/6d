<form action="<?php echo AppResource::url_for_user((int)$post->id > 0 ? "post" : "posts");?>" method="post">
	<fieldset>
		<legend><?php echo $legend;?></legend>
		<div>
			<label for="post[title]">Title</label>
			<input type="text" name="post[title]" id="post[title]" value="<?php echo $post->title;?>" />
		</div>
		<div>
			<label for="post[body]">Body</label>
			<textarea name="post[body]"><?php echo $post->body;?></textarea>
		</div>
		<div>
			<label for="post[status]">Status</label>
			<select id="post[status]" name="post[status]">
			<?php foreach(array("Public"=>"public", "Pending"=>"pending", "Draft"=>"draft") as $key=>$value):?>
				<option value="<?php echo $value;?>"<?php echo ($post->status === $value ? " selected" : null);?>><?php echo $key;?></option>
			<?php endforeach;?>
			</select>
		</div>
		<div>
			<label for="post[post_date]">Post on Date</label>
			<input type="text" id="post[post_date]" value="<?php echo date("Y-m-d g:i:s a", $post->post_date);?>" name="post[post_date]" />
		</div>
		<div>
			<label for="post[type]">Type</label>
			<input type="text" value="<?php echo $post->type;?>" name="post[type]" />
		</div>
<?php if($post->id > 0):?>
		<input type="hidden" value="<?php echo $post->id;?>" name="post[id]" />
		<input type="hidden" value="put" name="_method" />
<?php endif;?>
		<footer>
			<button type="submit"><?php echo $post->id > 0 ? "Save" : "Add";?></button>
		</footer>
	</fieldset>
</form>
<?php if($post->id > 0):?>
<form action="<?php echo AppResource::url_for_user("post");?>" method="post" onsubmit="return confirm('Are you sure you want to delete?');">
	<input type="hidden" value="<?php echo $post->id;?>" name="post[id]" />
	<input type="hidden" value="delete" name="_method" />
	<button type="submit">delete</button>
</form>
<?php endif;?>