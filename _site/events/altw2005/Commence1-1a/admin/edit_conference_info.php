<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Establish connection with database // Will be removed.
	$db = adodb_connect();
	
	if (!$db){
		echo "Could not connect to database server - please try later.";
		exit;
	}
	
	if($HTTP_POST_VARS["Submit"] == "Submit"){
		$error_array = array() ;
		$exempt_array = array ( "logofile" ) ;	
		//$exempt_array = array () ;	
		
		if($HTTP_POST_FILES["logofile"]["name"] != "")
			$vars = array_merge( $HTTP_POST_VARS , $HTTP_POST_FILES , $_FILES ) ;
		else	
			$vars =  $HTTP_POST_VARS  ;
			
		check_form( $vars , $error_array , &$exempt_array ) ;
	}

	if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 && $HTTP_POST_VARS["Submit"] == "Submit" )
	{
		include ( "$php_root_path"."$privilege_root_path/process_edit_conference_info.php" ) ;	
	}
	else
	{	
		do_html_header("Edit Conference Information");
		/*echo "Some error";
		echo "<pre>";
		print_r($error_array);
		echo "</pre>";
		echo "<br>File type: ".$HTTP_POST_FILES["logofile"]["type"];*/
		if($HTTP_POST_VARS["Submit"] == "Undo Changes")
		{
			unset ($HTTP_POST_VARS);
			/*	
			unset ( $HTTP_POST_VARS["name"] ) ;
			unset ( $HTTP_POST_VARS["codename"] ) ;
			unset ( $HTTP_POST_VARS["date"] ) ;
			unset ( $HTTP_POST_VARS["location"] ) ;
			unset ( $HTTP_POST_VARS["hostname"] ) ;
			unset ( $HTTP_POST_VARS["error_array"] ) ;
			*/
		}
	}

//Call the function to get the conference information
$conferenceInfo = get_conference_info();

?>
<script language="JavaScript" src="script/popcalendar.js"></script>
<form action="edit_conference_info.php" enctype="multipart/form-data" method="post" name="form1">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td width="25%">Name:</td>
      <td width="75%">
	  <?php if ($conferenceInfo -> ConferenceID) { ?>
	  <input type="hidden" name="conferenceID" value="<?php echo $conferenceInfo -> ConferenceID; ?>">
      <?php } ?>
	  <input name="name" type="text" id="name" size="60" maxlength="100" value="<?php echo ( isset ( $HTTP_POST_VARS["name"] ) ? $HTTP_POST_VARS["name"] : $conferenceInfo -> ConferenceName ) ; ?>"> 
        <font color="#FF0000"><?php echo $error_array["name"][0] ; ?></font></td>
    </tr>
    <tr> 
      <td>Code Name:</td>
      <td><input name="codename" type="text" id="codename" size="20" maxlength="20" value="<?php echo ( isset ( $HTTP_POST_VARS["codename"] ) ? $HTTP_POST_VARS["codename"] : $conferenceInfo -> ConferenceCodeName ) ; ?>">
        (e.g., WDIC2003) <font color="#FF0000"><?php echo $error_array["codename"][0] ; ?></font></td>
    </tr>
    <tr> 
      <td>Start Date:</td>
      <td>
      <input name="ConferenceStartDate" type=text id="ConferenceStartDate" size="20" maxlength="30" value="<?php echo ( isset ( $HTTP_POST_VARS["ConferenceStartDate"] ) ? $HTTP_POST_VARS["ConferenceStartDate"] : $conferenceInfo -> ConferenceStartDate ) ; ?>" onclick="showCalendar(this, this, 'yyyy-mm-dd','en',1);">
      &nbsp;<font color="#FF0000"><?php echo $error_array["ConferenceStartDate"][0] ; ?></font></td>
    </tr>
    <tr>
      <td>End Date:</td>
      <td>
      <input name="ConferenceEndDate" type=text id="ConferenceEndDate" size="20" maxlength="30" value="<?php echo ( isset ( $HTTP_POST_VARS["ConferenceEndDate"] ) ? $HTTP_POST_VARS["ConferenceEndDate"] : $conferenceInfo -> ConferenceEndDate ) ; ?>" onclick="showCalendar(this, this, 'yyyy-mm-dd','en',1);">
      &nbsp;<font color="#FF0000"><?php echo $error_array["ConferenceEndDate"][0] ; ?></font></td>
    </tr>
    <tr> 
      <td>Location:</td>
      <td><input name="location" type="text" id="location" size="20" maxlength="20" value="<?php echo ( isset ( $HTTP_POST_VARS["location"] ) ? $HTTP_POST_VARS["location"] : $conferenceInfo -> ConferenceLocation ) ; ?>">
        <font color="#FF0000"><?php echo $error_array["location"][0] ; ?></font></td>
    </tr>
    <tr> 
      <td>Host Name:</td>
      <td><input name="hostname" type="text" id="hostname" size="50" maxlength="100" value="<?php echo ( isset ( $HTTP_POST_VARS["hostname"] ) ? $HTTP_POST_VARS["hostname"] : $conferenceInfo -> ConferenceHostName ) ; ?>">
        <font color="#FF0000"><?php echo $error_array["hostname"][0] ; ?></font></td>
    </tr>
    <tr> 
      <td>Contact Email:</td>
      <td><input name="email" type="text" id="email" value="<?php echo ( isset ( $HTTP_POST_VARS["email"] ) ? $HTTP_POST_VARS["email"] : $conferenceInfo -> ConferenceContact ) ; ?>" size="30" maxlength="50"><font color="#FF0000"><?php echo $error_array["email"][0] ; ?></font></td>
    </tr>
    <tr> 
      <td>Logo for Conference:</td>
      <td><input type="hidden" name="MAX_FILE_SIZE" value="1000000"><input name="logofile" type="file" id="logofile" size="50">&nbsp;(jpeg,png)&nbsp;
        <font color="#FF0000"><?php  if(isset($error_array["logofile"][0])) echo $error_array["logofile"][0] ; /*elseif(isset($error_array["logofile"][1])) echo $error_array["logofile"][1];*/ elseif (isset($error_array["logofile"][3])) echo $error_array["logofile"][3]; ?></font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><input name="Submit" type="submit" id="Submit" value="Submit"> <input name="Submit" type="submit" id="Submit" value="Undo Changes"></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>

<?php do_html_footer( &$err_message ) ; ?>
