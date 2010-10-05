<section id="addressbook_modal" style="display: none;" class="modal"></section>
<form action="<?php echo Application::urlForWithUser('post');?>" method="post" id="post_form">
	<fieldset class="type">
    	<legend>Type</legend>
		<ul id="post_type">
		<?php foreach(array('post'=>'Post', 'page'=>'Page', 'quote'=>'Quote', 'photo'=>'Photo', 'album'=>'Album', 'video'=>'Video', 'link'=>'Link') as $key=>$value):?>
            <li>
				<label for="<?php echo $key;?>"><?php echo $value;?></label>
				<input name="type" id="<?php echo $key;?>" type="radio" value="<?php echo $key;?>"<?php echo $post->type === $key ? ' checked="checked"' : '';?> />
				
			</li>
        <?php endforeach;?>
        </ul>
	</fieldset>
	<fieldset class="content">
		<legend>Post</legend>
		<p>
			<label for="title">Title</label>
			<input type="text" id="title" name="title" value="{$post->title}" />
		</p>
		<p>
			<label for="body">Post</label>
			<textarea name="body" id="body">{$post->body}</textarea>
		</p>
		<input type="hidden" name="id" value="{$post->id}" />
		<input type="hidden" name="source" value="{$post->source}" />
	</fieldset>
	<fieldset class="options">
    	<legend>Options</legend>
		<p>
			<label for="description">Excerpt</label>
			<textarea name="description" id="description">{$post->description}</textarea>
		</p>
		<p>
			<label for="tags" class="inline">Tags separated by commas</label>
			<input type="text" name="tags" id="tags" value="{$post->tags}" />
		</p>		
		<p>
			<label for="password">Password protect</label>
			<input name="password" id="password" type="password" value="" />
		</p>
		<p>
			<label for="post_date">Date posted</label>
			<input type="text" name="post_date" value="{$post->post_date}" id="post_date" />
		</p>
    	<p>
        	<input type="checkbox" id="is_published" name="is_published" value="true"<?php echo $post->is_published ? ' checked="checked"' : '';?> />
			<label for="is_published" class="checkbox">Make public</label>
		</p>
		<p>
        	<input type="checkbox" value="true" id="make_home_page" name="make_home_page"<?php echo $post->isHomePage($this->getHome_page_post_id()) ? ' checked="checked"' : null;?> />
			<label for="make_home_page" class="checkbox">Make this your home page</label>
		</p>
		<p id="send_to_list">
			<label for="send_to_list">
				<a href="<?php echo Application::urlForWithUser('addressbook.html');?>" id="address">Send to</a>
			</label>
		</p>
        
    </fieldset>
	<nav>
		<input type="hidden" name="last_page_viewed" value="{$last_page_viewed}" />
<?php if($post->id !== null):?>
		<input type="hidden" value="put" name="_method" />
<?php endif;?>
		<button type="submit" name="save_button" id="save_button">
			<span>
				<?php echo $post->id !== null ? 'Save post' : 'Create post';?>
			</span>
		</button>
<?php if($post->source !== Application::$current_user->url):?>
		<button type="submit" name="reblog" id="reblog">Reblog</button>
<?php endif;?>
	</nav>
</form>