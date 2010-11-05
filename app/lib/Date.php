<?php
class Date{
	public static function to_date($format, $date_as_string){
		return date($format, strtotime($date_as_string));
	}
	public static function time_since($since){
		$chunks = array(
			array(31536000, 'year')
			, array(2592000, 'month')
			, array(604800, 'week')
			, array(86400, 'day')
			, array(3600, 'hour')
			, array(60, 'minute')
			, array(1, 'second')
		);
		for($i = 0, $j = count($chunks); $i < $j; $i++){
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			if(($count = floor($since / $seconds)) != 0){
				break;
			}
		}
		$result = ($count == 1) ? '1 ' . $name : "$count {$name}s";
		return $result;
	}
}