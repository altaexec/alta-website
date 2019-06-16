<?php 


	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	$letterInfo = get_Letter_Info($HTTP_POST_VARS["letterID"]);
	
	if($HTTP_POST_VARS["Submit"] == "Reset"){
		unset($_SESSION["arrUpdateEmails"]);
		if(($letterInfo -> Title == "Paper Acceptance") || ($letterInfo -> Title == "Paper Rejection"))
			$url = "Location: mailing_list_by_papers.php?letterID=".$HTTP_POST_VARS["letterID"]."&recipientGroupName=".$HTTP_POST_VARS["recipientGroupName"];
		else		
			$url = "Location: mailing_list.php?letterID=".$HTTP_POST_VARS["letterID"]."&recipientGroupName=".$HTTP_POST_VARS["recipientGroupName"];
		
		header($url);
		exit;
	}
	
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
			if(($letterInfo -> Title == "Paper Acceptance") || ($letterInfo -> Title == "Paper Rejection"))
				$_SESSION["arrUpdateEmails"]  = array_merge_assoc($arrDiff,$HTTP_POST_VARS["to"]);				
			else
				$_SESSION["arrUpdateEmails"] = array_merge($arrDiff,$HTTP_POST_VARS["to"]);
		}else
			$_SESSION["arrUpdateEmails"] = $arrDiff;			
			
		/*
		echo "<br>Merge Array now is <pre>";
		print_r($_SESSION["arrUpdateEmails"]);
		echo "</pre><br>";*/		
		
	}
	/*else if(isset($_SESSION["arrUpdateEmails"])){
	
		echo "<br>Session Only.<br>";	
		//The session of $arrUpdateEmail exisit
		$arrUpdateEmails = & $_SESSION["arrUpdateEmails"];		
	
	}*/	
	else {
		//echo "<br>Very first time.<br>";	
	
		$arrUpdateEmails = get_Unsended_EmailList($letterID,$recipientGroupName);		
		if(count($arrUpdateEmails) > 0)
			$_SESSION["arrUpdateEmails"] = $arrUpdateEmails;
	}				
	
	//Redirect the page to view email list
	if(($letterInfo -> Title == "Paper Acceptance") || ($letterInfo -> Title == "Paper Rejection"))
		$url = "Location: view_emails_list_by_papers.php?letterID=".$HTTP_POST_VARS["letterID"]."&recipientGroupName=".$HTTP_POST_VARS["recipientGroupName"];
	else		
		$url = "Location: view_emails_list.php?letterID=".$HTTP_POST_VARS["letterID"]."&recipientGroupName=".$HTTP_POST_VARS["recipientGroupName"];
		
		header($url);
		exit;

?>
