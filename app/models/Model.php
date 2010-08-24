<?php
class Model{
	private function __construct(){}
	public function __destruct(){}
	
	public static function deleteTable($name, $config){
		$db = Factory::get($config->db_type, $config);
		$errors = array();
		$model = new $name();
		try{
			$db->deleteTable($model->getTableName($config));				
		}catch(Exception $e){
			$errors[] = $e;
		}
		return $errors;
	}
	public static function map($src, $dest){
		$dest_reflector = new ReflectionObject($dest);
		$src_reflector = new ReflectionObject($src);
		foreach($dest_reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
			$method_name = $method->getName();
			if(strpos($method_name, 'set') === 0){
				$src_set_method = $src_reflector->getMethod(String::replace('/^set/', 'get', $method_name));
				$dest->$method_name($src_set_method->invoke($src));
			}
		}
		console::log($dest);
		return $dest;
	}
	private static function getModels($config){
		$root = str_replace('resources', '', dirname(__FILE__));
		$folder = dir($root);
		$className = null;
		$reflector = null;
		$models = array();
		while(($file = $folder->read()) !== false){
			if(preg_match('/^\./', $file) == 0){
				$className = str_replace('.php', '', $file);
				class_exists($className) || require('models/' . $file);
				$reflector = new ReflectionClass($className);
				if($reflector->hasMethod('install')){
					$model = $reflector->newInstanceArgs(array(null, null));
					$models[] = $model;
				}
			}
		}
		$folder->close();
		return $models;		
	}
}