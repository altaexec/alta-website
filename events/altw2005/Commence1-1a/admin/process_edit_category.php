<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;

	
	if($HTTP_POST_VARS["Submit"] == "Undo Changes"){
		$str = "Location: edit_category.php?catID=".$HTTP_POST_VARS["catID"];
		header($str);
		exit;
	}

	if(update_Category($HTTP_POST_VARS["catID"],$HTTP_POST_VARS["catName"])){
		header("Location: view_categories.php");
		exit;
	}
	else{
		do_html_header("Problem");
		echo "Could not update the category information - please try again later";
		do_html_footer();
	}
		
?>