<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('Person') || require('models/Person.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class_exists('PostResource') || require('PostResource.php');
class PostsResource extends AppResource{
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
	
	public function get($page = 0){
		if(!AuthController::is_authorized()){
			$this->set_unauthorized();
			return null;
		}
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
		$this->title = 'Page ' . $this->page . ' Posts';
		$this->description = 'This is page ' . $this->page . ' of a list of posts on ' . Application::$member->person->profile->site_name;
		$this->posts = Post::find($this->start, $this->limit, $this->sort_by, $this->sort_by_direction, Application::$member->person_id);
		$this->total = Post::get_total_published_posts(Application::$member->person_id);
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
	public function post(Post $post, $people = array(), $groups = array(), $make_home_page = false, $public_key = null, $photo_names = array()){
		$errors = array();
		if(AuthController::is_authorized()){
			$post->source = Application::$current_user->url;
			$this->save($post, $people, $groups, $make_home_page);
			$this->redirect_to($post->custom_url);
		}else if($public_key != null && strlen($public_key)>0){
			$person = Person::findByPublicKeyAndUrl($public_key, $post->source);
			$response = 'ok';
			if($person != null && $person->is_approved){
				// This block of code gets an existing post and updates that.
				$existing_post = Post::findByPersonPostId($post->person_post_id, Application::$member->person_id);
				if($existing_post != null){
					$post->id = $existing_post->id;
					$post->is_published = $existing_post->is_published;
				}else{
					$post->is_published = false;
					$post->source = $person->url;
					$post->id = null;
				}
				$post->created = date('c');
				if($post->post_date === null || strlen($post->post_date) === 0 || $post->post_date === 'today'){
					$post->post_date = date('c');
				}
				$post->body = $this->filter_text($post->body);
				$post->title = $this->filter_text($post->title);
				$post->body = urldecode($post->body);
				$post->owner_id = Application::$member->person_id;
				if($post->type !== Post::$status){
					$post->custom_url = String::stringForUrl($post->title);
				}
				list($post, $errors) = Post::save($post);
				if(count($errors) > 0){
					foreach($errors as $key=>$error){
						error_log("an error occured: $key=$error");
					}
				}
			}else{
				$response = "Couldn't find a person with the given public key.";
			}
		}else{
			$response = 'My website doesn\'t have you in my system. You\'ll have to send me a <form action="' . App::url_for(Application::$member->member_name . '/follower') . '" method="post"><button type="submit"><span>Send Friend Request</span></button></form> to add you as a friend before you send me any messages.';
		}		
		return $response;
	}
	private function save(Post $post, $people, $groups, $make_home_page){
		$post->created = date('c');
		if(strlen($post->post_date) === 0){
			$post->post_date = date('c');
		}
		$post->owner_id = Application::$current_user->person_id;
		if(strlen($post->body) > 0){
			if($post->type !== 'status'){
				$post->custom_url = String::stringForUrl($post->title);
			}else{
				$post->is_published = true;
			}
			list($post, $errors) = Post::save($post);
			$post->person_post_id = $post->id;
			if($errors == null){
				if($make_home_page){
					$this->makeHomePage($post);
				}else if($post->isHomePage($this->getHome_page_post_id())){
					Setting::delete('home_page_post_id');
				}
				self::setUserMessage('Post was saved.');
				$this->sendPostToGroups($groups, $post);
				$this->sendPostToPeople($people, $post);
			}else{
				$message = 'An error occurred while saving your post:';
				foreach($errors as $key=>$value){
					$message .= "$key=$value";
				}
				self::setUserMessage($message);
			}
		}
	}
	private function makeHomePage($post){
		$setting = Setting::findByName('home_page_post_id');
		$setting->value = $post->id;
		$setting->owner_id = Application::$current_user->person_id;
		Setting::save($setting);
	}
	private function sendPostToGroups($groups, Post $post){
		if(count($groups) > 0){
			foreach($groups as $text){
				$text = urldecode($text);
				if($text === 'All Contacts'){
					$this->people = Person::findAllByOwner(Application::$current_user->person_id);
				}else{
					$this->people = Person::findByTagTextAndOwner(urlencode($text), Application::$current_user->person_id);
				}
				$this->sendToPeople($this->people, $post);
			}
		}
	}
	private function sendPostToPeople($people, Post $post){
		if(count($people) > 0){
			$people = Person::findByIds($people, Application::$current_user->person_id);
			if($people !== null && count($people) > 0){
				$this->sendToPeople($people, $post);
			}
		}		
	}
	private function sendToPeople($people, $post){
		$datum = array();
		$responses = array();
		$to = array();
		foreach($people as $person){
			error_log($person->name . ' ' . $person->public_key);
			if($person->id != Application::$current_user->person_id && $person->is_approved && $person->public_key !== null){
				error_log(sprintf("sendToPeople -> person= %s, current user = %s",$person->name, Application::$current_user->name));
				$datum[] = sprintf("person_post_id=%s&title=%s&body=%s&source=%s&is_published=%s&post_date=%s&public_key=%s&type=%s", urlencode($post->id), urlencode($post->title), urlencode($post->body), urlencode($post->source), $post->is_published, urlencode($post->post_date), urlencode($person->public_key), $post->type);
				$to[] = $person;
				error_log($datum[count($datum)-1]);
			}else{
				error_log("failed trying to send to " . $person->name);
			}
		}
		if(count($datum) > 0){
			$responses = NotificationResource::sendMultiNotifications($to, 'posts', $datum, 'post');
			if(count($responses) > 0){
				$message = array();
				foreach($responses as $key=>$response){
					$person = $to[$key];
					Resource::setUserMessage($person->name . ' responded with ' . $response);
				}
			}
		}else{
			Resource::setUserMessage("Could not send to anybody you picked because none of them have been confirmed as friends.");
		}
		error_log(Resource::get_user_message());
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