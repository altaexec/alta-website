<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	//extract ( $_SESSION , EXTR_REFS ) ;	
	
	// Define a few page vars
    $settingInfo = get_Conference_Settings();
	$trackStr = $settingInfo->TrackName; //Name for Track
	$topicStr = $settingInfo->TopicName; //Name for Topic
	$levelStr = $settingInfo->LevelName; //Name for Level
	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$paperID = & $HTTP_GET_VARS["paperID"];
	$status = & $HTTP_GET_VARS["status"];
	$title = $status." Paper";
	do_html_header($title);
	
	
	
	//Get the paper information
	$paperInfo = get_paper_info($paperID);
	
	$type = get_presentation_info($HTTP_GET_VARS["type"]);
	$curtype = get_presentation_info( get_presentation_type_for_paper($paperInfo -> PaperID) );
?>
<form action="process_accept_reject_paper.php" method="post" name="form1">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2"><?php echo stripslashes("<h3>#".$paperInfo->PaperID." ".$paperInfo -> Title."</h3>"); ?></td>
    </tr>
    <input type="hidden" name="paperID" value="<?php echo $paperInfo->PaperID; ?>">
    <tr> 
      <td width="15%"><strong>Authors:</strong> </td>
      <td width="85%"><?php echo retrieve_authors($paperInfo -> PaperID);?></td>
    </tr>
     <tr> 
      <td><strong><?php echo "$trackStr:"?></strong> </td>
      <td><?php echo  GetSelectedTrackText($paperInfo -> PaperID , &$err_message );?></td>
    </tr>
    <tr> 
      <td><strong><?php echo "$topicStr(s):"?></strong> </td>
      <td><?php echo  getSelectedCategoryCommaSeparated($paperInfo -> PaperID , &$err_message );?></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <input type="hidden" name="status" value="<?php echo $status; ?>">
    <input type="hidden" name="type" value="<?php echo $type -> PresentationTypeID; ?>">
    <tr> 
      <td><strong>Current Status:</strong></td>
      <td><?php echo $paperInfo -> PaperStatusName; ?> 
	  <?php if ($paperInfo -> PaperStatusName == "Accepted") { ?>
	  as <?php echo $curtype -> PresentationTypeName; ?>
	  <?php } ?>
	  </td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Change to:</strong></td>
      <td>
	  <?php echo $status; ?>
	  <?php if ($status == "Accepted") { ?>
	  as <?php echo $type -> PresentationTypeName; ?></td>
	  <?php } ?>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
	<?php 
	
		if($status == "Accepted")
			$result = check_Letter_Already_Sent($paperInfo -> PaperID,"5");		
		else if($status == "Rejected")
			$result = check_Letter_Already_Sent($paperInfo -> PaperID,"6");
			
		if($result === true){
			echo "<tr>\n<td colspan=\"2\">\n";
			echo "The notification letter has already been sent to inform the paper owner.<br>If you wish to inform the paper owner again, check the box below.";
			echo "</td>\n\n</tr>";
			}
		
	?>
    <tr> 
      <td colspan="2"><input name="informuser" type="checkbox" id="informuser" value="yes" <?php if ($HTTP_GET_VARS["back"] == "true") echo "checked"; ?>>
        Inform the user now</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="Submit" type="submit" id="Submit" value="Submit">
	  	<input name="Submit" type="submit" value="Back">
        <input name="Submit" type="submit" id="Submit" value="Cancel"> </td>
    </tr>
  </table>
</form>
<?php 
	
	do_html_footer();

?>
