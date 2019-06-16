<?php

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		//Cancel button is pressed
		unset($_SESSION["arrPhaseInfo"]);
		$str = "Location: admin_home.php";
		header($str);
		exit;	
	}
	
	//Insert the information into array
	$arrPhaseInfo = $HTTP_POST_VARS;
	$_SESSION["arrPhaseInfo"] = $HTTP_POST_VARS;
		
	do_html_header("Change Phase");
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		echo "Could not connect to database server - please try later.";		
		exit;
	}
	
	$newPhaseSQL = "SELECT PhaseID,PhaseName,".dbdf_out($db,"StartDate").",".dbdf_out($db,"EndDate").",Status";
	$newPhaseSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
	$newPhaseSQL .= " WHERE PhaseID = ".db_quote($db,$arrPhaseInfo["newPhaseID"]);	
	$newPhaseResult = $db -> Execute($newPhaseSQL);
	
	if(!$newPhaseResult){
		echo "Could not retrieve the phase information - please try again later.";
		exit;
	}
	
    // Get settings info
    $settingsInfo = get_Conference_Settings();
    
	//Get the new phase info
	$newPhaseInfo = $newPhaseResult -> FetchNextObj();		
	
	//Get Current Phase, use true flag to ensure we get value from database rather than session variable
	$currentPhaseInfo = getCurrentPhase(true);	
?>
<form name="form1" method="post" action="process_change_phase.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2">Changing phase will reflect on accessibility of both User 
        and Reviewer side. This may disable or enable some functions on User and 
        Reviewer side. Make sure that you are changing to desired Phase. Press 
        Confirm to proceed.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <?php if (!empty($arrPhaseInfo["currentPhaseID"])) {
  	?>
    <tr> 
      <td width="15%">From: </td>
      <td width="85%"><strong><?php echo $currentPhaseInfo -> PhaseName; ?></strong></td>
    </tr>
    <tr> 
      <td>Duration: </td>
      <td>
      From 
      <strong>
        <?php echo format_date($settingsInfo -> DateFormatShort,$currentPhaseInfo -> StartDate)?>
      </strong> 
      To 
      <strong>
        <?php echo format_date($settingsInfo -> DateFormatShort,$currentPhaseInfo -> EndDate)?>
      </strong>
      </td>
    </tr>
    <?php }?>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td>Change to: </td>
      <td><strong><?php echo $newPhaseInfo -> PhaseName; ?></strong></td>
    </tr>
    <tr> 
      <td>Duration:</td>
      <td>
      From 
      <strong>
        <?php echo format_date($settingsInfo -> DateFormatShort,$newPhaseInfo -> StartDate)?>
      </strong> 
      To 
      <strong>
        <?php echo format_date($settingsInfo -> DateFormatShort,$newPhaseInfo -> EndDate)?>
      </strong>
      </td>
    </tr>
    <tr> 
      <td colspan="2"></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="submit" name="Submit" value="Confirm"> <input name="Submit" type="submit" id="Submit" value="Back"></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><hr></td>
    </tr>
    <tr>
      <td colspan="2"><strong>Phase Information</strong></td>
    </tr>
    <tr> 
      <td colspan="2"><?php echo display_phase_table(); ?></td>
    </tr>
  </table>
</form>
<?php do_html_footer(); ?>
