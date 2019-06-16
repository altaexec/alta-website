<?php //////////// REVIEWER PHASE 2 ///////////////	
	
	$php_root_path = ".." ;
	$privilege_root_path = "/reviewer" ;
//	extract ( $HTTP_POST_VARS , EXTR_REFS ) ;

	require_once("includes/include_all_fns.php");		
	session_start() ;
//	extract ( $_SESSION , EXTR_REFS ) ;
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	$header = "Update Paper Bids" ;
	$accepted_privilegeID_arr = array ( 2 => "" ) ;
	$accepted_phaseID_arr = array ( 2 => "" ) ;
	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;			

	
			
	function redisplay( &$paperid_array , &$process , &$dbprefix , $err_message = "" )
	{
        //Establish connection with database
        $db = adodb_connect( &$err_message );
        
		//global $_SESSION ;
		
		$i = 0;
		$array = array() ;
			
		reset ( $paperid_array ) ;
		foreach( $paperid_array as $some => $paperID )
		{
			//Get the paper information
			if ( ( $paperInfo = get_paper_info($paperID , &$err_message ) ) === false )
			{
				do_html_header("Update Paper Bids Failed" , &$err_message );	
				$err_message .= " Cannot retrieve information from database. <br>\n" ;
				$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;				
				do_html_footer(&$err_message) ;
				exit ;
			}
			$array[$i]["paperid"] = $paperInfo -> PaperID ;
			$array[$i]["papertitle"] = stripslashes ( $paperInfo -> Title ) ;
			
			//Get the lastest file of the paper				
			if ( ( $FileIDData = get_latestFile($paperID , &$err_message ) ) === false )
			{
				do_html_header("Update Paper Bids Failed" , &$err_message );				
				$err_message .= " Could not execute \"get_latestFile\" in \"update_biddings.php\". <br>\n" ;
				$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;				
				do_html_footer(&$err_message) ;
				exit ;				
			}
			$array[$i]["fileid"] = $FileIDData -> FileID ;
			
			if ( $catcomsep = getSelectedCategoryCommaSeparated($paperInfo->PaperID , &$err_message ) )
			{
				$array[$i]["cat"] = $catcomsep ;
			}
			else
			{
				do_html_header("Update Paper Bids Failed" , &$err_message );				
				$err_message .= " Could not execute \"getSelectedCategoryCommaSeperated\" in \"update_biddings.php\". <br>\n" ;
				$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;				
				do_html_footer(&$err_message) ;
				exit ;				
			}		  
			
			if ( $authors = retrieve_authors( $paperInfo->PaperID , &$err_message ) )
			{
				$array[$i]["author"] = $authors ;
			}
			else
			{
				do_html_header("Update Paper Bids Failed" , &$err_message );				
				$err_message .= " Could not execute \"retrieve_authors\" in \"update_biddings.php\". <br>\n" ;
				$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;				
				do_html_footer(&$err_message) ;
				exit ;				
			}
			
			if ( $process === "update" )
			{
				$preferenceSQL = " SELECT PreferenceID FROM " . $GLOBALS["DB_PREFIX"] . "Selection " ;
				$preferenceSQL .= " WHERE PaperID = ". $paperInfo->PaperID;
				$preferenceSQL .= " AND Membername = '" . $_SESSION["valid_user"] . "'";
				$preferenceResult = $db -> Execute($preferenceSQL);
				if ( !$preferenceResult )
				{
					do_html_header("Update Paper Bids Failed" , &$err_message );				
					$err_message .= " Could not query \"Selection\" table in database by \"redisplay()\" of \"update_biddings.php\". <br>\n" ;
					$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;				
					do_html_footer(&$err_message) ;
					exit ;							
				}
				$userPreference = $preferenceResult -> FetchNextObj() ;
				$array[$i]["bidid"] = $userPreference -> PreferenceID ;			
			}
			
			if ( $bidtable = Generate_Preference_Radio_Input_Table( $paperInfo->PaperID , $array[$i]["bidid"] , &$err_message )	)
			{
				$array[$i]["bid"] = $bidtable ;
			}
			else
			{
				do_html_header("Update Paper Bids Failed" , &$err_message );				
				$err_message .= " Could not execute \"retrieve_authors\" in \"update_biddings.php\". <br>\n" ;
				$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;				
				do_html_footer(&$err_message) ;
				exit ;							
			}
			
			$i++;
				
		} //End of for loop
		return $array ;
	}
	
//	$papers_str = "" ;	
	$storepapers_str = "" ;
	
	// from bid_all_papers or edit_paper_bids to update_biddings to view_abstract back to update_biddings
	if ( $HTTP_POST_VARS["storepapers"] )	// for getting back stored checked boxes from view_abstract.	
	{
		foreach ( $HTTP_POST_VARS["storepapers"] as $some => $id )
		{
			$storepapers_str .= ( "<input type=\"hidden\" value=\"" . $id . "\" name=\"storepapers[]\">\n" ) ;
		}	
	}
	else if ( $HTTP_POST_VARS["papers"] )	// for storing checked boxes for bid_all_papers and edit_paper_bids.
	{
		reset ( $HTTP_POST_VARS["papers"] ) ;
		foreach ( $HTTP_POST_VARS["papers"] as $some => $storepapid )
		{
			$storepapers_str .= "<input type=\"hidden\" value=\"" . $storepapid . "\" name=\"storepapers[]\">\n" ;
		}
	}	
	
    if ( $HTTP_GET_VARS["paperid"] )
	{
		$HTTP_POST_VARS["papers"] = array ( $HTTP_GET_VARS["paperid"] ) ;
	}
	
	if ( $HTTP_POST_VARS["process"] )
	{
		$HTTP_GET_VARS["process"] = $HTTP_POST_VARS["process"] ;
	}
	
	if ( $HTTP_POST_VARS["showing"] )
	{
		$HTTP_GET_VARS["showing"] = $HTTP_POST_VARS["showing"] ;
	}
$showing = $HTTP_GET_VARS["showing"] ; //define variables for passing to function calls
$sort = $HTTP_GET_VARS["sort"] ;

	if ( $HTTP_POST_VARS["sort"] )
	{
		$HTTP_GET_VARS["sort"] = $HTTP_POST_VARS["sort"] ;
	}
	
	$array = array() ;
	$limit = 0 ;
	
	if ( count ( $HTTP_POST_VARS ) > 0 )
	{		
		if ( !$HTTP_POST_VARS["papers"] )
		{
//				header("Location: edit_paper_bids.php?err=") ;
			if ( strpos ( $_SERVER["HTTP_REFERER"] , "?" ) === false )
			{
				$str = "Location:" . $_SERVER["HTTP_REFERER"] . "?err=" ;
			}
			else
			{
				$str = "Location:" . $_SERVER["HTTP_REFERER"] . "&err=" ;				
			}
//					echo $str ;
			header($str) ;
			exit ;
		}				
		
		if ( $HTTP_POST_VARS["Submit"] == "Submit" )
		{		
			$selection = array() ;
			$numpapers = count ( $HTTP_POST_VARS["papers"] ) ;
			//Loop the total numbers of papers and put them into array called selection	
			for( $i=0 ; $i < $numpapers ; $i++ )
			{	
				$papid = $HTTP_POST_VARS["papers"][$i] ;
				$selection[$papid] = $HTTP_POST_VARS["selection".$papid] ;
			}
					
			if ( $HTTP_POST_VARS["process"] == "update" )
			{
				//Call the function to insert the information to database
				if ( ( $result = update_selection($selection , &$err_message ) ) !== NULL )
				{	
					if ( $result )
					{
						do_html_header("Updating Paper Bids Successful" , &$err_message );
						echo $result;
						do_html_footer(&$err_message);
						exit ;						
					}
					else
					{
						do_html_header("Bids not updated" , &$err_message ) ;
						$err_message .= " No changes to bids detected. <br>\n" ;						
					}
				}
				else
				{
					do_html_header("Updating Paper Bids Failed" , &$err_message );
					$err_message .= " Could not execute \"update_selection\" in \"update_biddings.php\". <br>\n";
					$err_message . "<br><br> Try <a href='update_biddings.php'>again</a>?" ;
				}
			}
			else if ( $HTTP_POST_VARS["process"] == "insert" )
			{
				//Call the function to insert the information to database
				if ( $result = select_paper($selection , &$err_message ) )
				{	
					do_html_header("Process Paper Bids Successful" , &$err_message );	
					echo $result;
					do_html_footer(&$err_message);					
					exit ;
				}
				else
				{
					do_html_header("Process Paper Bids Failed" , &$err_message );
					$err_message .= "Could not execute \"select_paper\" in \"bid_all_papers.php\". <br>\n" ;
					$err_message .= "<br><br> Try <a href='bid_all_papers.php'>again</a>?" ;
				}			
			}
		}
		else
		{
			do_html_header("Update Paper Bids" , &$err_message );
		}
	}
	else 
	{
		if ( count ( $HTTP_POST_VARS ) == 0 )
		{	
		
		}
		do_html_header("Update Paper Bids" , &$err_message );
	}	
	
$array = redisplay( $HTTP_POST_VARS["papers"] , $HTTP_POST_VARS["process"] , $GLOBALS["DB_PREFIX"] , &$err_message ) ;
if ( !( $limit = count ( $array ) ) )
{
//	do_html_header("Update Paper Bids" , &$err_message );			
	$err_message .= " You have not selected any papers to update the bids. <br>\n" ;
	do_html_footer(&$err_message) ;		
	exit ;
}				
	
?>
<script language="JavaScript">
<!-- Hide script from older browsers

function papercheckbox( mylink , query )
{
	document.frmPaper.action = ( mylink + query ) ;
	document.frmPaper.submit();
}

// End hiding script from older browsers -->
</SCRIPT>

<form name="frmPaper" method="post" action="update_biddings.php">
  <table width="100%" border="0" cellspacing="2" cellpadding="1">
    <?php
	for ( $i = 0 ; $i < $limit ; $i++ )
	{
?>
    <tr> 
      <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td colspan="2"> <strong><?php echo "Paper " . $array[$i]["paperid"] . " : " . $array[$i]["papertitle"] ; ?></strong> 
            </td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2">Area: 
              <?php 
	echo $array[$i]["cat"] ;			
?>
            </td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2">Authors: 
              <?php 
	echo $array[$i]["author"] ;
?>
            </td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2"> 
              <?php 
	echo $array[$i]["bid"] ;   
?>
            </td>
            <input type="hidden" name="papers[]" value="<?php echo $array[$i]["paperid"] ; ?>">
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;<a href="<?php echo "javascript:papercheckbox( 'view_abstract.php' , '?paperid=" . $array[$i]["paperid"] . "&sort=" . $HTTP_GET_VARS["sort"] . "&showing=" . $HTTP_GET_VARS["showing"] . "' )" ; ?>" >View 
              Abstract</a> | <a href='view_file.php?fileid=<?php echo $array[$i]["fileid"] ; ?>' >View 
              Paper</a> </td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td><hr></td>
    </tr>
    <?php //Increment the $i
	}
	
//	echo $papers_str ;
	
	echo $storepapers_str ;
	
	if ( isset ( $HTTP_POST_VARS["referer"] ) && $HTTP_POST_VARS["referer"] != "update_biddings.php" )
	{
		$HTTP_POST_VARS["myreferer"] = $HTTP_POST_VARS["referer"] ;
	}
?>
    <tr> 
	  <input type="hidden" value="<?php echo $HTTP_GET_VARS["sort"] ; ?>" name="sort">  
	  <input type="hidden" value="<?php echo $HTTP_GET_VARS["showing"] ; ?>" name="showing">
	  <input type="hidden" value="<?php echo $HTTP_GET_VARS["process"] ; ?>" name="process">
	  <input type="hidden" value="update_biddings.php" name="referer">	  
	  <input type="hidden" value="<?php echo $HTTP_POST_VARS["myreferer"] ; ?>" name="myreferer">	  
      <td> <input type="submit" name="Submit" value="Submit"> <input name="Submit" type="submit" id="Submit" value="Cancel" onClick="<?php echo "javascript:papercheckbox( '" . $HTTP_POST_VARS["myreferer"] . "' , '' )" ; ?> "> 
      </td>
    </tr>
  </table>
</form>
<?php 

do_html_footer(&$err_message);

?>
