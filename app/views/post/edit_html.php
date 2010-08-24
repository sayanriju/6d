<section id="addressbook_modal" style="display: none;" class="modal"></section>
<form action="<?php echo FrontController::urlFor('post');?>" method="post" id="post_form">
	<fieldset>
		<p id="send_to_list">
			<label for="send_to_list">
				<a href="<?php echo FrontController::urlFor('addressbook.html');?>" id="address">Send to</a>
			</label>
		</p>
		<p>
			<label for="title">Title</label>
			<input type="text" id="title" name="title" value="{$post->title}" />
		</p>
		<p>
			<label for="body">Post</label>
			<a href="<?php echo FrontController::urlFor('photos');?>" title="Add a photo" id="add-a-photo-link">+ add a photo</a>
			<textarea name="body" id="body">{$post->body}</textarea>
		</p>
		<input type="hidden" name="id" value="{$post->id}" />
		<input type="hidden" name="source" value="{$post->source}" />
		
	</fieldset>
	<fieldset class="options">
		<p>
			<label for="is_published" class="checkbox">Check to make public</label>
			<input type="checkbox" id="is_published" name="is_published" value="true"<?php echo $post->is_published ? ' checked="true"' : '';?> />
		</p>
		<p>
			<label for="make_home_page" class="checkbox">Check to make this your home page</label>
			<input type="checkbox" value="true" id="make_home_page" name="make_home_page"<?php echo $post->isHomePage($this->getHome_page_post_id()) ? ' checked="true"' : null;?> />
		</p>
		<p>
			<label for="type">Display post as</label>
			<select id="type" name="type">
<?php foreach(array('post'=>'Post', 'page'=>'Page', 'quote'=>'Quote', 'photo'=>'Photo', 'album'=>'Album', 'video'=>'Video', 'link'=>'Link') as $key=>$value):?>
			<option value="<?php echo $key;?>"<?php echo $post->type === $key ? ' selected="true"' : '';?>><?php echo $value;?></option>
<?php endforeach;?>
			</select>
		</p>
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
<?php if(strlen($post->source) > 0):?>
		<button type="submit" name="reblog" id="reblog">Reblog</button>
<?php endif;?>
	</nav>
</form>