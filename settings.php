<?php
class_exists("Setting") || require("models/Setting.php");
class Settings{
	public static $app_path;
	public static $theme;
	public static $site_header;
	public static $storage_provider;
	public static $max_filesize;
	public static function virtual_path($file){
		return dirname(__FILE__) . "/" . $file;
	}
	public static function path_for($file){
		return self::$app_path . "/" . $file;
	}
	public static function title($owner_id){
		$title = Setting::find("title", $owner_id);
		if($title !== null) return $title->value;
		return $title->value;
	}
}
Settings::$max_filesize = 2000000;
Settings::$app_path = dirname(__FILE__) . "/app";
Settings::$theme = "html5";
Settings::$site_header = "6d, an online identity building web app. Own your online identity. Own your content.";
Settings::$storage_provider = (object)array("type"=>"sqlite", "path"=>Settings::virtual_path("data/6d.db"));
$logger = new Logger();
//NotificationCenter::add($logger, "request_was_made");
