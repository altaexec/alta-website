<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Check whether the user needs to reset the from
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
	
		unset($_SESSION["arrPostInfo"]);	
		header("Location: view_all_papers.php");
		exit;
		
	}else if($HTTP_POST_VARS["Submit"] == "Back"){
		
		$url = "Location: accept_reject_paper.php?paperID=".$HTTP_POST_VARS["paperID"];
		$url .= "&status=".$HTTP_POST_VARS["status"]."&back=true";
		header($url);
		exit;
	}
	
	//Arrange the appropriate letter type
	switch($HTTP_POST_VARS["status"]){
		case "Accepted":
			$letterTitle = "Paper Acceptance";
			$letterType = "paperacceptance";			
			break;
		case "Rejected":
			$letterTitle = "Paper Rejection";
			$letterType = "paperrejection";						
			break;
	}	
	
	//Update the session variables
	$_SESSION["arrPostInfo"] = $HTTP_POST_VARS;
	$_SESSION["arrAttachmentInfo"] = $HTTP_POST_FILES;
	
	//Check whether the uploaded file is valid
	if(!empty($HTTP_POST_FILES["file"]["name"])){	
		if(is_uploaded_file($HTTP_POST_FILES["file"]["tmp_name"])){
			$realname = $HTTP_POST_FILES["file"]["name"];
			$tmpDir = get_cfg_var("upload_tmp_dir");			
			copy($HTTP_POST_FILES["file"]["tmp_name"],"$tmpDir/$realname");
			//rename($HTTP_POST_FILES["file"]["tmp_name"],$HTTP_POST_FILES["file"]["name"]);
		}
		else{
			echo "There is an error in attaching file- try again";
			exit;
		}
	}
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		do_html_header("Database Conntection Fail");
		$err_message .= "Could not connect to database server - please try later.";		
		do_html_footer( &$err_message );
		exit;		
	}
	
	//Get the paper information
	$paperID = $HTTP_POST_VARS["paperID"];
	$paperInfo = get_paper_info($paperID);
	
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	$conferenceInfo = get_conference_info();			
	
	//Format the subject of the letter
	$strSubject = format_Letter_Subject(stripslashes($HTTP_POST_VARS["subject"]));				
	
	//Format the content string and Store the content into the session
	$strContent = stripslashes(wordwrap($HTTP_POST_VARS["bodycontent"]))."\n\n";	
	$strContent .= $settingInfo -> EmailSignature."\n\n";

	//Get the constant of the letter and highlight the constants
	$arrConstants = evaluate_Letter_Constants($letterType);

	//Get the paper info
	$strAuthors = retrieve_authors($paperID, &$err_message);
	$strCat = getSelectedCategoryCommaSeparated($paperID, &$err_message);
	
	//Get the full name of the user according to his user name
	$strFullName = getMemberFullName($paperInfo -> MemberName);
	$arrReplaceInfo = array(
						"fullname" => $strFullName,
						"paperID" => $paperInfo -> PaperID,
						"papertitle" => $paperInfo -> Title,
						"authors" => $strAuthors,
						"papercat" => $strCat,
						"url" => $settingInfo -> HomePage,
						"confname" => $conferenceInfo -> ConferenceName,
						"confcode" => $conferenceInfo -> ConferenceCodeName,
						"contact" => $conferenceInfo -> ConferenceContact);							
						
	//Replace the dynamice constants with real values
	$strContent = replace_Dynamic_Values($arrConstants,$arrReplaceInfo,$strContent);
	
	//Store the content into the session
	$arrContent["subject"] = $strSubject;
	$arrContent["content"] = $strContent;
	$_SESSION["content"] = $arrContent;
	
	do_html_header($letterTitle);

?>
<form name="form1" method="post" action="process_accept_reject_mail.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2"><strong>Below is the information of the paper you are going 
        to <?php echo $HTTP_POST_VARS["status"]; ?>and the preview of your letter. An email will send out to 
        inform the user immediately. Click Send to confirm.</strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><?php echo stripslashes("<h3>".stripslashes($paperInfo -> Title)."</h3>"); ?></td>
    </tr>
    <input type="hidden" name="paperID" value="<?php echo $paperInfo->PaperID; ?>">
    <tr> 
      <td colspan="2"><strong>Paper #</strong><strong><?php echo $paperInfo->PaperID; ?></strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="15%"><strong>Authors:</strong> </td>
      <td width="85%"><?php echo $authors; ?></td>
    </tr>
    <tr> 
      <td><strong>Keywords:</strong> </td>
      <td><?php echo $catcomsep; ?></td>
    </tr>
    <tr> 
      <td><strong>Status:</strong></td>
      <td><?php echo $HTTP_POST_VARS["status"]; ?></td>
    </tr>
    <input type="hidden" name="status" value="<?php echo $HTTP_POST_VARS["status"]; ?>">
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>To:</strong></td>
      <td><?php echo $HTTP_POST_VARS["email"]; ?></td>
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
    <?php if(!empty($HTTP_POST_FILES["file"]["name"])){?>
    <tr> 
      <td><strong>Review File:</strong></td>
      <td><?php echo $HTTP_POST_FILES["file"]["name"]; ?></td>
    </tr>
    <?php }?>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr align="center"> 
      <td colspan="2"><strong>----- Letter Start Here -----</strong></td>
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
      <td colspan="2"><strong>----- Letter End Here -----</strong></td>
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
