<?php global $php_root_path ;
global $privilege_root_path ;

require_once("$php_root_path/includes/main_fns.php");
require_once("$php_root_path" . $privilege_root_path . "/includes/main_fns.php");
require_once("$php_root_path/includes/output_fns.php");
	
class phasebase
{
	var $phaseID ;

	function display_menu( &$header_str , $err_message= "" )
	{		
		echo $header_str ;
	}
}

?>