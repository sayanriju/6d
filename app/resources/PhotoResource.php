<?php
	class_exists('AppResource') || require('AppResource.php');
	class_exists('Photo') || require('models/Photo.php');
	class PhotoResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			$this->url = App::url_for(null);
			if(!AuthController::is_authorized()){
				$this->set_unauthorized();
				return;
			}
		}
		public function __destruct(){
			parent::__destruct();
		}
		public function delete($src){
			$src = str_replace(App::url_for(null), '', $src);
			$did_delete = Photo::delete($src);
			if(!$did_delete){
				self::set_user_message(sprintf('failed to delete %s', $src));
			}
			$this->redirect_to('photos');
		}
		public function put($ratio, $offset_x, $offset_y, $dst_w, $dst_h, $src_file_name, $dst_file_name){
			$src_file_name = str_replace(App::url_for(null), '', $src_file_name);
			$dst_file_name = str_replace(App::url_for(null), '', $dst_file_name);
			$extension = pathinfo($src_file_name, PATHINFO_EXTENSION);
			$src_image = null;
			$message = null;
	        if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG"){ 
	          $src_image=imagecreatefromjpeg($src_file_name); 
	        } 
	        if($extension == "png") { 
	          $src_image=imagecreatefrompng($src_file_name); 
	        }
	        if($extension == "gif") { 
	          $src_image=imagecreatefromgif($src_file_name); 
	        }			
			$src_w = $dst_w/$ratio;
			$src_h = $dst_h/$ratio;
			$dst_x = 0;
			$dst_y = 0;
			$src_x = $offset_x/$ratio;
			$src_y = $offset_y/$ratio;
			$dst_image = imagecreatetruecolor($dst_w,$dst_h);
			$did_save = imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
			// change the file name to point to the profile folder within media.
			//$file_name = String::replace('media', 'media/profile', $file_name);
			error_log($dst_w . ' h = ' . $dst_h . ' ration = ' . $ratio);
			if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG"){ 
	          $did_save = imagejpeg($dst_image, $dst_file_name); 
	        } 
	        if($extension == "png") { 
	          $did_save = imagepng($dst_image, $dst_file_name); 
	        }
	        if($extension == "gif") { 
	          $did_save = imagegif($dst_image, $dst_file_name); 
	        }
			if(!$did_save){
				$message = "I was unable to save $dst_file_name. It's probably because I don't support files of type $extension.";
			}
			return json_encode(array('ratio'=>$ratio, 'offset_x'=>$offset_x, 'offset_y'=>$offset_y, 'dst_w'=>$dst_w, 'dst_h'=>$dst_h, 'src_w'=>$src_w, 'src_h'=>$src_h, 'src_x'=>$src_x, 'src_y'=>$src_y, 'file_name'=>$dst_file_name, 'did_save'=>$did_save ? 'true' : 'false', 'message'=>$message));
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
			return App::url_for(null) . $src;
		}
		public static function getBigSrc($src){
			return App::url_for(null) . $src;
		}
	}

?>