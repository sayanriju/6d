<?php
	session_start();
	date_default_timezone_set('America/Chicago');
	$_appPath = str_replace(sprintf('%stests%sindex.php', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), '/app', __FILE__);
	$_rootPath = str_replace(sprintf('%stests%sindex.php', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), '', __FILE__);
	$output = '';
	set_include_path(get_include_path() . PATH_SEPARATOR . $_rootPath);
	set_include_path(get_include_path() . PATH_SEPARATOR . $_appPath);
	if(!isset($_SESSION))
		$_SESSION = array();

	if(!ob_start('ob_gzhandler')===false){
		ob_start();
	}
	if(file_exists($_rootPath . '/AppConfiguration.php')){
		require('AppConfiguration.php');
	}
	$root = str_replace('index.php', '', __FILE__);
	$unit = $root . 'unit/';
	$folder = dir($unit);
	while (false !== ($entry = $folder->read())){
		$path = $unit . $entry;
		if($entry != '.' && $entry != '..' && file_exists($path)){
			require($path);
			$pieces = explode('/', $path);
			$className = str_replace('.php', '', $pieces[count($pieces)-1]);
			$test = new $className();
			$test->execute();
			$output .= $test->message();
		}
	}
	$folder->close();
	ob_end_flush();
?>

<html>
	<head>
		<title>Tests</title>
	</head>
	<body>
		<?php echo $output;?>
	</body>
</html>		
