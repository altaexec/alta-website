<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	do_html_header("View Topic(s)");
?>

<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td>&nbsp;</td>
    <td align="right"><a href="add_new_category.php">Add new topic</a></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2">
      <?php 	
	//Call the function that display the topics
	echo display_category_table();  ?>
    </td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php 	do_html_footer(); ?>
