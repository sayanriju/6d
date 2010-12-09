<form action="<?php echo Application::url_with_member($post->id !== null ? 'post' : 'posts');?>" method="post" id="post_form">
	<fieldset class="type">
    	<legend>What type of post do you want to create?</legend>
		<ul id="post_type">
		<?php foreach(array('post'=>'Post', 'page'=>'Page', 'quote'=>'Quote', 'photo'=>'Photo', 'album'=>'Album', 'video'=>'Video', 'link'=>'Link') as $key=>$value):?>
            <li>
				<input name="type" id="<?php echo $key;?>" type="radio" value="<?php echo $key;?>"<?php echo $post->type === $key ? ' checked="checked"' : '';?> />
				<label for="<?php echo $key;?>"><?php echo $value;?></label>
				
			</li>
        <?php endforeach;?>
        </ul>
	</fieldset>
	<fieldset class="content">
		<legend>Post</legend>
		<p>
			<label for="title">Title of the post</label>
			<input type="text" id="title" name="title" value="<?php echo urldecode($post->title);?>" />
		</p>
		<p>
			<label for="body">Body of the post</label>
			<textarea name="body" id="body"><?php echo urldecode($post->body);?></textarea>
		</p>
		<input type="hidden" name="id" value="<?php echo $post->id;?>" />
		<input type="hidden" name="source" value="<?php echo $post->source;?>" />
	</fieldset>
	<fieldset class="options">
    	<legend>Options</legend>
		<p>
			<label for="description">A summary of the post</label>
			<textarea name="description" id="description"><?php echo urldecode($post->description);?></textarea>
		</p>
		<p>
			<label for="tags" class="inline">Tags separated by commas</label>
			<input type="text" name="tags" id="tags" value="<?php echo urldecode($post->tags);?>" />
		</p>		
		<!--<p>
			<label for="password">Password protect</label>
			<input name="password" id="password" type="password" value="" />
		</p>-->
		<p>
			<label for="post_date">Date posted (optional and will default to today)</label>
			<input type="text" name="post_date" value="<?php echo $post->post_date;?>" id="post_date" />
		</p>
    	<p>
        	<input type="checkbox" id="is_published" name="is_published" value="true"<?php echo $post->is_published ? ' checked="checked"' : '';?> />
			<label for="is_published" class="checkbox">Make this post public</label>
		</p>
		<p>
        	<input type="checkbox" value="true" id="make_home_page" name="make_home_page"<?php echo $post->isHomePage($this->getHome_page_post_id()) ? ' checked="checked"' : null;?> />
			<label for="make_home_page" class="checkbox">Make this post your home page</label>
		</p>
		<p id="send_to_list">
			<a href="<?php echo Application::url_with_member('addressbook.html');?>" id="address">Send to</a>
		</p>
        
    </fieldset>
	<nav>
<?php if($post->id !== null):?>
		<input type="hidden" value="put" name="_method" />
<?php endif;?>
		<button type="submit" name="save_button" id="save_button">
			<span>
				<?php echo $post->id !== null ? 'Save post' : 'Create post';?>
			</span>
		</button>
<?php if($post->id !== null && $post->source !== Application::$current_user->person->url):?>
		<button type="submit" name="reblog" id="reblog">Reblog</button>
<?php endif;?>
	</nav>
</form>