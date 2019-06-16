<?php
	
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$catID = &$HTTP_GET_VARS["catID"];
	
	do_html_header("Delete Category");

	//Call the funtion to get the category information
	$editCatInfo = get_category_info($catID);


?>
<form name="form1" method="post" action="process_delete_category.php">
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
    <tr> 
      <td colspan="2">Below is the information of category that is going to be 
        deleted. Press confirm to proceed.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="20%">Category ID:</td>
      <td width="80%"><?php echo $editCatInfo -> CategoryName; ?></td>
      <input type="hidden" name="catID" value="<?php echo $editCatInfo -> CategoryID; ?>">
    </tr>
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
