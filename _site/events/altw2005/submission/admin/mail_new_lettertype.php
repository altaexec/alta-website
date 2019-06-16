<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	do_html_header("New Formletter");
	
?>
<form name="form1" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td width="20%"><strong>Recipient Type:</strong></td>
      <td width="80%">&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Letter Title:</strong></td>
      <td><input name="title" type="text" id="title" size="40" maxlength="250"></td>
    </tr>
    <tr> 
      <td><strong>Subject:</strong></td>
      <td><input name="subject" type="text" id="subject" size="40" maxlength="250"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>Salutation:</strong></p>
        <p> 
          <input name="salutation" type="text" id="salutation" size="40" maxlength="100">
          <input name="username" type="checkbox" id="username" value="checkbox">
          append reviewer name after salutation</p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>Before Content:</strong></p>
        <p> 
          <textarea name="beforecontent" cols="80" rows="20" id="beforecontent"></textarea>
        </p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p>Dynamice Content: (optional)</p>
        <table width="80%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td colspan="4"><strong>Paper Information:</strong></td>
          </tr>
          <tr> 
            <td width="25%"><input type="checkbox" name="checkbox2" value="checkbox">
              PaperID</td>
            <td width="25%"><input type="checkbox" name="checkbox3" value="checkbox">
              Title</td>
            <td width="25%"><input type="checkbox" name="checkbox4" value="checkbox"> 
            </td>
            <td width="25%"><input type="checkbox" name="checkbox5" value="checkbox">
              PaperStatus</td>
          </tr>
          <tr> 
            <td><input type="checkbox" name="checkbox11" value="checkbox">
              Authors</td>
            <td><input type="checkbox" name="checkbox12" value="checkbox">
              Reviewers</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="4"><strong>User Information:</strong></td>
          </tr>
          <tr> 
            <td><input type="checkbox" name="checkbox6" value="checkbox">
              User Name</td>
            <td><input type="checkbox" name="checkbox7" value="checkbox">
              Previlege</td>
            <td><input type="checkbox" name="checkbox8" value="checkbox">
              Fullname</td>
            <td><input type="checkbox" name="checkbox9" value="checkbox">
              Organisation</td>
          </tr>
          <tr> 
            <td height="23"><input type="checkbox" name="checkbox10" value="checkbox">
              EmailAddress</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><p><strong>After Content:</strong></p>
        <p> 
          <textarea name="textarea" cols="80" rows="20" id="textarea"></textarea>
        </p></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit" value="Submit"> <input name="Submit" type="submit" id="Submit" value="Back">	
        <input name="Submit" type="submit" id="Submit" value="Cancel"></td>
    </tr>
  </table>
</form>
<?php do_html_footer(); ?>
