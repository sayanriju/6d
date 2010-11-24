<?php
	class_exists('TestTemplate') || require('lib/TestTemplate.php');
	class_exists('Resource::redirect_to') || require('lib/Resource::redirect_to.php');
	class_exists('PostResource') || require('resources/PostResource.php');
	class_exists('FollowerResource') || require('resources/FollowerResource.php');
    class Resource::redirect_toTest extends TestTemplate {
        public function __construct() {
            parent::__construct();
			$this->title = 'Resource::redirect_to Tests';

        }
		public function __destruct(){}
				
		public function setUp(){}
		public function tearDown(){}
		
    }
?>
