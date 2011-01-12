<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class_exists('PostResource') || require('PostResource.php');
class TaggedResource extends AppResource{
	public function __construct($attributes){
		parent::__construct($attributes);
		$this->total = 0;
	}
	public function __destruct(){}
	public $posts;
	public $page;
	public $sort_by;
	public $sort_by_direction;
	public $limit;
	public $start;
	public $post;
	public $total;
	
	public function get($tag = null, $page = 0){
		$this->limit = 5;
		$view = 'post/index';
		$this->sort_by = 'post_date';
		$this->sort_by_direction = 'desc';
		$this->start = 0;
		$this->page =  $page;
		if($this->page <= 0){
			$this->page = 1;
		}
		$this->start = ($this->page-1) * $this->limit;
		$this->title = 'Page ' . $this->page . ' for Posts Tagged ' . $tag;
		$this->description = 'This is page ' . $this->page . ' of a list of posts on ' . Application::$member->person->profile->site_name . ' that are tagged with ' . $tag;
		$this->posts = Post::findByTag(new Tag(array('text'=>$tag)), $this->start, $this->limit, $this->sort_by, $this->sort_by_direction, Application::$member->person_id);
		$this->total = Post::get_total_tagged_posts($tag, Application::$member->person_id);
		if(count($this->posts) === 0){
			$this->set_not_found();
			return;
		}
		for($i=0; $i<count($this->posts); $i++){
			$this->posts[$i]->conversation = PostResource::get_conversation_for($this->posts[$i]);
		}
		$this->keywords = implode(', ', String::getKeyWordsFromContent($this->output));
		$this->output = $this->render($view);
		return $this->render_layout('default');
	}	
}