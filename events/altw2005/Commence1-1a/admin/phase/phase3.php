<?php

global $php_root_path ;
global $privilege_root_path ;
	
require_once ("$php_root_path" . $privilege_root_path . "/phase/phasebase.php") ;

class phase3 extends phasebase
{
	function phase3()
	{
		$this->phaseID = 3 ;
	}
}

?>