<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class StreamResource extends AppResource{
	public function __construct($attributes){
		parent::__construct($attributes);
	}
	public function __destruct(){}
	public $posts;
	public $page;
	public $sort_by;
	public $sort_by_direction;
	public $limit;
	
	public function get(){
		if(!AuthController::is_authorized()){
			throw new Exception(Resource::redirect_to::UNAUTHORIZED, 401);
		}
		$this->posts = Post::findFriendsPublishedStatii(Application::$current_user->person->id);
		$this->output = $this->render('post/index');
		return $this->render('layouts/default');
	}
	private function getAllPosts($start, $limit, $sort_by, $sort_by_direction){
		return Post::find($start, $limit, $sort_by, $sort_by_direction, Application::$current_user->person_id);			
	}
	private function getPostsByTag($tag){
		return Post::findByTag($tag, $this->start, $this->limit, $this->sort_by, $this->sort_by_direction, Application::$current_user->person_id);
	}
	private function getPostsByAuthor($author){
		$person = new Person(array('id'=>$author_id));
		if($person->id > 0){
			$person = Person::findById($person->id);
			if($person !== null){
				if($person->is_owner){
					$person->url = null;
				}
				$posts = Post::findByPerson($person, $start, $this->limit, $this->sort_by, $this->sort_by_direction, Application::$current_user->person_id);
				$this->title = "All Posts by " . $person->name;
			}
		}
		return $posts;
	}
}