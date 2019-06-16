<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	switch($HTTP_GET_VARS["lettertype"]){
		case "useraccount":
			$strTitle = "Edit User Account Information Formletter";
			$letterTitle = "User Account Info";
			break;
		case "revieweraccount":
			$strTitle = "Edit Reviewer Account Information Formletter";
			$letterTitle = "Reviewer Account Info";			
			break;
		case "adminaccount":
			$strTitle = "Edit Admin Account Information Formletter";
			$letterTitle = "Admin Account Info";			
			break;
	}
	
	//Fetch letter information
	$letterInfo = get_LetterInfo_By_Title($letterTitle);	
	
	//Read the session variable if there is
	if(isset($_SESSION["arrLetterInfo"]))
		$arrLetterInfo = & $_SESSION["arrLetterInfo"];
		
	do_html_header($strTitle);
	
?>
<form name="form1" method="post" action="preview_letter.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td><strong>$confname</strong> - Conference Name (Eg: Workshop on Digital 
        Image Computing)<br>
        <strong>$confcode</strong> - Conference Code Name (Eg: WDIC2003)<br> <strong>Note:</strong> These two constants are available 
        at both Subject and Body field.</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong>&nbsp;&nbsp; <input name="subject" type="text" id="subject" size="50" maxlength="250" value="<?php echo isset($arrLetterInfo["subject"]) ?  stripslashes($arrLetterInfo["subject"]) : $letterInfo -> Subject; ?>"></td>
    </tr>
    <tr> 
      <td> <input type="hidden" name="letterID" value="<?php echo $letterInfo -> LetterID; ?>"> 
        <input type="hidden" name="lettertype" value="<?php echo $HTTP_GET_VARS["lettertype"]; ?>"> 
        <input type="hidden" name="title" value="<?php echo $strTitle; ?>"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><p>Use the constants values mentioned below whenever you wish to include 
          Dynamic information. The constants will be substituted with dynamic 
          values when the letter is sent out. These constants are case-sensitive. 
        </p>
        <p><strong>$fullname</strong> - User Fullname (Eg: John Smith)<br>
          <strong>$username</strong> - User login name (Eg: johnny)<br>
          <strong>$password</strong> - Login password <br>
          <strong>$url</strong> - Web site URL (address) to login (Eg: http://www.webcomments.com 
          )<br>
          <strong>$contact</strong> - Email of secretariat </p></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><p><strong>Content:</strong></p>
        <p> 
          <textarea name="bodycontent" cols="80" rows="25" id="bodycontent"><?php echo isset($arrLetterInfo["bodycontent"]) ? stripslashes($arrLetterInfo["bodycontent"]) : stripslashes($letterInfo ->  BodyContent); ?></textarea>
        </p></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><input type="submit" name="Submit" value="Submit"> <input name="Submit" type="submit" id="Submit" value="Cancel"></td>
    </tr>
  </table>
</form>
<?php do_html_footer(); ?>
