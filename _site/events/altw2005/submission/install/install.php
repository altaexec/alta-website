<?php 

$php_root_path = ".." ;
require_once("$php_root_path/includes/include_all_fns.php");
require_once("$php_root_path/includes/page_includes/page_fns.php");

$err_message = " Unable to process your request due to the following problems: <br>\n" ;
$homepage->showmenu = 0 ;

if ( $HTTP_POST_VARS["Submit"] == "Download" && $HTTP_POST_VARS["final"] )
{
	// the next three lines force an immediate download of the zip file: 
//	header("Content-type: application/octet-stream");  
	header('Content-Type: text/x-delimtext; name="preferences.php"');	 
	header('Content-disposition: attachment; filename="preferences.php"');
	echo stripslashes ( $HTTP_POST_VARS["final"] ) ;
	exit ;
}

$error_array = array() ;
$exempt_array = array_merge( get_user_details_form_exemptions() , array ( "DB_PREFIX" , "DB_PASSWORD" ) );
check_form ( $HTTP_POST_VARS , $error_array , &$exempt_array ) ;

if ( $HTTP_POST_VARS["pwdConfirm"] != $HTTP_POST_VARS["password"] )
{
	$error_array["password"][] = " Your new password and confirmation password are inconsistent. <br>\n" ;
	$error_array["pwdConfirm"][] = " Your new password and confirmation password are inconsistent. <br>\n" ;
	do_html_header("Setup Database" , &$err_message );		
}
else
{
//	echo "<br>\ncount: " . count ( $error_array ) . "<br>\n" ;
	if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
	{
//		echo "<br>\nBOLD First<br>\n" ;
//		$link = mysql_connect($HTTP_POST_VARS["db_hosdtname"], $HTTP_POST_VARS["db_username"], $HTTP_POST_VARS["db_pwd"])
//        	or die("Could not connect");
//		exit ;
		include '../install/process_install.php' ;
//		echo "<br>\nBOLD Last<br>\n" ;		
	}
	else
	{	
		do_html_header("Installation of COMMENCE System" , &$err_message );	
	}
}

/*
if ( count ( $HTTP_POST_VARS ) > 0 )
{
	include ( "process_install.php" ) ;
}
else
{
	do_html_header("Setup Database");	
}
*/
?>
<form name="form1" method="post" action="install.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2"><strong>Database Configuration</strong></td>
    </tr>
    <tr> 
            <td>&nbsp;</td>
    </tr>
    <tr> 
      <td width="30%">Database Server Hostname / DSN *</td>
      <td width="70%"><input name="DB_HOSTNAME" type="text" id="DB_HOSTNAME" value="<?php echo $HTTP_POST_VARS["DB_HOSTNAME"] ; ?>" size="30" maxlength="50"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["DB_HOSTNAME"][0] . "</font>" ; ?></td>
    </tr>
    <tr> 
      <td>Your Database Name *</td>
      <td><input name="DB_DATABASE" type="text" id="DB_DATABASE" value="<?php echo $HTTP_POST_VARS["DB_DATABASE"] ; ?>" size="20" maxlength="30"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["DB_DATABASE"][0] . "</font>" ; ?></td>
    </tr>
    <tr> 
      <td>Database Username *</td>
      <td><input name="DB_USERNAME" type="text" id="DB_USERNAME" value="<?php echo $HTTP_POST_VARS["DB_USERNAME"] ; ?>" size="20" maxlength="30"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["DB_USERNAME"][0] . "</font>" ; ?></td>
    </tr>
    <tr> 
      <td>Database Password </td>
      <td><input name="DB_PASSWORD" type="password" id="DB_PASSWORD" value="<?php echo $HTTP_POST_VARS["DB_PASSWORD"] ; ?>" size="20" maxlength="20"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["DB_PASSWORD"][0] . "</font>" ; ?></td>
    </tr>
    <tr> 
      <td>Prefix for tables in database </td>
      <td><input name="DB_PREFIX" type="text" id="DB_PREFIX" value="<?php echo $HTTP_POST_VARS["DB_PREFIX"] ; ?>" size="20" maxlength="20"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["DB_PREFIX"][0] . "</font>" ; ?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>Admin Configuration</strong></td>
    </tr>
    <tr> 
            <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>Administrator Username *</td>
      <td><input name="username" type="text" id="username" value="<?php echo $HTTP_POST_VARS["username"] ; ?>" size="20" maxlength="30"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["username"][0] . "</font>" ; ?></td>
    </tr>
    <tr> 
      <td>Administrator Password *</td>
      <td><input name="password" type="password" id="pwd" value="<?php echo $HTTP_POST_VARS["password"] ; ?>" size="20" maxlength="15"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["password"][0] . "</font>" ; ?></td>
    </tr>
    <tr> 
      <td>Administrator Password *<br /> [Confirm] </td>
      <td><input name="pwdConfirm" type="password" id="pwdConfirm" value="<?php echo $HTTP_POST_VARS["pwdConfirm"] ; ?>" size="20" maxlength="15"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["pwdConfirm"][0] . "</font>" ; ?></td>
    </tr>
    <?php get_user_details_form($HTTP_POST_VARS, $error_array) ?>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr> 
      <td>Overwrite existing tables:</td>
      <td><input type="checkbox" name="overwriteExistingTables" value="false"></td>
    </tr>
    <tr> 
      <td><input type="submit" name="Submit" value="Start Install"></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php do_html_footer( &$err_message ) ; ?>