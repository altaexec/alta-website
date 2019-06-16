<?php

global $php_root_path ;
global $privilege_root_path ;

require_once ("$php_root_path" . $privilege_root_path . "/phase/phasebase.php") ;

class phase4 extends phasebase
{
	function phase4()
	{
		$this->phaseID = 4 ;
	}
}

?>