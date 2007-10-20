<?php

interface Xinc_Listener_Interface 
{

	
	/**
	 * Enter description here...
	 *
	 * @param Xinc_Event $event
	 */
	public function processEvent(Xinc_Project &$project,Xinc_Project_Event &$event);
}

?>