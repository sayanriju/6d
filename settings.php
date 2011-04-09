<?php
class Settings{
	public static $app_path;
	public static $theme;
	public static $site_header;
	public static $storage_provider;
	public static function path_for($file){
		return self::$app_path . "/" . $file;
	}
}
Settings::$app_path = dirname(__FILE__) . "/app";
Settings::$theme = "default";
Settings::$site_header = "Chinchillalite, a RESTful PHP Framework";
Settings::$storage_provider = (object)array("type"=>"sqlite", "path"=>Settings::path_for("data/chinchillalite.db"));
$logger = new Logger();
//NotificationCenter::add($logger, "request_was_made");
