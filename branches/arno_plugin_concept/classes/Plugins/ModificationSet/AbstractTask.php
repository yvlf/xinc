<?php
abstract class Plugins_ModificationSet_AbstractTask implements Xinc_Plugin_Task_Interface  {
	
	public final function process(Xinc_Project &$project){
		if($this->checkModified()){
			
			$project->setStatus(Xinc_Project_Status::PASSED);
		}
		else
		{
			$project->setStatus(Xinc_Project_Status::STOPPED);
		}
	}
	
	    /**
     * Check if this modification set has been modified
     *
     */
	public abstract function checkModified();
	
	/**
	 * Check necessary variables are set
	 * 
	 * @throws Xinc_Exception_MalformedConfig
	 */
	public function validate(){
		try {
			return $this->validateTask();
		}
		catch(Exception $e){
			Xinc_Logger::getInstance()->error("Could not validate: ".$e->getMessage());
			return false;
		}
	}
	public abstract function validateTask();
	
}
?>