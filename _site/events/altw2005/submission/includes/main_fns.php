<?php 

/*
JEdit Editor Preferences
:tabSize=4:indentSize=4:noTabs=false:wrap=soft
:maxLineLen=120:folding=explicit:collapseFolds=1:
*/

global $php_root_path ;
require_once("$php_root_path/includes/db_connect.php");


///////// Debug utility function //////////
//{{{
// Dump array as text string
function dump_array($array, $expandall = FALSE)
{
  if(is_array($array))
  {
    $size = count($array);
    $string = "";
    if($size)
    {
      $count = 0;
      $string .= "{ ";
      // add each element's key and value to the string
      foreach($array as $var => $value)
      {
        if ( $expandall )
        {
            $string .= "$var = ".dump_array($value);
        }
        else
        {
            $string .= "$var = '$value'";
        }
        if($count++ < ($size-1))  
        {
          $string .= ", ";
        }
      }
      $string .= " }";
    }
    return $string;
  }
  else 
  {
    // if it is not an array, just return it
    return "'$array'";
  }
}
//}}}

// Filter array by intersecting keys of first array with values of second
function array_key_screen($array, $screen_array)
{
  $retArray = array();
  foreach ($array as $key => $value)
  {
    if (in_array($key, $screen_array))
    {
      $retArray[$key] = $value;
    }
  }
  return $retArray;
}


/////// Start Function Shared by all /////////////// 
//{{{
//Define the constant for the maximum number of papers that one page can display
define("MAX_PAPERS",5);

function get_Conference_Settings( $err_message="" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $settingSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Settings" ;
	$settingResult = $db -> Execute($settingSQL);
	
	if(!$settingResult)
	{
		$err_message .= "Could not retrieve the setting information by \"get_Conference_Settings\". - try again<br>\n" ;
		return NULL ;		
	}
	
	if ( $settingResult -> RecordCount() )
	{
		$settingArray = array();
        while ($settingPair = $settingResult -> FetchNextObj())
        {
            $settingArray = array($settingPair->Name => $settingPair->Value)
                            + $settingArray;
        }
        return (object)$settingArray;
	}
	else
	{
		return false ;
	}
}

function get_conference_info()
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	$conferenceSQL = "SELECT ConferenceID , ConferenceContact , ConferenceName , ConferenceCodeName , ";
    $conferenceSQL .= dbdf_out($db,"ConferenceStartDate").", ";
    $conferenceSQL .= dbdf_out($db,"ConferenceEndDate");
    $conferenceSQL .= ", ConferenceLocation , ConferenceHostName ,ConferenceContact, LogoFile, FileName, FileSize, FileType FROM " . $GLOBALS["DB_PREFIX"] . "Conference";
	$conferenceResult = $db -> Execute($conferenceSQL);	
	$conferenceInfo = $conferenceResult -> FetchNextObj();
	
	return $conferenceInfo;
}

function evaluate_pages_links($sort,$showing = 0,$num_rows,$max_num = 5){
	
	$quotient = ceil($num_rows / $max_num);
	$remainder = $num_rows % $max_num;
	
	$counter = 0;
	
	for($i = 1;$i <= $quotient;$i++){
		
		if($counter == $showing)
			$pagesLinks .= ' '.$i;
		else
			$pagesLinks .= ' <a href="'.$_SERVER['PHP_SELF'].'?sort='.$sort.'&showing='.$counter.'">'.$i.'</a>';	
			
		$counter += $max_num;
	}
	$pagesLinks .= '&nbsp;';
	return $pagesLinks;
}

function evaluate_records_range($showing,$num_rows,$max_num = 5){

		//Check to show from which record to which record
		if ($showing == 0){
			if($num_rows > $max_num)
				$from = "1 - ".$max_num;
			else
				$from = "1 - ".$num_rows;		
		}
		else{
				
			if(($num_rows - $showing) > $max_num)			
				$from = ($showing + 1). " - ".($showing + $max_num);
			else
				$from = ($showing + 1). " - ".$num_rows;			
		}
		
		return $from;

}

function evaluate_showing($showing){

	//Check the value of showing
	if(isset($showing))
		$showing = intval($showing);
	else
		$showing = 0;
	
	//Return the showing
	return $showing;

}

function evaluate_prev($sort , $showing , $num_rows = "" , $max_num = 5)
{
	global $php_root_path ;
	//Check whether need to show prev link
	if(($showing - $max_num) >= 0)				
		$prev = '<img src="' . $php_root_path . '/images/trileft.gif" width="5" height="10">&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?sort='.$sort.'&showing='.($showing - $max_num).'">Prev</a>';		
	else
		$prev = '<img src="' . $php_root_path . '/images/trileft.gif" width="5" height="10">&nbsp;Prev';

	return $prev;
}

function evaluate_next( $sort , $showing , $num_rows , $max_num = 5)
{
	global $php_root_path ;
	//Check whether need to show next link
	if(($num_rows - $showing) > $max_num)
		$next = '<a href="'.$_SERVER['PHP_SELF'].'?sort='.$sort.'&showing='.($showing + $max_num).'">Next</a>&nbsp;<img src="' . $php_root_path . '/images/tri.gif" width="5" height="10">';
	else
		$next = 'Next&nbsp;<img src="' . $php_root_path . '/images/tri.gif" width="5" height="10">';
	
	return $next;
}

function get_Num_Preferences($paperID){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	//Retrieve the information from Review Table
	//Only count positive preferences
	$preferenceSQL = "SELECT count(*) as NumPreferences FROM " . $GLOBALS["DB_PREFIX"] . "Selection";
	$preferenceSQL .= " WHERE PaperID =".$paperID." AND PreferenceID < 2";
	$preferenceResult = $db -> Execute($preferenceSQL);
	$preferenceInfo = $preferenceResult -> FetchNextObj();
		
	return $preferenceInfo -> NumPreferences;
}

function display ( $error_array )
{
	if ( is_array( $error_array ) )
	{		
		reset ( $error_array ) ;
		while ( list ( $key , $value ) = each ( $error_array ) )
		{
			if ( is_array( $error_array[$key] ) )
			{		
				while ( list ( $akey , $avalue ) = each ( $error_array[$key] ) )
				{
					echo $key . "(" . $akey . "): " . $avalue . "<br>\n" ;
				}
				reset ( $error_array[$key] ) ;
			}
			else
			{
				echo $key . ": " . $error_array[$key] . "<br>\n" ;
			}
		}
		reset ( $error_array ) ;
	}
	else
	{
		echo "<br>\n This is not an array. <br>\n" ;
	}	
}
//}}}
/////// End Function Shared by all ///////////////

/////////// Start Reviewer / Admin shared functions ////////////
//{{{
function check_privilege_type ( $id , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    //global $_SESSION ;
	$result = $db -> Execute("SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member WHERE MemberName = '" . addslashes ( $_SESSION["valid_user"] ) . "' AND PrivilegeTypeID=$id ");
	
	if(!$result)
	{
		$err_message .= " Cannot retrieve information from database in \"check_privilege_type\". <br>\n" ;
		return false ;
	}
	else 
	{
		return $result -> RecordCount() ;
	}
}

#Function that generate the characters
function generate_char($sType,$iLength)
{
    $str = "";
	$iLength = (int) $iLength;
	srand ((double) microtime() * 1000000);
	
	switch($sType){
		case "i":
			for($i=0;$i < $iLength;$i++){
			  //Generate the random number

  			  $rand_number = rand(1, 9);
			  $str .= $rand_number;		
			}//for
			break;
		case "s":
			for($i=0;$i < $iLength;$i++){
			  //Generate the random character
			 // $rand_char = chr(rand(0,25) + 65);
			 $rand_logic = rand(0,1) ;
			 
			 if ( $rand_logic )
			 {
			  	$rand_char = chr(rand(0,25) + 65);
			 }
			 else
			 {
			 	$rand_char = chr(rand(0,25) + 97);
			 }
			  $str .= $rand_char;
			}
			break;	
	}//switch
	
	return $str;
}

#Function that generate password
function generate_password(){
 	$new_password = generate_char("s",3).generate_char("i",3).generate_char("s",2).generate_char("i",1);
	return $new_password;
}

function check_review_exist($paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    //global $_SESSION;
	
	//SQL to retrieve the review of this paper
	$reviewSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Review";
	$reviewSQL .= " WHERE PaperID = '$paperID'";
	$reviewSQL .= " AND Membername = '".$_SESSION["valid_user"]."'";
	$reviewResult = $db -> Execute($reviewSQL);
	
	if ( !$reviewResult )
	{
		$err_message .= " Could not query the \"Review\" table in the database by \"check_review_exist\". <br>\n" ;
		return NULL ;
	}
	
	$reviewInfo = $reviewResult -> FetchNextObj();	
	
	if($reviewInfo -> Objectives != 0)
		return true;
	else
		return false;
}

	//  unformatted version
    function GetSelectedTrackText( &$paperID , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
		$sql = "SELECT TrackID FROM " . $GLOBALS["DB_PREFIX"] . "Paper " ;	
		$sql .= " WHERE PaperID = $paperID " ;
		$result = $db -> Execute($sql);
		$categorytable ;
		
		if(!$result)
		{
			$err_message .= " Could not get records from the PaperTable <br>\n ";	// Exception has occurred
			return false ;
		}
		else
		{						
			$categorytable = "" ;
			$rows = $result -> RecordCount() ;
			for ( $i = 0 ; $i < $rows ; )
			{
				for ( $j = 0 ; $j < 2 ; $i++ , $j++ )
				{
					if ( $records = $result -> FetchRow() )
					{
						$sql_cat = "SELECT TrackName FROM " . $GLOBALS["DB_PREFIX"] . "Track " ;	
						$sql_cat .= " WHERE TrackID = $records[0]" ;
						$catResult = $db -> Execute($sql_cat);
						
						if(!$catResult)
						{
							$err_message .= " Could not get records from the Track Table <br>\n ";	// Exception has occurred
							return $err_message ;
						}
						
						$catRecords = $catResult -> FetchRow() ;						
						$categorytable .= "$catRecords[0] " ;
					}
					else
					{
						$categorytable .= "&nbsp " ;
					}
				}
			}
			return $categorytable ;			
		}			
	}
	
function getSelectedCategoryCommaSeparated($paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT CategoryID FROM " . $GLOBALS["DB_PREFIX"] . "PaperCategory " ;	
	$sql .= " WHERE PaperID = ".$paperID ;
	$result = $db -> Execute($sql);
		
	if(!$result)
	{
		$err_message .= " Could not get records from the PaperCategory Table in \"getSelectedCategoryCommaSeparated\". <br>\n ";	// Exception has occurred
		return false ; 
	}
						
	$rows = $result -> RecordCount() ;
	for ( $i = 0 ; $i < $rows ; $i++)
	{
		$records = $result -> FetchNextObj();
						
		$sql_cat = "SELECT CategoryName FROM " . $GLOBALS["DB_PREFIX"] . "Category " ;	
		$sql_cat .= " WHERE CategoryID = ".$records -> CategoryID ;
		$catResult = $db -> Execute($sql_cat);
						
		if(!$catResult)
		{
			$err_message .= " Could not get records from the PaperCategory Table in \"getSelectedCategoryCommaSeparated\". <br>\n ";	// Exception has occurred
			return false ; 
		}
		
		$catRecords = $catResult -> FetchNextObj() ;	
		
		//Display the author name
		if($i == ($rows -1))
				$categoryStr .= $catRecords -> CategoryName;
		else
				$categoryStr .= $catRecords -> CategoryName.", ";
											

	}//For
			
	return $categoryStr;				
}

function get_comment($paperID , $reviewername , $err_message = "" )
{	
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$commentSQL = "SELECT Comments From " . $GLOBALS["DB_PREFIX"] . "Review ";
	$commentSQL .= " WHERE PaperID=$paperID";
	$commentSQL .= " AND MemberName='$reviewername' ";
//	echo $commentSQL ;
	$commentResult = $db -> Execute($commentSQL);
	
	if(!$commentResult)
	{
		$err_message .= " Could not query \"Review\" table of database in \"get_comment\". <br>\n" ;
		return false ;
	}
		
	$commentInfo = $commentResult -> FetchNextObj();
	return $commentInfo -> Comments;
}

function get_paper_info($paperID , $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	//Retrieve the paper information
	$paperSql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper P , " . $GLOBALS["DB_PREFIX"] . "PaperStatus PS";
	$paperSql .= " WHERE P.PaperStatusID = PS.PaperStatusID ";
	$paperSql .= " AND PaperID=".$paperID;
	$paperResult = $db -> Execute($paperSql);
	
	if(!$paperResult)
	{
		$err_message .= " Cannot retrieve information from database in \"get_paper_info\". <br>\n" ;
		return false ;
	}
	else 
	{
		$paperInfo = $paperResult -> FetchNextObj();		
		return $paperInfo;
	}
}

// Output same as "getAuthorsCommaSeparated"
function retrieve_authors($paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	//Retrieve the authors info of the paper
	$GetAuthorsSql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Written" ;
	$GetAuthorsSql .= " WHERE PaperID=" .$paperID;				
				
	//Execute the retrieve of written table
	$authors_result = $db -> Execute($GetAuthorsSql);
	
	if ( !$authors_result )
	{
		$err_message .= " Cannot retrieve information from database in \"retrieve_authors\". <br>\n" ;		
		return false ;	
	}
	
	$author_numrows = $authors_result -> RecordCount();
	$authorInfo = $authors_result -> FetchNextObj() ;
	$authorSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Author WHERE AuthorID = ".$authorInfo -> AuthorID;
	$author = $db -> Execute($authorSQL);
	$authorInfo = @$author -> FetchNextObj(); // suppress errors if no authors specified
	$authorCount = 1 ;
	$first = stripslashes ( $authorInfo -> FirstName ) ;
	$middle = stripslashes ( $authorInfo -> MiddleName ) ;
	$last = stripslashes ( $authorInfo -> LastName ) ;
	$authorIDList = formatAuthor ( $first , $middle , $last , $authorCount) ;
	
	while ( $authorInfo = $authors_result -> FetchNextObj() )
	{
		$authorSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Author WHERE AuthorID = ".$authorInfo -> AuthorID;
		$author = $db -> Execute($authorSQL);
		$authorInfo = $author -> FetchNextObj();
		$authorCount++ ;
		if ($authorCount == $author_numrows)
		{
			$first = stripslashes ( $authorInfo -> FirstName ) ;
			$middle = stripslashes ( $authorInfo -> MiddleName ) ;
			$last = stripslashes ( $authorInfo -> LastName ) ;
			$name = formatAuthor ( $first , $middle , $last , $authorCount) ;
			if ( $author_numrows == 2 )	// Check for only two authors
			{
				$authorIDList .= " and " . $name;
			}
			else
			{
			$authorIDList .= ", and " . $name;
			}
		}
		else
		{
			$first = stripslashes ( $authorInfo -> FirstName ) ;
			$middle = stripslashes ( $authorInfo -> MiddleName ) ;
			$last = stripslashes ( $authorInfo -> LastName ) ;
			$name = formatAuthor ( $first , $middle , $last , $authorCount) ;
			$authorIDList .= ", " . $name;
		}
		
	}		
	
	return $authorIDList ;
}

function get_ranking($value){

	//Use the switch case statement to return the rank
	switch($value){
		case 1:
			return "Strongly disagree";
			break;
		case 2:
			return "Weakly disagree";
			break;
		case 3:
			return "Undecided";
			break;
		case 4:
			return "Weakly agree";
			break;
		case 5;
			return "Strongly agree";
			break;
	
	}
}
//}}}
///////// End Reviewer / Admin shared functions ///////////////////

///////// Start User / Admin shared functions ////////////////////

///////// End User / Admin shared functions /////////////////////

//{{{ Registration Functions
function register($username, $details, $dbprefix, $err_message = "", $privilegeid = 1, $password = "", $trackID = 0 , $db = NULL)
// register new person with db
// return true or error message
{
    //Establish connection with database
    if (!$db)	$db = adodb_connect( &$err_message );
	
	global $privilege_root_path ;
	global $error_array ;	  
	
	// check if username is unique 
  	$result = $db -> Execute("SELECT * FROM " . $dbprefix . "Member WHERE MemberName=".db_quote($db,$username)); 
  
  	if (!$result)
	{
    	$err_message .= "Could not execute query Member. <br>\n";
		return false ;
	}
  
  //If the record is found, the user name has taken
  	if ($result -> RecordCount() > 0) 
  	{
		$error_array["username"][0] = "This user name was already taken. <br>\n" ;
     	$err_message .= "That username \"$username\" was already taken - go back and choose another one. <br>\n";
		return false ;
	}

    // Grab the fields in the Registration table, and then intersect them with
    // the list of form fields that map to values
    // Note: This is obviously going to be slower than simply hard-coding, but
    // it's also safer. Whether it's better is another question entirely.
    $result = $db -> Execute("SHOW COLUMNS FROM ".$dbprefix."Registration");
    $fields = $result -> FetchRow();
    $fieldlist = array();
    while ($fields)
    {
      $fieldlist[] = $fields[0];
      $fields = $result -> FetchRow();
    }
    $fieldlist = array_key_screen(get_user_details_field_map(),$fieldlist);
    // Got a safe list, so now use it to filter the $details
    $details = array_key_screen($details, $fieldlist);
    // Now produce a list mapping field names to values
    // Note: At worst this should only produce null values if the form fields
    // and map don't match. All fields in $fieldlist are in Registration
    $fieldVals = array();
    foreach ($fieldlist as $key => $value)
    {
        $fieldVals[$key] = $details[$value];
    }
    // Make values "nice"
    foreach ($fieldVals as $key => $value)
    {
        $fieldVals[$key] = addslashes($value);
    }
    // Produce the strings for field names & values
    $strFieldNameList = implode(",",array_keys($fieldVals));
    $strFieldValueList = "'".implode("','",$fieldVals)."'";
    
    // This wasn't in details, so make it properly formatted
    $username = addslashes($username);
    
    // For readability (and code legacy) make a few assignments for $details
    // that will always exist.
	$firstname  = $fieldVals['FirstName'];
  	$middlename = $fieldVals['MiddleName'];
  	$lastname   = $fieldVals['LastName'];
  	$email      = $fieldVals['Email'];
    
    // Insert into Registration table first
  	$result = $db -> Execute("INSERT INTO " . $dbprefix . "Registration(".$strFieldNameList.") VALUES 
                         (".$strFieldValueList.")");
    if (!$result)
	{
    	$err_message .= "Could not register you in database - please try again later. <br>\n";
		return false ;
	}	
  	else
  	{
		//Get the RegisterID from Registration table
		$registerID = $db -> Insert_ID();
		
		if ( $privilegeid === 1 )
		{
			$password = generate_password();
			$confer = get_conference_info() ;
			
/* 			// Replaced by Conference Settings's HomePage
			$uri = $_SERVER["SCRIPT_URI"] ;
			$replacefrom = ( strpos( $uri , $privilege_root_path ) + strlen ( "/" ) ) ;
			$url = substr_replace ( $uri , "index.php" , $replacefrom ) ;			
*/
			
			global $php_root_path ;
			include_once("$php_root_path/admin/includes/main_fns.php");

			//Retrieve the setting information and conference info
			$settingInfo = get_Conference_Settings();
			$conferenceInfo = get_conference_info();			
			
			//Fetch letter information
			$letterInfo = get_Letter_Info( 2 );
			
			//Format the subject of the letter
			$arrConstants = array("\$confname","\$confcode");	
			$arrReplaceInfo = array("confname" => $conferenceInfo -> ConferenceName,"confcode" => $conferenceInfo -> ConferenceCodeName);
			$strSubject = replace_Dynamic_Values( $arrConstants , $arrReplaceInfo , $letterInfo -> Subject );
			
			//Get the constant of the letter
			$arrConstants = evaluate_Letter_Constants("useraccount");
			
			//Format the subject of the letter
			$arrReplaceInfo = array( 
									"confname" => $conferenceInfo -> ConferenceName, 
									"confcode" => $conferenceInfo -> ConferenceCodeName , 
									"fullname" => "$firstname $middlename $lastname" , 
									"username" => stripslashes ( stripslashes ( $username ) ) , 
									"password" => $password , 
									"url" => $settingInfo -> HomePage , 
									"contact" => $conferenceInfo -> ConferenceContact
									) ;
			$strContent = replace_Dynamic_Values($arrConstants,$arrReplaceInfo, $letterInfo -> BodyContent );

			//Update the mail log	
			$result = updateMailLog( stripslashes ( stripslashes ( $username ) ) , 2 );	

/*			// Debug use only
			echo "\$strSubject=$strSubject<br><br>\n\n" ;
			echo "\$strContent=$strContent<br><br>\n\n" ;
			exit ;
*/		
			
			//If can log the email
			if($result === true){		
				//Send Email to user
				$mail = new Mail();
					
				$mail -> Organization($conferenceInfo -> ConferenceCodeName);
				$mail -> ReplyTo($conferenceInfo -> ConferenceContact);
					
				$mail -> From($conferenceInfo -> ConferenceContact);
				$mail -> To( stripslashes( $email ) );	
				$mail -> Subject(stripslashes($strSubject));
				$mail -> Body( $strContent );
					
				$mail -> Cc($conferenceInfo -> ConferenceContact);	
					
				$mail -> Priority(1);		
				$mail -> Send();
			}	
			else
			{			
				deleteMailLog( stripslashes ( stripslashes ( $username ) ) , 2 ) ;
			}		
		}
		
		//Insert into Member Database
		$result = $db -> Execute("INSERT INTO " . $dbprefix . "Member VALUES(".db_quote($db,$username).", ".db_quote($db, sha1($password)).", $privilegeid , '$registerID','$trackID')");
		
		if (!$result)
		{
			delete_registration ( $registerID , &$err_message ) ;
    		$err_message .= "Could not register you in database - please try again later. <br>\n";	
			return false ;
		}
					
	  	return true ;		
	}
}

function delete_registration ( &$regID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member" ;
	$sql .= " WHERE RegisterID = $regID " ;
	$selectResult = $db -> Execute($sql) ;
	
	if( !$selectResult )
	{		
		$err_message .= " Could not access Records from Member Table <br>\n ";	// Exception has occurred			
		return false ;
	}	
	$memberInfo = $selectResult -> FetchNextObj();
	$memberName = $memberInfo -> MemberName;
	//echo "$memberName";
	
	// Delete any reviews
	$del = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Review " ;
	$del .= " WHERE MemberName = '$memberName' " ;
	
	//echo "$del";
	$result = $db -> Execute($del) ;
	if( !$result )
	{		
		$err_message .= " Could not Delete Reviews from Review Table <br>\n ";	// Exception has occurred			
		return false ;
	}	
	
	// Delete a registration from the registration table
	$del = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Registration " ;
	$del .= " WHERE RegisterID = $regID " ;
	$result = $db -> Execute($del) ;
	if( !$result )
	{		
		$err_message .= " Could not Delete Records from Registration Table <br>\n ";	// Exception has occurred			
		return false ;
	}	
	
	// BL Also need to delete from Member Table and Review Table for consistency
	$del = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Member" ;
	$del .= " WHERE RegisterID = $regID " ;
	$result = $db -> Execute($del) ;
	
	if( !$result )
	{		
		$err_message .= " Could not Delete Records from Member Table <br>\n ";	// Exception has occurred			
		return false ;
	}	

	return true ;
}
//}}}

//{{{
function get_referenced_authors ( &$firstname , &$middlename , &$lastname , &$email , &$authorIDList , $err_message = "" , $errors = false )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT AuthorID FROM " . $GLOBALS["DB_PREFIX"] . "Author " ;
	$sql .= " WHERE FirstName = '" . addslashes ( $firstname ) . "' AND MiddleName = '" . addslashes ( $middlename ) . "' AND LastName = '" . addslashes ( $lastname ) . "' AND Email = '" . addslashes ( $email ) . "' " ;
	$result = $db -> Execute($sql);
		
	if(!$result)	// Roll back the changes
	{
		$err_message .= " Could not get the referenced Author ID from the Author Table <br>\n ";	// Exception has occurred					
		$errors = true ;
		return false ;
	}
	else
	{	
		if ( $result -> RecordCount() > 0 )
		{
			while ( $row = $result -> RecordCount() )
			{			
				$authorIDList[] = $row[0] ;
			}		

			return true ;		
		}
		else
		{
			return false ;
		}
	}		
}

function add_authors ( &$firstname , &$middlename , &$lastname , &$email , $referencedIDList = array() , $err_message = "" )
{	
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $authorIDList = array() ;
	$numauthors = count ( $lastname ) ; 
	$errors = false ;	
    
	for ( $i = 0 ; $i < $numauthors ; $i++ )
	{
// No need to check for already referenced authors!
/*BL		if ( !( get_referenced_authors ( $firstname[$i] , $middlename[$i] , $lastname[$i] , $email[$i] , $referencedIDList , &$err_message , &$errors ) ) )
		{
	
			if ( $errors )			
				break ;	
*/		
			//Insert the author info to Author table
			$sql = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Author ";
			$sql .= " (FirstName,MiddleName,LastName,Email) ";
			$sql .= " VALUES('" . addslashes ( $firstname[$i] ) . "' , '" . addslashes ( $middlename[$i] ) . "' , '" . addslashes ( $lastname[$i] ) . "' , '" . addslashes ( $email[$i] ) . "' ) " ;
			
			$result = $db -> Execute($sql);
			
			if(!$result)	// Roll back the changes
			{
				$err_message .= " Could not Insert the Author information into Author Table <br>\n ";	// Exception has occurred						
				$errors = true ;
				break ;				
			}

			$authorIDList[] = $db -> Insert_ID();			
//BL		}
	}
	
	if ( $errors )	// Roll back the changes
	{
		delete_authors ( $authorIDList , &$err_message ) ;
		return false ;
	}
	
	return $authorIDList ;
}

function delete_authors ( &$authorIDList , $err_message = "" )  // Caution it may delete referenced authors from other papers.
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $numauthors = count ( $authorIDList ) ;
	
	for ( $i = 0 ; $i < $numauthors ; $i++ )
	{
		// Delete a file from File table
		$delautid = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Author" ;
		$delautid .= " WHERE AuthorID = $authorIDList[$i] " ;
		$result = $db -> Execute($delautid) ;
		
		if( !$result )
		{		
			$err_message .= " Could not Delete Authors from failed Author Deletion <br>\n ";	// Exception has occurred			
			return false ;
		}
	}		

	return true ;
}

function add_written ( &$paperID , &$authorIDList , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $numauthors = count ( $authorIDList ) ;
	
	for ( $i = 0 ; $i < $numauthors ; $i++ )
	{
		//Insert the info into Written table
		$sql = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Written ";
//		$sql = "INSERT INTO Written ";	// Test rollback
		$sql .= " VALUES('$paperID','$authorIDList[$i]') ";
		
		$result = $db -> Execute($sql);
		
		if(!$result)
		{
			$err_message .= "Could not Add Records into the Written Table <br>\n" ;
			delete_written( $paperID , &$err_message ) ;
			return false ;
	//		return "Could not update the written information";				
		}

	}
	
	return true ;
}

function delete_written( &$paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Delete a paper from Paper table
	$delpaper = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Written " ;
	$delpaper .= " WHERE PaperID = $paperID " ;
	$result = $db -> Execute($delpaper) ;
	
	if( !$result )
	{		
		$err_message .= " Could not Delete Records from failed Written Deletion <br>\n ";	// Exception has occurred			
		return false ;
	}	

	return true ;
}

function add_paper( $insertArray , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $values = implode(", ", array_map("db_quote", array_fill(0,count($insertArray),&$db), $insertArray));
	
	//Insert the paper info to paper table
	$sql = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Paper " ;
	$sql .= "( ".implode(", ", array_keys($insertArray)).", MemberName ) " ;
	$sql .= " VALUES ( " . $values . ", " . db_quote( $db, $_SESSION["valid_user"] ) . " ) " ;
    $result = $db -> Execute($sql);
	
	if( !$result )
	{		
		$err_message .= " Could not Insert the Paper into the Paper Table <br>\n ";	// Exception has occurred			
		return false ;		
	}
	else
	{
		//Get the paperID first
		$paperID = $db -> Insert_ID();		
		return $paperID ;
	}
}

function update_paper( $insertArray , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Remove SessionID as this is a new entry
	$fields = array_diff_assoc($insertArray, array("PaperID" => $insertArray['PaperID']));
	
	$assignments = array();
	foreach ($fields as $key => $value)
	{
		$assignments[] = $key."=".db_quote($db, $value);
	}
	
	$sql =  "UPDATE " . $GLOBALS["DB_PREFIX"] . "Paper ";
	$sql .= "SET ".implode(", ", $assignments)." ";
	$sql .= "WHERE PaperID = ".$insertArray['PaperID'];
	$result = $db -> Execute($sql);
    
	if(!$result)
	{
		$err_message .= " Failed to Update Paper information in Paper Table <br>\n ";
		return false ;
	}
	else
	{
		return true ;
	}
}

function delete_paper ( &$paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Delete a paper from Paper table
	$delpaper = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Paper " ;
	$delpaper .= " WHERE PaperID = $paperID " ;
	$result = $db -> Execute($delpaper) ;
	
	if( !$result )
	{		
		$err_message .= " Could not Delete Paper from failed Paper Deletion <br>\n ";	// Exception has occurred			
		return false ;
	}
	
	return true ;
}	

function add_file ( &$userfile , &$filename , &$filesize , &$filetype , &$paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    // Use @ to suppress errors in the case of empty file (abstract upload only)
	$file = @addslashes(fread(fopen($userfile,"r"),filesize($userfile))) ;
	
	// Insert a new file into File table
	$upfile = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "File " ;
	$upfile .= " ( File , FileName , FileSize , FileType , DateTime , PaperID ) " ;
	$upfile .= " VALUES ( '$file' , '$filename' , '$filesize' , '$filetype' , Now() , $paperID ) " ;

	$result = $db -> Execute($upfile);		
	
	$fileID = $db -> Insert_ID();

	if ( !$result )	// Roll back the changes
	{
		$err_message .= " Could not Insert the File into the File Table <br>\n " ;
		return false ;	
	}
	
	return $fileID ;
}

function update_file ( &$fileID , &$userfile , &$filename , &$filesize , &$filetype , &$paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    // Use @ to suppress errors if empty file (abstract submission only)
	@$file = addslashes(fread(fopen($userfile,"r"),filesize($userfile))) ;
	
	// Insert a new file into File table
	$upfile = "UPDATE " . $GLOBALS["DB_PREFIX"] . "File SET" ;
	$upfile .= " File = '$file', FileName = '$filename', FileSize = '$filesize', FileType = '$filetype', DateTime = Now(), PaperID = $paperID " ;
	$upfile .= " WHERE FileID = $fileID " ;
	$result = $db -> Execute($upfile) ;

	if ( !$result )	// Roll back the changes
	{
		$err_message .= " Could not Update the File into the File Table<br>\n " ;
		return false ;	
	}
	else
	{	
		return true ;
	}
}

function delete_file ( &$fileID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Delete a file from File table
	$delfile = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "File " ;
	$delfile .= " WHERE FileID = $fileID " ;
	$result = $db -> Execute($delfile) ;
	
	if( !$result )
	{		
		$err_message .= " Could not Delete File from failed File Deletion <br>\n ";	// Exception has occurred			
		return false ;
	}

	return true ;
}

function add_paperLevel ( &$paperID , &$levelIDList , $err_message = "" )
{
	// Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	// This command can only ever apply to SESUG, so fake success if not SESUG 
	if (! $settingInfo -> SESUG ) return true;
	
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $numlevel = count ( $levelIDList ) ;
	
	for ( $i = 0 ; $i < $numlevel ; $i++ )
	{
		//Insert the info into Written table
		$sql = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "PaperLevel ";  // correct statement
		$sql .= " VALUES( $paperID , $levelIDList[$i] ) ";
		
		$result = $db -> Execute($sql);
		
		if(!$result)
		{
			$err_message .= " Could not add records into the paper level table <br>\n" ;
			return false ;				
		}
	}
	
	return true ;
}

function add_paperCategory ( &$paperID , &$categoryIDList , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $numcategory = count ( $categoryIDList ) ;
	
	for ( $i = 0 ; $i < $numcategory ; $i++ )
	{
		//Insert the info into Written table
		$sql = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "PaperCategory ";  // correct statement
//		$sql = "INSERT INTO PaperCategory ";  //DEBUG for intentional error
		$sql .= " VALUES( $paperID , $categoryIDList[$i] ) ";
		
		$result = $db -> Execute($sql);
		
		if(!$result)
		{
			$err_message .= " Could not Add Records into the PaperCategory Table <br>\n" ;
//			delete_paperCategory ( $paperID , &$err_message ) ; // Debug
			return false ;				
		}
	}
	
	return true ;
}

function delete_paperCategory ( $paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Delete a paper from Paper table
	$delpaper = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "PaperCategory " ;
	$delpaper .= " WHERE PaperID = $paperID " ;
	$result = $db -> Execute($delpaper) ;
	
	if ( !$result )
	{		
		$err_message .= " Could not delete records from paper category table<br/>\n ";	// Exception has occurred
		return false ;
	}	

	return true ;
}

function delete_paperLevel ( &$paperID , $err_message = "" )
{
	// Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	// This command can only ever apply to SESUG, so fake success if not SESUG 
	if (! $settingInfo -> SESUG ) return true;
	
    //Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Delete a paper from Paper table
	$delpaper = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "PaperLevel " ;
	$delpaper .= " WHERE PaperID = $paperID " ;
	$result = $db -> Execute($delpaper) ;
	
	if( !$result )
	{		
		$err_message .= " Could not delete records from paper level table<br/>\n ";	// Exception has occurred			
		return false ;
	}	

	return true ;
}

function upload_file( &$title , &$paperabstract , &$presenterbio , &$numpages , &$userfile , &$filename , &$filesize , 				&$filetype ,  &$firstname , &$middlename , &$lastname , &$email , &$attended , &$presented , &$keyword1 , 			&$keyword2 , &$keyword3 , &$level , &$trackList , &$category , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db)
	{
		$err_message .= " Could not connect to database server - please try later. <br>\n";
		return false ;
	}

	$paperID ;
		
	// Convert array to scalar
	$track = $trackList[0];
	//Insert the paper info to paper table
	
    $insertFields = 
        array ( "Title" => $title , 
                "PaperAbstract" => $paperabstract , 
                "PresenterBio" => $presenterbio , 
                "NumberofPages" => $numpages , 
                "TrackID" => $track , 
                "SESUG_Attended" => $attended , 
                "SESUG_Presented" => $presented , 
                "Keyword1" => $keyword1 , 
                "Keyword2" => $keyword2 , 
                "Keyword3" => $keyword3 );
    foreach ($insertFields as $key => $value)
    {
        if ($value == NULL)
        {
            $insertFields = array_diff($insertFields,array($key => $value));
        }
    }
    
	if ( $paperID = add_paper( $insertFields , &$err_message ) )  // Note it is = ( equality )
	{
		
		if ( $fileID = add_file( $userfile , $filename , $filesize , $filetype , $paperID , &$err_message ) )
		{
			$referencedIDList = array() ; // This variable is no longer used!
			$authorIDList = add_authors( $firstname , $middlename , $lastname , $email , &$referencedIDList , &$err_message ) ;
			
			if ( $allID = array_merge( $authorIDList , $referencedIDList ) )
			{				
				if ( add_written( $paperID , $allID , &$err_message ) )
				{
					if ( add_paperLevel( $paperID , $level , &$err_message ) )
					{
						if ( add_paperCategory( $paperID , $category , &$err_message ) )
						{
                            return $fileID ;
						}
						else
						{
							delete_paperLevel( $paperID , &$err_message ) ;
							delete_written( $paperID , &$err_message ) ;
							delete_authors( $authorIDList , &$err_message ) ;
							delete_file( $fileID , &$err_message ) ;
							delete_paper( $paperID , &$err_message ) ;																						
							$err_message .= " Could not execute add_paperCategory. <br>\n" ;						
							return false ;					
						}	// add_paperCategory
					}
				    else
					{
						delete_written( $paperID , &$err_message ) ;
						delete_authors( $authorIDList , &$err_message ) ;
						delete_file( $fileID , &$err_message ) ;
						delete_paper( $paperID , &$err_message ) ;																						
						$err_message .= " Could not execute add_paperLevel. <br>\n" ;						
						return false ;			
					}
				}
				else
				{
					delete_authors( $authorIDList , &$err_message ) ;
					delete_file( $fileID , &$err_message ) ;
					delete_paper( $paperID , &$err_message ) ;
					$err_message .= " Could not execute add_written. <br>\n" ;																											
					return false ;
				}	// add_written
			}
			else
			{			
				delete_file( $fileID , &$err_message ) ;
				delete_paper( $paperID , &$err_message ) ;
				$err_message .= " Could not execute allID. <br>\n" ;				
				return false ;
			}		
		}
		else
		{
			delete_paper( $paperID , &$err_message ) ;
			$err_message .= " Could not execute add_file. <br>\n" ;			
			return false ;
		}
	}
	else
	{
		$err_message .= " Could not execute add_paper. <br>\n" ;	
		return false ;
	}			
}

function get_latestFile( $paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql_latestFile = "SELECT MAX( DateTime ) AS DateTime FROM " . $GLOBALS["DB_PREFIX"] . "File ";
	$sql_latestFile .= " WHERE PaperID = " . $paperID ;
	$latestFileResult = $db -> Execute( $sql_latestFile ) ;
	
	if ( !$latestFileResult )
	{
		$err_message .= " Could not retrieve File Max Upload Time from the File Table <br>\n " ;
		return false ;
	}
	
	$latestFileData = $latestFileResult -> FetchNextObj() ;		
	$sql_file = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "File ";
	$sql_file .= " WHERE PaperID = $paperID AND DateTime='$latestFileData->DateTime' " ;
	$FileIDResult = $db -> Execute( $sql_file ) ;
		
	if ( !$FileIDResult )
	{
		$err_message .= " Could not retrieve File information from the File Table <br>\n " ;
		return false ;
	}
	else
	{	
		return $FileIDResult -> FetchNextObj();
	}
}
function get_latestFileID( $paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql_latestFile = "SELECT MAX( DateTime ) AS DateTime FROM " . $GLOBALS["DB_PREFIX"] . "File ";
	$sql_latestFile .= " WHERE PaperID = " . $paperID ;
	$latestFileResult = $db -> Execute( $sql_latestFile ) ;
	
	if ( !$latestFileResult )
	{
		$err_message .= " Could not retrieve File Max Upload Time from the File Table <br>\n " ;
		return false ;
	}
	
	$latestFileData = $latestFileResult -> FetchNextObj();		
	$sql_file = "SELECT FileID FROM " . $GLOBALS["DB_PREFIX"] . "File ";
	$sql_file .= " WHERE PaperID = $paperID AND DateTime='$latestFileData->DateTime' " ;
	$FileIDResult = $db -> Execute( $sql_file ) ;
		
	if ( !$FileIDResult )
	{
		$err_message .= " Could not retrieve File information from the File Table <br>\n " ;
		return false ;
	}
	else
	{	
		return $FileIDResult -> FetchNextObj();
	}
}

//Function that updates paper details
function update_paper_details( &$paperID , &$title , &$paperabstract , &$presenterbio , &$numpages , &$userfile , &$filename , 
								&$filesize , &$filetype , &$firstname , &$middlename , &$lastname , &$email , &$level , &$trackArray, &$category  , &$attended , &$presented , &$keyword1 , &$keyword2 , &$keyword3 ,$err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db)
	{
    	$err_message .= " Could not connect to database server - please try later. <br>\n ";
		return false ;
	}	
	
	// Convert from array to scalar
	$track = $trackArray[0];
	$execute = false ;
	$FileData = get_latestFile( $paperID , &$err_message ) ;
	$fileID = $FileData->FileID ;
	
	if ( isset($userfile) && $userfile == "" )	// No new file upload
	{
		$execute = true ;
	}
	else if ( isset($userfile) && $userfile != "" )	// New file upload
	{
		if ($filesize == 0)
		{
			$err_message .= " Problem: uploaded file is zero length <br>\n ";
			return false ;
		}	
	
		//Then compare with current file size
		if ( ( $FileData -> FileSize == $filesize ) && ( $FileData -> FileType == $filetype ) )
		{			
			if ( update_file( $FileData->FileID , $userfile , $filename , $filesize , $filetype , $paperID , &$err_message ) )
			{
				$execute = true ;	
			}
			else
			{
				$err_message .= " Could not execute update_file. <br>\n" ;			
				return false ;					
			}
		}
		else	// Different file so add new
		{
			if ( $fileID = add_file( $userfile , $filename , $filesize , $filetype , $paperID , &$err_message ) )
			{
				$execute = true ;				
			}
			else
			{
				$err_message .= " Could not execute add_file. <br>\n" ;
				return false ;					
			}
		}		
	}
	else  // Catch unhandled exception
	{
		$err_message .= " Unhandled Exception has occurred. <br>\n " ;
		return false ;
	}
	
	if ( $execute )
	{
		$insertFields = 
        array ( "PaperID" => $paperID,
				"Title" => $title , 
                "PaperAbstract" => $paperabstract , 
                "PresenterBio" => $presenterbio , 
                "NumberofPages" => $numpages , 
                "TrackID" => $track , 
                "SESUG_Attended" => $attended , 
                "SESUG_Presented" => $presented , 
                "Keyword1" => $keyword1 , 
                "Keyword2" => $keyword2 , 
                "Keyword3" => $keyword3 );
		foreach ($insertFields as $key => $value)
		{
			if ($value == NULL)
			{
				$insertFields = array_diff($insertFields,array($key => $value));
			}
		}
		
		if ( update_paper( $insertFields ,&$err_message ) )
		{
			if ( delete_written( $paperID , &$err_message ) )
			{		
				$referencedIDList = array() ;	// An empty array is considered as false
				$newAuthorIDList = add_authors( $firstname , $middlename , $lastname , $email , &$referencedIDList , &$err_message ) ;
				if ( $allID = array_merge( $newAuthorIDList , $referencedIDList ) )
				{					
					if ( add_written( $paperID , $allID , &$err_message ) )
					{
						if ( delete_paperCategory( $paperID , &$err_message ) )
						{
							if ( delete_paperLevel( $paperID , &$err_message ) )
							{
								if (add_paperLevel($paperID , $level , &$err_message ) )
								{
									if ( add_paperCategory( $paperID , $category , &$err_message ) )
									{
										if ( delete_unreferenced_authors( &$err_message ) )
										{
											return $fileID ;
										}
										else // delete_unreferencedAuthors
										{
											$err_message .= " Could not execute delete_unreferencedAuthors. <br>\n" ;
											return false ;
										}	
									}
									else // add_paperCategory
									{
										$err_message .= " Could not execute add_paperCategory. <br>\n" ;							
										return false ;
									}
								}
							}
							else	// delete_paperCateogry
							{
								$err_message .= " Could not execute delete_paperCateogry. <br>\n" ;						
								return false ;
							}
						}
					}
					else // add_written
					{
						$err_message .= " Could not execute add_written. <br>\n" ;
						return false ;
					}		
				}
				else // add_authors
				{
					$err_message .= " Could not execute add_authors. <br>\n" ;
					return false ;
				}		
			}
			else // delete_written
			{
				$err_message .= " Could not execute delete_written. <br>\n" ;			
				return false ;
			}
		}
		else // update_paper
		{			
			$err_message .= " Could not execute update_paper. <br>\n" ;		
			return false ;
		}				
	}
}

function delete_unreferenced_authors ( $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql_written = "SELECT AuthorID FROM " . $GLOBALS["DB_PREFIX"] . "Written " ;
    
	$result = $db -> Execute( $sql_written );
    
	if(!$result)
	{
		$err_message .= " Could not Query AuthorID from Written table in Deletion of unreferenced Authors function <br>\n " ;
		return false ;
	}
	else
	{		
		$authorIDList = array();
		$sql_authorIDList = "" ;	
					
		// mysql_result($result,0);  // Optimize to this later
		while ( $row = $result -> FetchNextObj() )
		{
			$authorIDList[] = $row -> AuthorID ;
		}		
		
		$element = each( $authorIDList ) ;
		$sql_authorIDList .= $element["value"] ;
		
		foreach ( $authorIDList as $key => $value )
		{
			$sql_authorIDList .= " , " . $value ;	
		}
	
		$sql_written = "SELECT AuthorID FROM " . $GLOBALS["DB_PREFIX"] . "Author " ;
		$sql_written .= " WHERE AuthorID NOT IN ( $sql_authorIDList ) " ;
	
		$result = $db -> Execute( $sql_written ) ;
	
		if(!$result)
		{
			$err_message .= " Could not Query AuthorID from Author table in Deletion of unreferenced Authors function <br>\n " ;
			return false ;
		}
		else
		{
			$deleteAuthorIDList = array() ;
				
			while ( $row = $result-> FetchNextObj() )
			{
				$deleteAuthorIDList[] = $row -> AuthorID ;
			}
			
			if ( delete_authors( $deleteAuthorIDList , &$err_message ) )
			{
				return true ;
			}
			else
			{
				$err_message .= " Could not Delete Unreferenced Authors in Deletion of unreferenced Authors function <br>\n "  ;
				return false ;
			}
		}
	}
}

// Format name strings into a single string.
// Parameters $first, $middle, $last, $order are the name strings and the author order on
// the paper. Order may be used at a later date to provide various author formats depending
// on author position etc.

function formatAuthor( $first , $middle , $last , $order = 1 )
{
	$first = trim ( $first ) ;		// Check for additional whitespace
	$middle = trim ( $middle ) ;
	$last = trim ( $last ) ;

	if ( strlen ( $first ) == 1 )		// Check for initial only
	{
		$first .= "." ;			// Add punctuation
	}
	if ( strlen ( $middle ) == 1 )		// Check for initial only
	{
		$middle .= "." ;		// Add punctuation
	}
	if ( strlen ( $middle ) == 0 )		// Check for no middle name
	{
		$name = $first . " " . $last;
	}
	else
	{
		$name = $first . " " . $middle . " " . $last ;
	}
	return $name ;
}

// Format name strings into a single string.
// Parameters $authorInfo, $order are the author record and the author order on
// the paper. Order may be used at a later date to provide various author formats depending
// on author position etc. More compact Version of formatAuthor.

function formatAuthorInfo( $authorInfo , $order = 1 )
{
	$first = trim ( stripslashes  ( $authorinfo->FirstName ) ) ; // Check for additional whitespace	
	$middle = trim ( stripslashes ( $authorinfo->MiddleName ) ) ;
	$last = trim ( stripslashes ( $authorinfo->LastName ) ) ;

	if ( strlen ( $first ) == 1 )		// Check for initial only
	{
		$first .= "." ;			// Add punctuation
	}
	if ( strlen ( $middle ) == 1 )		// Check for initial only
	{
		$middle .= "." ;		// Add punctuation
	}
	if ( strlen ( $middle ) == 0 )		// Check for no middle name
	{
		$name = $first . " " . $last;
	}
	else
	{
		$name = $first . " " . $middle . " " . $last ;
	}
	return $name ;
}

// Get the name the browser 
// Return values IEWin, NetscapeWin, NetscapeX11, OperaWin, Unknown
// Netscape Windows= Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.0.1) Gecko/20020823 Netscape/7.0
// Netscape Unix = Mozilla/4.8 [en] (X11; U; SunOS 5.8 sun4u)
// IE6 = Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Q312461; .NET CLR 1.0.3705)
// Opera = Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.11 [en]

function getBrowser( )
{
	$agent = $_SERVER["HTTP_USER_AGENT"] ; 
	$isIE = stristr($agent, "Mozilla") && (stristr($agent, "compatible")) && (!stristr($agent, "Opera")) ;
	$isOpera = stristr($agent, "Mozilla") && stristr($agent, "Opera") ;
	$isNSwin = stristr($agent, "Mozilla") && (stristr($agent, "Netscape")) ;
	$isNSX11 = stristr($agent, "Mozilla") && stristr($agent, "X11") ;
	
	$browser = "Unknown" ;
	if ($isIE)
	{
		$browser = "IEWin" ;
	}
	else if ($isNSwin)
	{
		$browser = "NetscapeWin" ;
	}
	else if ($isNSX11)
	{
		$browser = "NetscapeX11";
	}
	else if ($isOpera)
	{
		$browser = "OperaWin";
	}
	return $browser;



}

function getAuthorsCommaSeparated ( &$written_result , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $authorIDList;
	
	$writtenInfo = $written_result -> FetchNextObj() ;
	$authorIDList = $writtenInfo -> AuthorID ;	
	$numAuthors = $written_result -> RecordCount();
	
	while ( $writtenInfo = $written_result -> FetchNextObj() )
	{
		$authorIDList .= " , " . $writtenInfo -> AuthorID ;
	}	
	
	$author_sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Author WHERE AuthorID IN ( " . $authorIDList . " ) " ;
		
	$author_result = $db -> Execute($author_sql);
		
	if(!$author_result)
	{
		$err_message .= " Error in reading author table <br>\n";
		return false ;
	}

	$authorInfo = $author_result -> FetchNextObj() ;
	$authorCount = 1 ;
	$first = stripslashes ( $authorInfo -> FirstName ) ;
	$middle = stripslashes ( $authorInfo -> MiddleName ) ;
	$last = stripslashes ( $authorInfo -> LastName ) ;
	$authorIDList = formatAuthor( $first , $middle , $last , $authorCount) ;
	
	while ( $authorInfo = $author_result -> FetchNextObj() )
	{
		$authorCount++ ;
		if ($authorCount == $numAuthors)
		{
			$first = stripslashes ( $authorInfo -> FirstName ) ;
			$middle = stripslashes ( $authorInfo -> MiddleName ) ;
			$last = stripslashes ( $authorInfo -> LastName ) ;
			$name = formatAuthor( $first , $middle , $last , $authorCount) ;
			if ( $numAuthors == 2 )	// Check for only two authors
			{
				$authorIDList .= " and " . $name;
			}
			else
			{
			$authorIDList .= ", and " . $name;
			}
		}
		else
		{
			$first = stripslashes ( $authorInfo -> FirstName ) ;
			$middle = stripslashes ( $authorInfo -> MiddleName ) ;
			$last = stripslashes ( $authorInfo -> LastName ) ;
			$name = formatAuthor( $first , $middle , $last , $authorCount) ;
			$authorIDList .= ", " . $name;
		}
		
	}		
	
	return $authorIDList ;
}

//Function that updates the user details
function update_details( $details , $err_message = "" )
{

	$db = adodb_connect( &$err_message );
	
	if (!$db)
	{
    	$err_message .= "Could not connect to database server - please try later. <br>\n";
		return false ;
	}
	
		
	$sql = "SELECT R.RegisterID FROM " . $GLOBALS["DB_PREFIX"] . "Member M , " . $GLOBALS["DB_PREFIX"] . "Registration R WHERE M.RegisterID = R.RegisterID and MemberName = '" . addslashes ( $_SESSION["valid_user"] ) . "'";
	
	$result = $db -> Execute($sql);
	
	if($result -> RecordCount() > 0 ){
		
		$info = $result -> FetchNextObj();
		$registerID = $info -> RegisterID;		
	}
	else
	{
		$err_message .= "The User ID is invalid <br>\n";
		return false ;
	}
	
    // Grab the fields in the Registration table, and then intersect them with
    // the list of form fields that map to values
    // Note: This is obviously going to be slower than simply hard-coding, but
    // it's also safer. Whether it's better is another question entirely.
    $result = $db -> Execute("SHOW COLUMNS FROM ". $GLOBALS["DB_PREFIX"] ."Registration");
    $fields = $result -> FetchRow();
    $fieldlist = array();
    while ($fields)
    {
      $fieldlist[] = $fields[0];
      $fields = $result -> FetchRow();
    }
    $fieldlist = array_key_screen(get_user_details_field_map(),$fieldlist);
    // Got a safe list, so now use it to filter the $details
    $details = array_key_screen($details, $fieldlist);
    // Now produce a list mapping field names to values
    // Note: At worst this should only produce null values if the form fields
    // and map don't match. All fields in $fieldlist are in Registration
    $fieldVals = array();
    foreach ($fieldlist as $key => $value)
    {
        $fieldVals[$key] = $details[$value];
    }
    // Make values "nice"
    foreach ($fieldVals as $key => $value)
    {
        $fieldVals[$key] = addslashes($value);
    }
    
    // Produce the strings for field names & values
    $sql = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Registration SET ";
    $sqlVals = array();
    foreach ($fieldVals as $key => $value)
    {
        $sqlVals[] = $key."='$value'";
    }
    $sql .= implode(",",$sqlVals);
    $sql .= " WHERE RegisterID = ".db_quote($db, $registerID);	
	$result = $db -> Execute($sql);
	
	if(!$result)
	{
		$err_message .= " Update information process failed <br>\n";
		return false ;
	}
	else
	{		 
		return true ;
	}
}

//Function to writhdraw the paper from processing
function withdraw_paper( $id , $err_message = "" )
{
	$db = adodb_connect( &$err_message );
	
	if (!$db)
	{
    	$err_message .= " Could not connect to database server - please try later. <br>\n";
		return false ;
	}
	
	$sql = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Paper SET Withdraw = 'true'";
	$sql .= " WHERE PaperID = ".db_quote($db, $id);
	
	$result = $db -> Execute($sql);
	
	if($result)
	{
		return true ;
	}
	else
	{
		$err_message .= " Unable to Update withdraw request on Paper table. <br>\n";
		return false ;
	}
}

function getMemberInfo($MemberName){

	
	//Establish database connection
	$db = adodb_connect();
  
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"]."PrivilegeType P," . $GLOBALS["DB_PREFIX"] . "Registration R";
	$sql .= " WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
	$sql .= " AND M.RegisterID = R.RegisterID";
	$sql .= " AND M.MemberName = ".db_quote($db, $MemberName);
	
	$result = $db -> Execute($sql);
	if(!$result)
		return "Could not retrieve the Member email - pls try again later";
		
	
	$userInfo = $result -> FetchNextObj();
	
	return $userInfo;


}

function get_member_info_with_id($registerID){

	
	//Establish database connection
	$db = adodb_connect();
  
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"]."PrivilegeType P," . $GLOBALS["DB_PREFIX"] . "Registration R";
	$sql .= " WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
	$sql .= " AND M.RegisterID = R.RegisterID";
	$sql .= " AND M.RegisterID = $registerID";
	$result = $db -> Execute($sql);
	
	if(!$result)
		return "Could not retrieve the Member email - pls try again later";
	
	$userInfo = $result -> FetchNextObj();
	return $userInfo;
}

function getMemberFullName($MemberName){
	
	
	//Establish database connection
	$db = adodb_connect();
  
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$sql  = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Registration R," . $GLOBALS["DB_PREFIX"] . "Member M";
	$sql .= " WHERE M.RegisterID = R.RegisterID";
	$sql .= " AND M.MemberName = '".$MemberName."'";
	
	$result = $db -> Execute($sql);
	if(!$result)
		return "Could not retrieve the Member name - pls try again later";
		
	
	$userInfo = $result -> FetchNextObj();
	
	//Get the full name
	
	$fullName = formatAuthor( $userInfo -> FirstName , $userInfo -> MiddleName, $userInfo -> LastName);
	if ($userInfo -> LastName == "")
	    {
		$fullName = "N/A";
	    }
	return $fullName;
}
//}}}


////////////// Start Reviewers Bidding Functions //////////////////
//{{{
function get_querystring_from_href ( &$link )
{
	$querystring_array = array() ;
	$strarray = explode ( "</a>" , $link ) ;
//		echo "<br>\n get_querystring_from_href for " . $link . "<br>\n" ;
//		display ( $strarray ) ;
	$limit = count ( $strarray ) ;
	if ( !$limit )
	{
		return $querystring_array ;
	}
	
	$str = "<a href=" ;
	$len = strlen ( $str ) ;	
//		echo "\$len=" . $len . "<br>\n" ;
	
	for ( $a=0 ; $a < $limit ; $a++ )
	{
		if ( ( $str_pos = strpos ( $strarray[$a] , $str ) ) !== false )
		{
//				echo "\$a=" . $a . "<br>\n" ;
//				echo "\$str_pos=" . $str_pos . "<br>\n" ;			
			$inner_str = "?" ;
//			$inner_len = strlen ( $inner_str ) ;
			$inner_len = 0 ;
//				echo "\$inner_len=" . $inner_len . "<br>\n" ;				
			$start = $str_pos + $len ;
//				echo "\$start=" . $start . "<br>\n" ;				
			if ( ( $inner_str_pos = strpos ( $strarray[$a] , $inner_str , $start ) ) !== false )
			{			
				$inner_start = $inner_str_pos + $inner_len ;
//					echo "\$inner_start=" . $inner_start . "<br>\n" ;					
				$str_endpos = strpos ( $strarray[$a] , "\"" , $inner_start ) ;
//					echo "\$str_endpos=" . $str_endpos . "<br>\n" ;										
				$sub_len = $str_endpos - $inner_start ;
//					echo "\$sub_len=" . $sub_len . "<br>\n" ;										
//					echo "querystring=" . substr( $strarray[$a] , $inner_start , $sub_len ) . "<br>\n" ;
				$querystring_array[] = substr( $strarray[$a] , $inner_start , $sub_len ) ;
			}
		}			
	}
//		$newlink = implode ( "</a>" , $strarray ) ;
	reset ( $querystring_array ) ;
	return $querystring_array ;		
}

function delete_href ( &$link )
{
	$strarray = explode ( "</a>" , $link ) ;
//		echo "<br>\n get_querystring_from_href for " . $link . "<br>\n" ;
//		display ( $strarray ) ;
	$limit = count ( $strarray ) ;
	if ( !$limit )
	{
		return $link ;
	}
	
	$str = "<a href=" ;
	$len = strlen ( $str ) ;	
//		echo "\$len=" . $len . "<br>\n" ;
	
	for ( $a=0 ; $a < $limit ; $a++ )
	{
		if ( ( $str_pos = strpos ( $strarray[$a] , $str ) ) !== false )
		{
//				echo "\$a=" . $a . "<br>\n" ;
//				echo "\$str_pos=" . $str_pos . "<br>\n" ;			
			$inner_str = "\"" ;
			$inner_len = strlen ( $inner_str ) ;
//				echo "\$inner_len=" . $inner_len . "<br>\n" ;				
			$start = $str_pos + $len ;
//				echo "\$start=" . $start . "<br>\n" ;				
			if ( ( $inner_str_pos = strpos ( $strarray[$a] , $inner_str , $start ) ) !== false )
			{			
				$inner_start = $inner_str_pos + $inner_len ;
//					echo "\$inner_start=" . $inner_start . "<br>\n" ;					
				$str_endpos = strpos ( $strarray[$a] , "\"" , $inner_start ) ;
//					echo "\$str_endpos=" . $str_endpos . "<br>\n" ;										
				$sub_len = $str_endpos + $inner_len - $inner_str_pos ;
//					echo "\$sub_len=" . $sub_len . "<br>\n" ;										
//					echo "querystring=" . substr( $strarray[$a] , $inner_start , $sub_len ) . "<br>\n" ;
//					$querystring_array[] = substr( $strarray[$a] , $inner_start , $sub_len ) ;
				$strarray[$a] = substr_replace ( $strarray[$a] , "" , $inner_str_pos , $sub_len ) ;					
			}
		}			
	}
	$newlink = implode ( "</a>" , $strarray ) ;
	return $newlink ;
}

function insert_js_call_in_href ( &$js , &$link )
{
	$strarray = explode ( "</a>" , $link ) ;
	$limit = count ( $strarray ) ;
	if ( !$limit || !(count($js)) )
	{
		return $link ;
	}
	$str = "<a href=" ;
	$len = strlen ( "<a href=" ) ;
	$d = 0 ;
	for ( $a=0 ; $a < $limit ; $a++ )
	{
		if ( ( $str_pos = strpos ( $strarray[$a] , $str ) ) !== false )
		{
			$str_pos = strpos ( $strarray[$a] , ">" , ( $str_pos + $len ) ) ;
			$strarray[$a] = substr_replace ( $strarray[$a] , $js[$d] , $str_pos , 0 ) ;
			$d++ ;
		}			
	}
	$newlink = implode ( "</a>" , $strarray ) ;
	return $newlink ;
}

function format_Sorting_URL($url,$sortType){
	switch($sortType){
		case "ASC":
			$returnURL = "<a href=\"".$url."\"><img src=\"../images/down.gif\" border=0></a>";
			break;
		case "DESC":
			$returnURL = "<a href=\"".$url."\"><img src=\"../images/up.gif\" border=0></a>";
			break;
	}
	
	return $returnURL;
}

function check_User_Account_Exist($userName)
{
	//Establish database connection
	$db = adodb_connect();
  
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Member";
	$sql .= " WHERE MemberName = ".db_quote($db,$userName);
	$result = $db -> Execute($sql);
	
	if(!$result)
		return "Could not retrieve the user informaiton";
		
	if($result -> RecordCount() == 0)
		return false;
	else
		return true;
}
//}}}

//{{{  Registration Template Functions

function get_user_details_form($postVars, $error_array = array())
{
    ?>
<tr>
	<td height="24">First Name *</td>
	<td>
		<input name="firstname" type="text" id="firstname" value="<?php echo $postVars['firstname'] ; ?>" size="20" maxlength="30" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["firstname"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td>Middle Name</td>
	<td>
		<input name="middlename" type="text" id="middlename" value="<?php echo $postVars['middlename'] ; ?>" size="20" maxlength="30" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["middlename"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td>Last Name *</td>
	<td>
		<input name="lastname" type="text" id="lastname" value="<?php echo $postVars['lastname'] ; ?>" size="20" maxlength="30" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["lastname"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td>Organisation *</td>
	<td>
		<input name="org" type="text" id="org" value="<?php echo $postVars['org'] ; ?>" size="30" maxlength="50" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["org"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">Address 1 * </td>
	<td>
		<input name="address1" type="text" id="address1" value="<?php echo $postVars['address1'] ; ?>" size="50" maxlength="100" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["address1"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">Address 2</td>
	<td>
		<input name="address2" type="text" id="address2" value="<?php echo $postVars['address2'] ; ?>" size="50" maxlength="100" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["address2"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">City *</td>
	<td>
		<input name="city" type="text" id="city" value="<?php echo $postVars['city'] ; ?>" size="20" maxlength="30" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["city"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">State/Province *</td>
	<td>
		<input name="state" type="text" id="state" value="<?php echo $postVars['state'] ; ?>" size="20" maxlength="30" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["state"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">Postal Code *</td>
	<td>
		<input name="postalcode" type="text" id="postalcode" value="<?php echo $postVars['postalcode'] ; ?>" size="15" maxlength="15" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["postalcode"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td height="28" valign="top">Country *</td>
	<td>
		<?php
			  	echo GetCountryDropDownBox( $postVars["country"] ) ;
			  	echo "<font color=\"#FF0000\">" . $error_array["country"][0] . "</font>" ; 
			  ?></td>
</tr>
<tr>
	<td valign="top">
		<?php echo $GLOBALS["PRIMARY_EMAIL_NAME"]; ?> *</td>
	<td>
		<input name="email" type="text" id="email" value="<?php echo $postVars['email'] ; ?>" size="30" maxlength="50" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["email"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">
		<?php echo $GLOBALS["SECONDARY_EMAIL_NAME"]; ?></td>
	<td>
		<input name="emailHome" type="text" id="emailHome" value="<?php echo $postVars['emailHome'] ; ?>" size="30" maxlength="50" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["emailHome"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">
		<?php echo $GLOBALS["PRIMARY_PHONE_NAME"]; ?> *</td>
	<td>
		<input name="phoneno" type="text" id="phoneno" value="<?php echo $postVars['phoneno'] ; ?>" size="25" maxlength="25" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["phoneno"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">
		<?php echo $GLOBALS["SECONDARY_PHONE_NAME"]; ?></td>
	<td>
		<input name="phonenoHome" type="text" id="phonenoHome" value="<?php echo $postVars['phonenoHome'] ; ?>" size="25" maxlength="25" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["phonenoHome"][0] . "</font>" ; ?>
	</td>
</tr>
<tr>
	<td valign="top">Fax No </td>
	<td>
		<input name="faxno" type="text" id="faxno" value="<?php echo $postVars['faxno'] ; ?>" size="25" maxlength="25" />
		<?php echo "<font color=\"#FF0000\">" . $error_array["faxno"][0] . "</font>" ; ?>
	</td>
</tr>
<?php
}

function get_user_details_form_exemptions()
{
  return array ( "faxno" , "address2" , "middlename" , "emailHome" , "phonenoHome" );
}

function get_user_details_field_map()
{
  return array(
    "FirstName" => "firstname",
    "MiddleName" => "middlename",
    "LastName" => "lastname",
    "Organisation" => "org",
    "Address1" => "address1",
    "Address2" => "address2",
    "City" => "city",
    "State" => "state",
    "PostalCode" => "postalcode",
    "Country" => "country",
    "Email" => "email",
    "EmailHome" => "emailHome",
    "PhoneNumber" => "phoneno",
    "PhoneNumberHome" => "phonenoHome",
    "FaxNumber" => "faxno"
    );
}
//}}}

//{{{   Date Format Functions
/*
format_date() is used to format ISO 8601 dates into friendlier formats. 
It takes the same format syntax as date().

Parameters:
$format = the date() style format to output (default = 'Y-m-d')
$date   = the ISO 8601 date to format (default = today's date)
*/
function format_date($format = 'Y-m-d', $datetime = FALSE)
{
    if (!($datetime)) $datetime = date('Y-m-d H:i:s'); // If no date supplied, use current time
    list($date,$time) = split('[ ]',$datetime);
	list($year,$month,$day) = split('[/.-]',$date);
    list($hour,$minute,$second) = split('[.:]',$time);
    return date($format, mktime($hour,$minute,$second,$month,$day,$year));
}

function get_date_obj($datetime = FALSE)
{
    if (!($datetime)) $datetime = date('Y-m-d H:i:s'); // If no date supplied, use current time
    $output = array();
	list($output["Date"],$output["Time"]) = split('[ ]',$datetime);
	list($output["Year"],$output["Month"],$output["Day"]) = split('[/.-]',$output["Date"]);
    list($output["Hour"],$output["Minute"],$output["Second"]) = split('[.:]',$output["Time"]);
	return (object)$output;
}


//}}}

//{{{	Post Submission Management fuctions


//{{{ Unscheduled Paper 
/*
	Assigns a specified paper to a particular presentation type, with the
	session to be allocated at a later date.
*/
function assign_paper_presentation_type( $paperID, $presentationTypeID, $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $sql = "REPLACE INTO " . $GLOBALS["DB_PREFIX"] . "UnscheduledPaper " ;
	$sql .= "( PaperID, PresentationTypeID )" ;
	$sql .= " VALUES ( $paperID , $presentationTypeID ) " ;
    $result = $db -> Execute($sql);
	
	if( !$result )
	{		
		$err_message .= " Could not assign paper '$paperID' to presentation type '$presentationTypeID' <br>\n ";	// Exception has occurred			
		return false ;		
	}
	return true;
}

function remove_paper_presentation( $paperID, $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $sql = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "UnscheduledPaper " ;
	$sql .= "WHERE PaperID = ".$paperID ;
	$result = $db -> Execute($sql);
	
	if ($db -> Affected_Rows()) return true;
	
	// If we got here, then it's already been assigned a slot
	$sql = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "SessionSlot " ;
	$sql .= "WHERE PaperID = ".$paperID ;
	$result = $db -> Execute($sql);
	
	if ($db -> Affected_Rows()) return true;
	
	return false ;
}

//}}}

//{{{ Rooms

function get_rooms( $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Room";
	$result = $db -> Execute($sql);
	
	if (!$result) return NULL;
	
	$rooms = array();
	while ($room = $result -> FetchNextObj())
	{
		$rooms[] = $room;
	}
	
	return $rooms;
}

function get_room_info($roomID , $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Room ";
	$sql .= "WHERE RoomID=$roomID";
	$result = $db -> Execute($sql);
	
	if (!$result)
		return NULL;
	return $result -> FetchNextObj();
}

function get_rooms_array()
{
	$rooms = get_rooms();
	$rooms_array = array();
	foreach ($rooms as $room)
	{
		$rooms_array[$room -> RoomID] = $room -> RoomName;
	}
	
	return $rooms_array;
}

function add_new_room($roomName, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Room ";
	$sql .= "SET RoomName=".db_quote($db,$roomName);
	$result = $db -> Execute($sql);
	
	if (!$result)
	{
		$err_message .= "Failed to insert new room into database.";
	}
	return $db -> Insert_ID();
}

function update_room($roomID, $roomName, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db) return false;
	
	$sql =  "UPDATE " . $GLOBALS["DB_PREFIX"] . "Room ";
	$sql .= "SET RoomName = ".db_quote($db,$roomName);
	$sql .= "WHERE RoomID = ".$roomID;
	$result = $db -> Execute($sql);
	
	return $result;
}

function delete_room($roomID , $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db) return false;
	
	$sql =  "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Room ";
	$sql .= "WHERE RoomID = ".$roomID;
	$result = $db -> Execute($sql);
	
	return $result;
}

function sessions_in_room($roomID, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db) 
	{
		$err_message .= "Unable to connect to database.<br />\n";
		return true;	// Don't want to make assumption that it ISN'T used
	}
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "WHERE RoomID=".$roomID;
	$result = $db -> Execute($sql);
	
	if ($result) {
		$sessions = array();
		while ($session = $result -> FetchNextObj())
			$sessions[] = $session;
	}
	return $sessions;
}

//}}}

//{{{ Presentation Types

class PresentationType
{
	var $PresentationTypeID;
	var $PresentationTypeName;
	var $SlotLength;
}

function get_presentation_types($err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "PresentationType;";
	$result = $db -> Execute($sql);
	
	if (!$result)
	{
		$err_message .= "Failed to insert new room into database.";
	} else {
		$presentationTypes = array();
		while ($type = $result -> FetchNextObj())
			$presentationTypes[] = $type;
	}
	return $presentationTypes;
}


function add_new_presentation_type($presentationType, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "PresentationType ";
	$sql .= "SET PresentationTypeName=".db_quote($db,$presentationType -> PresentationTypeName);
	$sql .= ",   SlotLength=".$presentationType -> SlotLength;
	$result = $db -> Execute($sql);
	
	if (!$result)
	{
		$err_message .= "Failed to insert new presentation type into database.";
	}
	return $db -> Insert_ID();
}

function update_presentation_type($presentationType, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "UPDATE " . $GLOBALS["DB_PREFIX"] . "PresentationType ";
	$sql .= "SET PresentationTypeName=".db_quote($db,$presentationType -> PresentationTypeName);
	$sql .= ",   SlotLength=".$presentationType -> SlotLength;
	$sql .= " WHERE PresentationTypeID=".$presentationType -> PresentationTypeID;
	$result = $db -> Execute($sql);
	
	if (!$result)
		$err_message .= "Failed to update presentation type into database.";
	return $result;
}

function delete_presentation_type($typeID, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db) return false;
	
	$sql =  "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "PresentationType ";
	$sql .= "WHERE PresentationTypeID = ".$typeID;
	$result = $db -> Execute($sql);
	
	return $result;
}

function sessions_using_presentation_type($typeID, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db) 
	{
		$err_message .= "Unable to connect to database.<br />\n";
		return true;	// Don't want to make assumption that it ISN'T used
	}
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "WHERE PresentationTypeID=".$typeID;
	$result = $db -> Execute($sql);
	
	if ($result) {
		$sessions = array();
		while ($session = $result -> FetchNextObj())
			$sessions[] = $session;
	}
	
	return $sessions;
}

function papers_using_presentation_type($typeID, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db) 
	{
		$err_message .= "Unable to connect to database.<br />\n";
		return true;	// Don't want to make assumption that it ISN'T used
	}
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."UnscheduledPaper ";
	$sql .= "WHERE PresentationTypeID=".$typeID;
	$result = $db -> Execute($sql);
	
	if ($result) {
		$papers = array();
		while ($paper = $result -> FetchNextObj())
			$papers[] = $paper -> SessionID;
	}
	
	return $papers;
}

function get_presentation_type_for_paper($paperID, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	if (!$db) 
	{
		$err_message .= "Unable to connect to database.<br />\n";
		return true;	// Don't want to make assumption that it ISN'T used
	}
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."UnscheduledPaper ";
	$sql .= "WHERE PaperID=".$paperID;
	$result = $db -> Execute($sql);
	
	if ($result && $result -> RecordCount() > 0) {
		$entry = $result -> FetchNextObj();
		return $entry -> PresentationTypeID;
	}
	
	$sql =  "SELECT * ";
	$sql .= "FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "NATURAL JOIN ".$GLOBALS["DB_PREFIX"]."SessionSlot ";
	$sql .= "WHERE PaperID = ".$paperID;
	$result = $db -> Execute($sql);
	
	if ($result && $result -> RecordCount() > 0) {
		$entry = $result -> FetchNextObj();
		return $entry -> PresentationTypeID;
	}
	
	return "";
}

function get_presentation_info($typeID)
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "PresentationType ";
	$sql .= "WHERE PresentationTypeID=".$typeID;
	$result = $db -> Execute($sql);
	
	if (!$result)
	{
		$err_message .= "Failed to get presentation type info from database.";
		return NULL;
	}
	return $result -> FetchNextObj();
}

//}}}

//{{{ Session
class Session
{
	var $SessionID;
	var $SessionName;
	var $TrackID;
	var $PresentationTypeID;
	var $StartTime;
	var $EndTime;
	var $RoomID;
	var $ChairID;
}

class SessionSlotsInfo
{
	var $Session;
	var $Slots;
	var $MaxSlots;
}


function add_session($sessionObj, $err_message = "")
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Remove SessionID as this is a new entry
	$fields = array_diff_assoc(get_object_vars($sessionObj), array("SessionID"=>0));
	
	// Take the rest of the values, and insert them as a row, doing the quoting on the fly
	$sql =  "INSERT INTO ".$GLOBALS["DB_PREFIX"]."Session";
	$sql .= "(".implode(", ", array_keys($fields)).") VALUES ";
	$sql .= "(".implode(", ", array_map("db_quote",array_fill(0,count($fields),$db),$fields)).")";
	$result = $db -> Execute($sql);
	
	return $db -> Insert_ID();
}

function get_sessions( $sortBy = "StartTime", $descending = false,  $err_message = "" )
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	// Check if the sort arguement is valid
	if (!in_array( $sortBy, array_keys(get_object_vars(new Session))))
		$sortBy = "SessionID";
	$sql .= "ORDER BY $sortBy ".($descending?"DESC":"ASC");	
	$result = $db -> Execute($sql);
	
	$sessions = array();
	while ($session = $result -> FetchNextObj())
	{
		$sessions[] = $session;
	}
	
	return $sessions;
}

function get_similar_sessions( $sessionID, $err_message = "" )
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "WHERE SessionID=$sessionID";
	$result = $db -> Execute($sql);
	
	if (!($session = $result -> FetchNextObj()))
	{
		$err_message .= "Can't find SessionID=$sessionID<br />\n";
		return NULL;
	}
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "WHERE TrackID=".$session -> TrackID;
	$sql .= "  AND PresentationTypeID=".$session -> PresentationTypeID;
	$result = $db -> Execute($sql);
	
	$sessions = array();
	while ($session = $result -> FetchNextObj())
	{
		$sessions[] = $session;
	}
	
	return $sessions;
}

function update_session( $sessionObj, $err_message = "" )
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Remove SessionID as this is a new entry
	$fields = array_diff_assoc(get_object_vars($sessionObj), array("SessionID" => $sessionObj -> SessionID));
	
	$assignments = array();
	foreach ($fields as $key => $value)
	{
		$assignments[] = $key."=".db_quote($db, $value);
	}
	
	$sql =  "UPDATE " . $GLOBALS["DB_PREFIX"] . "Session ";
	$sql .= "SET ".implode(", ", $assignments)." ";
	$sql .= "WHERE SessionID=".$sessionObj -> SessionID;
	$result = $db -> Execute($sql);
	
	if (!$result)
		$err_message .= "Unable to update details for SessionID = ".$sessionObj -> SessionID;
	
	return $result;
}

function delete_session( $sessionID, $err_message = "" )
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "DELETE FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "WHERE SessionID=".$sessionID;
	$result = $db -> Execute($sql);
	
	if (!$result)
		$err_message .= "Unable to delete SessionID = ".$sessionID;
	
	return $result;
}

function get_session_info( $sessionID , $err_message = "" )
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "WHERE SessionID=".$sessionID;
	$result = $db -> Execute($sql);
	
	if (!$result)
		return NULL;
	
	return $result -> FetchNextObj();
}


function get_session_slots_info( $sessionID , $err_message = "" )
{
	$sessionSlotInfo = new SessionSlotsInfo;
	
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."SessionSlot ";
	$sql .= "WHERE SessionID=".$sessionID;
	$result = $db -> Execute($sql);
	
	$sessionSlotInfo -> Slots = array();
	
	while ($slot = $result -> FetchNextObj()){
		$sessionSlotInfo -> Slots[] = $slot;
		}
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "WHERE SessionID=".$sessionID;
	$result = $db -> Execute($sql);
	
	$session = $result -> FetchNextObj();
	$type = get_presentation_info($session -> PresentationTypeID);
	
	// Calculate session length to the minute
	$timespan = (format_date("U",$session -> EndTime) - format_date("U",$session -> StartTime))/60;
	
	// Divide available minutes by slot length to get maximum number of slots that can fit
	$sessionSlotInfo -> MaxSlots = floor($timespan / $type -> SlotLength);
	// Attach session to object
	$sessionSlotInfo -> Session = $session;
	
	return $sessionSlotInfo;
}

function get_running_days($err_message = "")
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT DISTINCT ".dbdf_out($db, "StartTime");
	$sql .= " FROM ".$GLOBALS["DB_PREFIX"]."Session";
	$result = $db -> Execute($sql);
	
	if (!$result)
		return NULL;
	
	$dates = array();
	while ($dateObj = $result -> FetchNextObj())
	{
		$dates[] = $dateObj -> StartTime;
	}
	sort($dates, SORT_STRING);
	
	return $dates;
}

//}}}

//{{{ Session Slot

class SessionSlot
{
	var $SessionID;
	var $SlotID;
	var $PaperID;
}

function get_session_slot_info($paperID , $err_message = "")
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."SessionSlot ";
	$sql .= "WHERE PaperID=".$paperID;
	$result = $db -> Execute($sql);
	
	if (!$result)
		return NULL;
	return $result -> FetchNextObj();
}

function add_session_slot($entry , $err_message = "")
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$fields = get_object_vars($entry);
	
	$sql =  "INSERT INTO ".$GLOBALS["DB_PREFIX"]."SessionSlot";
	$sql .= "(".implode(", ", array_keys($fields)).") VALUES ";
	$sql .= "(".implode(", ", array_map("db_quote",array_fill(0,count($fields),$db),$fields)).")";
	$result = $db -> Execute($sql);
	
	return $result;
}

function update_session_slots($slots, $err_message = "")
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	foreach ($slots as $entry)
	{
		// Remove SessionID as this is a new entry
		$fields = array_diff_assoc(get_object_vars($entry), array("PaperID" => $entry -> PaperID));
		
		$assignments = array();
		foreach ($fields as $key => $value)
		{
			$assignments[] = $key."=".db_quote($db, $value);
		}
		
		$sql =  "UPDATE " . $GLOBALS["DB_PREFIX"] . "SessionSlot ";
		$sql .= "SET ".implode(", ", $assignments)." ";
		$sql .= "WHERE PaperID=".$entry -> PaperID;
		
		$result = $db -> Execute($sql);
		
		if (!$result)
			$err_message .= "Unable to update details for PaperID = ".$entry -> PaperID;
	}
}

//}}}

//{{{ Presenter
class Presenter
{
	var $PaperID;
	var $RegisterID;
}

function get_presenter_info($paperID, $err_message = "")
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Presenter ";
	$sql .= "WHERE PaperID=".$paperID;
	$result = $db -> Execute($sql);
	
	if (!$result)
		return NULL;
	return $result -> FetchNextObj();
	
}

function add_presenter($entry, $err_message = "")
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$fields = get_object_vars($entry);
	
	$sql =  "INSERT INTO ".$GLOBALS["DB_PREFIX"]."Presenter";
	$sql .= "(".implode(", ", array_keys($fields)).") VALUES ";
	$sql .= "(".implode(", ", array_map("db_quote",array_fill(0,count($fields),$db),$fields)).")";
	$result = $db -> Execute($sql);
	
	return $result;
}

function update_presenter( $entry, $err_message = "" )
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Remove SessionID as this is a new entry
	$fields = array_diff_assoc(get_object_vars($entry), array("PaperID" => $entry -> PaperID));
	
	$assignments = array();
	foreach ($fields as $key => $value)
	{
		$assignments[] = $key."=".db_quote($db, $value);
	}
	
	$sql =  "UPDATE " . $GLOBALS["DB_PREFIX"] . "Presenter ";
	$sql .= "SET ".implode(", ", $assignments)." ";
	$sql .= "WHERE PaperID=".$sessionObj -> PaperID;
	$result = $db -> Execute($sql);
	
	if (!$result)
		$err_message .= "Unable to update details for PaperID = ".$entry -> PaperID;
	
	return $result;
}

//}}}

//{{{ Scheduling

//{{{ Automatic

function get_first_empty_slot($trackID, $presentationTypeID)
{
	 // Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Grab ordered list of suitable sessions
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Session ";
	$sql .= "WHERE TrackID = $trackID ";
	$sql .= "AND PresentationTypeID = $presentationTypeID ";
	$sql .= "ORDER BY StartTime";
	$result = $db -> Execute($sql);
	// echo $result -> RecordCount();
	if (!$result) return NULL;
	
	// Now check which have an available session
	while ($session = $result -> FetchNextObj())
	{
		$slots = get_session_slots_info($session -> SessionID);
		// If all slots are already filled, no point looking for a spare one.
		if ($slots -> MaxSlots == count($slots -> Slots))
			continue;
		
		$slotNumbers = range(1, $slots -> MaxSlots);
		foreach ($slots -> Slots as $slot)
		{
			$slotNumbers = array_diff($slotNumbers, array($slot -> SlotID));
		}
		
		$slot = new SessionSlot();
		$slot -> SessionID = $session -> SessionID;
		$slot -> SlotID = array_shift($slotNumbers); // Gets first available slot number
		return $slot;
	}
	
	return NULL;
}

function autoschedule_waiting_papers()
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	// Grab ordered list of suitable sessions
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."UnscheduledPaper ";
	$result = $db -> Execute($sql);
	
	while ($unPaper = $result -> FetchNextObj())
	{
		$paper = get_paper_info($unPaper -> PaperID);
		$nextSlot = get_first_empty_slot($paper -> TrackID, $unPaper -> PresentationTypeID);
		if ($nextSlot)
		{
			$nextSlot -> PaperID = $unPaper -> PaperID;
			remove_paper_presentation($unPaper -> PaperID);
			add_session_slot($nextSlot);
		}
	}
}

function get_papers_in_order()
{
	$papers = array();
	$sessions = get_sessions("StartTime");
	foreach ($sessions as $session)
	{
		$session_slots = get_session_slots_info($session -> SessionID);
		foreach ($session_slots -> Slots as $slot)
		{
			$papers[] = get_paper_info($slot -> PaperID);
		}
	}
	return $papers;
}
	
function resolve_slot_length_change($presentationTypeID)
{
	$sessions = sessions_using_presentation_type($presentationTypeID);
	if (count($sessions) == 0) return;
	
	$autoschedule_required = false;
	foreach ($sessions as $session)
	{
		$slots = get_session_slots_info($session -> SessionID);
		// if the session is over-subscribed now
		if (count($slots -> Slots) > $slots -> MaxSlots)
		{
			foreach ($slots -> Slots as $slot)
				if ($slot -> SlotID > $slots -> MaxSlots)
				{
					// shift back into Unassigned Papers
					remove_paper_presentation($slot -> PaperID);
					assign_paper_presentation($slot -> PaperID, $slot -> PresentationTypeID);
				}
			$autoschedule_required = true;
		}
	}
	if ($autoschedule_required)
		autoschedule_waiting_papers();
}


//}}}

//}}}

//{{{ Proceedings

/**
* Returns page numbers of papers
* 
* @return  Page Number ->  PaperID array
*/
function get_paper_pages( $err_message = "" )
{
	$papers = get_papers_in_order();
	
	$pages = array();
	$nextPage = 1;
	foreach ($papers as $paper)
	{
		$pages[$nextPage] = $paper -> PaperID;
		$nextPage += $paper -> NumberOfPages;
		$nextPage += ($nextPage + 1) % 2; // Ensure nextPage is odd
	}
	return $pages;
}


function get_author_papers( $err_message = "" )
{
	// Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Author ";
	$sql .= "ORDER BY LastName";
	$result = $db -> Execute($sql);
	if (!$result) return null;
	
	$authors = array();
	while ($author = $result -> FetchNextObj())
		$authors[$author -> AuthorID] = $author;
	$pages = array_flip(get_paper_pages(&$err_message));
	
	$sql =  "SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Written ";
	$result = $db -> Execute($sql);
	
	if (!$result) return null;
	
	$presentedPapers = get_papers_in_order();
	$paperAuthors = array();
	while ($written = $result -> FetchNextObj())
	{
		$inArray = false;
		foreach ($presentedPapers as $pp)
		{
			if ($pp -> PaperID == $written -> PaperID)
				$inArray = true;
		}
		if (!$inArray) continue;
		if (!$paperAuthors[$written -> AuthorID])
			$paperAuthors[$written -> AuthorID] = array();
		$paperAuthors[$written -> AuthorID][] = $written -> PaperID;
	}
	
	$outList = array();
	foreach ($paperAuthors as $authorID => $papers)
	{	
		$first = stripslashes ($authors[$authorID] -> FirstName);
		$middle = stripslashes ($authors[$authorID] -> MiddleName);
		$last = stripslashes ($authors[$authorID] -> LastName);
		if ( strlen ( $first ) == 1 )		// Check for initial only
		{
			$first .= "." ;			// Add punctuation
		}
		if ( strlen ( $middle ) == 1 )		// Check for initial only
		{
			$middle .= "." ;		// Add punctuation
		}
		$name = sprintf("%s, %s %s", $last,$first,$middle);
		
		if (!$outList[$name])
			$outList[$name] = array();
		foreach ($papers as $paper)
		{
			$outList[$name][$paper] = $pages[$paper];
		}
	}
	
	return $outList;
}

//}}}

//}}}


//{{{	Registration Form functions

//{{{	- Get registration form template XML

function get_registration_form_template_xml()
{
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	return $settingInfo -> RegFormXml;
}

//}}}

//{{{	- Get XML tags for currency

function get_currency_xml()
{
	$settingInfo = get_Conference_Settings();
	$outStr =	'<currency_symbol position="'.$settingInfo->CurrencyPosition.'">';
	$outStr .=	$settingInfo->CurrencySymbol;
	$outStr .=	'</currency_symbol>';
	return $outStr;
}
//}}}

//{{{	- Get selection XML
function get_selection_xml($selectionList)
{
	$selectionXml = "";
	foreach ($selectionList as $key=>$value)
	{
		if ($key == "regtype")
		{
			$selectionXml .= "<regtype_selection>";
			$selectionXml .= "<regtypeid>";
			$selectionXml .= $value;
			$selectionXml .= "</regtypeid>";
			$selectionXml .= "</regtype_selection>";
		} 
		else if ($key == "paytime")
		{
			$selectionXml .= "<paytime_selection>";
			$selectionXml .= "<paytimeid>";
			$selectionXml .= $value;
			$selectionXml .= "</paytimeid>";
			$selectionXml .= "</paytime_selection>";
		}
		else if ($key == "membership")
		{
			$selectionXml .= "<membership_selection>";
			foreach ($value as $membership)
			{
				$selectionXml .= "<memberid>";
				$selectionXml .= $membership;
				$selectionXml .= "</memberid>";
			}
			$selectionXml .= "</membership_selection>";
		} else {
			if ($value === array() || $value['quantity']==='0') continue;
			$selectionXml .= "<choice_selection>";
			$selectionXml .= "<choiceid>";
			$selectionXml .= $key;
			$selectionXml .= "</choiceid>";
			if (in_array('quantity', array_keys($value), true))
			{
				$selectionXml .= "<number>";
				$selectionXml .= $value['quantity'];
				$selectionXml .= "</number>";
			}
			else if (in_array('optionid', array_keys($value), true))
			{
				$selectionXml .= "<optionid>";
				$selectionXml .= $value['optionid'];
				$selectionXml .= "</optionid>";
			}
			$selectionXml .= "</choice_selection>";
		}
	}
	
	$selectionXml .= "<total>".get_selection_total_price($selectionXml)."</total>";
	
	return $selectionXml;
}
//}}}

//{{{	- Get price for given selection XML

function get_selection_total_price($selectionXml)
{
	global $php_root_path;
	
	$registration_form_template = get_registration_form_template_xml();
	$xslt_pricing_string = file_get_contents($php_root_path.'/includes/xslt/reg_prices.xsl');
	$xml = ereg_replace("<orderform>","<orderform>".$selectionXml, $registration_form_template);
	
	$arguments = array(
		 '/_xml' => $xml,
		 '/_xslprices' => $xslt_pricing_string,
	);
	
	// Create an XSLT processor
	$xsltproc = xslt_create();
	
	// Perform the transformation
	$priceStr = xslt_process($xsltproc, 'arg:/_xml', 'arg:/_xslprices', NULL, $arguments); 
	
	// Detect errors
	if (!$priceStr) die('XSLT processing error: '.xslt_error($xsltproc));
	
	// Destroy the XSLT processor
	xslt_free($xsltproc);
	
	$prices = array_map("trim",explode(';',$priceStr));
	return array_sum($prices);
}
//}}}

//{{{	- Get registration statement

function get_registration_statement($selectionXml)
{
	global $php_root_path;
	
	$registration_form_template = get_registration_form_template_xml();
	$xslt_registration_statement = file_get_contents($php_root_path.'/includes/xslt/reg_statement.xsl');
	
	$xml = ereg_replace("<orderform>","<orderform>".get_currency_xml(), $registration_form_template);
	$xml = ereg_replace("<orderform>","<orderform>".$selectionXml, $xml);
	
	$arguments = array(
		 '/_xml' => $xml,
		 '/_xslout' => $xslt_registration_statement,
	);
	
	// Create an XSLT processor
	$xsltproc = xslt_create();
	
	// Perform the transformation
	$htmlout = xslt_process($xsltproc, 'arg:/_xml', 'arg:/_xslout', NULL, $arguments); 
	
	// Detect errors
	if (!$htmlout) die('XSLT processing error: '.xslt_error($xsltproc));
	
	// Destroy the XSLT processor
	xslt_free($xsltproc);
	
	return $htmlout;
}

//}}}

//{{{	- Get registration form

function get_registration_form()
{
	global $php_root_path;
	
	$registration_form_template = get_registration_form_template_xml();
	$xslt_registration_form = file_get_contents($php_root_path.'/includes/xslt/reg_form.xsl');
	
	$xml = ereg_replace("<orderform>","<orderform>".get_currency_xml(), $registration_form_template);
	
	$arguments = array(
		 '/_xml' => $xml,
		 '/_xslform' => $xslt_registration_form,
	);
	
	// Create an XSLT processor
	$xsltproc = xslt_create();
	
	// Perform the transformation
	$htmlform = xslt_process($xsltproc, 'arg:/_xml', 'arg:/_xslform', NULL, $arguments); 
	
	// Detect errors
	if (!$htmlform) die('XSLT processing error: '.xslt_error($xsltproc));
	
	// Destroy the XSLT processor
	xslt_free($xsltproc);
	
	return $htmlform;
}
//}}}

//{{{	- Store registration form

function store_selection_xml($registerID, $selectionXml, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "PaymentForm ";
	$sql .= "SET RegisterID=".$registerID;
	$sql .= ",	 Form=".db_quote($db,$selectionXml);
	$result = $db -> Execute($sql);
	
	if (!$result)
	{
		$err_message .= "Failed to insert form into database.";
	}
	return $db -> Insert_ID();
}

//}}}

//{{{	- Retrieve registration forms

function retrieve_selection_xml_for_registerid($registerID, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "PaymentForm ";
	$sql .= "WHERE RegisterID=".$registerID;
	$result = $db -> Execute($sql);
	
	if (!$result) return NULL;
	
	$forms = array();
	while ($form = $result -> FetchNextObj())
	{
		$forms[] = $form;
	}
	
	return $forms;
}

function retrieve_selection_xml($formID, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "PaymentForm ";
	$sql .= "WHERE FormID=".$formID;
	$result = $db -> Execute($sql);
	
	if (!$result) return NULL;
	
	return $result -> FetchNextObj();
}

//}}}

//{{{	- Check if registrant has paid

function has_paid_registration($registerID)
{
	if (get_paid_registration($registerID)==NULL) return false;
	return true;
}

//}}}

//{{{	- Get paid form

function get_paid_registration($registerID)
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "PaymentForm ";
	$sql .= "WHERE	RegisterID=".$registerID;
	$sql .= " AND	Paid=1";
	$result = $db -> Execute($sql);
	
	if (!$result) return NULL;
	
	return $result -> FetchNextObj();
}
//}}}

//{{{	- Change paid status

function set_payment_status($formID, $paid, $err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$paid = ($paid) ? 1 : 0;
	
	$sql =  "UPDATE " . $GLOBALS["DB_PREFIX"] . "PaymentForm ";
	$sql .= " SET	Paid=".$paid;
	$sql .= " WHERE	FormID=".$formID;
	$result = $db -> Execute($sql);
	
	if (!$result)
	{
		$err_message .= "Failed to change payment status in database.";
		return false;
	}
	return true;
}
//}}}

//{{{	- Get registration statistics

function get_paid_selections($err_message = "")
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql =  "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "PaymentForm ";
	$sql .= "WHERE	Paid=1";
	$result = $db -> Execute($sql);
	
	if (!$result) return "";
	$outXml = "";
	while ($form = $result -> FetchNextObj())
	{
		$outXml .= $form->Form;
	}
	return $outXml;
}

function get_payment_statistics($err_message = "")
{
	global $php_root_path;
	
	$registration_form_template = get_registration_form_template_xml();
	$xslt_registration_statistics = file_get_contents($php_root_path.'/includes/xslt/reg_stats.xsl');
	$selectionXml = get_paid_selections(&$err_message);
	
	$xml = ereg_replace("<orderform>","<orderform>".get_currency_xml(), $registration_form_template);
	$xml = ereg_replace("<orderform>","<orderform>".$selectionXml, $xml);
	
	$arguments = array(
		 '/_xml' => $xml,
		 '/_xsl' => $xslt_registration_statistics,
	);
	
	// Create an XSLT processor
	$xsltproc = xslt_create();
	
	// Perform the transformation
	$outStr = xslt_process($xsltproc, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments); 
	
	// Detect errors
	if (!$outStr) die('XSLT processing error: '.xslt_error($xsltproc));
	
	// Destroy the XSLT processor
	xslt_free($xsltproc);
	
	return $outStr;
}

//}}}

//}}}
?>
