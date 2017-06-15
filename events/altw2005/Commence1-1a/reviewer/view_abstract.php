<?php //////////// REVIEWER PHASE 2 ///////////////
//////////// REVIEWER PHASE 3 ///////////////

	$php_root_path = ".." ;
	$privilege_root_path = "/reviewer" ;
//	extract ( $HTTP_GET_VARS , EXTR_REFS ) ;
//	extract ( $HTTP_POST_VARS , EXTR_REFS ) ;
//	extract ( $HTTP_POST_FILES , EXTR_REFS ) ;

	require_once("includes/include_all_fns.php");
//	session_cache_limiter('private') ;
	session_start() ;
	header("Cache-control: private");
//	extract ( $_SESSION , EXTR_REFS ) ;
	// Define a few page vars
    $settingInfo = get_Conference_Settings();
	$trackStr = $settingInfo->TrackName; //Name for Track
	$topicStr = $settingInfo->TopicName; //Name for Topic
	$levelStr = $settingInfo->LevelName; //Name for Level

	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	$header = "View Abstract" ;
	$accepted_privilegeID_arr = array ( 2 => "" ) ;
	$accepted_phaseID_arr = array ( 2 => "" , 3 => "" ) ;
	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;

	//Get the paper information
	if ( ( $paperInfo = get_paper_info($HTTP_GET_VARS["paperid"] , &$err_message ) ) === false )
	{
		do_html_header("View Abstract Failed" , &$err_message );
		$err_message .= " Cannot retrieve information from database. <br>\n" ;
		$err_message .= "<br><br> Try <a href='view_abstract.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?" ;
		do_html_footer(&$err_message);
		exit;
	}

	//Get the lastest file of the paper
	if ( ( $FileIDData = get_latestFile($HTTP_GET_VARS["paperid"] , &$err_message ) ) === false )
	{
		do_html_header("View Abstract Failed" , &$err_message );
		$err_message .= " Could not execute \"get_latestFile\" in \"view_abstract.php\". <br>\n" ;
		$err_message .= "<br><br> Try <a href='view_abstract.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?" ;
		do_html_footer(&$err_message);
		exit;
	}

	if ( $HTTP_POST_VARS["showing"] )
	{
		$HTTP_GET_VARS["showing"] = $HTTP_POST_VARS["showing"] ;
	}
	if ( $HTTP_POST_VARS["sort"] )
	{
		$HTTP_GET_VARS["sort"] = $HTTP_POST_VARS["sort"] ;
	}
	if ( !$HTTP_POST_VARS["referer"] )
	{
//		echo $_SERVER["HTTP_REFERER"] ;
		$HTTP_POST_VARS["referer"] = $_SERVER["HTTP_REFERER"] ;
	}

	$papers_str = "" ;
	if ( $HTTP_POST_VARS["papers"] )
	{
		foreach ( $HTTP_POST_VARS["papers"] as $some => $postpaperid )
		{
			$papers_str .= ( "<input type=\"hidden\" value=\"" . $postpaperid . "\" name=\"papers[]\">\n" ) ;
		}
	}

	$storepapers_str = "" ;
	if ( $HTTP_POST_VARS["storepapers"] )
	{
		foreach ( $HTTP_POST_VARS["storepapers"] as $some => $id )
		{
			$storepapers_str .= ( "<input type=\"hidden\" value=\"" . $id . "\" name=\"storepapers[]\">\n" ) ;
		}
	}

	$settingInfo = get_Conference_Settings();

	do_html_header("View Abstract" , &$err_message );
?>
<br><br>
<form name="frmPaper" method="post" action="<?php echo $HTTP_POST_VARS["referer"] ; ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td align="center"><h3>#<?php echo $paperInfo -> PaperID; ?>&nbsp;&nbsp;<?php echo stripslashes($paperInfo -> Title); ?></h3></td>
  </tr>
  <tr>
    <td align="center"><h4>
<?php if ( $authors = retrieve_authors( $paperInfo -> PaperID , &$err_message ) )
	{
		echo $authors ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read author table. Try <a href='view_abstract.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?</font>" ;
	}
?></h4>
 </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><hr></td>
  </tr>
  <tr>
    <td><p><strong>Abstract:</strong></p>
      <p><?php echo nl2br( ereg_replace( "  " , "&nbsp;&nbsp;" , stripslashes( $paperInfo -> PaperAbstract )));
	  ?></p></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><p><strong><?php echo "$trackStr:" ?></strong>
    <?php if ( $catcomsep = GetSelectedTrackText( $paperInfo -> PaperID , &$err_message ) )
	{
		echo $catcomsep ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read Paper Track table. Try <a href='view_abstract.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?</font>" ;
	}
	?></td>
  </tr>
  <tr>
    <td><p><strong><?php echo "$topicStr(s):" ?></strong>
    <?php if ( $catcomsep = getSelectedCategoryCommaSeparated( $paperInfo -> PaperID , &$err_message ) )
	{
		echo $catcomsep ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read Paper Category table. Try <a href='view_abstract.php?paperid=".$HTTP_GET_VARS["paperid"]."'>again</a>?</font>" ;
	}
	?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href='download_file.php?fileid=<?php echo $FileIDData -> FileID ; ?>'>Download Paper</a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
<?php

	echo $papers_str ;

	echo $storepapers_str ;

?>
  <input type="hidden" value="<?php echo $HTTP_GET_VARS["sort"] ; ?>" name="sort">
  <input type="hidden" value="<?php echo $HTTP_GET_VARS["showing"] ; ?>" name="showing">
  <input type="hidden" value="<?php echo $HTTP_POST_VARS["process"] ; ?>" name="process">
  <input type="hidden" value="<?php echo $HTTP_POST_VARS["myreferer"] ; ?>" name="myreferer">
  	<td><input type="submit" name="Submit" value="Back"></td>
  </tr>
</table>
</form>
<?php

do_html_footer( &$err_message );

?>
