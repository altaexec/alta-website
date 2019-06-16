<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		unset($_SESSION["arrLetterInfo"]);
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
	
	//Register the session
	$_SESSION["arrLetterInfo"] = $HTTP_POST_VARS;
	
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	
	//Fetch letter information
	$letterInfo = get_Letter_Info($HTTP_POST_VARS["letterID"]);
	
	//Format the content string and Store the content into the session
	$strContent = stripslashes($HTTP_POST_VARS["bodycontent"])."\n\n";
	$strContent .= $settingInfo -> EmailSignature."\n";
	

	
	//Highlight the dynamic contents
	$arrConstants = array("\$confname","\$confcode");	
	$strSubject = highlight_Dynamic_Values($arrConstants,stripslashes($HTTP_POST_VARS["subject"]));
	
	//Get the constant of the letter and highlight the constants
	$arrConstants = evaluate_Letter_Constants($HTTP_POST_VARS["lettertype"]);	
	$strContent = highlight_Dynamic_Values($arrConstants,$strContent);				
	
	do_html_header($letterInfo -> Title);
	
?>
<form name="form1" method="post" action="process_letter.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td><input type="hidden" name="lettertype" value="<?php echo $HTTP_POST_VARS["lettertype"]; ?>"></td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong>&nbsp;<?php echo $strSubject; ?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td align="center"><strong>----- Letter Starts -----</strong></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><?php echo nl2br($strContent); ?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td align="center"><strong>----- Letter Ends -----</strong></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><input type="submit" name="Submit" value="Confirm"> <input name="Submit" type="submit" id="Submit" value="Back"></td>
    </tr>
  </table>
</form>
<?php do_html_footer(); ?>

