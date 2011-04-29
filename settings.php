<?php
class_exists("Setting") || require("models/Setting.php");
class Settings{
	public static $app_path;
	public static $theme;
	public static $site_header;
	public static $storage_provider;
	public static function virtual_path($file){
		return dirname(__FILE__) . "/" . $file;
	}
	public static function path_for($file){
		return self::$app_path . "/" . $file;
	}
	public static function title($owner_id){
		$title = Repo::find("select value from settings where key='title' and owner_id=:owner_id", (object)array("owner_id"=>$owner_id))->first(new Setting());
		if($title !== null) return $title->value;
		return $title;
	}
}
Settings::$app_path = dirname(__FILE__) . "/app";
Settings::$theme = "earthlingtwo";
Settings::$site_header = "Chinchillalite, a RESTful PHP Framework";
Settings::$storage_provider = (object)array("type"=>"sqlite", "path"=>Settings::virtual_path("data/chinchillalite.db"));
$logger = new Logger();
//NotificationCenter::add($logger, "request_was_made");
