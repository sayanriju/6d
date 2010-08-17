<?php
class_exists('ServiceProviderCommand') || require('ServiceProviderCommand.php');
class IntroductionCommand extends ServiceProviderCommand{
	public function __construct($target, $sender){
		$this->target = $target;
		$this->sender = $sender;
	}
	public function __destruct(){}
	public $target;
	public $sender;
	public function execute(){
		throw new Exception("Not implemented");
	}
	public function getTargetUrl(){
		return $this->target->url;
	}
}