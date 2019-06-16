<?php //////////// REVIEWER PHASE 3 ///////////////	
	$php_root_path = ".." ;
	$privilege_root_path = "/reviewer" ;
//	extract ( $HTTP_GET_VARS , EXTR_REFS ) ;
//	extract ( $HTTP_POST_VARS , EXTR_REFS ) ;
//	extract ( $HTTP_POST_FILES , EXTR_REFS ) ;

	require_once("includes/include_all_fns.php");		
	session_start() ;
//	extract ( $_SESSION , EXTR_REFS ) ;

   // Define a few page vars
    $settingInfo = get_Conference_Settings();
	$trackStr = $settingInfo->TrackName; //Name for Track
	$topicStr = $settingInfo->TopicName; //Name for Topic
	$levelStr = $settingInfo->LevelName; //Name for Level
	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;	
	$header = "View Assigned Papers" ;
	$accepted_privilegeID_arr = array ( 2 => "" ) ;
	$accepted_phaseID_arr = array ( 3 => "" ) ;
	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;	
	
	//Establish connection with database
	$db = adodb_connect( &$err_message );
    
	//Call function to evaluate showing
	$showing = evaluate_showing( $HTTP_GET_VARS["showing"] );
    
	//Retrive the preferences on the papers
	$reviewSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Review R , " . $GLOBALS["DB_PREFIX"] . "Paper P";
	$reviewSQL .= " WHERE R.PaperID = P.PaperID";	
	$reviewSQL .= " AND R.Membername = '".$_SESSION["valid_user"] ."'";
	$reviewSQL .= " AND P.Withdraw = 'false'";	
	$reviewResult = $db -> Execute($reviewSQL);
	$totalPapers = $reviewResult -> RecordCount();
		
	//Check the sorting by Title

	switch($HTTP_GET_VARS["sort"]){
			case 1:
				$reviewSQL .= " ORDER BY R.Objectives ASC";
				break;
			case 2:
				$reviewSQL .= " ORDER BY R.Objectives DESC";
				break;
			case 3:
				$reviewSQL .= " ORDER BY P.Title ASC";
				break;
			case 4:
				$reviewSQL .= " ORDER BY P.Title DESC";
				break;				
			default:
				$reviewSQL .= " ORDER BY R.Objectives";
				break;							
	}			
		
	$reviewSQL .= " LIMIT ".$showing.",".MAX_PAPERS;		
		
	$reviewResult = $db -> Execute($reviewSQL);
	$total_rows = $reviewResult -> RecordCount();
	
	do_html_header("Assigned Papers" , &$err_message );		
	
	if ($totalPapers <= 0)
	{
		echo "There are no papers assigned to you. Please check again later.";
		do_html_footer(&$err_message);		
		exit;
	}
		
	//Call the function to display the range of records
	$from = evaluate_records_range($showing,$totalPapers);		
			
	//Call the function to evaluate prev
	$prev = evaluate_prev($HTTP_GET_VARS["sort"],$showing,$totalPapers);
	//Call the function to evaluate next
	$next = evaluate_next($HTTP_GET_VARS["sort"],$showing,$totalPapers);
	//Call the function to evaluate page links
	$pagesLinks = evaluate_pages_links($HTTP_GET_VARS["sort"],$showing,$totalPapers);		
?>	
<form name="frmPaper" method="post" action="update_biddings.php">
  <table width="100%" border="0" cellspacing="2" cellpadding="0">			
	<tr>
	  <td>From: <?php echo "<strong>$from</strong>";	?></td>
      <td align="right">&nbsp;</td>
	</tr>
  </table>				
  <table width="100%" border="0" cellpadding="0" cellspacing="2">
    <tr> 
      <td width="5%"><strong>ID<strong></td>
      <td width="70%">Order by:&nbsp;<strong>
	  <a href="view_assigned_papers.php?sort=3&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;Title&nbsp;<a href="view_assigned_papers.php?sort=4&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></strong> 
        | <strong><a href="view_assigned_papers.php?sort=1&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;Status&nbsp;<a href="view_assigned_papers.php?sort=2&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></strong></td>
      <td width="25%" align="right"><?php echo $prev; ?> | <?php echo $pagesLinks; ?>| <?php echo $next; ?></td><hr>
    </tr>
	<tr><td colspan="3"><hr></td></tr>
    <?php for($i = 0; $i<$total_rows;$i++)
	{									
		//Get the information
		$paperInfo = $reviewResult -> FetchNextObj();
			
		//Get the lastest file of the paper				
		if ( ( $FileIDData = get_latestFile( $paperInfo->PaperID , &$err_message ) ) === false )
		{
			$err_message .= " Could not execute \"get_latestFile\" in \"view_assigned_papers.php\". <br>\n" ;
		}					
			
		//Check whether the paper is already reviewed
		if ( ( $reviewExist = check_review_exist($paperInfo->PaperID) ) === NULL )
		{
			$err_message .= " Could not execute \"check_review_exist\" in \"view_assigned_papers.php\". <br>\n" ;
		}
?>
    <tr> 
      <td valign="top"> <strong>#<?php echo $paperInfo -> PaperID . " "; ?></strong> </td>
      <td valign="top">
        <p><a href='view_file.php?fileid=<?php echo $FileIDData -> FileID ; ?>'><?php echo stripslashes($paperInfo -> Title); ?></a><br>

	<?php
 	if ( $authors = retrieve_authors($paperInfo->PaperID , &$err_message ) )
	{
		echo $authors ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read author table. Try <a 		href='view_abstract.php?id=$id'>again</a>?</font>" ;
	}		
	?><p>


        <p><?php echo "$trackStr:"?> 
          <?php if ( $catcomsep = GetSelectedTrackText($paperInfo->PaperID , &$err_message ) )
		{
			echo $catcomsep ;
		}
		else
		{
			$err_message .= " Could not execute \"GetSelectedTrackText\" in \"view_assigned_papers.php\". <br>\n" ;
			echo " <font color=\"#FF0000\"> Could not read Paper Track table. Try <a href='view_assigned_papers.php'>again</a>?</font>" ;
		}
			  
		  ?>
		 <?php echo "$topicStr(s):"?> 
          <?php if ( $catcomsep = getSelectedCategoryCommaSeparated($paperInfo->PaperID , &$err_message ) )
		{
			echo $catcomsep ;
		}
		else
		{
			$err_message .= " Could not execute \"getSelectedCategoryCommaSeperated\" in \"view_assigned_papers.php\". <br>\n" ;
			echo " <font color=\"#FF0000\"> Could not read Paper Category table. Try <a href='view_assigned_papers.php'>again</a>?</font>" ;
		}
			  
		  ?>
	<br> Status:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php if($reviewExist == true) echo "Reviewed"; else echo "<font 			color=\"#FF0000\">Not Reviewed</font>" ; ?></strong></p>
        </td>
      <td valign="top">
	  	<ul>
			<li><a href="view_abstract.php?paperid=<?php echo $paperInfo -> PaperID ?>">View Abstract</a></li>
        <?php if($reviewExist == true) {?>
        	<li><a href='show_review.php?paperid=<?php echo $paperInfo->PaperID; ?>'>Show Review</a></li><br>
        	<li><a href='edit_review_form.php?paperid=<?php echo $paperInfo->PaperID; ?>'>Edit Review</a></li><br>
        <?php } else {?>
        	<li><a href='review_form.php?paperid=<?php echo $paperInfo->PaperID; ?>'>Make Review</a></li><br>
        <?php } ?>
		</ul>
      </td>
    </tr>
	<tr><td colspan="3"><hr></td></tr>
    <?php } //End of for loop
	?>
  </table>
  <table width="100%" border="0" cellspacing="2" cellpadding="5">
  	<tr>				
      <td>Total Papers : <strong><?php echo $totalPapers; ?></strong></td>
      <td align="right"><?php echo $prev; ?> | <?php echo $pagesLinks; ?>| <?php echo $next; ?></td>
 	</tr>
 </table>
</form>
<?php do_html_footer(&$err_message);
?>
