<?php
	class_exists('AppResource') || require('AppResource.php');
	class_exists('Photo') || require('models/Photo.php');
	class PhotoResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			$this->url = FrontController::urlFor(null);
			if(!AuthController::isAuthorized()){
				throw new Exception(FrontController::UNAUTHORIZED, 401);
			}
		}
		public function __destruct(){
			parent::__destruct();
		}
		public function put($photo){
			
		}
		
		public static function getThumbnailWidth($src){
			$size = getimagesize($src);
			return $size[0] / 4;
		}
		public static function getThumbnailHeight($src){
			$size = getimagesize($src);
			return $size[1] / 4;
		}
		public static function getWidth($src){
			$size = getimagesize($src);
			return $size[0]/4;
		}
		public static function getHeight($src){
			$size = getimagesize($src);
			return $size[1]/4;
		}
		
		public static function getLittleSrc($src){
			return FrontController::urlFor(null) . $src;
		}
		public static function getBigSrc($src){
			return FrontController::urlFor(null) . $src;
		}
	}

?>