<?php
	$php_root_path = ".." ;
	$privilege_root_path = "/user" ;
	require_once("$php_root_path/includes/include_all_fns.php");
	require_once("$php_root_path"."/admin/includes/libmail.php");	
	$homepage->showmenu = 0 ;

	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$error_array = array() ;			
	
	check_form ( $HTTP_POST_VARS , $error_array ) ;
   
	if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
	{	   
		if ( forget_password( $HTTP_POST_VARS["username"] , &$err_message ) )
		{
			do_html_header("Reseting Password Successful" , &$err_message );		
			echo "Your password has been reset and you will receive a new password to your email address shortly.<br><br>Go to the <a href='$php_root_path/index.php'>Login</a> page" ;
			do_html_footer( &$err_message );
			exit ;				
		}
		else
		{   
			do_html_header("Reseting Password Failed" , &$err_message );
			$err_message .= " <br><br> Try <a href='forget_pwd.php'>Again</a>? <br>Go to the <a href='$php_root_path/index.php'>Login</a> page. <br>\n" ;
		}	   
	}   
	else
	{   
		do_html_header("Reset Password" , &$err_message );
	}

?>
<form action="forget_pwd.php" method="post" name="loginForm" id="loginForm">
  <table width="80%" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="24" colspan="2">Not registered. <a href="registration.php">Sign up for an account.</a></td>
    </tr>
     <tr> 
      <td height="10" colspan="2">Hint: Use your email address as UserName.</td>
    </tr>
    <tr> 
      <td width="20%" height="24">&nbsp;</td>
      <td width="80%" height="24">&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>UserName</strong></td>
      <td><input name="username" type="text" id="username" size="25" value="<?php echo $HTTP_POST_VARS["username"] ; ?>" maxlength="50"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["username"][0] . "</font>" ; ?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Submit"></td>
    </tr>
  </table>
</form>     
<?php

do_html_footer( &$err_message );

?>