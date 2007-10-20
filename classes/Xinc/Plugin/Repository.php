<?php
require_once("Xinc/Plugin/Task/Exception.php");

class Xinc_Plugin_Repository {

	private static $instance;
	/**
	 * 
	 * @var Xinc_Plugin_Task_Interface[]
	 */
	private $definedTasks=array();
	private $plugins=array();
	public static function getInstance()
	{
		if (!Xinc_Plugin_Repository::$instance) {
			Xinc_Plugin_Repository::$instance = new Xinc_Plugin_Repository();
		}
		return Xinc_Plugin_Repository::$instance;
	}
	public function registerPlugin(Xinc_Plugin_Interface &$plugin){
		if(!$plugin->validate()){
			Xinc_Logger::getInstance()->error("cannot load plugin ".$plugin->getClassname());		
			return false;
		}
		$tasks=$plugin->getTaskDefinitions();
		
		$task=null;
		foreach($tasks as $task){
			if(isset($this->definedTasks[$task->getName()])){
				throw new Xinc_Plugin_Task_Exception();
			}
			$this->definedTasks[$task->getName()]=array("filename"=>$task->getFilename(),"classname"=>$task->getClassname(),"plugin"=>array("filename"=>$plugin->getFilename(),"classname"=>$plugin->getClassname()));
		}
	}

	public function &getTask($taskname){
		//var_dump($this->definedTasks);
		$taskData=$this->definedTasks[$taskname];
		if(empty($taskData)){
			
			throw new Xinc_Plugin_Task_Exception("undefined task $taskname");
		}
		require_once($taskData['filename']);
		if(!isset($this->plugins[$taskData['plugin']['classname']])){
			require_once($taskData['plugin']['filename']);
			$plugin=new $taskData['plugin']['classname'];
			$this->plugins[$taskData['plugin']['classname']]=&$plugin;

		}
		else
		{
			$plugin=$this->plugins[$taskData['plugin']['classname']];
		}
		 
		$className = $taskData['classname'];
		$Object = new $className($plugin);
		return $Object;
	}

}
?>