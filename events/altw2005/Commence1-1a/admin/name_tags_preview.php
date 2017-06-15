<?php 
	//Define the constant for the maximum number of papers that one page can display
	define("MAX_TAGS",6);

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the follow problems: <br>\n" ;
	
	do_html_header("Name Tags Preview");
    
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
	// echo $registerSQL;
	$registerResult = $db -> Execute($registerSQL);
	
	if(!$registerResult){
		echo "Could not retrieve the registration information - please try later";
		exit;
	}
	
	if($registerResult -> RecordCount() == 0){
		echo "No registrations have been made - please try again later";
		exit;
	}
	
	//Call the function to display the range of records
	$from = evaluate_records_range($showing,$numRegistrations,MAX_TAGS);		
			
	//Call the function to evaluate prev
	$prev = evaluate_prev($sort,$showing,$numRegistrations,MAX_TAGS);
	$next = evaluate_next($sort,$showing,$numRegistrations,MAX_TAGS);
	
	//Call the function to evaluate page links
	$pagesLinks = evaluate_pages_links($sort,$showing,$numRegistrations,MAX_TAGS);	

?>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td>&nbsp;</td>
    <td align="right"><a href="#" onClick="JavaScript: window.open('print_name_tags.php?sort=<?php echo $sort; ?>&showing=<?php echo $showing; ?>',null,'height=600,width=700,status=yes,toolbar=no,menubar=no,scrollbars=yes,location=no');">Printer 
      Friendly Version</a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr> 
    <td>From: <?php echo "<strong>$from</strong>";	?></td>
    <td align="right"><?php echo $prev; ?>&nbsp;|<?php echo $pagesLinks; ?>|&nbsp;<?php echo $next; ?></td>
  </tr>
  <tr> 
    <td colspan="2" align="center"><table width="80%" border="0" cellspacing="5" cellpadding="0">
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
                <td align="center"><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
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
                        <td colspan="3" align="center">
			<?php 
			$first = $registerInfo -> FirstName ;
			$middle = $registerInfo -> MiddleName ;
			$last = $registerInfo -> LastName ;
			$name = formatAuthor($first, $middle, $last) ;
			echo $name; ?>
			</td>
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
				}/*end of if statment*/	  
		  	}/*end of inner for loop*/?>
        </tr>
        <?php }/*end of outer for loop*/?>
      </table></td>
  </tr>
  <tr> 
    <td>Total Name Tags:&nbsp;<?php echo $numRegistrations; ?></td>
    <td align="right" ><?php echo $prev; ?>&nbsp;|<?php echo $pagesLinks; ?>|&nbsp;<?php echo $next; ?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td align="right" >&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td align="right"> <a href="#" onClick="JavaScript: window.open('print_name_tags.php?sort=<?php echo $sort; ?>&showing=<?php echo $showing; ?>',null,'height=600,width=700,status=yes,toolbar=no,menubar=no,scrollbars=yes,location=no');">Printer 
      Friendly Version</a> </td>
  </tr>
</table>
<?php do_html_footer();?>
