<?php
	class_exists('TestTemplate') || require('lib/TestTemplate.php');
	class_exists('Resource_openid') || require('plugins/openid/Resource_openid.php');
    class OpenIdTest extends TestTemplate {
        public function __construct() {
            parent::__construct();
			$this->title = 'OpenIdTest Tests';
        }
		public function __destruct(){}
				
		public function setUp(){}
		public function tearDown(){}
		
		public function testIntegerRepresentations(){
			
		}
    }
?>
