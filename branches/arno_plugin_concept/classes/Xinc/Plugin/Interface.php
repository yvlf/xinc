<?php
require_once("Xinc/Plugin/Exception.php");
interface Xinc_Plugin_Interface {
	
	
	
	public function validate();
	
	/**
	 * Returns the defined tasks of the plugin
	 * @return Xinc_Plugin_Task[]
	 */
	public function getTaskDefinitions();
	
	public function getFilename();
	public function getClassname();	
}
?>