<?php
	
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	do_html_header("Add New Category");
	
?>
<form name="form1" method="post" action="process_add_new_category.php">
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
    <tr> 
      <td width="15%">Category Name:</td>
      <td width="85%"><input name="catName" type="text" id="catname" size="40" maxlength="50"></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit2" value="Submit">
        <input name="Submit" type="submit" id="Submit2" value="Cancel"></td>
    </tr>
    <tr> 
      <td colspan="2"><hr></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <?php 
			//Call the function that display the categories
			echo display_category_table();
		?>
      </td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
</form>
