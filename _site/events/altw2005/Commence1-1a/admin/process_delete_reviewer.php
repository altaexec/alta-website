<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$registerID = $HTTP_POST_VARS["registerID"];
	
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		header("Location: view_all_reviewers.php");
		exit;
	}
	//Establish connection with database
	$db = adodb_connect();

	if(delete_registration($registerID, &$err_message)){
		header("Location: view_all_reviewers.php");
		exit;
	}
	else{
		do_html_header("Problem");
		echo "<font color='#FF0000'> Could not delete the reviewer information - please try again later <br> <br>";
		echo "$err_message </font>";
		do_html_footer();
	}
		
?>
