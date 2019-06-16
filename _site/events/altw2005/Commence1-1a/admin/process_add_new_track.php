<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;		
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$catName = $HTTP_POST_VARS["catName"];
	
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		header("Location: view_tracks.php");
		exit;
	}

	if(add_new_track($catName)){
		header("Location: view_tracks.php");
		exit;
	}
	else{
		do_html_header("Problem");
		echo "Could not insert the track information - please try again later";
		do_html_footer();
	}
		
?>
