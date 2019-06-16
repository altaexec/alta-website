<?php
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
//	extract ( $_SESSION , EXTR_REFS ) ;	

	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	$header = "Change Password" ;
//	$accepted_privilegeID_arr = array ( 1 => "" ) ;
//	$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
//	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $dbprefix , &$err_message ) ;	
/*	
	if ( !check_valid_user( &$err_message ) )
	{
		//This user is not logged in
		do_html_header("Change Password Failed" , &$err_message ) ;			
		$err_message .= " Sorry, You must login to change your password. <br>\n";
		$err_message .= "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
		do_html_footer( &$err_message );
		exit;
	}		
*/	
	if ( $HTTP_POST_VARS["submit"] == "Cancel" )
	{
		header("Location: admin_home.php") ;
		exit ;
	}
	
	$error_array = array() ;
	$exempt_array = array() ;
	
	check_form ( $HTTP_POST_VARS , $error_array , &$exempt_array ) ;
	
	if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
	{
		if ( change_password ( $_SESSION["valid_user"] , trim ( $HTTP_POST_VARS["oldpwd"] ) , trim ( $HTTP_POST_VARS["newpwd"] ) , trim ( $HTTP_POST_VARS["confirmpwd"] ) , &$err_message ) )
		{
			// provide link to members page
			do_html_header("Change Password Successful" , &$err_message );
			echo "The password has been changed.";
			do_html_footer( &$err_message );
			exit ;
		}
		else
		{
			 // otherwise provide link back, tell them to try again
			do_html_header("Change password failed" , &$err_message );
			$err_message .= "<br><br> Try <a href='change_pwd.php'>Again</a>? <br>\n" ;
		}
	}
	else 
	{
		if ( count ( $HTTP_POST_VARS ) == 0 )
		{	
			do_html_header("Change Password" , &$err_message );		
		}
		else
		{
			do_html_header("Change Password" , &$err_message );		
		}
	}	
?>
<form action="change_pwd.php" name="frmReset" method="post">
  <table width="80%" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td colspan="2"><br>
          <strong> Note: </strong> You may leave this page without changing your password by clicking 
          on any other links in the menu above or by clicking the "Cancel" button below.</p></td>
    </tr>
    <tr> 
      <td width="24%">&nbsp;</td>
      <td width="80%">&nbsp;</td>
    </tr>
    <tr> 
      <td>Old Password:</td>
      <td><input name="oldpwd" type="password" id="oldpwd" size="20" maxlength="15"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["oldpwd"][0] . "</font>" ; ?> 
      </td>
    </tr>
    <tr> 
      <td>New Password:</td>
      <td><input name="newpwd" type="password" id="newpwd" size="20" maxlength="15"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["newpwd"][0] . "</font>" ; ?> 
      </td>
    </tr>
    <tr> 
      <td>Confirm Password:</td>
      <td><input name="confirmpwd" type="password" id="confirmpwd" size="20" maxlength="15"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["confirmpwd"][0] . "</font>" ; ?>	
      </td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td><input name="submit" type="submit" id="submit" value="Submit"> &nbsp; 
        <input name="submit" type="submit" id="cancel" value="Cancel"></td>
    </tr>
  </table>


</form>

<?php

	do_html_footer(&$err_message);

?>
