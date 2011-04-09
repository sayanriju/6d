<?php
	class_exists("Request") || require("app/app.php");
    class RequestTest extends TestTemplate {
        public function __construct() {
            parent::__construct();
			$this->title = "AS the system, I should send asynchronous requests so the user isn't wainting for a response";
        }
		public function __destruct(){}
		public $member;
		public function setUp(){}
		public function tearDown(){}
		public function test_sending_an_asynch_request(){		
			$url =  str_replace("tests/", "tests/messages/test_request.php", App::url_for(null));
			$output = Request::send_asynch(new HttpRequest(array("url"=>$url, "method"=>"get")));
			$this->assert($output, "Sending an asynch request. Should return true:" . $output);
		}
		public function test_sending_data_via_asynch_request(){
			$url =  str_replace("tests/", "tests/messages/test_request.php", App::url_for(null));
			$output = Request::send_asynch(new HttpRequest(array("url"=>$url, "method"=>"post", "data"=>"id=1&name=joey", "optional_headers"=>array("Content-type"))));
			$this->assert($output, "Sending an asynch request with data. Should return true:" . $output);
		}
		public function test_sending_data_request(){
			$url =  str_replace("tests/", "tests/messages/test_request.php", App::url_for(null));
			$output = Request::send(new HttpRequest(array("url"=>$url, "method"=>"post", "data"=>"id=1&name=joey")));
			$this->assert($output !== false, "Sending an synchronous request with data. Should return true:" . $output);
		}
		
		public function sending_data_lots_of_times(){
			$url =  str_replace("tests/", "tests/messages/test_request.php", App::url_for(null));
			for($i = 0; $i < 10; $i++){
				$output = Request::send_asynch(new HttpRequest(array("url"=>$url, "method"=>"post", "data"=>"id=1&name=joey", "optional_headers"=>array("Content-type"))));
				$this->assert($output, "Load test: " . $output);
			}
		}
    }
?>
