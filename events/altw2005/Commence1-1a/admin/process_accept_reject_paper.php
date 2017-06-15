<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Establish database connection
  	$db = adodb_connect();
  
  	if (!$db){
   		echo "Could not connect to database server - please try later.";
		exit;
	}	
	
	//Check whether the user needs to reset the from
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		unset($_SESSION["arrPostInfo"]);	
		header("Location: view_all_papers.php");
		exit;
	}
	
	//Check whether the user needs to reset the from
	if($HTTP_POST_VARS["Submit"] == "Back"){
		unset($_SESSION["arrPostInfo"]);	
		$url = "Location: evaluate_paper_status.php?paperID=".$HTTP_POST_VARS["paperID"];
		$url .= "&status=".$HTTP_POST_VARS["status"];
		header($url);
		exit;
	}
		
	
	//Check whether the user wish to inform user immediately
	if($HTTP_POST_VARS["informuser"] == "yes"){
		$url = "Location: compose_accept_reject_mail.php?paperID=".$HTTP_POST_VARS["paperID"];
		$url .= "&status=".$HTTP_POST_VARS["status"];
		header($url);
		exit;		
	}
	
	//Get the paper information
	$paperInfo = get_paper_info($HTTP_POST_VARS["paperID"]);
	$prevtype = get_presentation_info( get_presentation_type_for_paper($paperInfo -> PaperID) );
	$curtype = get_presentation_info( $HTTP_POST_VARS["type"] );
	
	// Either add or remove presentation from scheduling system
	if ($HTTP_POST_VARS["status"]=="Accepted")
	{
		// Remove any previous record
		remove_paper_presentation($HTTP_POST_VARS["paperID"]);
		// Add to scheduling system as an unscheduled paper
		assign_paper_presentation_type($HTTP_POST_VARS["paperID"], $HTTP_POST_VARS["type"]);
		// If all is well, then it will get autoscheduled
		autoschedule_waiting_papers();
	} else {
		remove_paper_presentation($HTTP_POST_VARS["paperID"]);
	}
	
	//The admin does not wish to inform the user
	//Update the paper status to accepted or rejected
	if( update_PaperStatus($HTTP_POST_VARS["paperID"],$HTTP_POST_VARS["status"]) )
	{
		$title = "Paper is ".$HTTP_POST_VARS["status"];
		do_html_header($title);
	?>
		<p>The following paper is <?php echo $HTTP_POST_VARS["status"] ?><br><br>
		<h3> #<?php echo $paperInfo -> PaperID." ".stripslashes($paperInfo -> Title) ?></h3>
		<table>
		<tr><td><strong>Previous Status:</strong>&nbsp;&nbsp;</td><td><?php echo $paperInfo -> PaperStatusName . ($paperInfo -> PaperStatusName =="Accepted" ? " as ".$prevtype -> PresentationTypeName : "") ?></td></tr>			
		<tr><td><strong>New Status:</strong></td><td><?php echo $HTTP_POST_VARS["status"].($HTTP_POST_VARS["status"]=="Accepted" ? " as ".$curtype -> PresentationTypeName : "") ?></td></tr>
		</table><br><br>
		<br>Go back to <a href='view_all_papers.php?sort=<?php echo $_SESSION["sort"] ?>&showing=<?php echo $_SESSION["showing"] ?>'>View All Papers</a> page.<br><br>
	<?php
		//echo "Go back to <a href=\"view_all_papers.php\">view all papers</a></p>";
		do_html_footer();
	}	
?>
