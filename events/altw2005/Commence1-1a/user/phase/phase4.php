<?php

global $php_root_path ;
require_once ("$php_root_path/user/phase/phasebase.php") ;

class phase4 extends phasebase
{
	function phase4()
	{
		$this->phaseID = 4 ;
		$this->records = array() ;		
		$this->headers = array() ;		
	}

	function display_header($title , $err_message="" )
	{
		do_html_header($title , &$err_message );	
	}

	function display_menu( &$header_str , $err_message = "" )
	{
		global $php_root_path ;
		echo $header_str ;
?>	
		<td><a href="view_papers.php">View Papers</a></td>
		<td>|</td>
		<td><a href="edit_details.php">Edit Personal Info</a></td>
		<td>|</td>
		<td><a href="change_pwd.php">Change Password</a></td>
		<td>|</td>
<?php
		//Retrieve the setting information
		$settingInfo = get_Conference_Settings();
		if ($settingInfo -> RegistrationEnabled) {		
?>
		<td><a href="payment_forms.php">Register</a></td>
		<td>|</td>
<?php } ?>
		<td><a href="<?php echo $php_root_path ; ?>/logout.php">Logout</a></td>				
<?php				
	}

	function get_edit_paper_info_sql( &$paperid )
	{
		//global $_SESSION ;
				
		//Execute the retrieve of written table
		$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Written W , " . $GLOBALS["DB_PREFIX"] . "Paper P";
		$sql .=" WHERE W.PaperID=" . $paperid . " AND W.PaperID=P.PaperID AND P.Withdraw='false' AND P.PaperStatusID IN ( 3 , 4 ) AND P.MemberName='" . addslashes ( $_SESSION["valid_user"] ) . "'" ;
		return $sql ;
	}


	function set_view_paper_details( &$fileid , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
        //global $_SESSION ;
		
		$papersql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "File F , " . $GLOBALS["DB_PREFIX"] . "Paper P , " . $GLOBALS["DB_PREFIX"] . "PaperStatus PS";
		$papersql .= " WHERE F.FileID = $fileid AND P.MemberName = '" . addslashes ( $_SESSION["valid_user"] ) . "' AND P.PaperStatusID = PS.PaperStatusID AND P.PaperStatusID IN ( 3 , 4 ) AND P.PaperID = F.PaperID AND P.Withdraw='false'" ;
		// echo "\$papersql: " . $papersql . "<br>\n" ;	// Debug
		
		//Execute the retrieving of paper table
		$this -> paperResult = $db -> Execute($papersql);
		$rows = $this -> paperResult -> RecordCount() ;
		
		if ( !$this->paperResult )
		{
			do_html_header("View Paper Details Failed" , &$err_message );
			$err_message .= " There is error reading paper table. <br>\n";
			$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?" ;
			do_html_footer( &$err_message );	
			exit ;
		}
		else if ( !$rows )
		{
			do_html_header("View Paper Details Failed" , &$err_message );
			$err_message .= " The requested infomation is not available. <br>\n";
			$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?" ;
			do_html_footer( &$err_message );	
			exit ;
		}
		
	}

	function set_view_paper( $err_message = "" )
	{
		global $sort ;
		global $showing ;
		global $totalPapers ;
		global $rows ;
		
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
//		$paperSQL = "SELECT DISTINCT ( P.PaperID ) , P.Title , PS.PaperStatusName FROM " . $GLOBALS["DB_PREFIX"] . "Paper P , " . $GLOBALS["DB_PREFIX"] . "PaperStatus PS , " . $GLOBALS["DB_PREFIX"] . "File F";
//		$paperSQL .= " WHERE P.PaperStatusID = 3 AND P.PaperStatusID = PS.PaperStatusID AND P.PaperID = F.PaperID AND MemberName = '" . addslashes ( &$_SESSION["valid_user"] ) . "' AND Withdraw='false'";

		$paperSQL = "SELECT P.PaperID , P.Title , PS.PaperStatusName , MAX( F.DateTime ) AS DateTime " ;
		$paperSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "File F , " . $GLOBALS["DB_PREFIX"] . "Paper P , " . $GLOBALS["DB_PREFIX"] . "PaperStatus PS " ;
		$paperSQL .= " WHERE P.PaperStatusID IN ( 3 , 4 ) AND P.PaperStatusID=PS.PaperStatusID AND P.PaperID=F.PaperID AND P.MemberName = '" . addslashes ( &$_SESSION["valid_user"] ) . "' AND P.Withdraw='false' " ;
		$paperSQL .= " GROUP BY F.PaperID " ;
		
		$countPaperResult = $db -> Execute( $paperSQL ) ;
	  	if (!$countPaperResult)
		{
			do_html_header("View Papers Failed" , &$err_message ) ;		
			$err_message .= "Could not query View Paper database. <br>\n";
			$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			do_html_footer( &$err_message ) ;
			exit ;
		}
		
		$totalPapers = $countPaperResult -> RecordCount() ;		
		
		if ($totalPapers <= 0)
		{	
			$this->display_header("No Accepted Paper" , &$err_message ) ;
			echo $message .= " You do not have any accepted papers yet.<br>\n" ;
			do_html_footer( &$err_message ) ;						
			exit;
		}			
	
		//Check the sorting by Title
		switch($sort)
		{
			case 1:
			{
				$paperSQL .= " ORDER BY Title ASC";
				break;
			}
			case 2:
			{
				$paperSQL .= " ORDER BY Title DESC";
				break;
			}
			case 3:
			{
				$paperSQL .= " ORDER BY DateTime ASC";
				break;
			}
			case 4:
			{
				$paperSQL .= " ORDER BY DateTime DESC";
				break;
			}
			case 5:
			{
				$paperSQL .= " ORDER BY P.PaperID ASC";
				break;
			}			
			case 6:
			{
				$paperSQL .= " ORDER BY P.PaperID DESC";
				break;
			}			
			case 7:
			{
				$paperSQL .= " ORDER BY PS.PaperStatusName ASC";
				break;
			}			
			case 8:	
			{
				$paperSQL .= " ORDER BY PS.PaperStatusName DESC";
				break;
			}				
			default:
				$paperSQL .= " ORDER BY P.PaperID";
				break;							
		}							
			
		$paperSQL .= " LIMIT ".$showing.",".MAX_PAPERS;
		
		$this -> paperResult = $db -> Execute($paperSQL);
		$rows = $this -> paperResult -> RecordCount();
		
		do_html_header("View Papers" , &$err_message );	
	}

	function set_view_headers()
	{
		global $showing ;
		global $php_root_path ;		
		$this->headers = array() ;		
		$this->headers["<a href=\"view_papers.php?sort=5&showing=$showing\"><img src=\"$php_root_path/images/up.gif\" border=0></a>&nbsp;ID&nbsp;<a href=\"view_papers.php?sort=6&showing=$showing\"><img src=\"$php_root_path/images/down.gif\" border=0></a>"] = 8 ;
		$this->headers["<a href=\"view_papers.php?sort=1&showing=$showing\"><img src=\"$php_root_path/images/up.gif\" border=0></a>&nbsp;Title&nbsp;<a href=\"view_papers.php?sort=2&showing=$showing\"><img src=\"$php_root_path/images/down.gif\" border=0></a>"] = 35 ;
		$this->headers["<a href=\"view_papers.php?sort=3&showing=$showing\"><img src=\"$php_root_path/images/up.gif\" border=0></a>&nbsp;Upload Time&nbsp;<a href=\"view_papers.php?sort=4&showing=$showing\"><img src=\"$php_root_path/images/down.gif\" border=0></a>"] = 20 ;
		$this->headers["<a href=\"view_papers.php?sort=7&showing=$showing\"><img src=\"$php_root_path/images/up.gif\" border=0></a>&nbsp;Status&nbsp;<a href=\"view_papers.php?sort=8&showing=$showing\"><img src=\"$php_root_path/images/down.gif\" border=0></a>"] = 10 ;
		$this->headers["View Details"] = 10 ;
		$this->headers["Edit Paper"] = 10 ;
		// Comment out until edit_presentation_info.php is written 
		// $this->headers["Presenter"] = 10;
		reset ( $this->headers ) ;				
	}
	
	function set_view_records ( $err_message = "" )
	{
		reset ( $this->headers ) ;
		$this->records = array() ;							

		global $rows ;
		
		for( $i = 0 ; $i < $rows ; $i++ )
		{		
			$data = $this -> paperResult -> FetchNextObj();
			
			$slot = get_session_slot_info($data -> PaperID);
			$presenterObj = get_presenter_info($slot -> PaperID);
			if ($presenterObj)
			{
				$presenter = get_member_info_with_id($presenterObj -> RegisterID);
			}
			
			if ( $FileData = get_latestFile( $data->PaperID , &$err_message ) )
			{		
				list ( $field ) = each ( $this->headers )	;
				$this->records[$field][$i] = $data->PaperID ;
				list ( $field ) = each ( $this->headers )	;
//				$this->records[$field][$i]["view_file.php?fileid=$FileData->FileID\" target=\"_blank"] = stripslashes($data -> Title) ;
				$this->records[$field][$i]["view_file.php?fileid=$FileData->FileID"] = stripslashes($data -> Title) ;
				list ( $field ) = each ( $this->headers )	;			
				$this->records[$field][$i] = $FileData->DateTime ;
				list ( $field ) = each ( $this->headers )	;			
				$this->records[$field][$i]["show_all_reviews.php?paperid=$data->PaperID"] = $data->PaperStatusName ;
				list ( $field ) = each ( $this->headers )	;			
				$this->records[$field][$i]["view_paper_details.php?fileid=$FileData->FileID"] = "View" ;
				list ( $field ) = each ( $this->headers )	;			
				$this->records[$field][$i]["edit_paper_info.php?paperid=$data->PaperID"] = "Edit" ;
				//list ( $field ) = each ( $this->headers )	;			
				//$this->records[$field][$i]["edit_presentation_info.php?paperid=$data->PaperID"] = ($presenterObj) ? $presenter -> MemberName : "None Selected" ;
			}
			else
			{
				$err_message .= " Error getting file information from File Table <br>\n" ;
				$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			}
	
			reset ( $this->headers ) ;					
		} 	
		reset ( $this->headers ) ;
		reset ( $this->records ) ;							
	}
	
	
	
	function GenerateViewPaperTable()
	{
		global $showing ;
		global $rows ;
		
		reset ( $this->headers ) ;
		reset ( $this->records ) ;						

	?>
		<table width="100%" border="1" cellspacing="3" cellpadding="3">
		  <tr align="center"> 
	<?php		
		foreach ( $this->headers as $name => $width )
		{
	?>
			<td width="<?php echo $width ; ?>%"><STRONG><?php echo $name ; ?></STRONG></td>
	<?php
		}
		
		reset ( $this->headers ) ;		
		
		for( $i = 0 ; $i < $rows ; $i++ )
		{					
			echo "<tr align='center'>" ;
		  	while ( list ( $field ) = each ( $this->headers ) )
			{
				if ( is_array ( $this->records[$field][$i] ) )
				{
					list ( $urlname , $link ) = each ( $this->records[$field][$i] )
	?>				
			<td><a href="<?php echo $urlname ; ?>"><?php echo $link ; ?></a></td>
	<?php				
				}
				else
				{
	?>				
			<td><?php echo $this->records[$field][$i] ; ?></td>
	<?php					
				}
			}			
			reset ( $this->headers ) ;
		}
	?>
		</table>
	<?php		
	reset ( $this->headers ) ;	
	reset ( $this->records ) ;
	
	}	
}

?>
