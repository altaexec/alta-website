<?php

global $php_root_path ;
require_once ("$php_root_path/user/phase/phasebase.php") ;

class phase1 extends phasebase
{
	function phase1()
	{
		$this->phaseID = 1 ;
		$this->records = array() ;		
		$this->headers = array() ;
	}

	function display_menu( &$header_str , $err_message = "" )
	{
		global $php_root_path ;
		phasebase::display_menu ( $header_str , &$err_message ) ;
		echo $header_str ;
?>	
    	<td><a href="upload_paper.php">Add New Paper</a></td>
		<td>|</td>				
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

	function set_view_headers()
	{
		global $showing ;
		global $php_root_path ;
		$this->headers = array() ;		
		$this->headers["<a href=\"view_papers.php?sort=5&showing=$showing\"><img src=\"$php_root_path/images/up.gif\" border=0></a>&nbsp;ID&nbsp;<a href=\"view_papers.php?sort=6&showing=$showing\"><img src=\"$php_root_path/images/down.gif\" border=0></a>"] = 8 ;
		$this->headers["<a href=\"view_papers.php?sort=1&showing=$showing\"><img src=\"$php_root_path/images/up.gif\" border=0></a>&nbsp;Title&nbsp;<a href=\"view_papers.php?sort=2&showing=$showing\"><img src=\"$php_root_path/images/down.gif\" border=0></a>"] = 35 ;
		$this->headers["<a href=\"view_papers.php?sort=3&showing=$showing\"><img src=\"$php_root_path/images/up.gif\" border=0></a>&nbsp;Upload Time&nbsp;<a href=\"view_papers.php?sort=4&showing=$showing\"><img src=\"$php_root_path/images/down.gif\" border=0></a>"] = 25 ;
		$this->headers["<a href=\"view_papers.php?sort=7&showing=$showing\"><img src=\"$php_root_path/images/up.gif\" border=0></a>&nbsp;Status&nbsp;<a href=\"view_papers.php?sort=8&showing=$showing\"><img src=\"$php_root_path/images/down.gif\" border=0></a>"] = 15 ;
		$this->headers["View Details"] = 10 ;
		$this->headers["Edit Details"] = 10 ;			
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
			
			if ( $FileData = get_latestFile( $data->PaperID , &$err_message ) )
			{		
				list ( $field ) = each ( $this->headers )	;
				$this->records[$field][$i] = $data->PaperID ;
				list ( $field ) = each ( $this->headers )	;
//				$this->records[$field][$i]["view_file.php?fileid=$FileData->FileID\" target=\"_blank"] = stripslashes($data -> Title) ;
				$this->records[$field][$i]["view_file.php?fileid=$FileData->FileID\""] = stripslashes($data -> Title) ;
				list ( $field ) = each ( $this->headers )	;			
				$this->records[$field][$i] = $FileData->DateTime ;
				list ( $field ) = each ( $this->headers )	;			
				$this->records[$field][$i] = $data->PaperStatusName ;
				list ( $field ) = each ( $this->headers )	;			
				$this->records[$field][$i]["view_paper_details.php?fileid=$FileData->FileID"] = "View" ;
				list ( $field ) = each ( $this->headers )	;			
				$this->records[$field][$i]["edit_paper_info.php?paperid=$data->PaperID"] = "Edit" ;
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

}

?>
