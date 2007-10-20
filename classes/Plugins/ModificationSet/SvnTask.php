<?php
require_once("Plugins/ModificationSet/AbstractTask.php");

class Plugins_ModificationSet_SvnTask extends Plugins_ModificationSet_AbstractTask  {

	private $plugin;
	private $subtasks=array();

	/**
	 * Directory containing the Subversion project.
	 *
	 * @var string
	 */
	private $directory = '.';
	public function getName(){
		return "svn";
	}

	public function registerTask(Xinc_Plugin_Task_Interface &$task){
		$this->subtasks[]=$task;
	}
	public function getFilename(){
		return __FILE__;
	}
	public function getClassname(){
		return get_class($this);
	}



	/**
	 * Sets the svn checkout directory.
	 *
	 * @param string
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
	}


	public function __construct(Xinc_Plugin_Interface &$p){
		$this->plugin=$p;
	}

	public function getAttributes(){
		return array("directory");
	}
	public function getBuildSlot(){
		return Xinc_Plugin_Slot::PRE_PROCESS;
	}




	public function checkModified(){
		return $this->plugin->checkModified($this->directory);
	}

	public function validateTask(){
		if (!isset($this->directory)) {
			throw new Xinc_Exception_MalformedConfig('Element modificationSet/svn - required attribute \'directory\' is not set');
			//return false;
		}
		return true;
	}
	 
}
?>