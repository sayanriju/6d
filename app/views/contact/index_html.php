<form action="<?php echo FrontController::urlFor('contact');?>" method="post">
	<fieldset>
		<legend>Contact us</legend>
		<p>
			<label for="from">What's your email address so we can get back with you?</label>
			<input name="from" id="from" type="text" />
		</p>
		<p>
			<label for="message">Message</label>
			<textarea name="message" id="message" cols="60" rows="5"></textarea>
		</p>
		<p>
			<button type="submit"><span>Send</span></button>
		</p>
	</fieldset>
</form>