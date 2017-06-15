<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	//Fetch letter information
	$letterInfo = get_LetterInfo_By_Title("Reviewer Invitation and Instructions");
	
	//Read the session variable if there is
	if(isset($_SESSION["arrLetterInfo"]))
		$arrLetterInfo = & $_SESSION["arrLetterInfo"];		
		
	//Get the user email to include at Cc field
	$useremail = getMemberEmail($valid_user);
	
	//Make the URL for email list
	$parameterList = "letterID=". $letterInfo -> LetterID."&recipientGroupName=".$letterInfo -> RecipientGroupName;
	$url = "<a href=\"#\" onClick=\"".make_Popup_Window("mailing_list.php",$parameterList,600,800,"yes")."\">edit email list</a>&nbsp;|&nbsp; <a href=\"#\" onClick=\"".make_Popup_Window( "view_emails_list.php",$parameterList,600,800,"yes")."\">view email list</a>";
			
	do_html_header($letterInfo -> Title);	
	
?>
<form name="form1" method="post" action="confirm_send_reviewer_invitation.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="2">Use the constants values mentioned below whenever you wish 
        to include Dynamic information. The constants will be substituted with 
        dynamic values when the letter is sent out. These constants are case-sensitive.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="20%"><strong>Recipient Group:</strong></td>
      <td width="80%"><?php echo $letterInfo -> RecipientGroupName; ?></td>
      <input type="hidden" name="letterID" value="<?php echo $letterInfo -> LetterID; ?>">
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Recipient Emails:</strong></td>
      <td><?php echo $url; ?></td>
    </tr>
    <tr> 
      <td><strong>Cc:</strong></td>
      <td><input name="cc" type="text" id="cc" size="30" maxlength="30" value="<?php if(!empty($arrLetterInfo["cc"])) echo $arrLetterInfo["cc"]; else echo $useremail;?>"></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>$confname</strong> - Conference Name (Eg: Workshop 
          on Digital Image Computing)<br>
          <strong>$confcode</strong> - Conference Code Name (Eg: WDIC2003)<br>
          <strong>Note:</strong> These two constants are available at both Subject 
          and Body field.<br>
        </p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>Subject:</strong>&nbsp;&nbsp; <input name="subject" type="text" id="subject" size="50" maxlength="250" value="<?php echo !empty($arrLetterInfo["subject"]) ?  stripslashes($arrLetterInfo["subject"]) : $letterInfo -> Subject; ?>"></td>
      <input type="hidden" name="lettertype" value="<?php echo $HTTP_GET_VARS["lettertype"]; ?>">
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>$fullname</strong> - Reviewer Fullname (Eg: John 
          Smith)<br>
          <strong>$url</strong> - Web site URL (address) to login (Eg: http://www.webcomments.com 
          )<br>
          <strong>$contact</strong> - Email of secretariat </p></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>Body:</strong></p>
        <p> 
          <textarea name="bodycontent" cols="80" rows="25" id="bodycontent"><?php echo isset($arrLetterInfo["bodycontent"]) ? stripslashes($arrLetterInfo["bodycontent"]) : stripslashes($letterInfo ->  BodyContent); ?></textarea>
        </p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit" value="Submit"> <input name="Submit" type="submit" id="Submit" value="Cancel"></td>
    </tr>
  </table>
</form>
<?php do_html_footer(&$err_message); ?>

