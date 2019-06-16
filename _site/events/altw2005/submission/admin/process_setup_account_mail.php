<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;

	require_once("includes/include_all_fns.php");	
	session_start() ;
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	if($HTTP_POST_VARS["Submit"] == "Back"){
		unset($_SESSION["content"]);	
		header("Location: compose_setup_account_mail.php");
		exit;
	}
	
	//Get the session variable
	if(isset($_SESSION["arrAccountInfo"]))
		$arrAccountInfo = & $_SESSION["arrAccountInfo"];
	if(isset($_SESSION["content"]))
		$arrContent = $_SESSION["content"];
		
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		do_html_header("Database Conntection Fail");
		$err_message .= "Could not connect to database server - please try later.";		
		do_html_footer( &$err_message );
		exit;		
	}
	
	//get the conference info to get the contact mail
	$conferenceInfo = get_conference_info();
	
	//Call the function to setup reviwer account
	if(setup_new_account($arrAccountInfo,$arrContent["password"])){
	
		//Send Email to user
		$mail = new Mail();
			
		$mail -> Organization($conferenceInfo -> ConferenceCodeName);
		$mail -> ReplyTo($conferenceInfo -> ConferenceContact);
			
		$mail -> From($conferenceInfo -> ConferenceContact);
		$mail -> To($arrAccountInfo["email"]);	
		$mail -> Subject($arrContent["subject"]);
		$mail -> Body($arrContent["content"]);
			
		if ($arrAccountInfo["cc"] != "")	
			$mail -> Cc($arrAccountInfo["cc"]);	
			
		$mail -> Priority(1);		
		$mail -> Send();
		
		//Call the function to log the information
		$result = updateMailLog($arrAccountInfo["loginname"],$arrAccountInfo["letterID"]);
		
		if($result === true){
		
			do_html_header("Successful Account Setup");
			echo "<p>The following account has successfully setup.<br><br>";
			echo "Account Type: <strong>".$arrAccountInfo["accountType"]."</strong><br>";			
			echo "Login Name: <strong>".$arrAccountInfo["loginname"]."</strong><br>";
			echo "Email Address: <strong>".$arrAccountInfo["email"]."</strong><br><br>";
			echo "Go back to <a href=\"view_all_users.php\">View User Account</a>.</p>";
			do_html_footer();
		}
		else{
			$return = delete_Newly_Setup_Account($arrAccountInfo["loginname"]);
			do_html_header("Error Information");
			echo "<p>$result</p>";
			do_html_footer();
			exit;		
		}
			
		//Unset the session
		unset($_SESSION["arrLoginInfo"]);
		unset($_SESSION["arrAccountInfo"]);
		unset($_SESSION["content"]);
	
	}			

?>
