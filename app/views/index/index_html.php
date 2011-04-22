<?php class_exists("AuthController") || require("controllers/AuthController.php");?>
<section data-title="6d: Own your content. Your identity web app." id="index">
	<h1>Own your content</h1>

	<p>You want to share your thoughts, ideas, opinions, photos, and videos. Social networks are not private enough and email is restrictive.</p>

	<p>You want to socialize and interact online, but some of that should be public, while the other very private.</p>
	
	<p>You should own your content and have a reasonable level of control over it. That's the goal of 6d.</p>
	
	<p>6d is an online identity building application. Its purpose is to allow you to centralize your online life, photos, thoughts, posts, etc, but still share them with friends, colleagues, and/or the world.</p>
	
	<p>We're working on 6d. There's just 2 of us and we're tackling challenging problems. You'll find us online at joeyguerra.com and erikbigelow.com. Share something with us. And, feel free to join our mailing list, or even contribute. We're more than willing to take on help.</p>
	<form action="<?php echo AppResource::url_for_member("mailinglist");?>" method="post">
		<fieldset>
			<legend>Join the mailing list</legend>
			<input type="text" name="email" value="" />
			<button type="submit">Join</button>
		</fieldset>
	</form>
</section>