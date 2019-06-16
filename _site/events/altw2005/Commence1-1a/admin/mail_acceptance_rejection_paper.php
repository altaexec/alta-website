<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	switch($HTTP_GET_VARS["lettertype"]){
		case "paperacceptance":
			$strTitle = "Edit Paper Acceptance Letter";
			$letterTitle = "Paper Acceptance";			
			break;
		case "paperrejection":
			$strTitle = "Edit Paper Rejection Letter";
			$letterTitle = "Paper Rejection";						
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
      <td colspan="2"><strong>$confname</strong> - Conference Name (Eg: Workshop 
        on Digital Image Computing)<br>
        <strong>$confcode</strong> - Conference Code Name (e.g., WDIC2003)<br> <strong>Note:</strong> These two constants 
        are available at both Subject and Body field.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="15%"><strong>Subject:</strong></td>
      <td width="85%"><input name="subject" type="text" id="subject2" size="50" maxlength="250" value="<?php echo isset($arrLetterInfo["subject"]) ?  stripslashes($arrLetterInfo["subject"]) : $letterInfo -> Subject; ?>"></td>
    </tr>
    <tr> 
      <td colspan="2"> <input type="hidden" name="letterID" value="<?php echo $letterInfo -> LetterID; ?>"> 
        <input type="hidden" name="lettertype" value="<?php echo $HTTP_GET_VARS["lettertype"]; ?>"> 
        <input type="hidden" name="title" value="<?php echo $strTitle; ?>"></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p>Use the constants values mentioned below whenever you 
          wish to include Dynamic information. The constants will be substituted 
          with dynamic values when the letter is emailed. These constants are 
          case-sensitive. </p>
		  <p>
		  <strong>$fullname</strong> - User Fullname (e.g., John Smith)<strong><br />
          $paperID</strong> - Paper Number (PaperID) of paper (e.g., 1)<br />
          <strong>$papertitle</strong> - Title of paper<br />
          <strong>$authors</strong> - Authors of paper (e.g., Bill Gates, Michel 
          Dell, Steve Jobs)<br />
          <strong>$papercat</strong> - Categories of paper (e.g., Medical applications, 
          Computer vision)<br />
		  <strong>$presType</strong> - Presentation type of the paper (e.g. Poster, Oral)<br />
           <strong>$url</strong> - Web site URL (address) to login (e.g., http://www.conference.com/Commence
          )<br />
          <strong>$contact</strong> - Email of secretariat
		  </p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>Content:</strong></p>
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
