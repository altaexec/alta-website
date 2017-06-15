<?php

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	do_html_header("Assign Paper#".$id);
	
	//Establish database connection
  	$db = adodb_connect();
  
  	if (!$db){
   		echo "Could not connect to database server - please try later.";
		exit;
	}
		
	//Get the paper information
	$paperInfo = get_paper_info($id);
		
	//Get the lastest file of the paper				
	$FileIDData = get_latestFile($id , &$err_message );		

?>
<form name="form1" method="post" action="process_assign_paper.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td><h4><?php echo stripslashes($paperInfo -> Title); ?></h4></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Authors:</strong><?php echo retrieve_authors($paperInfo -> PaperID);?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>PaperID:</strong> <?php echo $paperInfo -> PaperID; ?></td>
    </tr>
    <tr>
      <td><strong>Keywords:</strong>&nbsp;<?php echo  getSelectedCategoryCommaSeparated($paperInfo -> PaperID , &$err_message );?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><hr></td>
    </tr>
    <tr> 
      <td><p><strong>Assign To:</strong></p></td>
    </tr>
    <tr> 
      <td><?php echo generateReviewerInputTable(); ?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><input type="hidden" name="paperID" value="<?php echo $paperInfo -> PaperID; ?>"><input type="submit" name="Submit" value="Submit"> </td>
    </tr>
  </table>
</form>
<?php 

	do_html_footer();

?>
