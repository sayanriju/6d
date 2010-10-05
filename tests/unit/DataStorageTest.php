<?php
	class_exists('TestTemplate') || require('lib/TestTemplate.php');
	class_exists('Person') || require('models/Person.php');
	class_exists('Member') || require('models/Member.php');
	
    class DataStorageTest extends TestTemplate {
        public function __construct() {
            parent::__construct();
			$this->title = 'DataStorageTest Tests';
        }
		public function __destruct(){}
		public $member;
		public function setUp(){}
		public function tearDown(){
			Member::delete($this->member);
		}
		public function testConnections(){
			$this->member = new Member(new Person(array('name'=>'test', 'email'=>'test@test.com', 'member_name'=>'test', 'password'=>'test', 'url'=>urlencode('localhost/6d/test'), 'is_approved'=>true, 'do_list_in_directory'=>true, 'session_id'=>session_id(), 'is_owner'=>false, 'owner_id'=>1, 'uid'=>uniqid(null, true))));
			$this->assert($this->member !== null, 'Initialize a member object');			
			$inspector = new ReflectionClass(get_class($this->member));
			$this->member = Member::saveAsPerson($this->member);
		}
		
		public function testFlatObject(){
			$person = new Person();			
			$inspector = new ReflectionClass(get_class($person));
			$this->assert($inspector->getParentClass()->getParentClass() === false, 'Expect no parent object.');
		}
		
    }
?>
