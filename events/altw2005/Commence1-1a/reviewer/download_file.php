<?php //////////// REVIEWER PHASE 2 ///////////////
//////////// REVIEWER PHASE 3 ///////////////	

	//Establish connection with database
//	require_once("includes/db_connect.php");
//	require_once("includes/user_authen_fns.php");
	$php_root_path = ".." ;
	$privilege_root_path = "/reviewer" ;
//	extract ( $HTTP_GET_VARS , EXTR_REFS ) ;

	require_once("includes/include_all_fns.php");		
	session_start() ;
//	extract ( $_SESSION , EXTR_REFS ) ;		
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;	
	$header = "Download File" ;
	$accepted_privilegeID_arr = array ( 2 => "" ) ;
	$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;	
	
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT File,FileName,FileSize,FileType FROM " . $GLOBALS["DB_PREFIX"] . "File F , " . $GLOBALS["DB_PREFIX"] . "Paper P" ;
	$sql .= " WHERE F.FileID=" . $HTTP_GET_VARS["fileid"] . " AND F.PaperID=P.PaperID AND Withdraw='false'" ;
	
	$result = $db -> Execute($sql);
	$rows = $result -> RecordCount();
	
	if ( !$result )
	{
		do_html_header("Download Paper Failed" , &$err_message ) ;	
   		$err_message .= " Could not connect to File database.<br>\n";
		$err = $err_message . "<br><br> Try <a href='download_file.php?fileid=".$HTTP_GET_VARS["fileid"]."'>again</a>?" ;		
		do_html_footer(&$err);		
		exit;
	}
	else if ( !$rows )
	{
		do_html_header("Download Paper Failed" , &$err_message ) ;	
   		$err_message .= " The requested file is not available.<br>\n";	
		$err = $err_message . "<br><br> Try <a href='download_file.php?fileid=".$HTTP_GET_VARS["fileid"]."'>again</a>?" ;		
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
