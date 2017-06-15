<?php //////////// REVIEWER PHASE 3 ///////////////
	$php_root_path = ".." ;
	$privilege_root_path = "/reviewer" ;
//	extract ( $HTTP_GET_VARS , EXTR_REFS ) ;
//	extract ( $HTTP_POST_VARS , EXTR_REFS ) ;
//	extract ( $HTTP_POST_FILES , EXTR_REFS ) ;

	require_once("includes/include_all_fns.php");		
	session_start() ;
//	extract ( $_SESSION , EXTR_REFS ) ;
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;		
	$header = "Show Review" ;
	$accepted_privilegeID_arr = array ( 2 => "" ) ;
	$accepted_phaseID_arr = array ( 3 => "" ) ;
	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;	
    
	//Get the paper information
	if ( ( $paperInfo = get_paper_info($HTTP_GET_VARS["paperid"] , &$err_message ) ) === false )
	{
		do_html_header("Show Review Failed" , &$err_message );	
		$err_message .= " Cannot retrieve information from database. <br>\n" ;
		$err = $err_message . "<br><br> Try <a href='show_review.php?paperid=" .$HTTP_GET_VARS["paperid"]."'>again</a>?" ;
		do_html_footer(&$err);
		exit;	
	}
		
	//Get the lastest file of the paper				
	if ( ( $FileIDData = get_latestFile($HTTP_GET_VARS["paperid"] , &$err_message ) ) === false )
	{
		do_html_header("Show Review Failed" , &$err_message );		
		$err_message .= " Could not execute \"get_latestFile\" in \"show_review.php\". <br>\n" ;
		$err = $err_message . "<br><br> Try <a href='show_review.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?" ;
		do_html_footer(&$err);
		exit;			
	}	
	
	//Call the function to retrieve the Review of the paper
	if ( ( $reviewInfo = get_review($HTTP_GET_VARS["paperid"] , &$err_message ) ) === false )
	{
		do_html_header("Show Review Failed" , &$err_message );		
		$err_message .= " Could not execute \"get_review\" in \"show_review.php\". <br>\n" ;
		$err = $err_message . "<br><br> Try <a href='show_review.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?" ;
		do_html_footer(&$err);
		exit;					
	}
	
	//Assign the values to the variables
	$objectives = $reviewInfo -> Objectives;
	$validity = $reviewInfo -> Validity;
	$innovativeness = $reviewInfo -> Innovativeness;
	$presentation = $reviewInfo -> Presentation;
	$bibliography = $reviewInfo -> Bibliography;
	$acceptpaper = $reviewInfo -> AcceptPaper;
	$acceptposter = $reviewInfo -> AcceptPoster;
			
	do_html_header("Review" , &$err_message );
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td><h4><?php echo stripslashes($paperInfo -> Title); ?></h4></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><strong>PaperID: </strong> <?php echo $paperInfo -> PaperID; ?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><strong>Authors: </strong><?php if ( $authors = retrieve_authors( $paperInfo -> PaperID , &$err_message ) )
	{
		echo $authors ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read author table. Try <a href='show_review.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?</font>" ;
	}
	?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><p><strong>Keywords: </strong><?php if ( $catcomsep = getSelectedCategoryCommaSeparated($paperInfo->PaperID , &$err_message ) )
	{
		echo $catcomsep ;
	}
	else
	{
		$err_message .= "Could not execute \"getSelectedCategoryCommaSeperated\" in \"show_review.php\". <br>\n" ;
		echo " <font color=\"#FF0000\"> Could not read Paper Category table. Try <a href='show_review.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?</font>" ;
	}		  		  
	?></p>
      </td>
  </tr>
  <tr> 
    <td><hr></td>
  </tr>
  <tr> 
    <td><p><strong>Ranking Criteria<br>
        </strong></p>
      <table width="70%" border="1" cellspacing="2" cellpadding="1">
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
and there are no technical and/or methodological flaws</td>
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
        </strong><?php if ( ( $comment = get_comment($paperInfo -> PaperID , $reviewInfo->MemberName , &$err_message ) ) === false )
		{
			$err_message .= "Could not execute \"getSelectedCategoryCommaSeperated\" in \"show_review.php\". <br>\n" ;
			echo " <font color=\"#FF0000\"> Could not retrieve Comments. Try <a href='show_review.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?</font>" ;
		}
		else
		{
		 	echo nl2br( ereg_replace( "  " , "&nbsp;&nbsp;" , stripslashes( $comment )));		
		}
		 ?></p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?php do_html_footer(&$err_message);

?>
