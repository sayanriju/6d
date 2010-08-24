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
				
		public function setUp(){}
		public function tearDown(){}
		public function testConnections(){
			$member = new Member(new Person(array('name'=>'meg', 'email'=>'megguerra@me.com', 'member_name'=>'meg', 'password'=>'texas', 'url'=>'localhost/6d/meg', 'is_approved'=>true, 'do_list_in_directory'=>true, 'session_id'=>session_id(), 'is_owner'=>false, 'owner_id'=>1, 'uid'=>uniqid(null, true))));
			$this->assert($member !== null, 'Initialize a member object');			
			$inspector = new ReflectionClass(get_class($member));
			$member = Member::saveAsPerson($member);
			var_dump($member);
			$members = $member->findAll(null);
			var_dump($members);
		}
		
		public function testFlatObject(){
			$person = new Person();			
			$inspector = new ReflectionClass(get_class($person));
			$this->assert($inspector->getParentClass()->getParentClass() === false, 'Expect no parent object.');
		}
		
    }
?>
