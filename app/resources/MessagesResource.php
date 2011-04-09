<?php
class_exists("AppResource") || require("AppResource.php");
class_exists("AuthController") || require("controllers/AuthController.php");
class_exists("Contact") || require("models/Contact.php");
class_exists("Message") || require("models/Message.php");
class_exists("Outbox") || require("models/Outbox.php");
class MessagesResource extends AppResource{
	public function __construct(){
		parent::__construct();
		$this->contacts = array();
		$this->contact_ids = array();
		if(!AuthController::is_authed()){
			$this->set_unauthed("Please signin.");
		}
	}
	public $message;
	public $contacts;
	public $contact_ids;
	public $messages;
	public function get(){
		$this->contacts = find_by::execute("owner_id=:owner_id", new Contact(array("owner_id"=>AuthController::$current_user->id)));
		if($this->contacts === null) $this->contacts = array();
		$this->messages = find_by::execute("owner_id=:owner_id", new Message(array("owner_id"=>AuthController::$current_user->id)));
		$this->title = "Messages";
		$this->output = View::render('message/index', $this);
		return View::render_layout('default', $this);
	}
	public function post($message, $contact_ids = null){
		$contact_ids = is_array($contact_ids) ? $contact_ids : null;
		if($contact_ids !== null){
			for($i=0; $i<count($contact_ids)-1; $i++){
				$contact_ids[$i] = (int)$contact_ids[$i];
			}
			
			$this->contacts = find_by::execute("ROWID in (" . implode(",", $contact_ids) . ") and owner_id=:owner_id", new Contact(array("owner_id"=>AuthController::$current_user->id)));
			if(!$this->contacts) $this->contacts = array();
			if(!is_array($this->contacts)) $this->contacts = array($this->contacts);
			
			foreach($this->contacts as $contact){
				$outbox = new Outbox(array("message"=>$message, "owner_id"=>(int)AuthController::$current_user->id, "sent"=>time(), "recipient"=>$contact->url));
				save_object::execute($outbox);
				$this->send_message($message, $contact);
			}
			App::set_user_message("Message was sent");
			$this->set_redirect_to(AuthController::$current_user->name . "/message");
			$this->output = View::render("message/index", $this);
			return View::render_layout("default", $this);
		}
	}
	
	private function send_message($message, $contact){
		$message = urlencode($message);
		$output = Request::send_asynch(new HttpRequest(array("url"=>"http://" . $contact->url . "/inbox", "method"=>"post", "data"=>"message=$message&sender=" . AuthController::$current_user->name . "@" . App::$domain, null)));
	}
}
