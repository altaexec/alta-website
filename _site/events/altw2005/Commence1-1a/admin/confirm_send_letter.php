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
	
	//Store all the post variables into session
	$_SESSION["arrLetterInfo"] = $HTTP_POST_VARS;
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		do_html_header("Database Conntection Fail");
		$err_message .= "Could not connect to database server - please try later.";		
		do_html_footer( &$err_message );
		exit;		
	}		
	
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	
	echo "URL: ".$settingInfo -> HomePage;
	
	//Fetch letter information
	$letterInfo = get_Letter_Info($HTTP_POST_VARS["letterID"]);			
	
	//Format the content string and Store the content into the session
	$strContent = stripslashes($HTTP_POST_VARS["bodycontent"])."\n\n";
	$strContent .= $settingInfo -> EmailSignature."\n\n";
	//$_SESSION["content"] = $strContent;
	
	//Get the constant of the letter and highlight the constants
	$arrConstants = evaluate_Letter_Constants($HTTP_POST_VARS["lettertype"]);	
	
	//Get the session array
	if(isset($_SESSION["arrUpdateEmails"]))
		$arrUpdateEmails = $_SESSION["arrUpdateEmails"];
	else {
		$arrUpdateEmails = get_Unsended_EmailList($letterInfo -> LetterID,$letterInfo -> RecipientGroupName);
		$_SESSION["arrUpdateEmails"] = $arrUpdateEmails;	
		
	}			
	
	//Loop the array and generate the dynamic contents
	while(list($username,$email) = each($arrUpdateEmails)){
		
		$strFullName = getMemberFullName($username);
		$arrReplaceInfo = array("fullname" => $strFullName,"url" => $settingInfo -> HomePage);		
		$tmpContent = replace_Dynamic_Values($arrConstants,$arrReplaceInfo,$strContent);
	
		//Put in the return formatted content into array
		$arrContent[$username] = $tmpContent;
	
	}
	
	//Register array of contents to session
	$_SESSION["arrContent"] = $arrContent;
	
	//Highlight the dynamic contents
	$strContent = highlight_Dynamic_Values($arrConstants,$strContent);
			
	
	do_html_header("Reviewer Invitation Letter");

?>
<form name="form1" method="post" action="process_send_letter.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2">This letter will be sent out to the recipients you have 
        choosen. Click &quot;view email list&quot; to view the recipient addresses. 
        Make sure you have selected all the desire recipients' addresses. Click 
        Confrim to proceed. </td>
    </tr>
    <tr> 
      <td colspan="2"><?php echo "<pre>";
			  print_r($arrUpdateEmails);
			  echo "</pre>";  ?></td>
    </tr>
    <tr> 
      <td width="20%"><strong>Recipient Group:</strong></td>
      <td width="80%"><?php echo get_RecipientGroupName($letterInfo -> RecipientGroupID); ?></td>
    </tr>
    <tr> 
      <td><strong>Recipient Emails:</strong></td>
      <td><a href="#" onClick="JavaScript: window.open('mailing_list.php?letterID=<?php echo $letterInfo -> LetterID; ?>&recipientGroupName=<?php echo $letterInfo -> RecipientGroupName; ?>',null,'height=600,width=700,status=yes,toolbar=no,menubar=no,scrollbars=yes,location=no');">edit 
        email list</a>&nbsp;|&nbsp; <a href="#" onClick="JavaScript: window.open('view_emails_list.php?letterID=<?php echo $letterInfo -> LetterID; ?>&recipientGroupName=<?php echo $letterInfo -> RecipientGroupName; ?>',null,'height=600,width=700,status=yes,toolbar=no,menubar=no,scrollbars=yes,location=no');">view 
        email list</a></td>
    </tr>
    <tr> 
      <td> <?php echo "<pre>";
	echo print_r($arrConstants);
	echo "</pre>"; ?></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong></td>
      <td><?php echo stripslashes($HTTP_POST_VARS["subject"]); ?></td>
    </tr>
    <?php if ($HTTP_POST_VARS["cc"] != ""){ ?>
    <tr> 
      <td><strong>Cc:</strong></td>
      <td><?php echo $HTTP_POST_VARS["cc"]; ?></td>
    </tr>
    <?php } ?>
    <tr> 
      <td><?php echo $HTTP_POST_VARS["lettertype"]; ?></td>
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
      <td colspan="2"><?php echo "<pre>";
			  print_r($arrContent);
			  echo "</pre>";?></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="Submit" type="submit" id="Submit" value="Confrim"> 
        <input name="Submit" type="submit" id="Submit" value="Back"> </td>
    </tr>
  </table>
</form>
<?php 
	
	do_html_footer();

?>
