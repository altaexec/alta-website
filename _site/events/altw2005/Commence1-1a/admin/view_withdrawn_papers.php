<?php
		
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	

	do_html_header("View Withdrawn Papers");
	
	
	 //Establish database connection
 	$db = adodb_connect();
  
	if (!$db){
    	echo "Could not connect to database server - please try later.";
		exit;		
	}
	
	
	$showing = $HTTP_GET_VARS["showing"];
	$sort = $HTTP_GET_VARS["sort"];
		
	//Call function to evaluate showing
	$showing = evaluate_showing($showing);
		

	//Retrieve all the papers
	$papersSQL = "SELECT *";
	$papersSQL .= " From " . $GLOBALS["DB_PREFIX"] . "Paper";
	$papersSQL .= " WHERE Withdraw = 'true'";
	$countResult = $db -> Execute($papersSQL);
	$num_rows = $countResult -> RecordCount();			
		
	//Check the sorting by Title
	switch($sort){
			case 1:
				$papersSQL .= " ORDER BY PaperID ASC";
				$strSort = "PaperID - Ascending";
				break;
			case 2:
				$papersSQL .= " ORDER BY PaperID DESC";
				$strSort = "PaperID - Descending";
				break;	
			case 3:
				$papersSQL .= " ORDER BY Title ASC";
				$strSort = "Title - Ascending";
				break;
			case 4:
				$papersSQL .= " ORDER BY Title DESC";
				$strSort = "Title - Descending";
				break;				
			default:
				$papersSQL .= " ORDER BY PaperID";
				$strSort = "PaperID - Ascending";
				break;							
	}	
		
		
	$papersSQL .= " LIMIT ".$showing.",".MAX_PAPERS;
		
	$papersResult = $db -> Execute($papersSQL);
	
	if ($num_rows <= 0){
		echo "There are no withdrawn papers.";
		exit;
	}
		
	//Call the function to display the range of records
	$from = evaluate_records_range($showing,$num_rows);		
			
	//Call the function to evaluate prev
	$prev = evaluate_prev($sort,$showing,$num_rows);
	$next = evaluate_next($sort,$showing,$num_rows);
	//Call the function to evaluate page links
	$pagesLinks = evaluate_pages_links($sort,$showing,$num_rows);	
		
?>	
<form name="frmPaper" method="post" action="process_withdrawn_papers.php">
  <table width="100%" border="0" cellspacing="2" cellpadding="0">			
	<tr>
      <td width="30%">From: <?php echo "<strong>$from</strong>";	?></td>
      <td width="40%" align="left">Order By:&nbsp;<strong><?php echo $strSort;  ?></strong></td>
      <td width="30%" align="right">
      	<input type="submit" name="Purge" value="Purge Selected">
        <input type="submit" name="Purge" value="Purge All"></td>
	</tr>
  </table>				
  <table width="100%" border="0" cellpadding="1" cellspacing="2">
    <tr> 
      <td width="5%" align="center">&nbsp;</td>
      <td width="5%" align="center"><strong> ID</strong></td>
      <td width="60%">
	  	<a href="view_withdrawn_papers.php?sort=1&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>PaperID</strong>&nbsp;<a href="view_withdrawn_papers.php?sort=2&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>&nbsp;&nbsp;
        <a href="view_withdrawn_papers.php?sort=3&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>Title</strong>&nbsp;<a href="view_withdrawn_papers.php?sort=4&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>&nbsp;&nbsp;
		</td>
      <td width="30%" align="right"><?php echo $prev; ?> | <?php echo $pagesLinks; ?>| <?php echo $next; ?></td>
    </tr>
    <?php			
	while($paperInfo = $papersResult -> FetchNextObj())
	{		
							
		//Get the lastest file of the paper				
		$FileIDData = get_latestFile($paperInfo->PaperID , &$err_message );
				

?>
    <tr>
      <td align="center" valign="top">&nbsp;</td>
      <td height="31" align="center" valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="middle">&nbsp;</td>
    </tr>
    <tr> 
      <td align="center" valign="top"> 
        <input type="checkbox" name="arrPaperID[]" value="<?php echo $paperInfo->PaperID; ?>"> 
      </td>
      <td height="31" align="center" valign="top"><?php echo $paperInfo->PaperID; ?>#</td>
      <td valign="top"><p><a href="view_file.php?fileid=<?php echo $FileIDData -> FileID ; ?>" target="_blank"><strong><?php echo stripslashes($paperInfo -> Title); ?></strong></a><br>
          <br>
          <strong>Category:</strong>&nbsp;<?php echo  getSelectedCategoryCommaSeparated($paperInfo->PaperID , &$err_message );?><br>
        </p></td>
      <td valign="middle"> <ul>
          <li><a href="view_abstract.php?id=<?php echo $paperInfo->PaperID ; ?>">View 
            Abstract</a></li>
          <li><a href='download_file.php?fileid=<?php echo $FileIDData -> FileID ; ?>'>Download File</a></li>
        </ul></td>
    </tr>
    <tr>
      <td colspan="4"><hr></td>
    </tr>
    <?php			
	} //End of while loop
?>
  </table>
  <table width="100%" border="0" cellspacing="2" cellpadding="5">
  	<tr>				
      <td>Total Papers : <strong><?php echo $num_rows; ?></strong></td>
      <td align="right"><?php echo $prev; ?> | <?php echo $pagesLinks; ?>| <?php echo $next; ?></td>
 	</tr>
 </table>
</form>
<?php			
	do_html_footer();
?>

	  

