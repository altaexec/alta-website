<?php

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$arrReviewers =& $_SESSION["arrReviewers"];
	$paperID =& $HTTP_POST_VARS["paperID"];
	
	if($HTTP_POST_VARS["Submit"] == "Back"){
		//The previous page was edit assign reviewers
		if($HTTP_POST_VARS["edit"] == "true"){
			$str = "Location: edit_assign_reviewers.php?paperID=".$paperID ;
			header($str);
			exit;
		}
		else{//The previous page was display assign papers
			
			$str = "Location: display_assign_paper.php?back=true&paperID=".$paperID;
			header($str);
			exit;
		}
	}
	
	//Check whether previous page is edit or normal display
	if($HTTP_POST_VARS["edit"] == "true"){//Previous page was edit
	
		//Get Reviewer of the papers
		$arrEditReviewers = get_Reviewers_Of_Paper($paperID);	

		//Call the function to edit the reviewers
		$result = edit_Assigned_Reviewers($paperID,$arrReviewers);
	
	}
	else{//Previous page was not edit

		//Call the function to insert the information to database
		$result = assign_paper($paperID,$arrReviewers);
	}
	
	if($result === true){
		//Check whether previous page is edit or normal display
		
		if($HTTP_POST_VARS["edit"] == "true"){
			do_html_header("Successful Assignment");
			echo "<br><br>The following paper was successfully edited.<br><br>";
			
			//Get the paper information
			$paperInfo = get_paper_info($paperID);
			echo "Paper #".$paperInfo -> PaperID." - ".stripslashes($paperInfo -> Title)."<br><br>";
						
			if (is_array($arrEditReviewers))
			{
				foreach($arrEditReviewers as $reviewerName)
				echo ++$i.". <strong>".$reviewerName."</strong><br>";
			}
			echo "<br><br>Change to:<br>";
			
			if (is_array($arrReviewers))
			{
			foreach($arrReviewers as $reviewerName)
				echo ++$j.". <strong>".$reviewerName."</strong><br>";
			}
			echo "<br>";
					
		}else{
			do_html_header("Successful Assignment");
			echo "<p>The following paper was successfully assigned.<br><br>";
		
				//Get the paper information
				$paperInfo = get_paper_info($paperID);	
				echo "Paper #".$paperID." - ".stripslashes($paperInfo -> Title)."<br><br>";
			
				//Display the assigned reviewer
				foreach($arrReviewers as $reviewerName)
					echo ++$j.". <strong>".$reviewerName."</strong><br>";
			
				echo "<br>";	
		}//end of else
		
		$sort = $_SESSION["sort"] ; // retrieve current sort and showing setings
		$showing = $_SESSION["showing"] ;		
		echo "Go back to <a href='view_all_papers.php?sort=".$sort."&showing=".$showing."'>View All Papers</a> page.<br><br>";
		
		//Unregister the session
		unset ( $_SESSION["sort"] ) ;
		unset ( $_SESSION["showing"] ) ;
		unset($_SESSION["arrReviewers"]);
 		do_html_footer();
	}
	else{
		do_html_header("Error in Form");
		echo $result;
		$sort = $_SESSION["sort"] ; // retrieve current sort and showing setings
		$showing = $_SESSION["showing"] ;		
		echo "<br><br>Go back to <a href='view_all_papers.php?sort=".$sort."&showing=".$showing."'>View All Papers</a> page.<br><br>";		
 		do_html_footer();	
	}

?>
