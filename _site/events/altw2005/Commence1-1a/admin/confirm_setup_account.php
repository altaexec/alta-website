<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;

	require_once("includes/include_all_fns.php");	
	session_start() ;
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
		
	//User click on Cancel button
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		unset($_SESSION["arrLoginInfo"]);
		header("Location: admin_home.php");
		exit;	
	}
	
	//Register the session	
	$_SESSION["arrLoginInfo"] = $HTTP_POST_VARS;
	
	switch($HTTP_POST_VARS["accountType"]){
		case "Reviewer":
			$letterType = "revieweraccount";
			$title = "Reviewer Account Setup";
			break;
		case "Administrator":
			$letterType = "adminaccount";
			$title = "Admin Account Setup";
			break;
	}
	
	//User Click on submit button
	if($HTTP_POST_VARS["informreviewer"] == "yes"){
	
		$url = "Location: compose_setup_account_mail.php?lettertype=".$letterType;
		header($url);
		exit;
	}
	
	do_html_header($title);
	
	//Check whether the username is already taken
	if(check_User_Account_Exist($HTTP_POST_VARS["loginname"])){
		$url = "setup_new_account.php?accountType=".$HTTP_POST_VARS["accountType"];
		echo "<form method=\"post\" action=\"".$url."\">";
		echo "<p> The login name you have selected is already taken. <br> Go back and select another name. <br><br><input type=\"submit\" name=\"submit\" value=\"Back\">";
		echo "</p>";
		do_html_footer();
		exit;
	
	}
	
?>
		
<!--Display the information to confirm-->
<form name="form1" method="post" action="process_setup_account.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="2">Below is the information that you have entered for Reviewer 
        Account. Click Confirm to confirm your request.</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>Login Information</strong></td>
    </tr>
    <tr> 
      <td colspan="2"><input type="hidden" name="accountType" value="<?php echo $HTTP_POST_VARS["accountType"]; ?>"></td>
    </tr>
    <tr> 
      <td width="25%">Login Name:</td>
      <td width="75%"><strong><?php echo $HTTP_POST_VARS["loginname"]; ?></strong></td>
      <input type="hidden" name="loginname" value="<?php echo $HTTP_POST_VARS["loginname"]; ?>">
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>First Name:</td>
      <td><?php echo $HTTP_POST_VARS["firstname"]; ?></td>
	  <input type="hidden" name="firstname" value="<?php echo $HTTP_POST_VARS["firstname"]; ?>">
    </tr>
	<?php if (!empty($HTTP_POST_VARS["middlename"])) { ?>
    <tr>
      <td>Middle Name:</td>
      <td><?php echo $HTTP_POST_VARS["middlename"]; ?></td>
	  <input type="hidden" name="middlename" value="<?php echo $HTTP_POST_VARS["middlename"]; ?>">
    </tr>
	<?php } ?>
    <tr>
      <td>Last Name:</td>
      <td><?php echo $HTTP_POST_VARS["lastname"]; ?></td>
	  <input type="hidden" name="lastname" value="<?php echo $HTTP_POST_VARS["lastname"]; ?>">
    </tr>
    <tr> 
      <td>Email Address:</td>
      <td><?php echo $HTTP_POST_VARS["email"]; ?></td>
      <input type="hidden" name="email" value="<?php echo $HTTP_POST_VARS["email"]; ?>">
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="Submit" type="submit" id="Submit" value="Confirm"> 
        <input type="submit" name="Submit" value="Back"></td>
    </tr>
  </table>
</form>	
	
<?php do_html_footer();?>
