<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;

	if ( !(session_is_registered_register_global_off ( "s_category_post" )) )
	{
		//Register the session
		session_register_register_global_off ("s_category_post");							
	}
	
	$s_category_post["numcat"] = $numcat;

	if($Submit == "Update Number"){
		//Updating the number of categories
		header("Location: setup_category.php");
	}
	else{
		
		//Insert the category to the table
		$result = insert_category($arrCategoryName);
		
		do_html_header("Updating Information Successful");
		echo $result;
		do_html_footer();
	}


?>