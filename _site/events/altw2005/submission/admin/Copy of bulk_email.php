<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	do_html_header("Mass Email");
	

	//Check whether the array is registered at the session
	if (session_is_registered_register_global_off ( "arrEmailInfo" ))
			$arrEmailInfo =  $_SESSION["arrEmailInfo"];
	
?>
<form enctype="multipart/form-data" action="confirm_bulk_email.php" method="post" name="form1">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td width="15%">* Recipients:</td>
      <td width="85%"><select name="to">
	      <option value="members" <?php if($arrEmailInfo["to"] == "members") echo "selected"; else echo "selected";  ?>>Everyone</option>
          <option value="User" <?php if($arrEmailInfo["to"] == "User") echo "selected";  ?>>Users</option>
          <option value="Reviewer" <?php if($arrEmailInfo["to"] == "Reviewer") echo "selected";  ?>>Reviewers</option>
          <option value="Administrator" <?php if($arrEmailInfo["to"] == "Administrator") echo "selected";  ?>>Administrators</option>
		  <option value="none">----------</option>
          <option value="accepted_owners">Accepted Papers Owners</option>
          <option value="rejected_owners">Rejected Papers Owners</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>Cc:</td>
      <td><input name="cc" type="text" id="cc" size="30" maxlength="30" value="<?php echo $arrEmailInfo["cc"]; ?>"></td>
    </tr>
    <tr> 
      <td>Bcc:</td>
      <td><input name="bcc" type="text" id="bcc" size="30" maxlength="30" value="<?php echo $arrEmailInfo["bcc"]; ?>"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>* Subject:</td>
      <td><input name="subject" type="text" id="subject" size="40" maxlength="50" value="<?php echo stripslashes($arrEmailInfo["subject"]); ?>"></td>
    </tr>
    <tr> 
      <td>Attachment:</td>
      <td><input name="file" type="file" value="<?php echo $arrEmailInfo["file"]; ?>" size="30"></td>
    </tr>
    <tr> 
      <td>* Priority</td>
      <td><select name="priority" id="priority">
          <option value="1" <?php if($arrEmailInfo["priority"] == 1) echo "selected";  ?>>Urgent</option>
          <option value="3" <?php if($arrEmailInfo["priority"] == 3) echo "selected";  ?>>Normal</option>
        </select></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p>* Message:</p>
        <p> 
          <textarea name="content" cols="80" rows="25" id="content"><?php echo stripslashes($arrEmailInfo["content"]); ?></textarea>
        </p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit" value="E-mail">
        <input name="Submit" type="submit" id="Submit" value="Reset"> </td>
    </tr>
  </table>
</form>
<?php 	do_html_footer(); ?>
