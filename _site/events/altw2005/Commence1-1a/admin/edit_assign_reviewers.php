<?php 
	
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;
			
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;			
	
	do_html_header("Edit Reviewers");
	
	//Get the paperID
	$paperID = & $HTTP_GET_VARS["paperID"];
	
	if(isset($_SESSION["arrReviewers"]))
		$arrReviewers = & $_SESSION["arrReviewers"];
								
?>
<form name="form1" method="post" action="confirm_assign_paper.php">
  <table width="100%" border="0" cellspacing="2" cellpadding="1">
    <?php			
		//Get the paper information
		$paperInfo = get_paper_info($paperID);
				
		//Get the lastest file of the paper				
		$FileIDData = get_latestFile($paperID , &$err_message);
		
		//Get Reviewer of the papers
		$arrEditReviewers = get_Reviewers_Of_Paper($paperID);
		for($i=0;$i<count($arrEditReviewers);$i++){
			if($i == count($arrEditReviewers) - 1)
				$strReviewers .= $arrEditReviewers[$i];
			else
				$strReviewers .= $arrEditReviewers[$i].", ";		
		}								

	?>
	<input type="hidden" value="<?php echo $paperID; ?>" name="paperID">
	<!--Send an hidden value to distinguish between edit reviwers and assign reviewers-->
	<input type="hidden" value="true" name="edit">
    <tr> 
      <td width="5%" valign="top"><div align="center"><?php echo "#" .$paperInfo->PaperID; ?></div></td>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td colspan="2"> <strong><?php echo stripslashes($paperInfo -> Title); ?></strong></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2">Category: <?php echo  getSelectedCategoryCommaSeparated($paperInfo -> PaperID , &$err_message );?></td>
          </tr>
          <tr> 
            <td colspan="2">Authors: <?php echo retrieve_authors($paperID);?></td>
          </tr>
          <tr>
            <td colspan="2">Current Reviewers: <?php echo $strReviewers; ?></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2">Assign to:</td>
          </tr>
          <tr> 
            <td colspan="2"><?php echo generateReviewerInputTable($paperInfo->PaperID); ?></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;<a href="view_abstract.php?id=<?php echo $paperInfo->PaperID; ?>" target="_blank">View 
              Abstract</a> | <a href='view_file.php?id=<?php echo $FileIDData -> FileID ; ?>' target='_blank'>View 
              File</a></td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2"><hr></td>
    </tr>
    <tr> 
      <td colspan="2">
        <input type="submit" name="Submit" value="Submit">
        <input name="Submit" type="submit" id="Submit" value="Cancel">
      </td>
    </tr>
  </table>
</form>
<?php
  do_html_footer();
?>
