<div id="addressbook" class="addressbook">
	<header>
		<nav>
			<ul></ul>
		</nav>
	</header>
	<section id="groups">
		<h1>Groups</h1>
		<ul>
		<?php foreach($groups as $key=>$group):?>
			<li rel="<?php echo $group->text;?>" class="<?php echo ($group->id == -1 ? 'selected' : '')?>">
				<span><?php echo $group->text;?></span>
				<input type="checkbox" id="group_<?php echo $key;?>" name="groups" value="<?php echo urlencode($group->text);?>" />
				
			<?php if(!in_array($group->text, array('All Contacts', 'Friend Requests'))):?>
				<form action="<?php echo App::url_for('group');?>" method="post" class="delete">
					<input type="hidden" value="<?php echo $group->text;?>" name="text" />
					<input type="hidden" value="delete" name="_method" />
					<button type="submit">x</button>
				</form>
			<?php endif;?>
			</li>
		<?php endforeach;?>
		</ul>
	</section>
	<section id="people">
		<h1>Name</h1>
		<?php echo $this->render('person/index', null, 'phtml');?>
	</section>
	<section id="detail" class="detail"></section>
	<div style="clear: both;"></div>
	<footer id="toolbar">
		<button id="add_group_button"><span>Create a Group</span></button>
		<button id="add_card_button"><span>Create a Card</span></button>
	</footer>
</div>
<section id="instructions">
	<ul>
		<li>You can delete a group or contact by selecting it and pressing the delete key</li>
		<li>Add a group by clicking on "Create a Group", filling out a name in the new field and pressing the "enter" or "return" key.</li>
		<li>Add a contact by clicking on "Create a Card" (as in roladex card), filling out their name in the new field and pressing hte "enter" or "return" key.</li>
	</ul>
</section>
