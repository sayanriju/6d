<?php
class_exists("Post") || require("models/Post.php");
class MailingListResource extends Resource{
	public function load_mailinglist(){
		
	}
}
NotificationCenter::add(new MailingListResource(), "load_mailinglist");
