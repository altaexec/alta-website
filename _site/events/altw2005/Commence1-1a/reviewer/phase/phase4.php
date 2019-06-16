<?php global $php_root_path ;
global $privilege_root_path ;

require_once ("$php_root_path" . $privilege_root_path . "/phase/phase1.php") ;

class phase4 extends phase1
{
	function phase4()
	{
		$this->phaseID = 4 ;
	}
}

?>