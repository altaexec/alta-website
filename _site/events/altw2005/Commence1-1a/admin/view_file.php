<?php
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");		
	session_start();
	global $valid_user ;	
	
	extract ( $HTTP_GET_VARS , EXTR_REFS ) ;
		
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	if ( !check_valid_user( &$err_message ) )
	{
		//This user is not login
		do_html_header("View File Failed" , &$err_message ) ;			
		$err_message .= " Sorry, You must login to view this file. <br>\n";
		$err_message .= "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
		do_html_footer(&$err_message);
		exit;
	}					
		
 	$db = adodb_connect ( &$err_message );
  
	if (!$db)
	{
		do_html_header("View File Failed" , &$err_message ) ;	
   		$err_message .= " Could not connect to database server - please try later.<br>\n";	
		$err_message .= "<br><br> Try <a href='view_file.php?fileid=" . $HTTP_GET_VARS["fileid"] . "'>again</a>?" ;	
		do_html_footer(&$err_message);		
		exit;
	}
	$sql = "SELECT File,FileName,FileSize,FileType FROM " . $GLOBALS["DB_PREFIX"] . "File F , " . $GLOBALS["DB_PREFIX"] . "Paper P" ;
	$sql .= " WHERE F.FileID=" . $HTTP_GET_VARS["fileid"] . " AND F.PaperID=P.PaperID" ;	
	$result = $db -> Execute($sql);
	$rows = $result -> RecordCount();
	
	if (!$result )
	{
		do_html_header("View File Failed" , &$err_message ) ;	
   		$err_message .= " Could not connect to File database.<br>\n";	
		$err_message .= "<br><br> Try <a href='view_file.php?fileid=" . $HTTP_GET_VARS["fileid"] . "'>again</a>?" ;	
		do_html_footer(&$err_message);		
		exit;
	}
	else if ( !$rows )
	{
		do_html_header("View File Failed" , &$err_message ) ;	
   		$err_message .= " The requested file is not available.<br>\n";	
		$err_message .= "<br><br> Try <a href='view_file.php?fileid=" . $HTTP_GET_VARS["fileid"] . "'>again</a>?" ;	
		do_html_footer(&$err_message);		
		exit;
	}	
	
    $row = $result -> FetchNextObj();
	$data = $row -> File;
	$name = $row -> FileName;
	$size = $row -> FileSize;
	$type = $row -> FileType;

	// Check for Internet Explorer to avoid inline PDF viewing bug

	$browser = getBrowser( ) ;
	if ($browser == "IEWin")
	{
		$method = "attachment" ;
	}
	else
	{
		$method = "inline" ;
	}
	
	
	header("Cache-control: private");	
	header("Content-type: $type");
	header("Content-length: $size");
	header("Content-Disposition: $method; filename=$name");
	header("Content-Description: PHP Generated Data");
	echo $data;
?>
