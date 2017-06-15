<?php

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	// extract ( $_SESSION , EXTR_REFS ) ;
	$valid_user = $_SESSION["valid_user"] ;
	
	do_html_header("Admin Home" , &$err_message );
	
	//Call the function to get the conference information
	$conferenceInfo = get_conference_info();		
    
	//Get Current Phase
	$currentPhaseInfo = getCurrentPhase();
	
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
    
    //Connect to database
    $db = adodb_connect();
    
    if($currentPhaseInfo -> PhaseName == "Reviewing" || $currentPhaseInfo -> PhaseName == "Final Paper Submission")
		$arrPaperReviewing =  get_Paper_Reviewing_Statistic();
	
	//Get the total number of submitted papers
	$countPapersSQL = "SELECT COUNT(*) AS totalPapers FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE Withdraw = 'false'";	
	$countPapersResult = $db -> Execute($countPapersSQL);
	$countPapersInfo = $countPapersResult -> FetchNextObj();
	
	//Get the total number of members
	$countUserSQL = "SELECT COUNT(*) AS totalUsers FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"] . "PrivilegeType P";
	$countUserSQL .= " WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
	$countUserSQL .= " AND PrivilegeTypeName = 'User'";
	$countUserResult = $db -> Execute($countUserSQL);
	$countUserInfo = $countUserResult -> FetchNextObj();
	
	//Get the total number of reviewers
	$countReviewerSQL = "SELECT COUNT(*) AS totalReviewers FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"] . "PrivilegeType P";
	$countReviewerSQL .= " WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
	$countReviewerSQL .= " AND PrivilegeTypeName = 'Reviewer'";
	$countReviewerResult = $db -> Execute($countReviewerSQL);
	$countReviewerInfo = $countReviewerResult -> FetchNextObj();
	
	//Get the total number of withdrawn papers
	$countWithdrawnSQL = "SELECT COUNT(*) AS totalWithdrawnPapers FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE Withdraw = 'true'";
	$countWithdrawnResult = $db -> Execute($countWithdrawnSQL);
	$countWithdrawnInfo = $countWithdrawnResult -> FetchNextObj();

	//Get the total number of accepted papers
	$countAcceptedSQL = "SELECT COUNT(*) AS totalAcceptedPapers FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE PaperStatusID= '3'";
	$countAcceptedResult = $db -> Execute($countAcceptedSQL);
	$countAcceptedInfo = $countAcceptedResult -> FetchNextObj();

	//Get the total number of rejected papers
	$countRejectedSQL = "SELECT COUNT(*) AS totalRejectedPapers FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE PaperStatusID= '4'";
	$countRejectedResult = $db -> Execute($countRejectedSQL);
	$countRejectedInfo = $countRejectedResult -> FetchNextObj();

	//Get the total number of marginal
	$countMarginalSQL = "SELECT COUNT(*) AS totalMarginalPapers FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE PaperStatusID= '6'";
	$countMarginalResult = $db -> Execute($countMarginalSQL);
	$countMarginalInfo = $countMarginalResult -> FetchNextObj();

	//Get the total number of papers in review
	$countReviewingSQL = "SELECT COUNT(*) AS totalReviewingPapers FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE PaperStatusID in (1,2,5)";
	$countReviewingResult = $db -> Execute($countReviewingSQL);
	$countReviewingInfo = $countReviewingResult -> FetchNextObj();
	
	//Get the total number of papers unscheduled
	$countUnscheduledSQL = "SELECT COUNT(*) AS totalUnscheduledPapers FROM " . $GLOBALS["DB_PREFIX"] . "UnscheduledPaper";
	$countUnscheduledResult = $db -> Execute($countUnscheduledSQL);
	$countUnscheduledInfo = $countUnscheduledResult -> FetchNextObj();
	
?> 
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td width="10%">&nbsp; </td>
    <td width="90%" align="right"><strong><?php echo format_date($settingInfo->DateFormatLong); ?></strong></td>
  </tr>
  <tr> 
    <td>&nbsp; </td>
    <td>
      <?php 	if ($conferenceInfo -> FileName != "")
					echo "<img src=\"view_logofile.php\" alt=\"Logo\">";
		?>
      <h4><?php echo $conferenceInfo -> ConferenceName; ?></h4></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>Welcome <strong><?php echo $valid_user; ?></strong>!</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td><table width="60%" border="1" cellpadding="3" cellspacing="0" bordercolor="#999999">
        <tr> 
          <td colspan="2"> 
            <?php 
		  	
	  		if ($currentPhaseInfo != false){ 

				$result = is_date_expired( $currentPhaseInfo -> EndDate , date ( "Y-m-d" , time() ) , &$err_message , "date" ); 

		  		if($result === true) {
                    ?>
                    <font color='#FF0000'>The current running phase is expired.</font><br>
                    <br><font color='#FF0000'>
                    <?php
				} else {
                    ?>
                    <font color='#000000'>
                    <?php
                }
				?>
				<strong>
                <?php echo $currentPhaseInfo -> PhaseName; ?>
                </strong><br>
				From <strong>
                <?php echo format_date($settingInfo->DateFormatShort, $currentPhaseInfo -> StartDate); ?>
                </strong> To <strong>
                <?php echo format_date($settingInfo->DateFormatShort, $currentPhaseInfo -> EndDate); ?>
                </strong>
				</font>
                <?php
			} else echo "Not current phase has activated yet";	
		   ?>
          </td>
        </tr>
        <tr> 
          <td width="70%">Total Papers Submitted:</td>
          <td width="30%"><?php echo $countPapersInfo -> totalPapers; ?></td>
        </tr>
        <tr> 
          <td>Total Number of Users:</td>
          <td><?php echo $countUserInfo -> totalUsers; ?></td>
        </tr>
        <tr> 
          <td>Total Number of Reviewers:</td>
          <td><?php echo $countReviewerInfo -> totalReviewers; ?></td>
        </tr>
        <tr> 
          <td>Number of Papers Withdrawn:</td>
          <td><?php echo $countWithdrawnInfo -> totalWithdrawnPapers; ?></td>
        </tr>
      </table> </td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> 
      <?php 	if((($currentPhaseInfo -> PhaseName == "Reviewing") || $currentPhaseInfo -> PhaseName == "Final Paper Submission") && (count($arrPaperReviewing) > 0)){?>
       <strong>Scheduling Status</strong><br>
      <br>
	  <table width="60%" border="1" cellpadding="3" cellspacing="0" bordercolor="#999999">
	<tr>
	  <td width ="70%"> Scheduled </td><td> <?php echo $countAcceptedInfo->totalAcceptedPapers - $countUnscheduledInfo->totalUnscheduledPapers ; ?> </td>
	</tr>
	<tr>
	  <td width ="70%"> Unscheduled </td><td> <?php echo $countUnscheduledInfo->totalUnscheduledPapers ; ?> </td>
	</tr>
	</td></tr>
	</table>
		
	<tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> 
	<strong>Decision Status</strong><br>
      <br>
	  <table width="60%" border="1" cellpadding="3" cellspacing="0" bordercolor="#999999">
	<tr>
	  <td width ="70%"> Accepted </td><td> <?php echo $countAcceptedInfo->totalAcceptedPapers ; ?> </td>
	</tr>
	<tr>
	  <td width ="70%"> Rejected </td><td> <?php echo $countRejectedInfo->totalRejectedPapers ; ?> </td>
	</tr>
	<tr>
	  <td width ="70%"> Marginal </td><td> <?php echo $countMarginalInfo->totalMarginalPapers ; ?> </td>
	</tr>
	<tr>
	  <td width ="70%"> Pending </td><td> <?php echo $countReviewingInfo->totalReviewingPapers ; ?> </td>
	</tr>
	</td></tr>
	</table>
  
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td> 
    <strong>Review Status</strong><br>
    <br>
	<table width="60%" border="1" cellpadding="3" cellspacing="0" bordercolor="#999999">
        <?php while(list($numReviews,$count) = each($arrPaperReviewing)){?>
        <tr> 
          <td width="70%">
		  <?php 
		  	if($numReviews == 0) echo "No reviews:"; 
		  	else if($numReviews > 1) echo "$numReviews reviews:";
			else echo "$numReviews review:"; ?>
          </td>
          <td width="60%"><?php echo $count;  ?></td>
        </tr>
        <?php } ?>
      </table>
	<?php } ?>  
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php

	do_html_footer( &$err_message );

?>
