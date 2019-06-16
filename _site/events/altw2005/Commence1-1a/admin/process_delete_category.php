<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$catID = $HTTP_POST_VARS["catID"];
	
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		header("Location: view_categories.php");
		exit;
	}

	if(delete_Category($catID)){
		header("Location: view_categories.php");
		exit;
	}
	else{
		do_html_header("Problem");
		echo "Could not delete the category information - please try again later";
		do_html_footer();
	}
		
?>