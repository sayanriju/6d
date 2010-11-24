<?php
class_exists('AppResource') || require('AppResource.php');
class ContactResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function get(){
		$this->title = "Send us a message";
		$this->output = $this->render('contact/index');
		return $this->render('layouts/default');
	}
	public function post($from, $message = null){
		if($from === null || $message === null){
			self::setUserMessage("Did you want to say something?");
			$this->output = $this->render('contact/index');
			return $this->render('layouts/default');
		}
		self::setUserMessage("Thanks for dropping a line. We'll get right on it dog gone it.");
		return $this->render('layouts/default');
	}
	
	private function send($emails, $message=null, $subject){
		$to = implode($emails,",");
		$headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=iso-8859-1\r\nFrom: webmaster@get6d.com\r\nReply-To: webmaster@get6d.com\r\nX-Mailer: PHP/" . phpversion();

		if($emails[0] !== null && strlen($emails[0]) > 0){
			if(mail($to, $subject, $message, $headers))
				return true;
			else
				return false;
		}else{
			return false;
		}
	}
	
	
}

?>