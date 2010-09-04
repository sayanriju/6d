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
			$this->assert(OpenidResource::btowc("0") == dechex(0), "Testing 0 == " . OpenidResource::btowc(0));
			$this->assert(OpenidResource::btowc("127") === dechex(127), "Testing 127 == " . OpenidResource::btowc(127));
			$this->assert(OpenidResource::btowc("128") === dechex(128), "Testing 128 == " . OpenidResource::btowc(128));
			$this->assert(OpenidResource::btowc("255") === dechex(255), "Testing 255 == " . OpenidResource::btowc(255));
			$this->assert(OpenidResource::btowc("32768") === dechex(32768), "Testing 32768 == " . OpenidResource::btowc(32768));
		}
    }
?>
