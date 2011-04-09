<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class_exists("Contact") || require("models/Contact.php");
class_exists("Inbox") || require("models/Inbox.php");
class InboxResource extends AppResource{
	public function __construct(){
		parent::__construct();
		$this->contacts = array();
	}
	public $message;
	public $messages;
	public $contacts;
	public $contact_ids;
	public function get(){
		$this->contacts = find_by::execute("owner_id=:owner_id", new Contact(array("owner_id"=>AuthController::$current_user->id)));
		$this->title = "Your Inbox";
		$this->messages = find_by::execute("owner_id=:owner_id", new Inbox(array("owner_id"=>AuthController::$current_user->id)));
		$this->messages = $this->messages === null ? array() : $this->messages;
		$this->output = View::render("inbox/index", $this);		
		return View::render_layout("default", $this);
	}
	public function post($message, $sender){
		$message = new Inbox(array("message"=>$message, "received"=>time(), "sender"=>$sender, "owner_id"=>AppResource::$member->id));
		save_object::execute($message);
		return "ok";
	}
}
