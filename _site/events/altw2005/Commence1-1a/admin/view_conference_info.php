<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	I think this line causes and error in the Phase object BL !!
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;

	do_html_header("Conference Information");

	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
    
    //Call the function to get the conference information
	$conferenceInfo = get_conference_info();
?>

<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr> 
    <td colspan="2"> 
      <?php 	if ($conferenceInfo -> FileName != "")
					echo "<img src=\"view_logofile.php\" alt=\"Logo\">";
	?>
      &nbsp;
      <h4><?php echo $conferenceInfo -> ConferenceName; ?></h4></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td width="20%">Code Name:</td>
    <td width="80%"><?php echo $conferenceInfo -> ConferenceCodeName; ?></td>
  </tr>
  <tr> 
    <td>Duration:</td>
    <td>
        From 
        <strong>
        <?php echo format_date($settingInfo->DateFormatShort, $conferenceInfo -> ConferenceStartDate) ?>
        </strong> To 
        <strong>
        <?php echo format_date($settingInfo->DateFormatShort, $conferenceInfo -> ConferenceEndDate) ?>
        </strong>
        </font>
    </td>
  </tr>
  <tr> 
    <td>Location:</td>
    <td><?php echo $conferenceInfo -> ConferenceLocation; ?></td>
  </tr>
  <tr> 
    <td>Host Name:</td>
    <td><?php echo $conferenceInfo -> ConferenceHostName; ?></td>
  </tr>
  <tr>
    <td>Contact Email:</td>
    <td><?php echo $conferenceInfo -> ConferenceContact; ?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php do_html_footer();?>
