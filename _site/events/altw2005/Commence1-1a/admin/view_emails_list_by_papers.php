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
	
	//Get the get and session variables
	if($HTTP_GET_VARS["recipientGroupName"])
		$recipientGroupName = $HTTP_GET_VARS["recipientGroupName"];
	else
		$recipientGroupName = $HTTP_POST_VARS["recipientGroupName"];
		
	if($HTTP_GET_VARS["letterID"])		
		$letterID = $HTTP_GET_VARS["letterID"];
	else
		$letterID = $HTTP_POST_VARS["letterID"];
	
	if($HTTP_GET_VARS["edit"])
		$edit = $HTTP_GET_VARS["edit"];
	else
		$edit = $HTTP_POST_VARS["edit"];
		
	if($HTTP_POST_VARS["showing"])
		$HTTP_GET_VARS["showing "] = $HTTP_POST_VARS["showing"];
		
	if($HTTP_POST_VARS["sort"])
		$HTTP_GET_VARS["sort"] = $HTTP_POST_VARS["sort"];
			
	$letterInfo = get_Letter_Info($letterID);
	
	$showing = $HTTP_GET_VARS["showing"];	
	//echo $showing."<br>";
	
	//Retrieve all the email
	if(isset($_SESSION["arrUpdateEmails"]))
		$arrUpdateEmails =& $_SESSION["arrUpdateEmails"];
	else {	
		$arrUpdateEmails = get_Unsended_EmailList($letterID,$letterInfo -> RecipientGroupName);	
		if(count($arrUpdateEmails) > 0)
			$_SESSION["arrUpdateEmails"] = $arrUpdateEmails;
	}
	
	$num_rows = count($arrUpdateEmails);
	
	//Check whether the letter has been sent to all recipients
	if ($num_rows == 0){
		echo "<html>\n<head>\n<title>Commence Conference System</title>";
		echo "<link href=\"$php_root_path/stylesheets/CommentStyle.css\" rel=\"stylesheet\" type=\"text/css\">\n";
		echo "</head>\n<h1>".$letterInfo -> Title."</h1>\n<body>\n";	
		echo "<p>This letter has already been sent to all the recipients. Choose the recipients you wish to send the letter again.<br><br>";
		echo "<a href=\"mailing_list_by_papers.php?letterID=".$letterInfo -> LetterID."&recipientGroupName=". $letterInfo -> RecipientGroupName."\">edit email list</a><br><br>";
		echo "<input name=\"Button\" type=\"button\" onClick=\"JavaScript:window.close()\" value=\"Close\">";
		echo "</p></body></html>";	
		exit;
	}

if($edit != "false")
	$url =	"<a href=\"mailing_list_by_papers.php?letterID=".$letterInfo -> LetterID."&recipientGroupName=".$letterInfo -> RecipientGroupName."\">edit email list</a>";
	
	//Splite the array into chunks
	$arrEmailChunks = array_chunk($arrUpdateEmails,MAX_EMAILS,TRUE);
	
	/*
	echo "<pre>";
	print_r($arrEmailChunks );
	echo "</pre>";*/	
	
	$index = $HTTP_GET_VARS["showing"] / MAX_EMAILS;
	$arrCurrentEmails = $arrEmailChunks[$index];		
	
	//Call the function to display the range of records
	$from = evaluate_records_range($HTTP_GET_VARS["showing"],$num_rows,MAX_EMAILS);					
	//Call the function to evaluate prev
	$prev = evaluate_prev($HTTP_GET_VARS["sort"],$HTTP_GET_VARS["showing"],$num_rows,MAX_EMAILS);
	//Call the function to evaluate next
	$next = evaluate_next($HTTP_GET_VARS["sort"],$HTTP_GET_VARS["showing"],$num_rows,MAX_EMAILS);
	//Call the function to evaluate page links
	$pagesLinks = evaluate_pages_links($HTTP_GET_VARS["sort"],$HTTP_GET_VARS["showing"],$num_rows,MAX_EMAILS);
	
	//Evalute the JavaScript for $prev
	$js = array() ;	
	$querystring_array = get_querystring_from_href ( $prev ) ;
	$dlimit = count ( $querystring_array ) ;
	for ( $d = 0 ; $d < $dlimit ; $d++ )
	{
		$js[$d] = " \"javascript:papercheckbox( 'view_emails_list_by_papers.php' , '" . $querystring_array[$d] . "')\" " ;
	}
	$prev = insert_js_call_in_href ( $js , $prev ) ;
	$prev = delete_href ( $prev ) ;		
	
	//Evaluate the JavaScript for $next
	$js = array() ;	
	$querystring_array = get_querystring_from_href ( $next ) ;
	$dlimit = count ( $querystring_array ) ;
	for ( $d = 0 ; $d < $dlimit ; $d++ )
	{
		$js[$d] = " \"javascript:papercheckbox( 'view_emails_list_by_papers.php' , '" . $querystring_array[$d] . "')\" " ;
	}
	$next = insert_js_call_in_href ( $js , $next ) ;
	$next = delete_href ( $next ) ;		
	
	
	//Evaluate the JavaScript for $pagelinks
	$js = array() ;	
	$querystring_array = get_querystring_from_href ( $pagesLinks ) ;
	$dlimit = count ( $querystring_array ) ;
	for ( $d = 0 ; $d < $dlimit ; $d++ )
	{
		$js[$d] = " \"javascript:papercheckbox( 'view_emails_list_by_papers.php' , '" . $querystring_array[$d] . "')\" " ;
	}
	$pagesLinks = insert_js_call_in_href ( $js , $pagesLinks ) ;
	$pagesLinks = delete_href ( $pagesLinks ) ;
	
	//Check the sorting by Title
	switch($HTTP_GET_VARS["sort"]){
			case 1:
				ksort($arrCurrentEmails);
				$sortStr = "PaperID - Ascending";
				break;
			case 2:
				krsort($arrCurrentEmails);
				$sortStr = "PaperID - Descending";
				break;
			case 3:
				asort($arrCurrentEmails);
				$sortStr = "Email - Ascending";
				break;
			case 4:
				arsort($arrCurrentEmails);
				$sortStr = "Email - Descending";
				break;																								
			default:
				ksort($arrCurrentEmails);
				$sortStr = "PaperID - Ascending";
				break;							
	}			

?>
<html>
<head>
<title>Commence Conference System</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php echo "<link href=\"$php_root_path/stylesheets/CommentStyle.css\" rel=\"stylesheet\" type=\"text/css\">\n"; ?>
<script language="JavaScript">

function papercheckbox( mylink , query )
{
	document.frmMailList.action = ( mylink + query ) ;
	document.frmMailList.submit();
}

</script>
</head>
<h1><?php echo stripslashes($letterInfo -> Title); ?></h1>
<body>
<?php 


	
?>
<form name="frmMailList" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="2"><input name="Button" type="button" onClick="JavaScript:window.close()" value="Close"> 
        <input type="hidden" name="insertAddress" value="true"> <input type="hidden" name="insertAddress" value="true"> 
        <input type="hidden" name="letterID" value="<?php echo $letterID; ?>"> 
        <input type="hidden" name="recipientGroupName" value="<?php echo $recipientGroupName; ?>"> 
        <input type="hidden" name="edit" value="<?php echo $edit; ?>"></td>
      <td colspan="3" align="right"><?php echo $url; ?></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
      <td colspan="3" align="right">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong><?php echo $sortStr;?></strong></td>
      <td colspan="3" align="right">Recipient Group: <strong><?php echo $letterInfo -> RecipientGroupName; ?></strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
      <td colspan="3" align="right">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">From: <strong><?php echo $from; ?></strong></td>
      <td colspan="3" align="right"><?php echo $prev; ?> | <?php echo $pagesLinks; ?>| 
        <?php echo $next; ?></td>
    </tr>
    <tr> 
      <td colspan="5">&nbsp;</td>
    </tr>
    <tr> 
      <td width="5%" align="center"> 
        <?php $urlStr = "javascript:papercheckbox( 'view_emails_list_by_papers.php' , '?sort=1&showing=".$HTTP_GET_VARS["showing"] . "')" ; echo format_Sorting_URL($urlStr,"ASC"); ?><strong>ID</strong><?php $urlStr = "javascript:papercheckbox( 'view_emails_list_by_papers.php' , '?sort=2&showing=".$HTTP_GET_VARS["showing"] . "')" ; echo format_Sorting_URL($urlStr,"DESC"); ?>
      </td>
      <td width="35%"><strong>Title</strong></td>
      <td width="15%"><strong>User Name</strong></td>
      <td width="15%"> 
        <?php $urlStr = "javascript:papercheckbox( 'view_emails_list_by_papers.php' , '?sort=3&showing=".$HTTP_GET_VARS["showing"] . "')" ; echo format_Sorting_URL($urlStr,"ASC"); ?>
        <strong>Email</strong> 
        <?php $urlStr = "javascript:papercheckbox( 'view_emails_list_by_papers.php' , '?sort=4&showing=".$HTTP_GET_VARS["showing"] . "')" ; echo format_Sorting_URL($urlStr,"DESC"); ?>
      </td>
      <td width="15%"><strong>Full Name</strong> &nbsp; </td>
    </tr>
    <?php while(list($paperID,$email) = each($arrCurrentEmails)){
			
			//Get the paper information
			$paperInfo = get_paper_info($paperID);
			
			//Retrieve all the user information
			$emailInfo = getMemberInfo($paperInfo -> MemberName);
			
			if($edit == "false") 
				$paperURL = "<a href=\"view_letter_for_papers.php?paperID=".$paperID."&letterID=".$letterInfo -> LetterID."\">".stripslashes($paperInfo -> Title)."</a>";			
			else
				$paperURL = stripslashes($paperInfo -> Title);			
	?>
    <tr> 
      <td align="center"><?php echo  $paperInfo -> PaperID; ?></td>
      <td><?php echo $paperURL; ?></td>
      <td><?php echo $emailInfo -> MemberName; ?></td>
      <td><?php echo $emailInfo -> Email; ?></td>
      <td><?php echo getMemberFullName($emailInfo -> MemberName); ?></td>
    </tr>
    <?php } ?>
    <tr> 
      <td colspan="5">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">Total: <strong><?php echo count($arrUpdateEmails); ?></strong></td>
      <td colspan="3" align="right"><?php echo $prev; ?> | <?php echo $pagesLinks; ?>| 
        <?php echo $next; ?></td>
    </tr>
  </table>
</form>
</body>
</html>
