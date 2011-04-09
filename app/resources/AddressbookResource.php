<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class_exists("Contact") || require("models/Contact.php");
class AddressbookResource extends AppResource{
	public function __construct(){
		parent::__construct();
		$this->contacts = array();
		if(!AuthController::is_authed()){
			$this->set_unauthed("Please signin to see your addressbook.");
		}
	}
	public $message;
	public $contacts;
	public $tags;
	public function get(){		
		$this->contacts = find_by::execute("owner_id=:owner_id", new Contact(array("owner_id"=>AuthController::$current_user->id)));
		if(!$this->contacts) $this->contacts = array();
		if(!is_array($this->contacts)) $this->contacts = array($this->contacts);
		$this->title = "Your addressbook";
		$this->output = View::render('addressbook/index', $this);
		return View::render_layout('default', $this);
	}
}