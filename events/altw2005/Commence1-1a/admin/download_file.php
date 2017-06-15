<?php
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");		
	session_start();
			
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	extract ( $HTTP_GET_VARS , EXTR_REFS ) ;	

	if ( !check_valid_user( &$err_message ) )
	{
		//This user is not logged in
		$homepage->showmenu = 0 ;		
		do_html_header("Download Paper Failed" , &$err_message ) ;			
		$err_message .= " Sorry, You must login to download this file. <br>\n";
		$err = $err_message . "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
		do_html_footer(&$err);
		exit;
	}	
	
 	$db = adodb_connect( &$err_message );
  
	if (!$db)
	{
		do_html_header("Download Paper Failed" , &$err_message ) ;	
   		$err_message .= "Could not connect to database server - please try later.<br>\n";		
		$err = $err_message . "<br><br> Try <a href='download_file.php?fileid=$fileid'>again</a>?" ;
		do_html_footer(&$err);		
		exit;	
	}		
/*		
	if ( ( $status = check_privilege_type ( 3 , &$err_message ) ) !== false )
	{
		if ( $status == 0 )
		{
			$homepage->showmenu = 0 ;	
			do_html_header("Download Paper Failed" , &$err_message ) ;	
			$err_message .= " You do not have the authority to download the requested file.<br>\n";	
			$err = $err_message . "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
			do_html_footer(&$err);		
			exit;
		}
	}
	else
	{
		do_html_header("Download Paper Failed" , &$err_message ) ;	
		$err_message .= " Could not connect to database.<br>\n";
		$err = $err_message . "<br><br> Try <a href='download_file.php?fileid=$fileid'>again</a>?" ;
		do_html_footer(&$err);		
		exit;	
	}	
*/	
	$sql = "SELECT File,FileName,FileSize,FileType FROM " . $GLOBALS["DB_PREFIX"] . "File F , " . $GLOBALS["DB_PREFIX"] . "Paper P" ;
	$sql .= " WHERE F.FileID=" . $fileid . " AND F.PaperID=P.PaperID" ;
	$result = $db -> Execute($sql);
	$rows = $result -> RecordCount() ;
	
	if ( !$result )
	{
		do_html_header("Download Paper Failed" , &$err_message ) ;	
   		$err_message .= " Could not connect to File database.<br>\n";
		$err = $err_message . "<br><br> Try <a href='download_file.php?fileid=$fileid'>again</a>?" ;		
		do_html_footer(&$err);		
		exit;
	}
	else if ( !$rows )
	{
		do_html_header("Download Paper Failed" , &$err_message ) ;	
   		$err_message .= " The requested file is not available.<br>\n";	
		$err = $err_message . "<br><br> Try <a href='download_file.php?fileid=$fileid'>again</a>?" ;		
		do_html_footer(&$err);		
		exit;
	}
	
    $row = $result -> FetchNextObj();
	$data = $row -> File;
	$name = $row -> FileName;
	$size = $row -> FileSize;
	$type = $row -> FileType;
	
	header("Cache-control: private");	
	header("Content-type: $type");
	header("Content-length: $size");
	header("Content-Disposition: attachment; filename=$name");
	header("Content-Description: PHP Generated Data");
	echo $data;
?>