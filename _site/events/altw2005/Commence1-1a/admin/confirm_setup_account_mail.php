<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;

	require_once("includes/include_all_fns.php");	
	session_start() ;
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		unset($_SESSION["arrAccountInfo"]);
		unset($_SESSION["content"]);
		header("Location: admin_home.php");
		exit;
	}
	
	//Store the post variables into array
	$arrAccountInfo = & $HTTP_POST_VARS;
	$_SESSION["arrAccountInfo"] = & $HTTP_POST_VARS;
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		do_html_header("Database Conntection Fail");
		$err_message .= "Could not connect to database server - please try later.";		
		do_html_footer( &$err_message );
		exit;		
	}
	
	//Make the title according to letter type
	switch($HTTP_POST_VARS["lettertype"]){
		case "revieweraccount":
			$letterTitle = "Reviewer Account Info";			
			break;
		case "adminaccount":
			$letterTitle = "Admin Account Info";			
			break;
	}
	
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	$conferenceInfo = get_conference_info();			
	
	//Format the subject of the letter
	$strSubject = format_Letter_Subject(stripslashes($HTTP_POST_VARS["subject"]));
	
	//Format the content string and Store the content into the session
	$strContent = stripslashes(wordwrap($HTTP_POST_VARS["bodycontent"]))."\n\n";	
	$strContent .= $settingInfo -> EmailSignature."\n\n";
	
	//Get the constant of the letter and highlight the constants
	$arrConstants = evaluate_Letter_Constants($HTTP_POST_VARS["lettertype"]);

	$arrReplaceInfo = array(
					"fullname" => $HTTP_POST_VARS["fullname"],
					"username" => $HTTP_POST_VARS["loginname"],
					"url" => $settingInfo -> HomePage,
					"confname" => $conferenceInfo -> ConferenceName,
					"confcode" => $conferenceInfo -> ConferenceCodeName,
					"contact" => $conferenceInfo -> ConferenceContact);
					
	//Generate the password for that users
	$password = generate_password();
	
	//Add the password into arrReplaceInfo to replace the password
	$arrReplaceInfo["password"] = $password;
	$strContent = replace_Dynamic_Values($arrConstants,$arrReplaceInfo,$strContent);

	//Put in the return formatted content into array
	$arrContent["subject"] = $strSubject;
	$arrContent["content"] = $strContent;
	$arrContent["password"] = $password;
	
	//Store the content into the session
	$_SESSION["content"] = $arrContent;	
		

	do_html_header($letterTitle);
	
?>
		
<!--Display the information to confirm-->
<form name="form1" method="post" action="process_setup_account_mail.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2">Below is the account informatin. An email will be sent out 
        to inform the user immediately. Click Send to proceed or click Back to 
        go back previous page.</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>Login Information</strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="25%"><strong>Login Name:</strong></td>
      <td width="75%"><strong><?php echo $arrAccountInfo["loginname"]; ?></strong></td>
    </tr>
    <tr>
      <td><strong>Full Name:</strong></td>
      <td><?php echo $arrAccountInfo["firstname"]." ".$arrAccountInfo["middlename"]." ".$arrAccountInfo["lastname"];?></td>
    </tr>
    <tr> 
      <td><strong>Email Address:</strong></td>
      <td><strong><?php echo $arrAccountInfo["email"]; ?></strong></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>To:</strong></td>
      <td><?php echo $arrAccountInfo["email"]; ?></td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong></td>
      <td><?php echo $strSubject; ?></td>
    </tr>
    <?php if ($arrAccountInfo["cc"] != ""){ ?>
    <tr> 
      <td><strong>Cc:</strong></td>
      <td><?php echo $arrAccountInfo["cc"]; ?></td>
    </tr>
    <?php } ?>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr align="center"> 
      <td colspan="2"><strong>----- Letter Starts Here -----</strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"> <p><?php echo nl2br($strContent); ?></p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr align="center"> 
      <td colspan="2"><strong>----- Letter Ends Here -----</strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit" value="Send"> <input type="submit" name="Submit" value="Back"></td>
    </tr>
  </table>
</form>	
	
<?php do_html_footer();

?>
