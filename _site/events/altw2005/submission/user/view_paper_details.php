<?php
$php_root_path = ".." ;
require_once("$php_root_path/includes/include_all_fns.php");
require_once("$php_root_path/includes/page_includes/page_fns.php");
session_start();
//extract ( $_SESSION , EXTR_REFS ) ;

$err_message = " Unable to process your request due to the following problems: <br>\n" ;
$header = "View Paper Details" ;
$accepted_privilegeID_arr = array ( 1 => "" ) ;
$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;
$fileid =& $HTTP_GET_VARS["fileid"] ;
$_SESSION["phase"]->set_view_paper_details( $fileid , &$err_message ) ;
$paperInfo = ( $_SESSION["phase"]->get_view_paper_details( $fileid , &$err_message ) ) ;

// Define a few page vars
$settingInfo = get_Conference_Settings();
$trackStr = $settingInfo->TrackName; //Name for Track
$topicStr = $settingInfo->TopicName; //Name for Topic
$levelStr = $settingInfo->LevelName; //Name for Level

do_html_header("View Paper Details" , &$err_message );

?>

<br>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="3">
  <tr>
    <td height="20"><strong>Title:</strong></td>
    <td width="80%"><p><?php echo stripslashes($paperInfo -> Title); ?></p></td>
  </tr>
  <tr>
    <td height="20"><strong>Authors:</strong></td>
    <td width="80%"><table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <?php
	if ( $result = getAuthorsCommaSeparated ( $_SESSION["phase"]->writtenResult , &$err_message ) )
	{
		echo $result ;
	}
	else
	{
		$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?";
	}
?>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="1" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td height="1"><strong>Pages:</strong></td>
    <td width="80%"><?php echo $paperInfo -> NumberOfPages; ?></td>
  </tr>
  <tr>
    <td height="1"><strong>Uploaded:</strong></td>
    <td width="80%"><?php echo $paperInfo -> DateTime; ?></td>
  </tr>
  <tr>
    <td height="20"><strong>File:</strong></td>
    <td width="80%"><?php echo $paperInfo -> FileName . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $paperInfo -> FileSize . " bytes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $paperInfo -> FileType ; ?></td>
  </tr>
  <tr>
    <td height="1" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" colspan="2"><strong>Abstract:</strong></td>
  </tr>
  <tr>
    <td height="20" colspan="2">
      <?php
	  echo nl2br( ereg_replace( "  " , "&nbsp;&nbsp;" , stripslashes( $paperInfo -> PaperAbstract ) ) ) ;
	  ?>
    </td>
  </tr>
  <tr><td>&nbsp</td></tr>
  <tr>
    <td height="20" colspan="2"><strong>Author/Presenter Bio:</strong></td>
  </tr>

  <tr>
    <td height="20" colspan="2">
      <?php
	  echo nl2br( ereg_replace( "  " , "&nbsp;&nbsp;" , stripslashes( $paperInfo -> PresenterBio ) ) ) ;
	  ?>
    </td>
  </tr>
  <tr>
    <td height="20" colspan="2">&nbsp;</td>
  </tr>

  <?php if ($settingInfo->SESUG) { ?>
  <tr>
    <td height="1"><strong><?php echo $attended ?> :</strong></td>
    <td width="80%"><?php echo $paperInfo -> SESUG_Attended; ?></td>
  </tr>

  <tr>
    <td height="1"><strong><?php echo $presented ?> :</strong></td>
    <td width="80%"><?php echo $paperInfo -> SESUG_Presented; ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td height="20" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" colspan="2">
      <?php
    if ($settingsInfo -> SESUG) {
        echo "<STRONG>$levelStr:</STRONG><BR>\n" ;
        if ( $result = GetSelectedLevel( $paperInfo->PaperID , &$err_message ) )
        {
            echo $result ;
        }
        else
        {
            $err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?";
        }
        echo "<br>" ;
    }
	echo "<STRONG>$trackStr:</STRONG><BR>\n" ;
	if ( $result = GetSelectedTrack( $paperInfo->PaperID , &$err_message ) )
	{
		echo $result ;
	}
	else
	{
		$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?";
	}
	echo "<br>" ;
	echo "<STRONG>$topicStr(s):</STRONG><BR>\n" ;
	if ( $result = GetSelectedCategory( $paperInfo->PaperID , &$err_message ) )
	{
		echo $result ;
		echo "&nbsp;" ;
		echo nl2br( ereg_replace( "  " , "&nbsp;&nbsp;" , stripslashes( $paperInfo -> Keyword1 ) ) )."   ".
			nl2br( ereg_replace( "  " , "&nbsp;&nbsp;" , stripslashes( $paperInfo -> Keyword2 ) ) )."   ".
			nl2br( ereg_replace( "  " , "&nbsp;&nbsp;" , stripslashes( $paperInfo -> Keyword3 ) ) );
	}
	else
	{
		$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?";
	}
?>
    </td>
  </tr>
  <tr>
    <td height="20" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" ><a href="download_file.php?fileid=<?php echo $_SESSION["phase"]->fileData->FileID ; ?>"><strong>Download Paper</strong></a></td>
    <td width="80%">
<?php
	if ( $_SESSION["phase"]->phaseID == 1 || $_SESSION["phase"]->phaseID == 4 )
	{
?>
		<a href="edit_paper_info.php?paperid=<?php echo $paperInfo->PaperID ; ?>"><strong>Edit Paper Details</strong></a>
<?php
	}
?>
	</td>
  </tr>
</table>
<?php

do_html_footer( &$err_message );

?>
