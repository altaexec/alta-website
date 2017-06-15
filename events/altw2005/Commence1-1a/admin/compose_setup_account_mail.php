<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;

	require_once("includes/include_all_fns.php");	
	session_start() ;
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Read the sessin from previous page
	if(isset($_SESSION["arrLoginInfo"]))	
		$arrAccountInfo = & $_SESSION["arrLoginInfo"];	
	
	//If the user click Back button,need to read this session
	if(isset($_SESSION["arrAccountInfo"]))	
		$arrAccountInfo = & $_SESSION["arrAccountInfo"];
		
	//Arrange the appropriate letter type
	switch($HTTP_GET_VARS["lettertype"]){
		case "revieweraccount":
			$letterTitle = "Reviewer Account Info";
			$accountType = "Reviewer";			
			break;
		case "adminaccount":
			$letterTitle = "Admin Account Info";
			$accountType = "Administrator";
			break;
	}
	
	do_html_header($letterTitle);
	
	//Check whether the username is already taken
	if(check_User_Account_Exist($arrAccountInfo["loginname"])){
		$url = "setup_new_account.php?accountType=".$arrAccountInfo["accountType"];
		echo "<form method=\"post\" action=\"".$url."\">";
		echo "<p> The login name you have selected is already taken. <br> Go back and select another name. <br><br><input type=\"submit\" name=\"submit\" value=\"Back\">";
		echo "</p>";
		do_html_footer();
		exit;
	
	}	
		
	//Fetch letter information
	$letterInfo = get_LetterInfo_By_Title($letterTitle);
	
	//Get the user email to include at Cc field
	$useremail = getMemberEmail($valid_user);
 
 	//Formate the username and fullname
 	if(isset($arrAccountInfo["middlename"]))
		$fullname = $arrAccountInfo["firstname"]." ".$arrAccountInfo["middlename"]." ".$arrAccountInfo["lastname"];
	else
		$fullname = $arrAccountInfo["firstname"]." ".$arrAccountInfo["lastname"];				
	
?>
<form name="form1" method="post" action="confirm_setup_account_mail.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2"><strong>Login Information</strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="20%"><strong>User Name:</strong></td>
      <td width="80%"> 
        <?php echo $arrAccountInfo["loginname"]; ?>
        <input type="hidden" name="letterID" value="<?php echo $letterInfo -> LetterID; ?>"> 
        <input type="hidden" name="loginname" value="<?php echo $arrAccountInfo["loginname"]; ?>"></td>
    </tr>
    <tr>
      <td><strong>Full Name:</strong></td>
      <td><?php echo $fullname; ?><input type="hidden" name="fullname" value="<?php echo $fullname; ?>"></td>
	  <input type="hidden" name="firstname" value="<?php echo isset($arrLoginInfo["firstname"]) ? $arrLoginInfo["firstname"] : $arrAccountInfo["firstname"];  ?>">
	  <input type="hidden" name="middlename" value="<?php echo isset($arrLoginInfo["middlename"]) ? $arrLoginInfo["middlename"] : $arrAccountInfo["middlename"];  ?>">
	  <input type="hidden" name="lastname" value="<?php echo isset($arrLoginInfo["lastname"]) ? $arrLoginInfo["lastname"] : $arrAccountInfo["lastname"];  ?>">
    </tr>
    <tr> 
      <td><strong>Email Address:</strong></td>
      <td> 
        <?php if (isset($arrLoginInfo["email"])) echo $arrLoginInfo["email"];
			  else if(isset($arrAccountInfo["email"])) echo $arrAccountInfo["email"];
		?>
      </td>
      <input type="hidden" name="email" value="<?php if (isset($arrLoginInfo["email"])) echo $arrLoginInfo["email"];
			  else if(isset($arrAccountInfo["email"])) echo $arrAccountInfo["email"];
		?>">
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong></td>
      <td><input name="subject" type="text" id="subject" size="50" maxlength="250" value="<?php echo isset($arrAccountInfo["subject"]) ?  stripslashes($arrAccountInfo["subject"]) : $letterInfo -> Subject; ?>"></td>
    </tr>
    <tr> 
      <td><strong>Cc:</strong></td>
      <td><input name="cc" type="text" id="cc" size="30" maxlength="30" value="<?php if(!empty($arrLetterInfo["cc"])) echo $arrLetterInfo["cc"]; else echo $useremail;?>"></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
        <tr> 
      <td colspan="2"><strong>$confname</strong> - Conference Name (Eg: Workshop 
        on Digital Image Computing)<br>
        <strong>$confcode</strong> - Conference Code Name (Eg: WDIC2003)<br> <strong>Note:</strong> These two constants 
        are available at both Subject and Body field.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>Subject:</strong>&nbsp;&nbsp; <input name="subject" type="text" id="subject" size="50" maxlength="250" value="<?php echo !empty($arrLetterInfo["subject"]) ?  stripslashes($arrLetterInfo["subject"]) : $letterInfo -> Subject; ?>"></td>
      <input type="hidden" name="lettertype" value="<?php echo $HTTP_GET_VARS["lettertype"]; ?>">
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>$fullname</strong> - User Fullname (Eg: John 
          Smith)<br>
          <strong>$username</strong> - User login name (Eg: johnny)<br>
          <strong>$password</strong> - Login password <br>
          <strong>$url</strong> - Web site URL (address) to login (Eg: http://www.webcomments.com 
          )<br>
          <strong>$contact</strong> - Email of secretariat </p></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>Body:</strong></p>
        <p> 
          <textarea name="bodycontent" cols="80" rows="25" id="bodycontent"><?php echo isset($arrLetterInfo["bodycontent"]) ? stripslashes($arrLetterInfo["bodycontent"]) : stripslashes($letterInfo ->  BodyContent); ?></textarea>
        </p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="hidden" name="accountType" value="<?php echo $accountType; ?>">
        <input type="submit" name="Submit" value="Submit"> <input name="Submit" type="submit" id="Submit" value="Back"> 
      </td>
    </tr>
  </table>
</form>
<?php do_html_footer();
?>
