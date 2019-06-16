<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Extract all HTTP variables
	// extract ( $_SESSION , EXTR_REFS ) ;
	extract ( $HTTP_POST_VARS , EXTR_REFS ) ;
	
	// echo "Purge = $Purge<br>";
	if($Purge == "Purge Selected"){
		//Delect the selected papers

		//Check whether the user selects any check boxes
		if(count($arrPaperID) == 0){
			do_html_header("Problem");	
	  		echo "Sorry, you haven't selected any paper to purge.";
			do_html_footer();
			exit;		
		}
		
		//Call the function to delete the paper
		$result = purge_Selected_Paper($arrPaperID);
		// echo "purge selected<br>";
		
		//If the return result is true then redirect back to previous page.
		if($result){
			header("Location: view_withdrawn_papers.php");
		}
		else{
			do_html_header("Problem");
			echo $result;
			do_html_footer();
		}

	}
	else
	{
		
		//Call the function to delete the paper
		$result = purge_Withdrawn_Papers();
		//	echo "purge all<br>";
		
		//If the return result is true then redirect back to previous page.
		if($result == 'true'){
			header("Location: view_withdrawn_papers.php");
		}
		else{
			do_html_header("Problem");
			echo $result;
			do_html_footer();
		}
	}


?>
