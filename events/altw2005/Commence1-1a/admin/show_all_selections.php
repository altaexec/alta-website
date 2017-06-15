<?php

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
		
	do_html_header("View Preferences");
	
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
	$preferenceSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Selection S," . $GLOBALS["DB_PREFIX"] . "Preference P";
	$preferenceSQL .= " WHERE S.PreferenceID = P.PreferenceID";
	$preferenceSQL .= " AND S.PaperID =".$id;
	$preferenceResult = $db -> Execute($preferenceSQL);
	$numPreferences = $preferenceResult -> RecordCount();
	
	if($numPreferences == 0){
		echo "No preferences has been made on this paper - please try again later.";
		exit;
	}

?>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td><h4><?php echo stripslashes($paperInfo -> Title); ?></h4></td>
  </tr>
  <tr> 
    <td><strong>PaperID:</strong> <?php echo $paperInfo -> PaperID; ?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><strong>Authors:</strong> <?php echo retrieve_authors($paperInfo -> PaperID);?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><p><strong>Keywords:<br>
        </strong><?php echo  getSelectedCategoryCommaSeparated($paperInfo->PaperID , &$err_message );?></p></td>
  </tr>
  <tr> 
    <td><hr></td>
  </tr>
  <?php 
 	
	for($i=0;$i<$numPreferences;$i++){
	
	$preferenceInfo = $preferenceResult -> FetchNextObj();
		
 
 ?>
  <tr> 
    <td><strong><a href="show_reviewer_preferences.php?name=<?php echo $preferenceInfo -> MemberName; ?>"><?php echo $preferenceInfo -> MemberName; ?></a></strong></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><strong>Preference:</strong> <?php echo $preferenceInfo -> PreferenceName; ?></td>
  </tr>
  <tr> 
    <td><hr></td>
  </tr>
  <?php }/*end of for loop*/?>
</table>

<br>
<strong> <a href=<?php
if ($paperInfo -> PaperStatusName == "Not Reviewed")
	echo "display_assign_paper.php" ;
else
	echo "edit_assign_reviewers.php" ;
?>
?paperID=<?php echo $paperInfo->PaperID ?> > Assign Paper </a> </strong>

<?php 

	do_html_footer();

?>
