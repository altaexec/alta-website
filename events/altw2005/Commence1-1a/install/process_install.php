<?php 
//Rewritten with better English BL

//Establish connection with database
$db = ADONewConnection("mysql");
$connected = $db->Connect( $HTTP_POST_VARS["DB_HOSTNAME"], 
                           $HTTP_POST_VARS["DB_USERNAME"], 
                           $HTTP_POST_VARS["DB_PASSWORD"], 
                           $HTTP_POST_VARS["DB_DATABASE"]);

if(!$connected)
{
	do_html_header("Installation Failed" , &$err_message ) ;	
	$err_message .= " Unable to connect to database. Please check that your \"Database Server Hostname\" , \"Database User Name\" , \"Database Password\" and \"Database Name\" are correct.<br>\n" ;
	$err_message .= $db -> ErrorNo() . ": " . $db -> ErrorMsg() . " <br>\n" ;	
	return ;
}

//echo "BOLD Second<br>\n" ;
// return ;

function OpenFile ( &$filename , &$openmode , $err_message = "" )
{
	$fp = fopen ( $filename , $openmode ) ;
	if ( !$fp )
	{
		$err_message .= " Could not read file \"$filename\" from the folder. <br>\n ";	// Exception has occurred
		return false ;
	}
	
	return $fp ;
}

function GetContentFromFile ( &$fp , &$filename )
{
	$filestring = fread ( $fp , filesize ( $filename ) ) ;
//		fclose ( $fp ) ;
	return $filestring ;
}

function GetContentArrayByDelimiter ( &$content , &$tag_array )
{
	list ( $opentag , $closetag ) = each ( $tag_array )	;
	$content_array = explode ( $closetag , $content ) ;
	reset ( $content_array ) ;
	$found_array = array() ;
	
	reset ( $tag_array ) ;		
	while ( list ( $opentag , $closetag ) = each ( $tag_array ) )
	{		
		$lower_open_tag = strtolower ( $opentag ) ;	
		$lower_close_tag = strtolower ( $closetag ) ;			
		
		while ( list ( $key , $value ) = each ( $content_array ) )
		{
			if ( ( $startpos = strpos ( strtolower ( $content_array[$key] ) , $lower_open_tag ) ) !== false )
			{		
				$found_array[$key] = substr ( $content_array[$key] , $startpos ) . $closetag ;
			}
		}

		reset ( $content_array ) ;		
	}		

	reset ( $tag_array ) ;
	reset ( $found_array ) ;
	return $found_array ;
}

function UpdateField ( &$content , &$opentag , &$closetag , &$content_array , &$add_text , $err_message = "" )
{		
	$tag_array = array ( "$opentag"=>$closetag ) ;
	if ( ( $target_array = GetContentArrayByDelimiter ( $content , $tag_array ) ) == false )
	{
		$err_message .= " Could not execute \"UpdateField\". <br>\n ";	// Exception has occurred
		return false ;							
	}

	while ( list ( $key , $value ) = each ( $target_array ) )
	{
		$seek_start_pos = strpos ( strtolower ( $content_array[$key] ) , strtolower ( $opentag ) ) ;
		$seek_start_len = strlen ( $opentag ) ;
		$content_array[$key] = substr ( $content_array[$key] , 0 , $seek_start_pos + $seek_start_len ) . $add_text ;
	}			
	
	return true ;		
}

function UpdateFileField ( &$filename , &$value_array ,  &$closetag , $err_message = "" , $opentag = "" )
{
    $err_message .= "a".dump_array( &$value_array, TRUE );
    if ( is_writable ( $filename ) )
	{
		$openmode = "r" ;
		if ( $fp = OpenFile ( $filename , $openmode , &$err_message ) )
		{
			$content = GetContentFromFile ( $fp , $filename ) ;							
			$content_array = explode ( $closetag , $content ) ;
//				echo "<br>\n Array count : " . count($content_array) . "<br>\n" ;				
			reset ( $content_array ) ;

			if ( is_array ( $value_array ) )
			{
				while ( list ( $opentag , $value ) = each ( $value_array ) )
				{
					if ( UpdateField ( $content , $opentag , $closetag , $content_array , $value , &$err_message ) == false )
					{
						$err_message .= " Could not execute \"UpdateField\" in \"UpdateFileField\". <br>\n ";	// Exception has occurred
						return false ;													
					}
				}
			}
			else
			{
				if ( UpdateField ( $content , $opentag , $closetag , $content_array , $value_array , &$err_message ) == false )
				{
					$err_message .= " Could not execute \"UpdateField\" in \"UpdateFileField\". <br>\n ";	// Exception has occurred
					return false ;													
				}
			}

//				display ( $content_array ) ;
			$final_text = implode ( $closetag , $content_array ) ;
//				echo "<br>\n" . $final_text . "<br>\n" ;

			$myfile = '../includes/db_connect.php' ;
			$mymode = 'w' ;
			$fp = OpenFile ( $myfile , $mymode , &$err_message ) ;
//			ftruncate ( $fp , 0 ) ;
//			rewind ( $fp ) ;
			if ( fwrite ( $fp , $final_text ) !== false )
			{
//					echo "<br>\n WRITING TO EXISTING FILE SUCCESS<br>\n" ;
				return true ;
			}
			else
			{
				$err_message .= " Could not execute \"fwrite\" in \"UpdateFileField\". <br>\n ";	// Exception has occurred
				return false ;			
			}								
		}
		else
		{
			$err_message .= " Could not execute \"OpenFile\" in \"UpdateFileField\". <br>\n ";	// Exception has occurred
			return false ;			
		}
	}
	else
	{
		$openmode = "r" ;
		if ( $fp = OpenFile ( $filename , $openmode , &$err_message ) )
		{
			$content = GetContentFromFile ( $fp , $filename ) ;							
			$content_array = explode ( $closetag , $content ) ;
//				echo "<br>\n Array count : " . count($content_array) . "<br>\n" ;				
			reset ( $content_array ) ;

			if ( is_array ( $value_array ) )
			{
				while ( list ( $opentag , $value ) = each ( $value_array ) )
				{
					if ( UpdateField ( $content , $opentag , $closetag , $content_array , $value , &$err_message ) == false )
					{
						$err_message .= " Could not execute \"UpdateField\" in \"UpdateFileField\". <br>\n ";	// Exception has occurred
						return false ;													
					}
				}
			}
			else
			{
				if ( UpdateField ( $content , $opentag , $closetag , $content_array , $value_array , &$err_message ) == false )
				{
					$err_message .= " Could not execute \"UpdateField\" in \"UpdateFileField\". <br>\n ";	// Exception has occurred
					return false ;													
				}
			}

//				display ( $content_array ) ;
			$final_text = implode ( $closetag , $content_array ) ;
//				echo "<br>\n" . $final_text . "<br>\n" ;
			return $final_text ;
		}
		else
		{
			$err_message .= " Could not execute \"OpenFile\" in \"UpdateFileField\". <br>\n ";	// Exception has occurred
			return false ;															
		}
	}
}

function BuildPrefFile( $db_array , &$err_message )
{
    $output_array = array();
    $output_array[] = '<?php';
    foreach ($db_array as $key => $value)
    {
        $output_array[] = "\$$key = '$value';";
    }
    $output_array[] = '?>';
    return implode("\n", $output_array);
}

//Setup the database here
$sql_filename = "sql/Database.sql" ;

if ( file_exists ( $sql_filename ) )
{
	include_once ( "../includes/main_fns.php" ) ;
	$openmode = "r" ;	

	if ( $fp = OpenFile ( $sql_filename , $openmode , &$err_message ) )
	{		
		$sql_content = GetContentFromFile ( $fp , $sql_filename ) ;
		$opentag = "CREATE TABLE" ;
		$closetag = ";" ;
		$tag_array = array ( "$opentag" => $closetag ) ;
		if ( ( $sql_create_array = GetContentArrayByDelimiter ( $sql_content , $tag_array ) ) == false )
		{
			do_html_header("Installation Failed" , &$err_message ) ;			
			$err_message .= " Installation halted at executing CREATE in \"GetContentArrayByDelimiter\". <br>\n" ;
			$err_message .= $db -> ErrorNo() . ": " . $db -> ErrorMsg() . " <br>\n" ;
			return ;			
		}
		// Drop tables
		preg_match_all("/(?<=CREATE TABLE )(\w+)/i",implode("\n\n",$sql_create_array), $matches);
		if ($HTTP_POST_VARS["overwriteExistingTables"])
		{
			foreach ($matches[0] as $match)
			{
				$sql = "DROP TABLE ".$HTTP_POST_VARS["DB_PREFIX"].$match;
				$db -> Execute($sql);
			}
		}
		
///////////// CREATE TABLES /////////////			
		while ( list ( $key , $sql ) = each ( $sql_create_array ) )
		{
			$sql = substr_replace ( $sql , " " . $HTTP_POST_VARS["DB_PREFIX"] , ( strpos ( $sql , $opentag ) + strlen ( $opentag ) ) , 1 ) ;
			$result = $db -> Execute($sql);
			
			if(!$result)	// Roll back the changes
			{
				do_html_header("Installation Failed" , &$err_message ) ;							
				$err_message .= " Could not create table in database. <br>\n ";	// Exception has occurred						
				$err_message .= $db -> ErrorNo() . ": " . $db -> ErrorMsg() . " <br>\n" ;		
				return ;
			}
		}
		
//			echo "CREATE DONE <br>\n" ;
		
		$opentag = "INSERT INTO" ;
		$tag_array = array ( "$opentag" => $closetag ) ;				
		if ( ( $sql_insert_array = GetContentArrayByDelimiter ( $sql_content , $tag_array ) ) == false )
		{
			do_html_header("Installation Failed" , &$err_message ) ;			
			$err_message .= " Installation halted at executing INSERT in \"GetContentArrayByDelimiter\". <br>\n" ;
			$err_message .= $db -> ErrorNo() . ": " . $db -> ErrorMsg() . " <br>\n" ;		
			return ;
		}
////////////// INSERT VALUES //////////////			
		while ( list ( $key , $sql ) = each ( $sql_insert_array ) )
		{
			$sql = substr_replace ( $sql , " " . $HTTP_POST_VARS["DB_PREFIX"] , ( strpos ( $sql , $opentag ) + strlen ( $opentag ) ) , 1 ) ;			
			$result = $db -> Execute($sql);
			
			if(!$result)	// Roll back the changes
			{
				do_html_header("Installation Failed" , &$err_message ) ;							
				$err_message .= " Could not insert values into table of database. <br>\n ";	// Exception has occurred
				$err_message .= $db -> ErrorNo() . ": " . $db -> ErrorMsg() . " <br>\n" ;		
				return ;
			}				
		}
//			echo "INSERT DONE <br>\n" ;		
	//////////////////Register the user as administrator///////////
		$reg_result = register($HTTP_POST_VARS["username"], $HTTP_POST_VARS, $HTTP_POST_VARS["DB_PREFIX"] , &$err_message , 3 , &$HTTP_POST_VARS["password"] , 0 , &$db ) ;
	//	echo "reg_result = $reg_result\n";
		if ( $reg_result )
		{				
			$setup_filename = '../install/db_connect_dist.php' ;
			$closetag = "\";" ;
			//$db_array = array ( "\$dbhostname=\""=>$HTTP_POST_VARS["db_hostname"]  , "\$dbdatabase=\""=>$HTTP_POST_VARS["db_name"] , "\$dbusername=\""=>$HTTP_POST_VARS["db_username"] , "\$dbpassword=\""=>$HTTP_POST_VARS["db_pwd"] , "\$dbprefix=\""=>$HTTP_POST_VARS["db_prefix"] ) ;
			
            // Find all relevant vars
            require_once("../includes/globals.php");
            $db_array = array();
            foreach ($HTTP_POST_VARS as $key => $value)
            {
                if (array_key_exists($key, $GLOBALS["PREFERENCE_VARS"]))
                {
                    $db_array[$key] = $value;
                }
            }
            
            reset ( $db_array ) ;
			
			//if ( ( $final_text = UpdateFileField ( $setup_filename , $db_array , $closetag , &$err_message ) ) )
			if ( $final_text = BuildPrefFile( $db_array , &$err_message ))
            {
				if ( $final_text === true )
				{			
					global $homepage ;		
					$homepage -> SetMetaHeader ( "<META HTTP-EQUIV=\"REFRESH\" CONTENT = \"3; URL=../index.php\">" ) ;			
					do_html_header("Installation Successful" , &$err_message );
					echo "This installation is successful.<br> You would now be automatically redirected to your Login page, otherwise you may click <a href='../index.php'>here</a> to login too.<br><br>\n";
					do_html_footer( &$err_message );
				}
				else
				{						
					do_html_header("Download Configuration File" , &$err_message );				
					$end_str = "Your server does not have permission to write to the file \"preferences.php\". " ;
					$end_str .= "As the last step of the installation, click the button below to begin downloading your new \"preferences.php\" file. <br><br>\n\n" ;
					$final_str = "<br><br>After downloading this file, you must manually upload the file into  \"/includes\" and replace any existing \"preferences.php\" file.<br> <br>\n\n " ;
				//	$final_str .= "For security reasons, you should delete the directory of \"/install\" after you have successfully logged as the administrator. " ;
					$final_str .= "Finally, you may click <a href='../index.php'>here</a> to login.<br><br>\n\n";					
?>					
	<form name="frmupload" method="post" action="install.php">
<?php					
					echo $end_str ;					
?>					
  	<div align="center">
  	<input type="hidden" value="<?php echo htmlspecialchars( $final_text ) ; ?>" name="final">
	<input type="submit" name="Submit" value="Download">
	</div>
<?php
					echo $final_str ;
?>										
	</form>
<?php					
					do_html_footer( &$err_message );					
				}
				exit ;				
			}
			else
			{
				do_html_header("Installation Failed" , &$err_message ) ;			
				$err_message .= " Installation halted at executing \"UpdateFileField\". <br>\n" ;
				$err_message .= $db -> ErrorNo() . ": " . $db -> ErrorMsg() . " <br>\n" ;		
				return ;	
			}		
		}
		else
		{
			do_html_header("Installation Failed" , &$err_message ) ;							
			$err_message .= " Could not register admin account into database. <br>\n ";	// Exception has occurred
			$err_message .= $db -> ErrorNo() . ": " . $db -> ErrorMsg() . " <br>\n" ;		
			return ;			
		}				
	}
	else
	{
		do_html_header("Installation Failed" , &$err_message ) ;	
		$err_message .= " Installation halted at opening of SQL file. <br>\n" ;
		return ;		
	}
}
else
{
	do_html_header("Installation Failed" , &$err_message ) ;			
	$err_message .= " Installation halted at locating of SQL file. <br>\n" ;
	return ;
}

?>