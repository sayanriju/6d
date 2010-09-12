<ul>
<?php foreach($people as $person):?>
	<li rel="<?php echo $person->id;?>" class="<?php echo $person->is_owner ? 'owner' : null;?>">
	<?php if(!$person->is_owner):?>
		<a href="<?php echo Application::urlForWithUser('person/' . $person->id);?>" title="edit <?php echo $person->name;?>">
			<span rel="<?php echo $person->id;?>"><?php echo $person->name;?></span>
		</a>
		<form action="<?php echo Application::urlForWithUser('person');?>" method="post" class="delete">
			<input type="hidden" value="<?php echo $person->id;?>" name="id" />
			<input type="hidden" value="delete" name="_method" />
			<button type="submit">x</button>
		</form>
	<?php else:?>
	<span rel="<?php echo $person->id;?>"><?php echo $person->name;?></span>
	<?php endif;?>
	</li>
<?php endforeach;?>
</ul>
