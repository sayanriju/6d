<?php
class Header_OpenId{
	public function __construct(){}
	public function __destruct(){}
	public function execute($output){
		$head = <<<eos
<link rel="openid2.provider" href="%s/">
</head>	
eos;
		$url = FrontController::urlFor(Application::$member->member_name);
		$head = sprintf($head, FrontController::urlFor('openid.txt'));
		$output = str_replace('</head>', $head, $output);
		return $output;
	}
}