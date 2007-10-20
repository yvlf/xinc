<?php
class Xinc_Plugin_Slot {
	const LISTENER=0; // Plugin is run in any slot (listeners)
	const PRE_PROCESS=10; // First step, ModificiationSets, BootStrappers etc
	const PROCESS=20; // Builders
	const POST_PROCESS=30; // Publishers
}
?>