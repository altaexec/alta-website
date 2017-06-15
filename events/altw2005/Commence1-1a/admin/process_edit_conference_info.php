<?php
/*
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
*/	
	if($HTTP_POST_VARS["Submit"] == "Submit")
	{
		
		// If conference is already set up
		// call the function to edit the conference information
		// otherwise set up conference.
		if ($HTTP_POST_VARS["conferenceID"])
		{
			$result = edit_conference_info($HTTP_POST_VARS,$HTTP_POST_FILES);
		} else {
			$result = setup_conference($HTTP_POST_VARS,$HTTP_POST_FILES) ;	
		}
		
		//echo $result;
		//exit;
		
		if($result === true){
			header("Location: view_conference_info.php");
			exit;
		}
		else{
			do_html_header("Process Edit Conference Info Failed" , &$err_message );
			$err_message .= $result;
		}
	}
	else
	{
		do_html_header("Edit Conference Information");	
	}	
	
?>