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
		public function put($ratio, $x, $y, $file_name){
			$file_name = str_replace(FrontController::urlFor(null), '', $file_name);
			$extension = pathinfo($file_name, PATHINFO_EXTENSION);
			$tmp_image = null;
	        if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG"){ 
	          $tmp_image=imagecreatefromjpeg($file_name); 
	        } 
	        if($extension == "png") { 
	          $tmp_image=imagecreatefrompng($file_name); 
	        }
	        if($extension == "gif") { 
	          $tmp_image=imagecreatefromgif($file_name); 
	        }
			$width = imagesx($tmp_image);
			$height = imagesy($tmp_image);
			$new_width = $ratio * $width;
			$new_height = $ratio * $height;
			$new_image = imagecreatetruecolor($new_width,$new_height);
			ImageCopyResized($new_image, $tmp_image, $x, $y, $x, $y, $new_width, $new_height, $width, $height);
	        $did_save = false;
			if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG"){ 
	          $did_save = imagejpeg($new_image, $file_name); 
	        } 
	        if($extension == "png") { 
	          $did_save = imagepng($new_image, $file_name); 
	        }
	        if($extension == "gif") { 
	          $did_save = imagegif($new_image, $file_name); 
	        }
			return implode(',', array($ratio, $x, $y, $file_name, $did_save));
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