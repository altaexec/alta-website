<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	//Click on either Back or Edit
	if($HTTP_POST_VARS){
		//Check if the user click back button
		if($HTTP_POST_VARS["Submit"] == "Back"){
			header("Location: view_letters.php");
			exit;
		}
		
		//Fetch letter information
		$letterInfo = get_Letter_Info($HTTP_POST_VARS["letterID"]);		
		
		//The user click edit button
		$arrURL = evaluate_Letter_URL($letterInfo -> Title,$letterInfo -> LetterID);
		
		//Rediret the page to edit the letter
		$url = "Location: ".$arrURL["edit"];
		header($url);
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
	
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	
	//Fetch letter information
	$letterInfo = get_Letter_Info($HTTP_GET_VARS["letterID"]);
	
	//Format the content string and Store the content into the session
	$strContent = stripslashes($letterInfo ->  BodyContent)."\n\n";
	$strContent .= $settingInfo -> EmailSignature."\n";
	
	//Highlight the dynamic contents
	$arrConstants = array("\$confname","\$confcode");	
	$strSubject = highlight_Dynamic_Values($arrConstants,stripslashes($letterInfo ->  Subject));	
	
	//Get the constant of the letter and highlight the constants
	$arrConstants = evaluate_Letter_Constants($HTTP_GET_VARS["lettertype"]);
	//Highlight the dynamic contents
	$strContent = highlight_Dynamic_Values($arrConstants,$strContent);				
	
	do_html_header($letterInfo -> Title);
	
?>
<form name="form1" method="post" action="view_each_letter.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td><input type="hidden" name="letterID" value="<?php echo $letterInfo -> LetterID; ?>"></td>
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
      <td> 
        <input name="Submit" type="submit" id="Submit" value="Edit">
        <input name="Submit" type="submit" id="Submit" value="Back"></td>
    </tr>
  </table>
</form>
<?php do_html_footer(); ?>

