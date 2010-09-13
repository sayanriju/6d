<section id="addressbook_modal" style="display: none;" class="modal"></section>
<form action="<?php echo Application::urlForWithUser('post');?>" method="post" id="post_form">
	<fieldset>
    <legend>New Post</legend>
		<ul id="post_type">
		<?php foreach(array('post'=>'Post', 'page'=>'Page', 'quote'=>'Quote', 'photo'=>'Photo', 'album'=>'Album', 'video'=>'Video', 'link'=>'Link') as $key=>$value):?>
            <li id="post_type_<?php echo $key;?>" class="post_type_radio"><label for="<?php echo $key;?>"><input type="radio" name="type" value="<?php echo $key;?>"<?php echo $post->type === $key ? ' checked' : '';?>><?php echo $value;?></label></li>
        <?php endforeach;?>
        </ul>
		<p>
			<label for="title">Title</label>
			<input type="text" id="title" name="title" value="{$post->title}" />
		</p>
		<p>
        	<a href="<?php echo FrontController::urlFor('photos');?>" title="Add a photo" id="add-a-photo-link">+ add a photo</a>
			<label for="body">Post</label>
			<textarea name="body" id="body">{$post->body}</textarea>
		</p>
		<input type="hidden" name="id" value="{$post->id}" />
		<input type="hidden" name="source" value="{$post->source}" />
		
	</fieldset>
	<fieldset class="options">
    <legend>Post Details</legend>
		<p>
			<label for="tags" class="inline">Tags separated by commas</label>
			<input type="text" name="tags" id="tags" value="{$post->tags}" />
		</p>		
		<p>
			<label for="description">Excerpt</label>
			<textarea name="description" id="description">{$post->description}</textarea>
		</p>
		<p>
			<label for="password">Password protect</label>
			<input name="password" id="password" type="password" value="" />
		</p>
		<p>
			<label for="post_date">Date posted</label>
			<input type="text" name="post_date" value="{$post->post_date}" id="post_date" />
		</p>
	</fieldset>
    <fieldset>
    <legend>Publish Details</legend>
    	<p>
        	<input type="checkbox" id="is_published" name="is_published" value="true"<?php echo $post->is_published ? ' checked="true"' : '';?> />
			<label for="is_published" class="checkbox">Make public</label>
		</p>
		<p>
        	<input type="checkbox" value="true" id="make_home_page" name="make_home_page"<?php echo $post->isHomePage($this->getHome_page_post_id()) ? ' checked="true"' : null;?> />
			<label for="make_home_page" class="checkbox">Make this your home page</label>
		</p>
        <p id="send_to_list">
			<label for="send_to_list">
				<a href="<?php echo FrontController::urlFor('addressbook.html');?>" id="address">Send to</a>
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