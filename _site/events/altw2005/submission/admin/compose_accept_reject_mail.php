<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		do_html_header("Database Conntection Fail");
		$err_message .= "Could not connect to database server - please try later.";		
		do_html_footer( &$err_message );
		exit;		
	}
	
	//Get the passing variabels
	$paperID = & $HTTP_GET_VARS["paperID"];
	$status = & $HTTP_GET_VARS["status"];
	
	//Check and get the session variable
	if(isset($_SESSION["arrPostInfo"]))
		$arrPostInfo = & $_SESSION["arrPostInfo"];		
		
	//Get the paper information
	$paperInfo = get_paper_info($paperID);	
	
	//Arrange the appropriate letter type
	switch($HTTP_GET_VARS["status"]){
		case "Accepted":
			$letterTitle = "Paper Acceptance";			
			break;
		case "Rejected":
			$letterTitle = "Paper Rejection";						
			break;
	}		
	
	//Fetch letter information
	$letterInfo = get_LetterInfo_By_Title($letterTitle);
	
	//Get the user email to include at Cc field
	$useremail = getMemberEmail($valid_user);
	
	do_html_header($letterTitle);

?>
<form enctype="multipart/form-data" action="confirm_accept_reject_mail.php" method="post" name="form1">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2"><?php echo stripslashes("<h3>".$paperInfo -> Title."</h3>"); ?></td>
    </tr>
    <input type="hidden" name="letterID" value="<?php echo $letterInfo -> LetterID; ?>">
    <input type="hidden" name="paperID" value="<?php echo $paperInfo -> PaperID; ?>">
    <tr> 
      <td colspan="2"><strong>Paper #<?php echo $paperInfo->PaperID; ?></strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="15%"><strong>Authors:</strong> </td>
      <td width="85%"><?php echo retrieve_authors($paperInfo -> PaperID);?></td>
    </tr>
    <tr> 
      <td><strong>Keywords:</strong> </td>
      <td><?php echo  getSelectedCategoryCommaSeparated($paperInfo -> PaperID , &$err_message );?></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <input type="hidden" name="status" value="<?php echo $status; ?>">
    <tr> 
      <td><strong>Status:</strong></td>
      <td><?php echo $status; ?></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>User Name:</strong></td>
      <td><?php echo $paperInfo -> MemberName; ?></td>
    </tr>
    <tr> 
      <td><strong>User Fullname:</strong></td>
      <td><?php echo getMemberFullName($paperInfo -> MemberName); ?></td>
    </tr>
    <tr> 
      <td><strong>User Email:</strong></td>
      <td><?php echo getMemberEmail($paperInfo -> MemberName); ?></td>
      <input type="hidden" name="email" value="<?php echo getMemberEmail($paperInfo -> MemberName); ?>">
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">Use the constants values mentioned below whenever you wish 
        to include Dynamic information. The constants will be substituted with 
        dynamic values when the letter is sent out. These constants are case-sensitive.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>$confname</strong> - Conference Name (Eg: Workshop 
        on Digital Image Computing)<br> <strong>$confcode</strong> - Conference 
        Code Name (Eg: WDIC2003)<br> <strong>Note:</strong> These two constants 
        are available at both Subject and Body field.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong>&nbsp;&nbsp; </td>
      <td><input name="subject" type="text" id="subject" size="50" maxlength="250" value="<?php echo !empty($arrLetterInfo["subject"]) ?  stripslashes($arrLetterInfo["subject"]) : $letterInfo -> Subject; ?>"></td>
      <input type="hidden" name="lettertype" value="<?php echo $HTTP_GET_VARS["lettertype"]; ?>">
    </tr>
    <tr> 
      <td><strong>Cc:</strong></td>
      <td><input name="cc" type="text" id="cc" size="30" maxlength="30" value="<?php if(!empty($arrLetterInfo["cc"])) echo $arrLetterInfo["cc"]; else echo $useremail;?>"></td>
    </tr>
    <tr> 
      <td><strong>Attach Reviews:</strong></td>
      <td><input name="file" type="file" size="30"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>$fullname</strong> - User Fullname (Eg: John Smith)<strong><br>
        $papernumber</strong> - PaperID of paper (Eg: PaperID#1)<br> <strong>$papertitle</strong> 
        - Title of paper<br> <strong>$authors</strong> - Authors of paper (Eg: 
        Bill Gates, Michel Dell, Steve Jobs)<br> <strong>$papercat</strong> - 
        Categories of paper (Eg: Medical applications, Computer vision)<br> <strong>$url</strong> 
        - Web site URL (address) to login (Eg: http://www.webcomments.com )<br> 
        <strong>$contact</strong> - Email of secretariat </td>
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
      <td colspan="2"><input name="Submit" type="submit" id="Submit" value="Send"> 
        <input name="Submit" type="submit" id="Submit" value="Back"> <input name="Submit" type="submit" id="Submit" value="Cancel"> 
      </td>
    </tr>
  </table>
</form>
<?php 
	
	do_html_footer();

?>
