<?php

require_once("Xinc.php");
$dir=dirname(__FILE__);
$xinc=new Xinc();
$xinc->main("config.xml","plugins.xml","log","status");

?>