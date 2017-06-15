<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;

	require_once("includes/include_all_fns.php");	
	session_start() ;
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$accountType = $HTTP_GET_VARS["accountType"];
	
	//Retrieve the session info if any
	if(isset($_SESSION["arrLoginInfo"]))
		$arrLoginInfo = & $_SESSION["arrLoginInfo"];
	
	$title = $HTTP_GET_VARS["accountType"]." Account Setup";
	do_html_header($title);
	
?>
<form name="form1" method="post" action="confirm_setup_account.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2"><strong><?php echo $accountType; ?> Login Information</strong></td>
    </tr>
    <tr> 
      <td colspan="2"><input type="hidden" name="accountType" value="<?php echo $accountType; ?>"></td>
    </tr>
    <tr> 
      <td width="25%">Login Name:</td>
      <td width="75%"><input name="loginname" type="text" id="loginname" size="25" maxlength="50" value="<?php if (isset($_SESSION["arrLoginInfo"])) echo $arrLoginInfo["loginname"];?>"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>First Name:</td>
      <td><input name="firstname" type="text" id="firstname" size="25" maxlength="30" value="<?php if (isset($_SESSION["arrLoginInfo"])) echo $arrLoginInfo["firstname"];?>"></td>
    </tr>
    <tr>
      <td>Middle Name:</td>
      <td><input name="middlename" type="text" id="middlename" size="25" maxlength="30" value="<?php if (isset($_SESSION["arrLoginInfo"])) echo $arrLoginInfo["middlename"];?>"></td>
    </tr>
    <tr> 
      <td>Last Name:</td>
      <td><input name="lastname" type="text" id="lastname" size="25" maxlength="30" value="<?php if (isset($_SESSION["arrLoginInfo"])) echo $arrLoginInfo["lastname"];?>"></td>
    </tr>
    <tr> 
      <td>Email Address:</td>
      <td><input name="email" type="text" id="email" size="30" maxlength="50" value="<?php if (isset($_SESSION["arrLoginInfo"])) echo $arrLoginInfo["email"];?>"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="informreviewer" type="checkbox" id="informreviewer" value="yes">
        inform the user now</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit" value="Submit"> <input name="Submit" type="submit" id="Submit" value="Cancel"> 
      </td>
    </tr>
  </table>
</form>
<?php do_html_footer();
?>
