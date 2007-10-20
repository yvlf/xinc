<?php
require_once("Xinc/Plugin/Task/Interface.php");
require_once("Plugins/ModificationSet/Interface.php");

class Plugins_ModificationSet_ModificationSetTask implements Xinc_Plugin_Task_Interface {

	private $subtasks=array();
	private $plugin;
	
	public function validate(){
		foreach($this->subtasks as $task){
			if(!in_array(Plugins_ModificationSet_AbstractTask,class_parents($task))){
				return false;
			}
				
		}
		return true;
	}
	public function getClassname(){
		return get_class($this);
	}
	public function getName(){
		return "modificationset";
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
		foreach($this->subtasks as $task){
			
			$task->process($project);
			if($project->getStatus() == Xinc_Project_Status::PASSED ){
				
				return;
			}
		}
		$project->setStatus(Xinc_Project_Status::STOPPED);

	}

}

?>