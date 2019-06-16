<?php
	
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	// Define a few page vars
    $settingInfo = get_Conference_Settings();
	$trackStr = $settingInfo->TrackName; //Name for Track
	$topicStr = $settingInfo->TopicName; //Name for Topic
	$levelStr = $settingInfo->LevelName; //Name for Level
	
	//Establish database connection
  	$db = adodb_connect();
  
  	if (!$db){
   		echo "Could not connect to database server - please try later.";
		exit;
	}

	//Get the GET VARS
	$paperID = & $HTTP_GET_VARS["paperID"];
	
	//Get the paper information
	$paperInfo = get_paper_info($paperID);	
	
	do_html_header("Evaluate Paper Status" , &$err_message );
	
	$curtype = get_presentation_info( get_presentation_type_for_paper($paperInfo -> PaperID) );
?>
<form name="form1" method="post" action="process_evaluate_paper_status.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td colspan="2"><input type="hidden" name="paperID" value="<?php echo $paperInfo->PaperID; ?>"> <h3>#<?php echo $paperInfo->PaperID; ?>&nbsp;<?php echo stripslashes($paperInfo -> Title); ?></h3></td>
    </tr>
    <tr> 
      <td width="20%"><strong>Authors:</strong> </td>
      <td width="80%"><?php echo retrieve_authors($paperInfo -> PaperID);?></td>
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
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Current Paper Status:</strong></td>
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
      <td><strong>Change To:</strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><table width="40%">
	  	  <?php if ($paperInfo -> PaperStatusName != "Marginal") { ?>
          <tr> 
            <td><label> 
              <input name="paperstatus" type="radio" value="Marginal" <?php if($HTTP_GET_VARS["status"] == "Marginal") echo "checked"; else if($HTTP_GET_VARS["status"] != "Accepted") echo "checked"; ?>>
              Marginal</label></td>
          </tr>
		  <?php } ?>
	  	  <tr> 
            <td><label> 
              <input type="radio" name="paperstatus" value="Accepted" <?php if($HTTP_GET_VARS["status"] == "Accepted" || $paperInfo -> PaperStatusName == "Accepted" || $HTTP_GET_VARS["status"] = "Marginal") echo "checked"; ?>>
              Accept</label>
			  as
			  <select name="PresentationType">
			  <?php 
			  $types = get_presentation_types();
			  foreach ($types as $type) {  ?>
				<option value="<?php echo $type -> PresentationTypeID ?>" <?php if (get_presentation_type_for_paper($paperInfo -> PaperID) == $type -> PresentationTypeID) echo "selected" ?>>
				<?php echo $type -> PresentationTypeName ?>
				</option>
			  <?php } ?>
			  </select>
			  </td>
          </tr>
		  <?php if ($paperInfo -> PaperStatusName != "Rejected") { ?>
          <tr> 
            <td><label> 
              <input type="radio" name="paperstatus" value="Rejected" <?php if($HTTP_GET_VARS["status"] == "Rejected") echo "checked"; ?>>
              Reject</label></td>
          </tr>
		  <?php } ?>		  
        </table></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit" value="Submit">
        <input name="Submit" type="submit" id="Submit" value="Cancel"></td>
    </tr>
  </table>
</form>
<?php			
	do_html_footer( &$err_message );
?>
