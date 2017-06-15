<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;

	require_once("includes/include_all_fns.php");	
	session_start() ;
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	//Get the session variable
	$arrLetterInfo = & $_SESSION["arrLetterInfo"];
	$arrPassword = & $_SESSION["arrPassword"];
	
	if($HTTP_POST_VARS["Submit"] == "Back"){
		unset($_SESSION["arrContent"]);
		$url = "Location: send_account_info.php?lettertype=".$arrLetterInfo["lettertype"];	
		header($url);
		exit;
	}
	
	//get the conference info to get the contact mail
	$conferenceInfo = get_conference_info();
	
	//Get the letter info and its constant
	$letterInfo = get_Letter_Info($arrLetterInfo["letterID"]);
	$arrConstants = evaluate_Letter_Constants($arrLetterInfo["lettertype"]);
	
	
	//Check if there is session email list to retrieve out
	if(isset($_SESSION["arrUpdateEmails"]))
		$arrEmails = $_SESSION["arrUpdateEmails"];
	else
		$arrEmails = get_Unsended_EmailList($letterInfo -> LetterID,$letterInfo -> RecipientGroupName);
		
	$arrContent = $_SESSION["arrContent"];
			
	//Call the function to setup reviwer account
	while(list($memberName,$email) = each($arrEmails)){	
	
		//Update the mail log	
		$result = updateMailLog($memberName,$letterInfo -> LetterID);
		
		if($result === true) {//Successful Mail Log update
		
			//Now update the user password
			$result = updateUserPassword($memberName,$arrPassword[$memberName]);	
			
			//If can log the email
			if($result === true){//Successful Password Update		
				
				//Send Email to user
				$mail = new Mail();
					
				$mail -> Organization($conferenceInfo -> ConferenceCodeName);
				$mail -> ReplyTo($conferenceInfo -> ConferenceContact);
					
				$mail -> From($conferenceInfo -> ConferenceContact);
				$mail -> To($email);	
				$mail -> Subject(stripslashes($arrLetterInfo["subject"]));
				$mail -> Body($arrContent[$memberName]);
					
				if ($arrLetterInfo["cc"] != "")	
					$mail -> Cc($arrLetterInfo["cc"]);	
					
				$mail -> Priority(1);		
				$mail -> Send();

				//Log the successful send email
				$arrSuccessfulEmails[$memberName] = $email;				
			}
			else {
				
				//Password Update fail, delete the mail log
				$result = deleteMailLog($memberName,$letterID);
				
				if($result !== true){
					do_html_header("Error Information");	
					echo "<p>$result</p>";
					do_html_footer();
					exit;
				}
				
				//Log the unsuccessful email
				$arrFaliureEmails[$memberName] = $email;
							
			}
			
		}
		
	}//end of while loop
	

	do_html_header("Successful Send");			
	echo "Letter Title: <strong>".$letterInfo -> Title."</strong><br><br>";
	echo "<br>Go back to <a href=\"view_letters.php\">View Letters</a> page.</p>";	
	//Display the successful send emails list
	echo display_Letter_Recipients("The email was successfully sent to the following recipients.",$arrSuccessfulEmails);
	//Display unsucessful email list
	echo display_Letter_Recipients("The letter is <strong>NOT</strong> sent to following recipients.",$arrFaliureEmails);	
	do_html_footer();


	//Unset the session
	unset($_SESSION["arrLetterInfo"]);
	unset($_SESSION["arrUpdateEmails"]);
	unset($_SESSION["arrContent"]);
	unset($_SESSION["arrPassword"]);
	unset($_SESSION["arrCurrentRecords"]);		

?>
