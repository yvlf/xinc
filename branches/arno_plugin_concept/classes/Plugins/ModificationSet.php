<?php
require_once("Xinc/Plugin/Interface.php");

require_once("Plugins/ModificationSet/ModificationSetTask.php");


class Plugins_ModificationSet implements Xinc_Plugin_Interface {
	public function validate(){
		return true;
	}
	public function getTaskDefinitions(){
		return array(new Plugins_ModificationSet_ModificationSetTask($this));
	}
	public function getFilename(){
		return __FILE__;
	}
	public function getClassname(){
		return get_class($this);
	}
}
?>