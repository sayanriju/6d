<h1><?php echo AppResource::$member->name;?></h1>
<?php if(AuthController::$current_user !== null && $owner->id === AuthController::$current_user->id):?>
<form action="<?php AppResource::url_for_member("profile");?>" method="post">
	<input type="hidden" value="edit" name="state" />
	<button type="submit">Edit</button>
</form>
<?php endif;?>