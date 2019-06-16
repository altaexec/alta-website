<?php

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	//Read back the session variable
	if(isset($_SESSION["arrPhaseInfo"]))
		$arrPhaseInfo = & $_SESSION["arrPhaseInfo"];
		
	do_html_header("Change Phase");
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		echo "Could not connect to database server - please try later.";		
		exit;
	}
	
	$phasesSQL = "SELECT PhaseID,PhaseName,".dbdf_out($db,"StartDate").",".dbdf_out($db,"EndDate").",Status";
	$phasesSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
	$phasesSQL .= " WHERE Status = 'false'";
	$phasesSQL .= " ORDER BY PhaseID";	
	$phasesResult = $db -> Execute($phasesSQL);
	
	if(!$phasesResult){
		echo "Could not retrieve the phase information - please try again later.";
		exit;
	}		
	
    //Retrieve the setting information
	$settingInfo = get_Conference_Settings();
    
    //Get Current Phase, set $database flag to true to force lookup of database not session variable
	$currentPhaseInfo = getCurrentPhase(true);	
?>
<form name="form1" method="post" action="confirm_change_phase.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <?php if ($currentPhaseInfo != false){ 
  
		list($year,$month,$day) = split('[/.-]',$currentPhaseInfo -> EndDate);
		$result = is_date_expired( $currentPhaseInfo -> EndDate , date ( "Y-m-d" , time() ) , &$err_message , "date" );  
  ?>
    <tr> 
      <td><strong> 
        <?php if($result === true) echo "<font color='#FF0000'>".$currentPhaseInfo -> PhaseName."</font>"; else echo $currentPhaseInfo -> PhaseName; ?>
        </strong></td>
    </tr>
    <input type="hidden" name="currentPhaseID" value="<?php echo $currentPhaseInfo -> PhaseID; ?>">
    <tr> 
      <td>Duration: 
        <?php 
	  	if($result === true) echo "<font color='#FF0000'>"; else echo "<font color='#000000'>";	
		?>
        From <strong>
        <?php echo format_date($settingInfo -> DateFormatShort, $currentPhaseInfo -> StartDate); ?>
        </strong> To <strong>
        <?php echo format_date($settingInfo -> DateFormatShort, $currentPhaseInfo -> EndDate); ?>
        </strong>
        </font>
      </td>
    </tr>
    <?php  }
  		else echo "<tr><td>No Current Phase Setup yet.</td></tr>";
  	?>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Change to:</strong> </td>
    </tr>
    <tr> 
      <td><table width="80%" border="0" cellspacing="0" cellpadding="1">
          <tr> 
            <td width="5%">&nbsp;</td>
            <td width="45%">Phase Name</td>
            <td width="25%">Start Date</td>
            <td width="25%">End Date</td>
          </tr>
          <?php for($i=0;$i < $phasesResult -> RecordCount();$i++){
		  
		  	$phaseInfo = $phasesResult -> FetchNextObj();
		  ?>
          <tr> 
            <td align="center"> <input name="newPhaseID" type="radio" value="<?php echo $phaseInfo -> PhaseID; ?>" 
			<?php if($arrPhaseInfo["newPhaseID"] == $phaseInfo -> PhaseID) echo "checked"; else if($i== 0) echo "checked";?>></td>
            <td><?php echo $phaseInfo -> PhaseName; ?></td>
            <td><?php echo format_date($settingInfo -> DateFormatShort, $phaseInfo -> StartDate); ?></td>
            <td><?php echo format_date($settingInfo -> DateFormatShort, $phaseInfo -> EndDate); ?></td>
          </tr>
          <?php }/*end of while loop*/?>
        </table></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><input type="submit" name="Submit2" value="Submit"> <input name="Submit" type="submit" id="Submit2" value="Cancel"></td>
    </tr>
  </table>
</form>
<?php do_html_footer(); ?>
