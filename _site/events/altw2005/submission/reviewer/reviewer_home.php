<?php //////////// REVIEWER PHASE 2 ///////////////
//////////// REVIEWER PHASE 3 ///////////////	

$php_root_path = ".." ;
$privilege_root_path = "/reviewer" ;
//extract ( $HTTP_GET_VARS , EXTR_REFS ) ;
//extract ( $HTTP_POST_VARS , EXTR_REFS ) ;
//extract ( $HTTP_POST_FILES , EXTR_REFS ) ;

//require_once("$php_root_path/includes/output_fns.php");
//require_once("$php_root_path/includes/user_authen_fns.php");
//require_once("$php_root_path"."$privilege_root_path/phase/include_all_phase.php");
require_once("includes/include_all_fns.php");	

session_start() ;
// extract ( $_SESSION , EXTR_REFS ) ;
$header = "Reviewer Home" ;
$accepted_privilegeID_arr = array ( 2 => "" ) ;
$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;	

$err_message = " Unable to process your request due to the following problems: <br>\n" ;

do_html_header("Reviewer Home" , &$err_message );

?>
<p>Welcome back, <?php echo $_SESSION["valid_user"] ; ?>!<br></p>
<?php 
switch ( $_SESSION["phase"]->phaseID )
{
	case 2:
	{
		if ( ( $selresult = get_result_of_paperid_selected( $_SESSION["valid_user"] , $GLOBALS["DB_PREFIX"] , &$err_message ) ) === NULL )
		{
			do_html_header("View Reviewer Home Failed" , &$err_message );	
			$err_message .= " Could not execute \"get_result_of_paperid_selected\" in \"reviewer_home.php\". <br>\n" ;
			$err_message . "<br><br> Try <a href='reviewer_home.php'>again</a>?" ;
			do_html_footer( &$err_message );
			exit;			
		}			
	
	
		if ( ( $result = get_result_of_paperid_not_selected( &$err_message ) ) !== NULL )			
		{
			if ( $result -> RecordCount() > 0 )
			{
				$stat = "" ;
				if ( $selresult -> RecordCount() == 0 )
				{
					$stat = "All" ;
				} else 
				{
					$stat = "More" ;
				}
?>
			<a href="<?php echo "$php_root_path"."$privilege_root_path" ; ?>/bid_all_papers.php">Bid <?php echo $stat ; ?> Papers</a><br><br>
<?php 	
				
			}	
			
		}
		else
		{
//			$homepage->showmenu = 0 ;
			do_html_header("View Reviewer Home Failed" , &$err_message );	
			$err_message .= " Could not execute \"get_result_of_paperid_not_selected\" in \"reviewer_home.php\". <br>\n" ;
			$err_message . "<br><br> Try <a href='reviewer_home.php'>again</a>?" ;
			do_html_footer( &$err_message );
			exit;
		}		
		
		if ( $selresult -> RecordCount() > 0 )
		{
?>	
			<a href="<?php echo "$php_root_path"."$privilege_root_path" ; ?>/edit_paper_bids.php">View My Bids </a>
<?php 		
		}
		break ;
	}
	case 3:
	{
?>
		<a href="view_assigned_papers.php">View Assigned Papers</a>
<?php 
		break ;
	}
	default:
	{
		break ;
	}	
}

do_html_footer(&$err_message);

?>
