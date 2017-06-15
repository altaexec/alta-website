<?php
	
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	require_once("$php_root_path/includes/page_includes/page_fns.php");
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	

	do_html_header("Admin View All Papers" , &$err_message );
	

	 //Establish database connection
 	$db = adodb_connect();
  
	if (!$db){
    	echo "Could not connect to database server - please try later.";
		exit;		
	}
	
	$showing = $HTTP_GET_VARS["showing"];
	$sort = $HTTP_GET_VARS["sort"];
	
	//Call function to evaluate showing
	$showing = evaluate_showing($showing);
	$_SESSION["sort"] = $sort ;	
	$_SESSION["showing"] = $showing ;
		
	//Retrieve all the papers
	$papersSQL = "SELECT *";
	$papersSQL .= " From " . $GLOBALS["DB_PREFIX"] . "Paper P," . $GLOBALS["DB_PREFIX"] . "PaperStatus PS";
	$papersSQL .= " WHERE P.PaperStatusID = PS.PaperStatusID AND Withdraw = 'false'";
	$countResult = $db -> Execute($papersSQL);
	$num_rows = $countResult -> RecordCount();	
		
	//Check the sorting by Title
	switch($sort){
			case 1:
				$papersSQL .= " ORDER BY PaperID ASC";
				$strSort = "PaperID - Ascending";
				break;
			case 2:
				$papersSQL .= " ORDER BY PaperID DESC";
				$strSort = "PaperID - Descending";
				break;	
			case 3:
				$papersSQL .= " ORDER BY Title ASC";
				$strSort = "Title - Ascending";
				break;
			case 4:
				$papersSQL .= " ORDER BY Title DESC";
				$strSort = "Title - Descending";
				break;
			case 5:
				$papersSQL .= " ORDER BY PS.PaperStatusID ASC";
				$strSort = "PaperStatus - Ascending";
				break;
			case 6:
				$papersSQL .= " ORDER BY PS.PaperStatusID DESC";
				$strSort = "PaperStatus - Descending";
				break;	
			case 7:
				$papersSQL .= " ORDER BY P.TrackID ASC";
				$strSort = "TrackID - Ascending";
				break;
			case 8:
				$papersSQL .= " ORDER BY P.TrackID DESC";
				$strSort = "TrackID - Descending";
				break;				
			case 9:
				$papersSQL .= " ORDER BY OverallRating ASC";
				$strSort = "Evaluation - Descending";
				break;				
			case 10:
				$papersSQL .= " ORDER BY OverallRating DESC";
				$strSort = "Evaluation - Ascending";
				break;					
			default:
				$papersSQL .= " ORDER BY PaperID";
				$strSort = "PaperID - Ascending";
				break;							
	}					

	$papersSQL .= " LIMIT ".$showing.",".MAX_PAPERS;
		
	$papersResult = $db -> Execute($papersSQL);
	
	if ($num_rows <= 0){
		echo "Sorry, there are no papers in the database.";
		exit;
	}
		
	//Call the function to display the range of records
	$from = evaluate_records_range($showing,$num_rows);		
			
	//Call the function to evaluate prev
	$prev = evaluate_prev($sort,$showing,$num_rows);
	//Call the function to evaluate next
	$next = evaluate_next($sort,$showing,$num_rows);
	//Call the function to evaluate page links
	$pagesLinks = evaluate_pages_links($sort,$showing,$num_rows);
		
?>	
<form name="frmPaper" method="post" action="display_assign_papers.php">
<br>
  <table width="100%" border="0" cellspacing="2" cellpadding="0">
    <tr> 
      <td width="30%">From: <?php echo "<strong>$from</strong>";	?></td>
      <td width="40%" align="left">Ordered by:&nbsp;<strong><?php echo $strSort;  ?></strong></td>
      <td width="30%" align="right">Total Papers : <strong><?php echo $num_rows; ?></strong></td>
    </tr>
    <tr>
	<td>&nbsp;</td>		
      	<td colspan=2  align="right"><br><?php echo $prev; ?>&nbsp;|<?php echo $pagesLinks; ?>|&nbsp;<?php echo $next; ?></td>
    </tr>
  </table>				
  <table width="100%" border="0" cellpadding="1" cellspacing="2">
    <tr> 
      <td width="5%" align="center">&nbsp;</td>
      <td width="5%" align="center"><strong> ID</strong></td>
      <td width="65%">
	  	Order by: <a href="view_all_papers.php?sort=1&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>PaperID</strong>&nbsp;<a href="view_all_papers.php?sort=2&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>
	    &nbsp;|&nbsp;
        <a href="view_all_papers.php?sort=3&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>Title</strong>&nbsp;<a href="view_all_papers.php?sort=4&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>
        &nbsp;|&nbsp;
		<a href="view_all_papers.php?sort=5&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>Status</strong>&nbsp;<a href="view_all_papers.php?sort=6&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>
		&nbsp;|&nbsp;
			<a href="view_all_papers.php?sort=7&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>TrackID</strong>&nbsp;<a href="view_all_papers.php?sort=8&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>
		&nbsp;|&nbsp;
			<a href="view_all_papers.php?sort=9&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>Evaluation</strong>&nbsp;<a href="view_all_papers.php?sort=10&showing=<?php echo $showing; ?>"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>
		</td>
<td width="25%">&nbsp</td>
</tr>
<hr>
    </tr>
    <?php	
	
	while($paperInfo = $papersResult -> FetchNextObj())
	{		
// $t=getdate(); echo "Time 1 " . $t['seconds'] . "<br>"; //Debug BL		
	        //Get the lastest file of the paper				
		//$FileIDData = get_latestFile($paperInfo->PaperID , &$err_message );
		$FileIDData = get_latestFileID($paperInfo->PaperID , &$err_message );
		//Get reviewer of the paper and format into string
		$arrReviewers = get_Reviewers_Of_Paper($paperInfo->PaperID);
		$strReviewers = "";
 		for($i=0;$i<count($arrReviewers);$i++){
			if($i == count($arrReviewers) - 1)
				$strReviewers .= "<a href=\"#\" onClick=\"JavaScript:window.open('view_reviewer_info.php?name=".$arrReviewers[$i]."',null,'height=200,width=500,status=yes,toolbar=no,menubar=no,scrollbars=yes,location=no')\">".$arrReviewers[$i]."</a>";
			else
				$strReviewers .= "<a href=\"#\" onClick=\"JavaScript:window.open('view_reviewer_info.php?name=".$arrReviewers[$i]."',null,'height=200,width=500,status=yes,toolbar=no,menubar=no,scrollbars=yes,location=no')\">".$arrReviewers[$i]."</a>, ";		
	}

?>
      
      <tr>
      <td colspan=4> <hr> </td>
      </tr>
      <tr> 
      <td align="center" valign="top">&nbsp;</td>
      <td height="31" align="center" valign="top">#<?php echo $paperInfo->PaperID; ?></td>
      <td valign="top"><a href="view_file.php?fileid=<?php echo $FileIDData -> FileID ; 	?>"><strong><?php echo stripslashes($paperInfo -> Title); ?></strong></a><br>
          
	<?php
 	if ( $authors = retrieve_authors($paperInfo->PaperID , &$err_message ) )
	{
		echo $authors ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read author table. Try <a 	href='view_abstract.php?id=$id'>again</a>?</font>" ;
	}		
	?><br><br>

	<strong>Track:</strong>&nbsp;<?php echo  getSelectedTrackText($paperInfo->PaperID , &$err_message );?>
	<br>
	<strong>Topic(s):</strong>&nbsp;<?php echo  getSelectedCategoryCommaSeparated($paperInfo->PaperID , &$err_message );?>
	<br>
          <?php if($paperInfo -> PaperStatusName != "Not Reviewed"){  ?>
          <?php if (count($arrReviewers) < 3) echo "<font color=\"#FF0000\"><strong>Reviewers:</strong></font>"; else echo "<strong>Reviewers:</strong>";?>&nbsp;<?php echo $strReviewers; ?>
          <br>
		  <?php }?>
          <strong>Status:</strong> <?php echo $paperInfo -> PaperStatusName;  ?> 
          	  
           <?php 
           		// if(get_Number_Of_Reviews($paperInfo->PaperID) > 0){
		  		//	echo "(".get_Number_Of_Reviews($paperInfo->PaperID).")";
		   		//	}
		   ?>
      <?php 
	  if ($paperInfo -> OverallRating > 0 )
	  {
	  	echo "&nbsp; <strong>Evaluation: </strong> ";
	  	echo sprintf("%01.1f", $paperInfo -> OverallRating);
	  	echo "/10";
	  	echo " from ".get_Number_Of_Reviews($paperInfo->PaperID)." review(s)";
	  }
	  ?>
	  &nbsp;
	  <strong>User: </strong> <?php echo $paperInfo -> MemberName ?>
	  <br>
          </td>
      <td valign="middle"> <ul>
          <li><a href="view_abstract.php?id=<?php echo $paperInfo->PaperID ; ?>">View 
            Abstract</a></li>
		  <?php //Check whether Reviewer Bidding Phase is set to true

 			if((get_Num_Preferences($paperInfo->PaperID) != 0) && (checkPhase("Reviewer Bidding"))){
		  ?>
		  <li><a href="show_all_selections.php?id=<?php echo $paperInfo->PaperID ; ?>">View Preferences</a>&nbsp;<?php echo "(".get_Num_Preferences($paperInfo->PaperID).")"; ?></li>
          <?php
		  }//end of if statement
		  ?>
		  
        <?php 
		//if the paper status is not reviewed yet, then display the link to assign to reviewer
		if ($paperInfo -> PaperStatusName == "Not Reviewed"){ ?>
		<li><a href="display_assign_paper.php?paperID=<?php echo $paperInfo->PaperID ; ?>">Assign Reviewers</a></li>
        <?php } else { ?>		  
    	<li><a href="edit_assign_reviewers.php?paperID=<?php echo $paperInfo->PaperID ; ?>">Edit Reviewers</a></li>
		  <?php
		  }
		  
		  //Check whether paper status is reviewing,if it is display show review link
		  if(($paperInfo -> PaperStatusName == "Reviewing") && (get_Number_Of_Reviews($paperInfo->PaperID) != 0)){
		  ?>
          <li><a href="show_all_reviews.php?id=<?php echo $paperInfo->PaperID; ?>">View 
            Reviews</a></li>
          <?php
		  }//end of if
		
		  //Check whether the paper status is Reviewed,then display view all reviews link
		  if ($paperInfo -> PaperStatusName == "Reviewed" || $paperInfo -> PaperStatusName == "Accepted" || $paperInfo -> PaperStatusName == "Rejected" || $paperInfo -> PaperStatusName == "Marginal"){ ?>
          <li><a href="show_all_reviews.php?id=<?php echo $paperInfo->PaperID; ?>">View 
            All Reviews</a></li>
          <?php
		  }//end of if
			
			//When the paper has been reviewed, display accept and reject paper link
			if(($paperInfo -> PaperStatusName != "Not Reviewed") && (checkPhase("Reviewing"))){ ?>
          <li><a href="evaluate_paper_status.php?paperID=<?php echo $paperInfo->PaperID; ?>">Decide on Paper#<?php echo $paperInfo->PaperID; ?></a></li>
          <?php }//end of if?>
		  
        </ul></td>
    </tr>
    
    <?php			
	} //End of while loop
?>
  </table>
  <table width="100%" border="0" cellspacing="2" cellpadding="5">
	<tr>
	<td colspan = 2> <hr> </td>
	</tr>
  	<tr>				
      <td>Total Papers : <strong><?php echo $num_rows; ?></strong></td>
      <td align="right"><?php echo $prev; ?> | <?php echo $pagesLinks; ?>| <?php echo $next; ?></td>
 	</tr>
 </table>
</form>
<?php			
	do_html_footer( &$err_message );
?>

	  

