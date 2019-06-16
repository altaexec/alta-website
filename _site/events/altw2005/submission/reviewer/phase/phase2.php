<?php global $php_root_path ;
global $privilege_root_path ;

require_once ("$php_root_path" . $privilege_root_path . "/phase/phasebase.php") ;

class phase2 extends phasebase
{
	function phase2()
	{
		$this->phaseID = 2 ;
	}
	
	function display_menu( &$header_str , $err_message= "" )
	{		
		global $php_root_path ;	
		global $privilege_root_path ;		
		//Establish connection with database		
		$db = adodb_connect( &$err_message );
		
		//global $_SESSION ;	
			
		if (!$db)
		{
			$err_message .= " Could not connect to database server in \"display_menu\" of class \"phase2\". - please try later. <br>\n" ;
			global $homepage ;
			
			$homepage->showmenu = 0 ;
			do_html_header("Show Menu Failed" , &$err_message );			
			$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			do_html_footer(&$err_message);			
			exit ;
		}		

		if ( ( $reg = verify_Registration_Exist( &$err_message ) ) !== NULL )
		{
			if ( $reg )
			{
				echo $header_str ;
				if ( ( $selresult = get_result_of_paperid_selected ( $_SESSION["valid_user"] , $GLOBALS["DB_PREFIX"] , &$err_message ) ) === NULL )
				{
					$homepage->showmenu = 0 ;
					do_html_header("Show Menu Failed" , &$err_message );	
					$err_message .= " Could not execute \"get_result_of_paperid_selected\" in \"display_menu\" of class \"phase2\". <br>\n" ;
					$err_message .= "<br><br> Try <a href='reviewer_home.php'>again</a>?" ;
					do_html_footer( &$err_message );
					exit;				
				}									
				
				if ( ( $result = get_result_of_paperid_not_selected( &$err_message ) ) !== NULL )			
				{
					if ( $selresult -> RecordCount() == 0 )
					{
						$stat = "All" ;
					} else
					{
						$stat = "More" ;
					}
?>			
		<td><a href="bid_all_papers.php">Bid <?php echo $stat ; ?> Papers</a></td>
		<td>|</td>				
<?php 				
				}
				else
				{
					$homepage->showmenu = 0 ;
					do_html_header("Show Menu Failed" , &$err_message );	
					$err_message .= " Could not execute \"get_result_of_paperid_not_selected\" in \"display_menu\" of class \"phase2\". <br>\n" ;
					$err_message .= "<br><br> Try <a href='reviewer_home.php'>again</a>?" ;
					do_html_footer( &$err_message );
					exit;				
				}
				
				if ( $selresult -> RecordCount() > 0 )
				{
?>
		<td><a href="edit_paper_bids.php">View My Bids</a></td>
		<td>|</td>
<?php
 				}
?>			
<!--		<td><a href="chose_papers.php">Choose Papers To Bid</a></td>
		<td>|</td>	-->
		<td><a href="edit_details.php">Edit Personal Info</a></td>
		<td>|</td>
		<td><a href="<?php echo $php_root_path ; ?>/logout.php">Logout</a></td>		
<?php 		}
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
			$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			do_html_footer(&$err_message);			
			exit ;
		}		
	}
}

?>
