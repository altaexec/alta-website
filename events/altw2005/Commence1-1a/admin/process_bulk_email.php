<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$arrEmailInfo = & $_SESSION["arrEmailInfo"];
	$arrAttachmentInfo = & $_SESSION["arrAttachmentInfo"];
	
	//Call the function to get the conference information
	$conferenceInfo = get_conference_info();	

	if($HTTP_POST_VARS["Submit"] == "Confirm"){
		
		$arrEmails = get_emails($arrEmailInfo["to"]);
		
		//Create the instance  of mail
		$mail = new Mail();
		
		$mail -> Organization($conferenceInfo -> ConferenceCodeName);
		$mail -> ReplyTo($conferenceInfo -> ConferenceContact);
		
		$mail -> From("admin@webcomments.com");
//		$mail -> To($arrEmails);
		$mail -> To("thu_ya@hotmail.com");
		$mail -> Subject(stripslashes(trim($arrEmailInfo["subject"])));
		$mail -> Body(stripslashes(trim($arrEmailInfo["content"])));
		
		if ($arrEmailInfo["cc"] != "")
			$mail -> Cc($arrEmailInfo["cc"]);
		if ($arrEmailInfo["bcc"] != "")	
			$mail -> Bcc($arrEmailInfo["bcc"]);
		if(!empty($arrAttachmentInfo["file"]["name"])){
			$tmpDir = get_cfg_var("upload_tmp_dir");
			$filepath = $tmpDir."/".$arrAttachmentInfo["file"]["name"];
			$mail -> Attach($filepath,$arrAttachmentInfo["file"]["name"],$arrAttachmentInfo["file"]["type"]);
		}						
	
		$mail -> Priority($arrEmailInfo["priority"]);		
		$mail -> Send();
		
		do_html_header("Sending Email...");
		echo "The emails has been sent successfully to following recipients.<br><br>";
			
		for($i = 0;$i < count($arrEmails);$i++){
			echo $arrEmails[$i]."<br>";
		}
		
		//Unregister the session variable
		session_unregister_register_global_off ("arrEmailInfo");
		session_unregister_register_global_off ("arrAttachmentInfo");		
		
		//Delete the attachment file if thereis
		if(!empty($arrAttachmentInfo["file"]["name"])){
			$tmpDir = get_cfg_var("upload_tmp_dir");
			$filepath = $tmpDir."/".$arrAttachmentInfo["file"]["name"];
			unlink($filepath);
		}
		
		do_html_footer();		
	}
	else{//Back button is pressed
		if(!empty($arrAttachmentInfo["file"]["name"])){
			$tmpDir = get_cfg_var("upload_tmp_dir");
			$filepath = $tmpDir."/".$arrAttachmentInfo["file"]["name"];
			unlink($filepath);		
		}		
			
		session_unregister_register_global_off ("arrAttachmentInfo");			

		header("Location: bulk_email.php");
		exit;
	}

?>
