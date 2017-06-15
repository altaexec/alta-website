<?php
	
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the follow problems: <br>\n" ;
	
	do_html_header("Edit Category");

	$catID =& $HTTP_GET_VARS["catID"];

	//Call the funtion to get the category information
	$editCatInfo = get_category_info($catID);
	
?>
<form name="form1" method="post" action="process_edit_category.php">
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
    <tr> 
      <td width="20%">&nbsp;</td>
      <td width="80%">&nbsp;</td>
      <input type="hidden" name="catID" value="<?php echo $editCatInfo -> CategoryID; ?>">
    </tr>
    <tr> 
      <td>Category Name:</td>
      <td> <input name="catName" type="text" id="catname" size="40" maxlength="50" value="<?php echo $editCatInfo -> CategoryName; ?>"> 
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit2" value="Submit">
        <input name="Submit" type="submit" id="Submit2" value="Undo Changes"></td>
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
