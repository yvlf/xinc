<?php
require_once("Xinc/Plugin/Interface.php");
require_once("Plugins/Test/TestTask.php");


class Plugins_Test implements Xinc_Plugin_Interface {
	
	public function validate(){
		return true;
	}


	
	/**
	 * Returns the defined tasks of the plugin
	 * @return Xinc_Plugin_Task[]
	 */
	public function getTaskDefinitions(){
		return array(new Plugins_Test_TestTask($this));
	}


	
	
	public function getFilename(){
		return __FILE__;
	}
	public function getClassname(){
		return get_class($this);
	}
	
}
?>