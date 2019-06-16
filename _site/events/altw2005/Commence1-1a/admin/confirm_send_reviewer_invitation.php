<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Check whether the user needs to reset the from
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		unset($_SESSION["arrUpdateEmails"]);
		unset($_SESSION["arrContent"]);
		unset($_SESSION["arrLetterInfo"]);
		unset($_SESSION["arrCurrentRecords"]);			
		header("Location: view_letters.php");
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
	
	$_SESSION["arrLetterInfo"] = $HTTP_POST_VARS;	
	
	//Fetch letter information
	$letterInfo = get_Letter_Info($HTTP_POST_VARS["letterID"]);	
	
	//Get the session array
	if(isset($_SESSION["arrUpdateEmails"])){
		$arrUpdateEmails = $_SESSION["arrUpdateEmails"];
	}
	else {
		$arrUpdateEmails = get_Unsended_EmailList($letterInfo -> LetterID,$letterInfo -> RecipientGroupName);
		if(count($arrUpdateEmails) > 0)
			$_SESSION["arrUpdateEmails"] = $arrUpdateEmails;	
		
	}		
	
	//The user has not selected any email address to send
	if(count($arrUpdateEmails) == 0){
		do_html_header("Reviewer Invitation Letter");	
		echo "<p>No emails has been selected to send the letter. <br>Specify at least one email address.<br><br>";
		echo "<form name=\"form1\" method=\"post\" action=\"process_send_reviewer_invitation.php\">";
		echo 	"<input name=\"Submit\" type=\"submit\" id=\"Submit\" value=\"Back\"> ";
		echo "</form>";
		do_html_footer();
		exit;
	
	}
	
	//Make the parameter list for popup window
	$parameterList = "letterID=". $letterInfo -> LetterID."&recipientGroupName=".$letterInfo -> RecipientGroupName."&edit=false";
	$strPopupURL = "<a href=\"#\" onClick=\"".make_Popup_Window("view_emails_list.php",$parameterList,600,700,"yes")."\">preview formatted letters</a>";	
	
	//Retrieve the setting information and conference info
	$settingInfo = get_Conference_Settings();
	$conferenceInfo = get_conference_info();			
	
	//Format the subject of the letter
	$strSubject = format_Letter_Subject(stripslashes($HTTP_POST_VARS["subject"]));

	//Format the content string and Store the content into the session
	$strContent = stripslashes(wordwrap($HTTP_POST_VARS["bodycontent"]))."\n\n";	
	$strContent .= $settingInfo -> EmailSignature."\n\n";
	
	//Get the constant of the letter and highlight the constants
	$arrConstants = evaluate_Letter_Constants($HTTP_POST_VARS["lettertype"]);	
	
	//Loop the array and generate the dynamic contents
	if(count($arrUpdateEmails) > 0){
		while(list($username,$email) = each($arrUpdateEmails)){
			
			$strFullName = getMemberFullName($username);
			$arrReplaceInfo = array(
							  "fullname" => $strFullName,
							  "url" => $settingInfo -> HomePage,
							  "confname" => $conferenceInfo -> ConferenceName,
							  "confcode" => $conferenceInfo -> ConferenceCodeName,
							  "contact" => $conferenceInfo -> ConferenceContact);
							  		
			$tmpContent = replace_Dynamic_Values($arrConstants,$arrReplaceInfo,$strContent);
		
			//Put in the return formatted content into array
			$arrContent[$username] = $tmpContent;
		
		}
	}
	
	//Register array of contents to session
	$_SESSION["arrContent"] = $arrContent;
	
	//Store all the post variables into session
	$arrPostVars = $HTTP_POST_VARS;	
	$arrPostVars["subject"] = $strSubject;
	$_SESSION["arrLetterInfo"] = $arrPostVars;	
	
	//Highlight the dynamic contents
	$strContent = highlight_Dynamic_Values($arrConstants,$strContent);
			
	
	do_html_header("Reviewer Invitation Letter");

?>
<form name="form1" method="post" action="process_send_reviewer_invitation.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2">This letter will be sent out to the recipients you have 
        choosen. Click &quot;view email list&quot; to view the recipient addresses. 
        Make sure you have selected all the desire recipients' addresses. Click 
        Confirm to proceed. </td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="20%"><strong>Recipient Group:</strong></td>
      <td width="80%"><?php echo get_RecipientGroupName($letterInfo -> RecipientGroupID); ?></td>
    </tr>
    <tr> 
      <td><strong>Recipient Emails:</strong></td>
      <td><?php echo $strPopupURL; ?></td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong></td>
      <td><?php echo $strSubject; ?></td>
    </tr>
    <?php if ($HTTP_POST_VARS["cc"] != ""){ ?>
    <tr> 
      <td><strong>Cc:</strong></td>
      <td><?php echo $HTTP_POST_VARS["cc"]; ?></td>
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
      <td colspan="2"><?php echo nl2br($strContent); ?></td>
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
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="Submit" type="submit" id="Submit" value="Confirm"> 
        <input name="Submit" type="submit" id="Submit" value="Back"> </td>
    </tr>
  </table>
</form>
<?php 
	
	do_html_footer();

?>
