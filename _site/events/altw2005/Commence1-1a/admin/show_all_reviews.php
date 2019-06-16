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
	
	do_html_header("All Reviews");
	
	//Establish database connection
  	$db = adodb_connect();
  
  	if (!$db){
   		echo "Could not connect to database server - please try later.";
		exit;
	}
	
	$id = & $HTTP_GET_VARS["id"];	
		
	//Get the paper information
	$paperInfo = get_paper_info($id);
		
	//Get the lastest file of the paper				
	$FileIDData = get_latestFile($id , &$err_message );
	
	//Retrieve the information from Review Table
	$reviewSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Review";
	$reviewSQL .= " WHERE PaperID='$id'";
	$reviewSQL .= " AND Comments != ''";	
	$reviewResult = $db -> Execute($reviewSQL);
	$numReviews = $reviewResult -> RecordCount();

			

?>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td><h4>#<?php echo $paperInfo -> PaperID; ?>&nbsp;<?php echo stripslashes($paperInfo -> Title); ?></h4></td>
  </tr>
  <tr> 
    <td><strong>Authors:</strong>&nbsp;<?php echo retrieve_authors($paperInfo -> PaperID);?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><strong><?php echo "$trackStr:"?></strong>&nbsp;<?php echo  GetSelectedTrackText($paperInfo->PaperID , &$err_message );?></td>
  </tr>
  <tr> 
    <td><strong><?php echo "$topicStr(s):"?></strong>&nbsp;<?php echo  getSelectedCategoryCommaSeparated($paperInfo->PaperID , &$err_message );?></td>
  </tr>
  <tr> 
    <td><hr></td>
  </tr>
  <?php 
 	
	for($i=0;$i<$numReviews;$i++){
	
	$reviewInfo = $reviewResult -> FetchNextObj();
	
	//Assign the values to the variables
	$objectives = $reviewInfo -> Objectives;
	$validity = $reviewInfo -> Validity;
	$innovativeness = $reviewInfo -> Innovativeness;
	$presentation = $reviewInfo -> Presentation;
	$bibliography = $reviewInfo -> Bibliography;	
	$acceptpaper = $reviewInfo -> AcceptPaper;	
	$acceptposter = $reviewInfo -> AcceptPoster;	
 
 ?>
  <tr> 
    <td><strong>Reviewer Name:</strong> <?php echo $reviewInfo -> MemberName; ?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><strong>Ranking Criteria </strong><br> <table width="70%" border="1" cellspacing="2" cellpadding="1">
        <tr> 
          <td width="70%"><strong>Name</strong></td>
          <td width="30%"><strong>Rank</strong></td>
        </tr>
        <tr> 
          <td>The paper fits in with the objectives of this workshop</td>
          <td><?php echo get_ranking($objectives); ?></td>
        </tr>
        <tr> 
			 <td>All reported claims and results are correct and valid,
and there are no technical and/or methodological flaws:</td>
          <td><?php echo get_ranking($validity); ?></td>
        </tr>
        <tr> 
			 <td>The paper is innovative and makes a genuine contribution
to the field</td>
          <td><?php echo get_ranking($innovativeness); ?></td>
        </tr>
        <tr> 
			 <td>The objectives, methodology and contributions of the
paper are clearly described</td>
          <td><?php echo get_ranking($presentation); ?></td>
        </tr>
        <tr> 
          <td>The bibliography is relevant and exhaustive</td>
          <td><?php echo get_ranking($bibliography); ?></td>
        </tr>
        <tr> 
          <td>The paper should be accepted as a regular paper</td>
          <td><?php echo get_ranking($acceptpaper); ?></td>
        </tr>
        <tr> 
          <td>The paper should be accepted as a poster</td>
          <td><?php echo get_ranking($acceptposter); ?></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><p><strong>Detailed Comments:<br>
        </strong><?php echo nl2br(stripslashes(get_comment($paperInfo -> PaperID,$reviewInfo -> MemberName , &$err_message ))); ?></p></td>
  </tr>
  <tr> 
    <td><hr></td>
  </tr>
  <?php }/*end of for loop*/?>
</table>
<br>
<strong><a href=evaluate_paper_status.php?paperID=<?php echo $paperInfo -> PaperID ; ?>>Decide on Paper #<?php echo $paperInfo -> PaperID ; ?></a></strong>
<?php 

	do_html_footer();

?>
