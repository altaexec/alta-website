<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	//Get the get and session variables
	if($HTTP_GET_VARS["recipientGroupName"])
		$recipientGroupName = $HTTP_GET_VARS["recipientGroupName"];
	else
		$recipientGroupName = $HTTP_POST_VARS["recipientGroupName"];
		
	if($HTTP_GET_VARS["letterID"]){		
		$letterID = $HTTP_GET_VARS["letterID"];
		unset($_SESSION["arrCurrentRecords"]);
	}
	else
		$letterID = $HTTP_POST_VARS["letterID"];
		
	//Fetch letter information
	$letterInfo = get_Letter_Info($letterID);			
	
	//Retrieve all the email
	$arrEmails = get_Already_Sent_EmailList($letterID);
	
	//Retrieve the session if there is
	if(isset($_SESSION["arrUpdateEmails"]))	
		$arrUpdateEmails = & $_SESSION["arrUpdateEmails"];		

	function limit_view_emails(&$emailSQL, &$sort , &$showing , &$sortStr , $max_view_emails = 5 ){
		
		//Check the sorting by Title
		switch($sort){
				case 1:
					$emailSQL .= " ORDER BY PP.PaperID ASC";
					$sortStr = "PaperID - Ascending";
					break;
				case 2:
					$emailSQL .= " ORDER BY PP.PaperID DESC";
					$sortStr = "PaperID - Descending";
					break;
				case 3:
					$emailSQL .= " ORDER BY PP.Title ASC";
					$sortStr = "Title - Ascending";
					break;
				case 4:
					$emailSQL .= " ORDER BY PP.Title DESC";
					$sortStr = "Title - Descending";
					break;													
				case 5:
					$emailSQL .= " ORDER BY M.MemberName ASC";
					$sortStr = "UserName - Ascending";
					break;
				case 6:
					$emailSQL .= " ORDER BY M.MemberName DESC";
					$sortStr = "UserName - Descending";
					break;
				case 7:
					$emailSQL .= " ORDER BY R.Email ASC";
					$sortStr = "Email - Ascending";
					break;
				case 8:
					$emailSQL .= " ORDER BY R.Email DESC";
					$sortStr = "Email - Descending";
					break;																
				case 9:
					$emailSQL .= " ORDER BY R.LastName ASC";
					$sortStr = "Fullname - Ascending";
					break;
				case 10:
					$emailSQL .= " ORDER BY R.LastName DESC";
					$sortStr = "Fullname - Descending";
					break;
				case 11:
					$emailSQL .= " ORDER BY R.LastName ASC";
					$sortStr = "Email Status - Ascending";
					break;		
				case 12:
					$emailSQL .= " ORDER BY R.LastName DESC";
					$sortStr = "Email Status - Descending";
					break;								
				default:
					$emailSQL .= " ORDER BY PP.PaperID ASC";
					$sortStr = "PaperID - Ascending";
					break;							
		}
	
		//Limit the records according to max_view_emails
		$emailSQL .= " LIMIT ".$showing.",".$max_view_emails;
		
		return $emailSQL;
	
	}//end of function
					
	//if($HTTP_POST_VARS["showing"])
	//	$HTTP_GET_VARS["showing "] = $HTTP_POST_VARS["showing"];
		
	//if($HTTP_POST_VARS["sort"])
	//	$HTTP_GET_VARS["sort"] = $HTTP_POST_VARS["sort"];
	
	//Call function to evaluate showing
	$HTTP_GET_VARS["showing"] = evaluate_showing($HTTP_GET_VARS["showing"]);	
	$emailSQL = get_Recipient_Group_SQL($letterInfo -> RecipientGroupName);
	//echo "<br><br>".$emailSQL."<br>";
	$emailResult = $db -> Execute($emailSQL);
	$num_rows = $emailResult -> RecordCount();
		
	$emailSQL = limit_view_emails($emailSQL, $HTTP_GET_VARS["sort"] , $HTTP_GET_VARS["showing"] ,$sortStr ,MAX_EMAILS);
	echo $emailSQL;	
	$emailResult = $db -> Execute($emailSQL);
	
	if(!$emailResult){
		echo "Could not retrieve user email information - try again";
		exit;
	}
	
	$limit_rows = $emailResult -> RecordCount();
	
	//Update the session accordingly
	if(($HTTP_POST_VARS["to"]) || isset($_SESSION["arrUpdateEmails"])){
		
		/*
		echo "<br>Both post and session.<br>";
		
		echo "<br><br>Session \$arrUpdateEmails Array is <pre>";
		print_r($_SESSION["arrUpdateEmails"]);
		echo "</pre><br>Current Record \$arrCurrentRecords array is <pre>";
		print_r($_SESSION["arrCurrentRecords"]);
		echo "</pre><br>";*/
		
		if(count($_SESSION["arrCurrentRecords"]) > 0)
			//Get the array without previous records
			$arrDiff = array_diff_assoc($_SESSION["arrUpdateEmails"],$_SESSION["arrCurrentRecords"]);
		else
			$arrDiff = $_SESSION["arrUpdateEmails"];
		
		/*
		echo "<br>Diff Array array is <pre>";
		print_r($arrDiff);
		echo "</pre><br>";
		echo "<br>Post Array array is <pre>";
		print_r($HTTP_POST_VARS["to"]);
		echo "</pre><br>";*/			
		
		if($HTTP_POST_VARS["to"]){
			//Merge the session array with post array
			$_SESSION["arrUpdateEmails"]  = array_merge_assoc($arrDiff,$HTTP_POST_VARS["to"]);		
			$arrUpdateEmails = $_SESSION["arrUpdateEmails"];
		}else {
			$_SESSION["arrUpdateEmails"] = $arrDiff;
			$arrUpdateEmails = $_SESSION["arrUpdateEmails"];			
		}		
		
		/*
		echo "<br>Merge Array now is <pre>";
		print_r($_SESSION["arrUpdateEmails"]);
		echo "</pre><br>";*/	
		
	}/*
	else if(isset($_SESSION["arrUpdateEmails"])){
	
		//echo "<br>Session Only.<br>";	
		//The session of $arrUpdateEmail exisit
		$arrUpdateEmails = & $_SESSION["arrUpdateEmails"];		
	
	}*/	
	else {
		//echo "<br>Very first time.<br>";	
	
		$arrUpdateEmails = get_Unsended_EmailList($letterID,$letterInfo -> RecipientGroupName);		
		if(count($arrUpdateEmails) > 0)
			$_SESSION["arrUpdateEmails"] = $arrUpdateEmails;
		else
			$_SESSION["arrUpdateEmails"] = array();
			
	}			

	$arrCurrentRecords = array();
	//All the members of a certain limited by max_view_emails
	for($i = 0;$i < $limit_rows;$i++){
		$emailInfo = $emailResult -> FetchNextObj();
		//list($memberName,$email) = each($arrCurrentRecords);
		$arrCurrentRecords[$emailInfo -> PaperID] = $emailInfo -> Email;
		$arrEmailInfo[$i]["paperid"] = $emailInfo -> PaperID;
		$arrEmailInfo[$i]["title"] = stripslashes($emailInfo -> Title);				
		$arrEmailInfo[$i]["memberName"] = $emailInfo -> MemberName;
		$arrEmailInfo[$i]["email"] = $emailInfo -> Email;
		//$arrEmailInfo[$i]["memberName"] = $memberName;
		//$arrEmailInfo[$i]["email"] = $email;		

		//The user modified the check boxes
		/*
		if($HTTP_POST_VARS["to"]){			
			while(list($memberName,$email) = each($HTTP_POST_VARS["to"])){
				if($emailInfo -> MemberName == $memberName)
					$arrEmailInfo[$i]["check"] = "checked";
			}
		}
		else */ 
		if(count($arrUpdateEmails) > 0){
			if(arrayKeyExists($emailInfo -> PaperID,$arrUpdateEmails))
				$arrEmailInfo[$i]["check"] = "checked";
		}/*
		else if(count($arrEmails) > 0){ //No session
			if(!arrayKeyExists($emailInfo -> MemberName,$arrEmails)) 
				$arrEmailInfo[$i]["check"] = "checked";				
		}*/
		//else //There email has not sent to anybody yet.
			//$arrEmailInfo[$i]["check"] = "checked";
		
		$sent = "<font color=\"#0000FF\"><strong>Sent</strong></font>";
		$notsent = "<font color=\"#FF0000\"><strong>Not sent</strong></font>";
		
		//Evaluate the email status
		if(count($arrEmails) > 0){//Some emails has been sent
			if(arrayKeyExists($emailInfo -> PaperID,$arrEmails))			
				$arrEmailInfo[$i]["status"] = $sent ;
			else
				$arrEmailInfo[$i]["status"] = $notsent ;						
		}else //No emails has been sent out		
			$arrEmailInfo[$i]["status"] = $notsent ;	
		
	} //end of record set loop
	
	//Reset the array

	reset($arrEmailInfo);
	if($HTTP_GET_VARS["sort"] == 11) { //Array is to be sort by "status" ascending
		asort($arrEmailInfo);
		$sortStr = "Email Status - Ascending";
	}else if($HTTP_GET_VARS["sort"] == 12) { //Array is to be sort by "status" descending
		arsort($arrEmailInfo);
		$sortStr = "Email Status - Descending";			
	}	
	
	//Insert the current records into session
	$_SESSION["arrCurrentRecords"] = $arrCurrentRecords;
	
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
		$js[$d] = " \"javascript:papercheckbox( 'mailing_list_by_papers.php' , '" . $querystring_array[$d] . "')\" " ;
	}
	$prev = insert_js_call_in_href ( $js , $prev ) ;
	$prev = delete_href ( $prev ) ;		
	
	//Evaluate the JavaScript for $next
	$js = array() ;	
	$querystring_array = get_querystring_from_href ( $next ) ;
	$dlimit = count ( $querystring_array ) ;
	for ( $d = 0 ; $d < $dlimit ; $d++ )
	{
		$js[$d] = " \"javascript:papercheckbox( 'mailing_list_by_papers.php' , '" . $querystring_array[$d] . "')\" " ;
	}
	$next = insert_js_call_in_href ( $js , $next ) ;
	$next = delete_href ( $next ) ;		
	
	
	//Evaluate the JavaScript for $pagelinks
	$js = array() ;	
	$querystring_array = get_querystring_from_href ( $pagesLinks ) ;
	$dlimit = count ( $querystring_array ) ;
	for ( $d = 0 ; $d < $dlimit ; $d++ )
	{
		$js[$d] = " \"javascript:papercheckbox( 'mailing_list_by_papers.php' , '" . $querystring_array[$d] . "')\" " ;
	}
	$pagesLinks = insert_js_call_in_href ( $js , $pagesLinks ) ;
	$pagesLinks = delete_href ( $pagesLinks ) ;	

?>
<html>
<head>
<title>Commence Conference System</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php echo "<link href=\"$php_root_path/stylesheets/CommentStyle.css\" rel=\"stylesheet\" type=\"text/css\">\n"; ?>
<script language="JavaScript">
<!-- Hide script from older browsers

function papercheckbox( mylink , query )
{
	document.frmMailList.action = ( mylink + query ) ;
	document.frmMailList.submit();
}

// End hiding script from older browsers -->
</script>
</head>
<h1><?php echo $letterInfo -> Title; ?></h1>
<body>
<?php 	
	
	if($emailResult -> RecordCount() == 0){
		echo "The requested recipientgroup returns no records. Try others recipient group.";
		exit;	
	}
?>
<form name="frmMailList" method="post" action="update_mailing_list.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="7"><input type="hidden" name="letterID" value="<?php echo $letterID; ?>"></td>
    </tr>
    <tr> 
      <td colspan="6"><input type="submit" name="Submit" value="Update"> <input name="Button" type="button" onClick="JavaScript:window.close()" value="Close"> 
        <input type="submit" name="Submit" value="Reset"> <input type="hidden" name="letterID" value="<?php echo $letterID; ?>"> 
        <input type="hidden" name="recipientGroupName" value="<?php echo $letterInfo -> RecipientGroupName; ?>"></td>
      <td align="right"><a href="view_emails_list_by_papers.php?letterID=<?php echo $letterID; ?>&recipientGroupName=<?php echo $letterInfo -> RecipientGroupName; ?>">view 
        email list</a></td>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
      <td colspan="2" align="right">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="5"><strong><?php echo $sortStr;?></strong></td>
      <td colspan="2" align="right">Recipient Group: <strong><?php echo $letterInfo -> RecipientGroupName; ?></strong></td>
    </tr>
    <tr> 
      <td colspan="7">&nbsp;</td>
    </tr>
    <tr> 
      <td width="5%"><strong>To:</strong></td>
      <td width="5%"><a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=1&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>ID</strong>&nbsp;<a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=2&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></td>
      <td width="25%"><a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=3&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>Title</strong>&nbsp;<a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=4&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></td>
      <td width="15%"><a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=11&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp; 
        <strong>Email Status</strong>&nbsp;<a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=12&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></td>
      <td width="15%"><a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=5&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp; 
        <strong>User Name</strong>&nbsp; <a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=6&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a> 
      </td>
      <td width="15%"><a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=7&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>Email</strong>&nbsp;<a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=8&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>	
      </td>
      <td width="20%"><a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=9&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>Full 
        Name</strong>&nbsp;<a href="<?php echo "javascript:papercheckbox( 'mailing_list_by_papers.php' , '" ; ?>?sort=10&showing=<?php echo $HTTP_GET_VARS["showing"] . "')" ; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>	
      </td>
    </tr>
    <?php 
		$r = 0;
		
		foreach($arrEmailInfo as $arrPaperInfo){
		
			if($r % 2)
				echo "<tr bgcolor=\"#FFFFFF\">";
			else
				echo "<tr bgcolor=\"#CCFFFF\">";
						
		?>
    <td><input type="checkbox" name="to[<?php echo $arrPaperInfo["paperid"]; ?>]" value="<?php echo $arrPaperInfo["email"]; ?>" <?php echo $arrPaperInfo["check"]; ?>></td>
    <td align="center"><?php echo $arrPaperInfo["paperid"]; ?></td>
    <td><?php echo $arrPaperInfo["title"]; ?></td>
    <td><?php echo $arrPaperInfo["status"]; ?></td>
    <td><?php echo $arrPaperInfo["memberName"]; ?></td>
    <td><?php echo $arrPaperInfo["email"]; ?></td>
    <td><?php echo getMemberFullName($arrPaperInfo["memberName"]); ?></td>
    </tr>
    <?php $r++; } ?>
    <tr> 
      <td colspan="7">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="5"><?php echo "From :<strong>".$from."</strong>"; ?></td>
      <td colspan="2" align="right"><?php echo $prev; ?> | <?php echo $pagesLinks; ?>| 
        <?php echo $next; ?></td>
    </tr>
  </table>
</form>
</body>
</html>
