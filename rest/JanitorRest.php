<?php
// This script will cleanup mess in the database

class JanitorRest {
	
	public function GET($request, $options) {
		$option = "";
		if (count($options) > 0) {
			$option = (trim($options[0]) != "") ? $options[0] : $option;
		}
		
		if ($option != "clean" && $option != "DbCheck") {
			exit();
		}
		
		$item = new QueueItem(time(), "JanitorRest." . $option, null,	 false, 0, true);
		QueueServer::addItemToDatabase($item);
		
		return array("status"=>"added to queue");
	}
	
	
	public function clean() {
		HookHandler::getInstance()->fire('onInfo', "Janitor starts cleaning.");
		HookHandler::getInstance()->fire("onJanitorClean");
	}
	
	public function DbCheck() {
		HookHandler::getInstance()->fire('onInfo', "Janitor checking the datbase.");
		HookHandler::getInstance()->fire("onJanitorDbCheck", "before");
		HookHandler::getInstance()->fire("onJanitorDbCheck", "check");
		HookHandler::getInstance()->fire("onJanitorDbCheck", "after");
	}
}
?>