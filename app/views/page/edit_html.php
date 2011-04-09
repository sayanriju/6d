<form action="<?php echo AppResource::url_for_member("page");?>" method="post">
	<fieldset>
		<legend>Edit a page</legend>
		<input type="text" name="page[title]" value="<?php echo $page->title;?>" />
		<input type="text" name="page[name]" value="<?php echo $page->name;?>" />
		<textarea name="page[body]"><?php echo $page->body;?></textarea>
	</fieldset>
	<button type="submit">Save</button>
<?php if($page !== null):?>
	<input type="hidden" name="_method" value="put" />
	<input type="hidden" name="page[id]" value="<?php echo $page->id;?>" />
<?php endif;?>
</form>