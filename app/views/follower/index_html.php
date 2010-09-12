<ol>
<?php foreach($people as $person):?>
	<li rel="<?php echo $person->id;?>">
		<a href="<?php echo Application::urlForWithUser('follower/' . $person->id);?>" title="edit <?php echo $person->name;?>"><span rel="<?php echo $person->id;?>"><?php echo $person->name;?></span>
		</a>
		<form action="<?php echo Application::urlForWithUser('follower');?>" method="post" class="delete">
			<input type="hidden" value="<?php echo $person->id;?>" name="id" />
			<input type="hidden" name="_method" value="delete" />
			<button type="submit">x</button>
		</form>
	</li>
<?php endforeach;?>
</ol>
