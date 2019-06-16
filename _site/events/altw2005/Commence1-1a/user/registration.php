<?php
$php_root_path = ".." ;
$privilege_root_path = "/user" ;
require_once("$php_root_path/includes/include_all_fns.php");
require_once("$php_root_path/includes/page_includes/page_fns.php");
require_once("$php_root_path"."/admin/includes/libmail.php");

$error_array = array() ;
$exempt_array = get_user_details_form_exemptions();
$err_message = " Unable to process your request due to the following problems: <br>\n" ;
$homepage->showmenu = 0 ;

check_form( $HTTP_POST_VARS , $error_array , &$exempt_array ) ;

if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
{
	$db = adodb_connect ( &$err_message );
	if (!$db)
	{
		do_html_header("User Account Setup Failed" , &$err_message );	
    	$err_message .= "Could not connect to database server - please try later. <br>\n";
		$err_message .= "<br><br> Try <a href='registration.php'>again</a>?<br>Go to the <a href='$php_root_path/index.php'>Login</a> page" ; 		
	}

	$reg_result = register($HTTP_POST_VARS["username"],$HTTP_POST_VARS, $GLOBALS["DB_PREFIX"] , &$err_message );

	if ( $reg_result )
	{	
		 // provide link to members page
		 do_html_header("User Account Setup Successful" , &$err_message );
		 echo "<br><br>User Account Setup was successful. Your password will be sent to your e-mail account. <br><br>Go to the <a href='$php_root_path/index.php'>Login</a> page ";
		 do_html_footer( &$err_message );
		 exit ;
	}
	else
	{
		// otherwise provide link back, tell them to try again
		do_html_header("User Account Setup Failed" , &$err_message );
		$err_message .= "<br><br> Try <a href='registration.php'>again</a>?<br>Go to the <a href='$php_root_path/index.php'>Login</a> page" ; 
	}
}
else
{
	do_html_header("User Account Setup" , &$err_message );
    //echo dump_array($error_array, TRUE);
}
?>

<form name="frmRegister" method="post" action="registration.php">
<!--<form name="frmRegister" method="post" action="phpinfo.php">-->        
  
<br><br>
<table width="100%" border="0" cellspacing="3" cellpadding="0">
    <tr> 
      <td colspan="2"><p><strong> Hint: </strong> 
          Use your email address as your user name - it is unique and easy to remember when you log in.
    	  Fields that have an asterix * are mandatory.
          After account setup, your username and password 
          will be e-mailed to your primary email address.</p></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td width="17%"><strong>User Name *<br> (or Email)</strong></td>
      <td width="83%"> <input name="username" type="text" id="username" value="<?php echo $HTTP_POST_VARS["username"] ; ?>" size="20" maxlength="30"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["username"][0] . "</font>" ; ?> 
      </td>
    </tr>
    <?php get_user_details_form($HTTP_POST_VARS, $error_array) ?>
    <tr> 
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">&nbsp;</td>
      <td><input type="submit" name="submit" value="Submit"> &nbsp; <input type="reset" name="reset" value="Reset"></td>
    </tr>
  </table>
     </form>
<?php

do_html_footer( &$err_message );

?>
