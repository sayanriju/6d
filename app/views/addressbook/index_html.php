<ul>
</ul>
<section id="contacts">
	<dl>
		<dt>Contacts</dt>
		<?php foreach($contacts as $contact):?>
			<dd>
				<a href="http://<?php echo $contact->url;?>" title="<?php echo $contact->name;?>"><?php echo $contact->name;?></a>				
				<a href="<?php echo AppResource::url_for_user("contact?id={$contact->id}");?>">edit</a>
				<a href="<?php echo AppResource::url_for_user("message", array("contact_ids[]"=>$contact->id));?>" title="Send <?php echo $contact->name;?> a message">new message</a>
			</dd>
		<?php endforeach;?>
	</dl>
	<form action="<?php echo AppResource::url_for_user("contact");?>" method="get">
		<button type="submit">Add a contact</button>
	</form>

</section>
