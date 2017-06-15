<?php
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");		
	session_start();
	global $valid_user ;	
	
	// extract ( $HTTP_GET_VARS , EXTR_REFS ) ;
		
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	if ( !check_valid_user( &$err_message ) )
	{
		//This user is not login
		do_html_header("View File Failed" , &$err_message ) ;			
		$err_message .= " Sorry, You must login to view this file. <br>\n";
		$err = $err_message . "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
		do_html_footer(&$err);
		exit;
	}					
		

	//Get the conference info and logo data
	$conferenceInfo = get_conference_info();
	$data = @$conferenceInfo -> LogoFile;
	$type = @$conferenceInfo -> FileType;
	
	//Display the image
	header("Content-type: $type");
	echo $data;
?>