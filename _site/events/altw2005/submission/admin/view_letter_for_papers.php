<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	//Get the member name
	$letterID = $HTTP_GET_VARS["letterID"];
	$letterInfo = get_Letter_Info($letterID);			
	$paperID = $HTTP_GET_VARS["paperID"];
	
	//Get the content array from session
	$arrContent = $_SESSION["arrContent"];	
		
	//Make the title
	$paperInfo = get_paper_info($paperID);
	$title = "Preview Letter for \"".$paperInfo -> Title."\"";
	
?>
<html>
<head>
<title>Commence Conference System</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php echo "<link href=\"$php_root_path/stylesheets/CommentStyle.css\" rel=\"stylesheet\" type=\"text/css\">\n"; ?>
</head>
<h1><?php echo stripslashes($title); ?></h1>
<body>
<form name="form1" method="post" action="view_emails_list_by_papers.php?letterID=<?php echo $letterID; ?>&recipientGroupName=<?php echo $letterInfo -> RecipientGroupName; ?>&edit=false">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  	<tr align="center"> 
      <td colspan="2"><strong>----- Letter Starts -----</strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><?php echo nl2br($arrContent[$paperID]); ?></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr align="center"> 
      <td colspan="2"><strong>----- Letter Ends -----</strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="Submit" type="submit" id="Submit" value="Back"></td>
    </tr>
  </table>
</form>
</body>
</html>
