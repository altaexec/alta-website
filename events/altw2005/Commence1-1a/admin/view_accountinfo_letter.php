<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	switch($HTTP_POST_VARS["lettertype"]){
		case "useraccount":
			$title = "Preview User Account Info Letter";
			break;
		case "revieweraccount":
			$title = "Preview Reviewer Account Info Letter";			
			break;
		case "adminaccount":
			$title = "Preview Admin Account Info Letter";			
			break;
	}
	
	
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		unset($_SESSION["arrLetterInfo"]);
		header("Location: view_letters.php");
		exit;
	}
	
	//Register the session
	$_SESSION["arrLetterInfo"] = $HTTP_POST_VARS;
	
	do_html_header($title);
	
?>
<form name="form1" method="post" action="process_letter.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td><input type="hidden" name="lettertype" value="<?php echo $HTTP_POST_VARS["lettertype"]; ?>"></td>
    </tr>
    <tr> 
      <td><strong>To:</strong> useremail@domain.com</td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong>&nbsp;<?php echo stripslashes($HTTP_POST_VARS["subject"]); ?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><p><?php echo $HTTP_POST_VARS["salutation"]; ?> 
          <?php if ($HTTP_POST_VARS["username"] == "append") echo " <strong>UserName</strong>"; ?>
        </p></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><p><?php echo nl2br(stripslashes($HTTP_POST_VARS["beforecontent"])); ?></p></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td> <p>Below is Login information:</p>
        <p>User Name: loginName<br>
          Password: ******</p></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><p> <?php echo nl2br(stripslashes($HTTP_POST_VARS["aftercontent"])); ?> 
        </p></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><input type="submit" name="Submit" value="Confrim"> <input name="Submit" type="submit" id="Submit" value="Back"></td>
    </tr>
  </table>
</form>
<?php do_html_footer(); ?>
