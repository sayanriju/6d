<?php
class Context{
	public static function server($key){
		return array_key_exists($key, $_SERVER) ? $_SERVER[$key] : null;
	}
}