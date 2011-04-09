<?php foreach($groups as $group):?>
	<article>
		<header>
			<h2>
				<a href="<?php echo App::url_for("groups/" . $group->name);?>" title="Display everyone in <?php echo $group->name;?>">
					<?php echo $group->name;?>
				</a>
			</h2>
		</header>
		<footer>
	<?php if(AuthController::is_authed()):?>
			<a href="<?php echo App::url_for("group", array("id"=>$group->id));?>">edit</a>
	<?php endif;?>
		</footer>
	</article>
<?php endforeach;?>
<?php if(AuthController::is_authed()):?>
<a href="<?php echo App::url_for("group");?>">add a group</a>
<?php endif;?>