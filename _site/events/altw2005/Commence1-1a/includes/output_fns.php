<?php global $php_root_path ;
global $privilege_root_path ;

require ( "$php_root_path/includes/page_includes/page.php" ) ;

$homepage = new Page() ;

//Header function
function do_html_header($title , $err_message = "" )
{
  // print an HTML header
  
	if($title)
		do_html_heading($title);	
		
	global $homepage ;
	global $php_root_path ;
	
	$homepage->DisplayHeader ( &$err_message ) ;  	
}

//Footer function
function do_html_footer( $err_message = "" )
{
	// print an HTML footer
	global $homepage ;
	if ( $err_message != " Unable to process your request due to the following problems: <br>\n" )
		$homepage->DisplayFooter ( &$err_message ) ;
	else
		$homepage->DisplayFooter() ;
}

//Heading function
function do_html_heading($heading)
{
	// print heading
	global $homepage ;
	$homepage->SetHeader ( $heading ) ;
}

function session_register_register_global_off ( $name )
{
	global $$name ;
	//global $_SESSION ;	
//	echo "<br>\n\$name= " . $name . "<br>\n" ;
//	echo "<br>\n\$\$nameme= " . $$name . "<br>\n" ;
	if ( isset ( $$name ) )
	{
		$_SESSION[$name] = $$name ;
	}
	else
	{
		$_SESSION[$name] = array() ;		
	}
}

function session_unregister_register_global_off ( $name )
{
	global $$name ;
	//global $_SESSION ;	
//	echo "<br>\n\$name= " . $name . "<br>\n" ;
//	echo "<br>\n\$\$nameme= " . $$name . "<br>\n" ;	
	unset ( $_SESSION[$name] ) ;
	return ( !isset ( $_SESSION[$name] ) ) ;
}

function session_is_registered_register_global_off ( $name )
{
	global $$name ;
	//global $_SESSION ;
//	echo "<br>\n\$name= " . $name . "<br>\n" ;
//	echo "<br>\n\$\$nameme= " . $$name . "<br>\n" ;	
	return ( isset ( $_SESSION[$name] ) ) ;
}

?>
