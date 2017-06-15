<?php

$php_root_path = "." ;

// require_once("$php_root_path/includes/include_all_fns.php");
require_once("$php_root_path/includes/output_fns.php");
require_once("$php_root_path/includes/main_fns.php");

session_start();
global $homepage ;	
$err_message = " Unable to process your request due to the following problems: <br>\n" ;

if (isset($_SESSION["real_user"]))   // Check for su to admin account
{    
    $_SESSION["valid_user"] = $_SESSION["real_user"] ;
    unset($_SESSION["real_user"]) ;
    $str = "Location: $php_root_path/admin/admin_home.php";
    header( $str ); // Redirect browser
    exit;
}	


$old_user = $_SESSION["valid_user"] ;  // store  to test if they *were* logged in
unset ( $_SESSION["valid_user"] ) ;
unset ( $_SESSION ) ;
$result_dest = session_destroy();

// start output html

if (!empty($old_user))
{
  if ( !isset( $_SESSION ) && $result_dest )
  {
    // if they were logged in and are now logged out
	$homepage->showmenu = 0 ;	
	do_html_header("Logging Out Successful" , &$err_message );
    echo "Logged out.<br>";
  	echo  "<br><br><a href='$php_root_path/index.php'>Login</a> again?";
  }
  else
  {
   // they were logged in and could not be logged out
	$homepage->showmenu = 0 ;   
	do_html_header("Logging Out Failed", &$err_message );   
    $err_message .= " Could not log you out.<br>\n" ;
	$err_message .= "<br><br> Please <a href='$php_root_path/index.php'>Login</a>." ;
  }
}
else
{
  // if they weren't logged in but came to this page somehow
	$homepage->showmenu = 0 ;  
	do_html_header("Logging Out Failed", &$err_message);  
  $err_message .= " You were not logged in, and so have not been logged out.<br>\n";
  $err_message .= "<br><br> Please <a href='$php_root_path/index.php'>Login</a>." ;
}

do_html_footer( &$err_message );

?>
