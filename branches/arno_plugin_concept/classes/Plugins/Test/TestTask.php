<?php
require_once("Xinc/Plugin/Task/Interface.php");

class Plugins_Test_TestTask implements Xinc_Plugin_Task_Interface {
	
	private $id;
	private $subtasks=array();
	private $plugin;
	private $method;
	
	public function getName(){
		return "test";
	}
	
	public function registerTask(Xinc_Plugin_Task_Interface &$task){
		$this->subtasks[]=$task;
	}
	public function getFilename(){
		return __FILE__;
	}

	

	public function __construct(Xinc_Plugin_Interface &$p){
		$this->plugin=$p;
	}

	public function getAttributes(){
		return array("id","method");
	}
	public function getBuildSlot(){
		return Xinc_Plugin_Slot::PRE_PROCESS;
	}

	public function process(Xinc_Project &$project){
		if($this->id==1){
			touch("lockfile");
			$project->setStatus(Xinc_Project_Status::PASSED);
		}
		else
		{
			$project->setStatus(Xinc_Project_Status::FAILED);
		}
	}
	public function getClassname(){
		return get_class($this);
	}
	
	public function setId($value){
		$this->id=$value;
	}
	
	public function setMethod($method){
		$this->method=$method;
	}
	public function validate(){
		return true;
	}
	
}
?>