<?php 
	//Define the constant for the maximum number of papers that one page can display
	define("MAX_TAGS",6);
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
		

	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		echo "Could not connect to database server - please try later.";
		exit;
	}
	
	$showing = $HTTP_GET_VARS["showing"];
	//Call function to evaluate showing
	$showing = evaluate_showing($showing);	
	
	//Call the function to get the conference information
	$conferenceInfo = get_conference_info();
	
	//Retrieve the registration information
	$registerSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Registration";
	$countResult = $db -> Execute($registerSQL);
	$numRegistrations = $countResult -> RecordCount();
	
	$registerSQL .= " LIMIT ".$showing.",".MAX_TAGS;
	$registerResult = $db -> Execute($registerSQL);
	
	if(!$registerResult){
		echo "Could not retrieve the registration information - please try later";
		exit;
	}
	
	if($registerResult -> RecordCount() == 0){
		echo "There is no registraitons has been made - please try again later";
		exit;
	}	

?>
<html>
<head>
<title>Commence Conference System</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="stylesheets/CommentStyle.css" rel="stylesheet" type="text/css">
<?php echo "<link href=\"$php_root_path/stylesheets/CommentStyle.css\" rel=\"stylesheet\" type=\"text/css\">\n"; ?>
</head>
<h1>Print Name Tags</h1>
<body>
<table width="80%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td><table width="80%" border="0" cellspacing="0" cellpadding="0">
        <?php 			
		  	for ( $i = 0 ; $i < $registerResult -> RecordCount() ; ){?>
        <tr> 
          <?php 	
				for ( $j = 0 ; $j < 2 ; $i++ , $j++ )
				{
		  			
					if($registerInfo = $registerResult -> FetchNextObj()){			
			?>
          <td align="center"><table width="324" height="216" border="1" cellpadding="1" cellspacing="0" bordercolor="#666666">
              <tr> 
                <td align="center"> <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td width="20%" align="right">
                       <?php 	if ($conferenceInfo -> FileName != "")
									echo "<img src=\"view_logofile.php\" alt=\"Logo\">";
								else
									echo "&nbsp;";
						?>					  
					  </td>
                      <td width="60%" align="center"><strong><?php echo $conferenceInfo -> ConferenceCodeName; ?></strong></td>
                      <td width="20%">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td colspan="3" align="center"><strong><em><?php echo $conferenceInfo -> ConferenceName; ?></em></strong><br> 
                        <?php echo $conferenceInfo -> ConferenceLocation; ?>,&nbsp;<?php echo $conferenceInfo -> ConferenceStartDate; ?> to <?php echo $conferenceInfo -> ConferenceEndDate; ?></td>
                    </tr>
                    <tr> 
                      <td colspan="3" align="center">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td colspan="3" align="center"><?php 
			$first = $registerInfo -> FirstName ;
			$middle = $registerInfo -> MiddleName ;
			$last = $registerInfo -> LastName ;
			$name = formatAuthor($first, $middle, $last) ;
			echo $name; ?></td>
                    </tr>
                    <tr> 
                      <td colspan="3" align="center"><?php echo $registerInfo -> Organisation; ?></td>
                    </tr>
                    <tr> 
                      <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td colspan="3" align="center">Hosted by <?php echo $conferenceInfo -> ConferenceHostName; ?></td>
                    </tr>
                    <tr> 
                      <td colspan="3" align="center">&nbsp;</td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
          <?php 
				} /* end of if statment */
		  	} /* end of inner for loop */?>
        </tr>
        <?php } /* end of outer for loop */?>
      </table></td>
  </tr>
  <tr> 
    <td><input type="button" name="Button2" value="Print" onClick="JavaScript:window.print()"> <input type="button" name="Button" value="Close" onClick="JavaScript:window.close()"> 
    </td>
  </tr>
</table>
</body>
</html>

