<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('PostResource') || require('PostResource.php');
class BlogResource extends AppResource{
	public function __construct($attributes = null){
		parent::__construct($attributes);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $posts;
	public $post;
	public $page;
	public $sort_by;
	public $sort_by_direction;
	public $limit;
	public function get($id = null, $q = null, $limit = 5, $tag = null){
		$this->q = $q;
		$post_title = null;
		$this->limit = intval($limit);
		$view = 'post/index';
		array_shift($this->url_parts);
		if(count($this->url_parts) > 0){
			$this->page = intval($this->url_parts[0]);
			if($this->page === 0){
				$post_title = array_shift($this->url_parts);
			}
		}
		if($this->page <= 0){
			$this->page = 1;
		}
		$this->start = ($this->page-1) * $this->limit;
		$this->sort_by = 'id';
		$this->sort_by_direction = 'desc';
		if($post_title === 'author'){
			$author_id = array_shift($this->url_parts);
			$this->posts = $this->getPostsByAuthor($author_id);
		}else if($post_title !== null){
			$this->post = Post::findAllPublished($post_title, Application::$member->person_id);
			$this->title = $this->post->title;
		}else if($tag !== null){
			$this->title = 'All Posts Tagged ' . $tag;
			$this->posts = $this->getPostsByTag(new Tag(array('text'=>$tag)));				
		}else if($this->q !== null){
			$this->title = "Results for $this->q";
			$this->posts = Post::search($q, $this->page, $this->limit, $this->sort_by, $this->sort_by_direction, Application::$current_user->person_id);
		}else{
			$this->title = 'All Posts';
			$this->posts = $this->getAllPosts($this->start, $this->limit, array($this->sort_by=>$this->sort_by_direction));
		}
		$this->keywords = implode(', ', String::getKeyWordsFromContent($this->output));
		if($this->post !== null){
			$this->description = $this->post->title;
			$this->post->conversation = PostResource::get_conversation_for($this->post);
			$view = 'post/show';
		}else{
			for($i=0; $i<count($this->posts); $i++){
				$this->description .= $this->posts[$i]->title . ',';
				$this->posts[$i]->conversation = PostResource::get_conversation_for($this->posts[$i]);
			}
		}
		$this->output = $this->renderView($view);
		return $this->renderView('layouts/default');
	}
	private function getAllPosts($start, $limit, $sort_by){
		return Post::findPublishedPosts($start, $limit, $sort_by, Application::$member->person_id);			
	}
	
	private function getPostsByTag($tag){
		return Post::findByTag($tag, $this->start, $this->limit, $this->sort_by, $this->sort_by_direction, Application::$member->person_id);
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