<?php
require_once("Xinc/Plugin/Slot.php");
require_once("Xinc/Project/Status.php");
require_once("Xinc/Plugin/Task/Processor/Interface.php");

interface Xinc_Plugin_Task_Interface extends Xinc_Plugin_Task_Processor_Interface  {
	/**
	 * Returns the slot of the process the plugin is run
	 *
	 */
	public function getBuildSlot();
		
	
	public function validate();
	public function getAttributes();
	public function getName();
	public function getFilename();
	public function getClassname();
	//public function registerTask(Xinc_Plugin_Task_Interface  &$task);
	public function __construct(Xinc_Plugin_Interface &$plugin);
	public function process(Xinc_Project &$project);
}
?>