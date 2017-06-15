<?php

global $php_root_path ;
global $privilege_root_path ;

require_once("$php_root_path/includes/main_fns.php");
require_once("$php_root_path" . $privilege_root_path . "/includes/main_fns.php");
require_once("$php_root_path/includes/output_fns.php");
	
class phasebase
{
	var $phaseID ;

	function display_menu( &$header_str , $err_message= "" )
	{	
		global $valid_user ;
		global $homepage ;
		global $php_root_path ;
		session_start();  // Need to send this before menu is displayed or else get headers already sent error

			//Check whether the user has login to view this page.
		if ( !check_valid_user( &$err_message ) )
		{
			//This user is not login
			$homepage->showmenu = 0 ;	
			do_html_header("Admin Home Entry Failed" , &$err_message ) ;			
			$err_message .= " Sorry, You must login to view this page. <br>\n";
			$err = $err_message . "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
			do_html_footer($err);
			exit;
		}	
		
		//Establish connection with database
		$db = adodb_connect( &$err_message );
        
		// If database connection failed
		if (!$db)
		{
			$homepage->showmenu = 0 ;			
			do_html_header("Connection to Admin Home Failed" , &$err_message );	
			$err_message .= " Could not connect to database server - please try later. <br>\n" ;
			$err = $err_message . "<br><br> Try <a href='admin_home.php'>again</a>?" ;
			do_html_footer( &$err );
			exit;
		}
		
		// If database connection failed when retrieving privileges
		if (!( ( $status = check_privilege_type( 3 , &$err_message ) ) !== false ))
		{
			$homepage->showmenu = 0 ;			
			do_html_header("Admin Home Failed" , &$err_message ) ;	
			$err_message .= " Could not connect to database.<br>\n";
			$err = $err_message . "<br><br> Try <a href='admin_home.php'>again</a>?" ;
			do_html_footer(&$err);		
			exit;	
		}
		
		// If not an admin account
		if ( $status == 0 )
		{
			$homepage->showmenu = 0 ;	
			do_html_header("Admin Home Failed" , &$err_message ) ;	
			$err_message .= " You do not have the authority to access this page.<br>\n";	
			$err = $err_message . "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
			do_html_footer(&$err);		
			exit;
		}
		
		
		// If database failed while verifying the conference has been set up
		if (!( ( $reg = verify_Conference_Exist( &$err_message ) ) !== NULL ))
		{
			$homepage->showmenu = 0 ;			
			do_html_header("Connection to Admin Home Failed" , &$err_message );	
			$err_message .= " Could not connect to database server - please try later. <br>\n" ;
			$err = $err_message . "<br><br> Try <a href='admin_home.php'>again</a>?" ;
			do_html_footer( &$err );
			exit;			
		}
		
		// If conference hasn't been set up, and not about to correct it
		$correction_page = $php_root_path."/admin/edit_conference_info.php";
		$correctingConferenceInfo = strstr($_SERVER["REQUEST_URI"],ltrim($correction_page,"."));
		if (!$reg && !$correctingConferenceInfo )
		{
			$str = "Location: $correction_page" ;
			header( $str ); // Redirect browser
			exit; // Make sure that code below does not get executed when we redirect. 
		}
		
		// If database connection fails while verifying phases are set up
		if (!( ( $ph = verify_ConferencePhase_Set( &$err_message ) ) !== NULL ))
		{
			$homepage->showmenu = 0 ;			
			do_html_header("Connection to Admin Home Failed" , &$err_message );	
			$err_message .= " Could not connect to database server - please try later. <br>\n" ;
			$err = $err_message . "<br><br> Try <a href='admin_home.php'>again</a>?" ;
			do_html_footer( &$err );
			exit;									
		}
		
		// If phases aren't set up
		$correction_page = $php_root_path."/admin/edit_phases.php";
		$correctingPhaseInfo = strstr($_SERVER["REQUEST_URI"],ltrim($correction_page,"."));
		if ( $ph != 0 && !$correctingPhaseInfo && !$correctingConferenceInfo)
		{
			$str = "Location: $correction_page";
			header( $str ); // Redirect browser
			exit; // Make sure that code below does not get executed when we redirect. 												
		}
		
		// If it's just a plain, simple, normal page...
		echo $header_str ;
		echo "<script type='text/javascript'>\n" ;
		echo "function Go(){return}\n" ;
		echo "</script>\n" ;
		echo "<script type='text/javascript' src='script/user_menu.js'></script>\n" ;
		echo "<script type='text/javascript' src='script/menu_com.js'></script>\n" ;
		echo "<noscript>Sorry, your browser does not support this script</noscript>\n" ;						
	}
}

?>