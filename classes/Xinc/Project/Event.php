<?php
class Xinc_Project_Event{
	
	const INIT=0; // Project loaded
	const PRE_PROCESS_START=10; // 
	const PRE_PROCESS_DONE=11;
	const PROCESS_START=20;
	const PROCESS_DONE=21;
	const POST_PROCESS_START=30;
	const POST_PROCESS_DONE=31;
	
	private $slot;
	private $event;
	private $status;
	
	public function __construct($slot,$event,$status){
		$this->slot=$slot;
		$this->event=$event;
		$this->status=$status;
	}
	
	public function getEvent(){
		return $this->event;
	}
	
	public function getStatus(){
		return $this->status;
	}
	
	public function getSlot(){
		return $this->slot;
	}
	
}
?>