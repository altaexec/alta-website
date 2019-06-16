<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	do_html_header("View Phase");
		
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		echo "Could not connect to database server - please try later.";		
		exit;
	}
	
	//Get Current Phase
	$currentPhaseInfo = getCurrentPhase();
	if ( ( $phasesResult = getAllPhases ( &$err_message ) ) === NULL )
	{
		do_html_header("View Phases Failed" , &$err_message) ;					
		$err_message .= " Could not execute \"getAllPhases\" in \"view_phases.php\". <br>\n" ;
		$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $HTTP_SERVER_VARS['QUERY_STRING'] . "'>again</a>?" ;
		do_html_footer(&$err_message);		
		exit ;
	}
	
    // Get settings info
    $settingsInfo = get_Conference_Settings();
?>
<form name="form1" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <?php if ($currentPhaseInfo != false){ // matches } noted below
		$result = is_date_expired( $currentPhaseInfo -> EndDate , format_date() , &$err_message , "date" );  
  ?>
    <tr> 
      <td> 
        <?php if($result === true) echo "<font color='#FF0000'>The current running phase is already expired. It is time to change to the next phase.</font>"; ?>
      </td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong> 
        <?php if($result === true) echo "<font color='#FF0000'>".$currentPhaseInfo -> PhaseName."</font>"; else echo $currentPhaseInfo -> PhaseName; ?>
        </strong></td>
    </tr>
    <tr> 
      <td>
      Duration: 
      <?php if($result === true) echo "<font color='#FF0000'>"; ?>
      From 
      <strong>
        <?php echo format_date($settingsInfo -> DateFormatShort,$currentPhaseInfo -> StartDate)?>
      </strong> 
      To 
      <strong>
        <?php echo format_date($settingsInfo -> DateFormatShort,$currentPhaseInfo -> EndDate)?>
      </strong>
      <?php if($result === true) echo "</font>"; ?>
      </td>
    </tr>
    <?php  } //matching } above
  else echo "<tr><td>No Current Phase Setup yet.</td></tr>";
  ?>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>Available Phases:</td>
    </tr>
    <tr> 
      <td>
      <?php echo display_phase_table() ?>
      </td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Phase Information</strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td> <p><strong>Paper Submission</strong> - Also known as the &quot;Call 
          for Papers&quot; phase. Users open their accounts to register interest 
          in the conference. Later they may upload their papers, if any. Users 
          can make changes to their papers and upload additional papers until 
          the next phase.</p>
        <p><strong>Section Chair Bidding</strong> - This is where the section chairs bid 
          (indicate preferences) for papers they wish to review or where they 
          have a conflict of interest. Users are prevented from submitting new 
          papers and editing current papers in this phase. This process is designed 
          to reduce the risk of inappropriate assignment of papers to section chairs. 
          You may skip bidding if you wish to manually assign all section chairs to 
          papers. </p>
        <p><strong>Reviewing</strong> - The users are prevented from submitting 
          new papers and editing current papers while the section chairs are reviewing 
          the papers. Section Chairs can submit and edit their reviews until the end 
          of this phase.</p>
        <p><strong>Final Paper Submission</strong> - Users may revise existing 
          papers but are prevented from submitting additional papers. Section Chairs 
          are prevented from changing their reviews.</p></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<p>&nbsp;</p>
<?php do_html_footer(); ?>
