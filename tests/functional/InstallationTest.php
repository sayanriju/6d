<?php
	class_exists('TestTemplate') || require('lib/TestTemplate.php');	
    class InstallationTest extends TestTemplate {
        public function __construct() {
            parent::__construct();
			$this->title = 'Installation Tests';
        }
		public function __destruct(){}
		public $member;
		public function setUp(){}
		public function tearDown(){}
		public function testInstallation(){
			
		}
    }
?>
