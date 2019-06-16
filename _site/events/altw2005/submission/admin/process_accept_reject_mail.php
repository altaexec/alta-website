<?php
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	if($HTTP_POST_VARS["Submit"] == "Back"){
		//Redirect back to the prvious page
		unset($_SESSION["content"]);	
		$url = "Location: compose_accept_reject_mail.php?paperID=".$HTTP_POST_VARS["paperID"];
		$url .= "&status=".$HTTP_POST_VARS["status"];
		header($url);
		exit;
	}
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		do_html_header("Database Conntection Fail");
		$err_message .= "Could not connect to database server - please try later.";		
		do_html_footer( &$err_message );
		exit;		
	}
	
	//Read the session variables
	$arrPostInfo =& $_SESSION["arrPostInfo"];
	$arrAttachmentInfo = & $_SESSION["arrAttachmentInfo"];	
	$arrContent = & $_SESSION["content"];
	
	//get the conference info to get the contact mail
	$conferenceInfo = get_conference_info();
	//Get the paper information
	$paperInfo = get_paper_info($HTTP_POST_VARS["paperID"]);				
	
	if(update_PaperStatus($arrPostInfo["paperID"],$arrPostInfo["status"])){	
	
		//Send Email to user
		$mail = new Mail();
			
		$mail -> Organization($conferenceInfo -> ConferenceCodeName);
		$mail -> ReplyTo($conferenceInfo -> ConferenceContact);
			
		$mail -> From($conferenceInfo -> ConferenceContact);
		$mail -> To($arrPostInfo["email"]);	
		$mail -> Subject($arrContent["subject"]);
		$mail -> Body($arrContent["content"]);
			
		if ($arrPostInfo["cc"] != "")	
			$mail -> Cc($arrPostInfo["cc"]);
			
		if(!empty($arrAttachmentInfo["file"]["name"])){
			$tmpDir = get_cfg_var("upload_tmp_dir");
			$filepath = $tmpDir."/".$arrAttachmentInfo["file"]["name"];
			$mail -> Attach($filepath,$arrAttachmentInfo["file"]["name"],$arrAttachmentInfo["file"]["type"]);
		}		
			
		$mail -> Priority(1);		
		$mail -> Send();
		
		//Call the function to log the information
		$result = updateMailLog($paperInfo -> PaperID,$arrPostInfo["letterID"]);
		
		if($result === true){
			do_html_header("Successful Update");		
			echo "<p>The following paper has been ".$HTTP_POST_VARS["status"].". An email has been sent to inform the user.<br><br>";
			echo "<strong>PaperID#".$HTTP_POST_VARS["paperID"]."</strong><br>";
			echo "<strong>PaperID:</strong> ".stripslashes($paperInfo -> Title)."<br>";
			echo "<strong>Status:</strong> ".$HTTP_POST_VARS["status"]."<br><br>";
			echo "Go back to <a href=\"view_all_papers.php\">view all papers</a>.</p>";
			do_html_footer();		
		}else {
			do_html_header("Error Information");
			echo "<p>$result</p>";
			do_html_footer();
			exit;		
		
		}

	}
	else{
		do_html_header("Error Information");
		echo "<p>Could not update the paper information - please try again</p>";
		do_html_footer();
		exit;
	
	}
	
	//Unregister the session
	unset($_SESSION["arrPostInfo"]);
	unset($_SESSION["arrAttachmentInfo"]);
		
?>
