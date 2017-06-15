<?php
	
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$registerID = &$HTTP_GET_VARS["registerID"];
	
	do_html_header("Delete Reviewer");

	//Call the function to get the reviewer information
	$editReviewerInfo = get_reviewer_info($registerID);


?>
<form name="form1" method="post" action="process_delete_reviewer.php">
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
    <tr> 
      <td colspan="2">Here are the contact details of the reviewer who is going to be 
        deleted. Press confirm to proceed.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <input type="hidden" name="registerID" value="<?php echo $editReviewerInfo -> RegisterID; ?>">
    </tr>
    <tr> 
      <td width=20%>Reviewer Name:</td>
      <td><?php echo $editReviewerInfo -> FirstName . " " . $editReviewerInfo -> LastName;?> </td>
    </tr>
    <tr> 
      <td>Organisation:</td>
      <td><?php echo ($editReviewerInfo -> Organisation ? $editReviewerInfo -> Organisation : "N/A");?> </td>
    </tr>
    <tr> 
      <td>Address:</td>
      <td><?php echo $editReviewerInfo -> Address1 . "<br>" . $editReviewerInfo -> Address2;?> </td>
    </tr>
    <tr> 
      <td>City:</td>
      <td><?php echo $editReviewerInfo -> City;?> </td>
    </tr>
    <tr> 
      <td>Post Code:</td>
      <td><?php echo $editReviewerInfo -> PostCode;?> </td>
    </tr>
    <tr> 
      <td>Country:</td>
      <td><?php echo $editReviewerInfo -> Country;?> </td>
    </tr>
    <tr> 
      <td>Email:</td>
      <td><?php echo $editReviewerInfo -> Email;?> </td>
    </tr>
    <tr> 
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="Submit2" type="submit" id="Submit2" value="Confirm"> 
        <input name="Submit" type="submit" id="Submit3" value="Cancel"></td>
    </tr>
    <tr> 
      <td colspan="2"><hr></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
</form>
