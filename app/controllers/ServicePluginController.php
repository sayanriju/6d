<?php
class_exists('ServiceProvderCommand') || require('commands/ServiceProviderCommand.php');
class_exists('PluginController') || require('lib/PluginController.php');
class_exists('DefaultServicePlugin') || require('plugins/DefaultServicePlugin.php');
class ServicePluginController{
	public function __construct(){}
	public function __destruct(){}
	public static function execute($command){
		return self::getServicePlugin($command)->execute($command);
	}
	public static function getServicePlugin($command){
		$plugins = PluginController::get_plugins('plugins', 'Service');
		$servicePlugin = null;
		foreach($plugins as $plugin){
			if($plugin->canHandle($command)){
				$servicePlugin = $plugin;
				break;
			}
		}
		if($servicePlugin === null){
			$servicePlugin = new DefaultServicePlugin();
		}
		return $servicePlugin;
	}
}