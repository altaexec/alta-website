<?php global $php_root_path ;
global $privilege_root_path ;
	
require_once ("$php_root_path" . $privilege_root_path . "/phase/phasebase.php") ;

class phase3 extends phasebase
{
	function phase3()
	{
		$this->phaseID = 3 ;
	}

	function display_menu( &$header_str , $err_message= "" )
	{		
		global $php_root_path ;	
		global $privilege_root_path ;		
		//Establish connection with database		
		$db = adodb_connect( &$err_message );
			
			
		if (!$db)
		{
			$err_message .= " Could not connect to database server in \"display_menu\" of class \"phase2\". - please try later. <br>\n" ;
			global $homepage ;
			
			$homepage->showmenu = 0 ;
			do_html_header("Show Menu Failed" , &$err_message );			
			$err = $err_message . "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			do_html_footer(&$err);			
			exit ;
		}		

		if ( ( $reg = verify_Registration_Exist( &$err_message ) ) !== NULL )
		{
			if ( $reg )
			{
				echo $header_str ;
?>			
		<td><a href="view_assigned_papers.php">View Assigned Papers</a></td>
		<td>|</td>
		<td><a href="edit_details.php">Edit Personal Info</a></td>
		<td>|</td>
		<td><a href="<?php echo $php_root_path ; ?>/logout.php">Logout</a></td>		
<?php }
			else
			{
				$str = "Location: fillin_reviewer_info.php" ;
				header( $str ); // Redirect browser
				exit; // Make sure that code below does not get executed when we redirect. 					
			}
		}
		else
		{		
			$err_message .= " Cannot retrieve information from database in \"display_menu\" of class \"phase2\". <br>\n" ;
			global $homepage ;

			$homepage->showmenu = 0 ;
			do_html_header("Show Menu Failed" , &$err_message );
			$err = $err_message . "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			do_html_footer(&$err);			
			exit ;
		}					
	}	
}

?>