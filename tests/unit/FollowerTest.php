<?php
	class_exists('TestTemplate') || require('lib/TestTemplate.php');
	class_exists('Person') || require('model/Person.php');
    class FollowerTest extends TestTemplate {
        public function __construct() {
            parent::__construct();
			$this->title = 'FollowerResource Tests';
        }
		public function __destruct(){}
				
		public function setUp(){
			
		}
		public function tearDown(){}
		public function testFollowerRequest(){
			$person = new Person(array('name'=>'Test Follower', 'url'=>'http://localhost/6d/test'));
			
			$this->assert(true, 'testing');
		}
    }
?>
