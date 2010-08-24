<dl>
<?php foreach($files as $file):?>
	<dd>
		<form name="backup" id="backup" action="<?php echo FrontController::urlFor('backup');?>" method="post">
			<fieldset>
				<legend><?php echo $file;?></legend>
				<input name="file_name" type="hidden" value="<?php echo $file;?>" />
				<input name="_method" type="hidden" value="put" />
				<button type="submit"><span>Restore</span></button>
			</fieldset>
		</form>
		<form name="delete_backup" id="delete_backup" action="<?php echo FrontController::urlFor('backup');?>" method="post">
			<input name="file_name" type="hidden" value="<?php echo $file;?>" />
			<input name="_method" type="hidden" value="delete" />
			<button type="submit"><span>Delete</span></button>
		</form>
	</dd>
<?php endforeach;?>
</dl>
<form name="backup" id="backup" action="<?php echo FrontController::urlFor('backup');?>" method="post">
	<fieldset>
		<legend>Backup content</legend>
		<input name="file_name" type="hidden" />
		<button type="submit"><span>Backup</span></button>
	</fieldset>
</form>