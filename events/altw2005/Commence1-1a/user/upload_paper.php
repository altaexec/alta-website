<?php 
	$php_root_path = ".." ;
	require_once("$php_root_path/includes/include_all_fns.php");
  	require_once("$php_root_path/includes/page_includes/page_fns.php");	
	session_start();
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;		
	$header = "Upload Paper" ;
	$accepted_privilegeID_arr = array ( 1 => "" ) ;
	$accepted_phaseID_arr = array ( 1 => "" ) ;
	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;	
	
	$error_array = array() ;
    
    // Define a few page vars
    $settingInfo = get_Conference_Settings();
	$trackStr = $settingInfo->TrackName; //Name for Track
	$topicStr = $settingInfo->TopicName; //Name for Topic
	$levelStr = $settingInfo->LevelName; //Name for Level
    
    if ($settingInfo->AbstractOnlySubmissions || $settingInfo->SESUG) //Abstract submission only for SESUG
    {
		$exempt_array = array ( "email" , "middlename" , "presenterbio"  , "keyword1" , "keyword2" , "keyword3" , "userfile" ) ;
		$fullPaper = false;
	}
	else{
		$exempt_array = array ( "email" , "middlename", "presenterbio", "keyword1" , "keyword2" , "keyword3" ) ;
		$fullPaper = true;
	}

	if ( count ( $HTTP_POST_VARS ) > 0 )
	{	
		if ( $HTTP_POST_VARS["Submit"] == "Update Authors" )
		{
			if ( isIntegerMoreThanZero ( $HTTP_POST_VARS["numauthors"] , &$error_array["numauthors"] ) || !empty ( $HTTP_POST_VARS["numauthors"] ) )
			{

			}
			else if ( trim ( $HTTP_POST_VARS["numauthors"] ) == "" )
			{
				$error_array["numauthors"][0] = " This entry cannot be empty. <br>\n" ;

			}
		}
		else 
		{	
			if ( $settingInfo->SESUG && !$HTTP_POST_VARS["level"] )
			{
			$error_array["level"][0] = "You must choose at least one $levelStr.<br>\n" ;
			}
			if ( !$HTTP_POST_VARS["track"] )
			{
			$error_array["track"][0] = "You must choose a $trackStr.<br>\n" ;
			}
			if ( !$HTTP_POST_VARS["category"] )
			{
				$error_array["category"][0] = "You must choose at least one $topicStr.<br>\n" ;
			}
            
			$vars = array_merge ( $HTTP_POST_VARS , $_FILES , $HTTP_POST_FILES ) ;
		    //display( $vars ) ;
            
			check_form( $vars , $error_array , &$exempt_array ) ;
		}		
	}	
    
	if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
	{
        if ( $HTTP_POST_VARS["Submit"] === "Submit" ) 
		{		
			//Everything is fine, then upload the file
			if ( $fileID = upload_file( $HTTP_POST_VARS["title"] , $HTTP_POST_VARS["abstract"] , $HTTP_POST_VARS["presenterbio"] , $HTTP_POST_VARS["numpages"] , $HTTP_POST_FILES["userfile"]["tmp_name"] , $HTTP_POST_FILES["userfile"]["name"] , $HTTP_POST_FILES["userfile"]["size"] , $HTTP_POST_FILES["userfile"]["type"] ,
								   $HTTP_POST_VARS["firstname"] , $HTTP_POST_VARS["middlename"] , $HTTP_POST_VARS["lastname"] , $HTTP_POST_VARS["email"] , $HTTP_POST_VARS["attended"] ,$HTTP_POST_VARS["presented"] ,$HTTP_POST_VARS["keyword1"] ,$HTTP_POST_VARS["keyword2"] ,$HTTP_POST_VARS["keyword3"] ,$HTTP_POST_VARS["level"] , $HTTP_POST_VARS["track"] , $HTTP_POST_VARS["category"] , &$err_message ) )
			{		
				do_html_header("Successful Uploading..." , &$err_message );
				echo " The file is uploaded successfully to the database. <br><br> View your new paper at <a href='view_paper_details.php?fileid=" .  $fileID . "'>View Paper Details</a> page. <br>" ;
				do_html_footer( &$err_message );
				exit ;				
			}
			else
			{
				do_html_header("Problem Uploading..." , &$err_message );
				$err_message .= "<br><br> Go to the <a href='upload_paper.php'>Upload Paper</a> page. " ;
			}
		}
		else
		{
			do_html_header("Upload Paper" , &$err_message ) ;
		}
	}
	else 
	{
		if ( count ( $HTTP_POST_VARS ) == 0 )
		{	
//			echo "<br>\n POST = 0 <br>\n" ;

		}
		do_html_header("Upload Paper" , &$err_message ) ;		
	}
	
	$maxfilesize = $settingInfo->MaxUploadSize ;
?>

<form enctype="multipart/form-data" name="frmupload" method="post" action="upload_paper.php"> 
<!-- <form enctype="multipart/form-data" name="frmupload" method="post" action="phpinfo.php"> -->
(* indicates mandatory field)<br><br>
	
  <table width="100%" border="0" cellpadding="3" cellspacing="0">
    <tr> 
      <td width="20%"><strong>Title *:</strong></td>
      <td width="80%"> </td>
    </tr>
    <tr>
        <td colspan="2"><input name="title" type="text" value="<?php echo stripslashes($HTTP_POST_VARS["title"]) ?>" id="title" size="75" maxlength="255"> 
        <font color="#FF0000"><?php echo $error_array["title"][0] ?></font> </td>
    </tr>
    <tr> 
      <td><strong>Number of Pages *:</strong></td>
      <td> <input name="numpages" type="text" value="<?php echo $HTTP_POST_VARS["numpages"] ?>" id="numpages" size="3" maxlength="3"> 
        <font color="#FF0000"><?php echo $error_array["numpages"][0] ?></font> 
      </td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td> <font color="#FF0000">&nbsp; </font> </td>
    </tr>
    <tr> 
      <td><strong>Number of Authors *:</strong></td>
        <?php 	// show at least one author field
        if (isset( $HTTP_POST_VARS["numauthors"] )){			
	  		$numauthors =  $HTTP_POST_VARS["numauthors"];
	  	}
	  	else{
	  		$numauthors =  1 ; 
	  	}
	  	?>
      <td><input name="numauthors" type="text" value="<?php echo $HTTP_POST_VARS["numauthors"] ?>" id="numauthors" size="3" maxlength="2"> 
        <input type="submit" name="Submit" value="Update Authors"> <font color="#FF0000"><?php echo $error_array["numauthors"][0] ?></font></td>
    </tr>
    <tr> 
      <td colspan="2"> <?php 
		$firstname = $HTTP_POST_VARS["firstname"] ;
		$middlename = $HTTP_POST_VARS["middlename"] ;
		$lastname = $HTTP_POST_VARS["lastname"] ;
		$email = $HTTP_POST_VARS["email"] ;

		$firstname_error_array = $error_array["firstname"] ;
		$middlename_error_array = $error_array["middlename"] ;
		$lastname_error_array = $error_array["lastname"] ;
		$email_error_array = $error_array["email"] ;
						
	  	echo GenerateAuthorInputTable($numauthors) ;
	  ?> </td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>File<?php if ($fullPaper) echo " *"; ?>:</strong> 
      <?php $maxMbytes=$maxfilesize/pow(2,20); echo " (maximum file size is $maxMbytes Mb)" ; ?> 
        </font></td>
    </tr>
    <tr> 
      <td colspan="2"><input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxfilesize; ?>"> 
        <input name="userfile" type="file" size="50"> <font color="#FF0000"> 
        <?php 
		  $err_mess = ( $error_array["userfile"][0] ? $error_array["userfile"][0] : $error_array["userfile"][4] ) ;
		  echo $err_mess ;
		?>
        </font></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><strong>Abstract *:</strong><font color="#FF0000"><?php echo $error_array["abstract"][0] ?></font></td>
    </tr>
    <tr> 
      <td colspan="2"> <textarea name="abstract" cols="75" rows="10" id="textarea4"><?php echo stripslashes($HTTP_POST_VARS["abstract"]) ?></textarea></td>
    </tr>
    <tr> 
      <td colspan="2"><strong>Author/Presenter Biography:</strong><font color="#FF0000"><?php echo $error_array["presenterbio"][0] ?></font></td>
    </tr>
    <tr> 
      <td colspan="2"> <textarea name="presenterbio" cols="75" rows="10" id="textarea5"><?php echo stripslashes($HTTP_POST_VARS["presenterbio"]) ?></textarea></td>
    </tr>

	<tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <?php if ($settingInfo->SESUG) { ?>
	<tr> 
      <td><strong><?php echo $attended ?> *:</strong></td>
      <td> <input name="attended" type="text" value="<?php echo $HTTP_POST_VARS["attended"] ?>" id="attended" size="3" maxlength="3"> 
        <font color="#FF0000"><?php echo $error_array["attended"][0] ?></font> 
      </td>
	 </tr>

	 <tr>
	  <td><strong><?php echo $presented ?> *:</strong></td>
      <td> <input name="presented" type="text" value="<?php echo $HTTP_POST_VARS["presented"] ?>" id="presented" size="3" maxlength="3"> 
        <font color="#FF0000"><?php echo $error_array["presented"][0] ?></font> 
      </td>
    </tr>
    <?php } ?>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <?php	
	$db = adodb_connect( &$err_message );
	
	if (!$db)
	{
    	$err_message .= "Could not connect to database server - please try later. <br>\n ";
		$err_message .= "<br><br> Try <a href='upload_paper.php'>again</a>?" ;
		exit ;
	}	
	if ($settingInfo->SESUG) {
	echo "<strong>$levelStr (select all that apply) *:</strong>\n" ;	
	echo "<font color=\"#FF0000\">" . $error_array["level"][0] . "</font>" ;
		if ( $result = GenerateSelectedCategoryInputTable ( $HTTP_POST_VARS["level"] , &$err_message, 0 , "Level" ) )
		{
			echo $result ;
		}
		else
		{
			$err_message .= "<br><br> Try <a href='upload_paper.php'>again</a>?" ;
		}
	echo "<br>" ;
	}
    echo "<strong>$trackStr *:</strong>\n " ;
	echo "<font color=\"#FF0000\">" . $error_array["track"][0] . "</font>" ;
		
		if ( $result = GenerateSelectedCategoryInputTable( $HTTP_POST_VARS["track"] , &$err_message , 0 , "Track" ) )
		{
			echo $result ;
		}
		else
		{
			$err_message .= "<br><br> Try <a href='upload_paper_info.php?paperid=" . $HTTP_POST_VARS["paperid"] . "'>again</a>?" ;
		}
		
	echo "<br>" ;
	echo "<STRONG>$topicStr(s) *:</STRONG>\n" ;	
	echo "<font color=\"#FF0000\">" . $error_array["category"][0] . "</font>" ;
	if ( $result = GenerateSelectedCategoryInputTable ( $HTTP_POST_VARS["category"] , &$err_message ) )
	{
		echo $result ;
	}
	else
	{
		$err_message .= "<br><br> Try <a href='upload_paper.php'>again</a>?" ;
	}
	

?>
      </td>
    </tr>
    <?php if ($settingInfo->SESUG) { ?> 
	<tr>
	  <td><strong><?php echo $keyword ?> :</strong></td>
      <td> <input name="keyword1" type="text" value="<?php echo $HTTP_POST_VARS["keyword1"] ?>" id="keyword1" size="20" maxlength="50"> 
        <font color="#FF0000"><?php echo $error_array["keyword1"][0] ?></font> 
      </td>
    </tr>

	<tr>
	  <td><strong><?php echo $keyword ?> :</strong></td>
      <td> <input name="keyword2" type="text" value="<?php echo $HTTP_POST_VARS["keyword2"] ?>" id="keyword2" size="20" maxlength="50"> 
        <font color="#FF0000"><?php echo $error_array["keyword2"][0] ?></font> 
      </td>
    </tr>

	<tr>
	  <td><strong><?php echo $keyword ?> :</strong></td>
      <td> <input name="keyword3" type="text" value="<?php echo $HTTP_POST_VARS["keyword3"] ?>" id="keyword3" size="20" maxlength="50"> 
        <font color="#FF0000"><?php echo $error_array["keyword3"][0] ?></font> 
      </td>
    </tr>
    <?php } ?>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Submit">

</td>
    </tr>
  </table>
</form>

<?php

do_html_footer( &$err_message );

?>
