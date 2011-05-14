<form action="<?php echo AppResource::url_for_member($post->id > 0 ? 'post' : 'posts');?>" method="post" id="post_form">
	<fieldset class="type">
    	<legend>What type of post do you want to create?</legend>
<?php echo $post->type;?>
		<ul id="post_type" class="horizontal">
		<?php foreach(array('post'=>'Post', 'page'=>'Page', 'quote'=>'Quote', 'photo'=>'Photo', 'album'=>'Album', 'video'=>'Video', 'link'=>'Link') as $key=>$value):?>
            <li>
				<input name="post[type]" id="<?php echo $key;?>" type="radio" value="<?php echo $key;?>"<?php echo $post->type === $key ? ' checked="checked"' : '';?> />
				<label for="<?php echo $key;?>"><?php echo $value;?></label>
			</li>
        <?php endforeach;?>
        </ul>
	</fieldset>
	<fieldset class="content">
		<legend>Post</legend>
		<p>
			<label for="post[title]">Title of the post</label>
			<input type="text" id="title" name="post[title]" value="<?php echo urldecode($post->title);?>" />
		</p>
		<p>
			<label for="body">Body of the post</label>
			<textarea name="post[body]" id="body"><?php echo urldecode($post->body);?></textarea>
		</p>
		<input type="hidden" name="post[id]" value="<?php echo $post->id;?>" />
	</fieldset>
	<fieldset class="options">
    	<legend>Options</legend>
		<p>
			<label for="excerpt">A summary of the post</label>
			<textarea name="post[excerpt]" id="excerpt"><?php echo urldecode($post->excerpt);?></textarea>
		</p>
		<p>
			<label for="post_date">Date posted (optional and will default to today)</label>
			<input type="text" value="<?php echo date("Y-m-d g:i:s a", $post->post_date);?>" name="post[post_date]" />
		</p>
		<p>
			<label for="post[status]">Status</label>
			<select id="post[status]" name="post[status]">
			<?php foreach(array("Public"=>"public", "Pending"=>"pending", "Draft"=>"draft") as $key=>$value):?>
				<option value="<?php echo $value;?>"<?php echo ($post->status === $value ? " selected" : null);?>><?php echo $key;?></option>
			<?php endforeach;?>
			</select>
		</p>
        
    </fieldset>
	<nav>
<?php if($post->id > 0):?>
		<input type="hidden" value="put" name="_method" />
<?php endif;?>
		<button type="submit" name="save_button" id="save_button">
			<span>
				<?php echo $post->id !== null ? 'Save post' : 'Create post';?>
			</span>
		</button>
	</nav>	
</form>
<?php if($post->id > 0):?>
<form action="<?php echo AppResource::url_for_user("post");?>" method="post" onsubmit="return confirm('Are you sure you want to delete?');">
	<input type="hidden" value="<?php echo $post->id;?>" name="post[id]" />
	<input type="hidden" value="delete" name="_method" />
	<button type="submit">delete</button>
</form>
<?php endif;?>
