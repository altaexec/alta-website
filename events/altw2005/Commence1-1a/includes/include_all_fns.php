<?php //Include all the functions pages that are required for every page
	//db_connect.php is already included at main_fns.php
	//So no need to include db_connect.php here again
	global $php_root_path ;
	require_once("$php_root_path/includes/globals.php"); // Global constants go here
	require_once("$php_root_path/includes/data_validation_fns.php");	
	require_once("$php_root_path/includes/output_fns.php");	
	require_once("$php_root_path/includes/user_authen_fns.php");
	require_once("$php_root_path/includes/main_fns.php");
	require_once("$php_root_path/user/phase/include_all_phase.php");

?>