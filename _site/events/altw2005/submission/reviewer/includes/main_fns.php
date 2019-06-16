<?php 

////////////// Start of Functions Shared by Reviewers //////////////////

function Generate_Preference_Radio_Input_Table( &$paperID , &$selectedPreferenceID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Preference";
    $result = $db -> Execute($sql);
	
	if(!$result)
	{
		$err_message .= " Could not get the Preference Name from the Preference Table in \"Generate_Radio_Preference_Input_Table\"<br>\n ";	// Exception has occurred
		return false ;
	}
	else
	{			
		$preferencetable = '<table width="100%" height="100%" border="0" cellpadding="5" cellspacing="0">' . "\n" ;
		$rows = $result -> RecordCount();			

		for ( $i = 0 ; $i < $rows ; )
		{
			$preferencetable .= "<tr> \n" ;
			for ( $j = 0 ; $j < 2 ; $i++ , $j++ )
			{
				$preferencetable .= "<td>" ;
				if ( $records = $result -> FetchRow() )
				{
					$preferencetable .= "<input type=\"radio\" name=\"selection$paperID\" value=\"$records[0]\" " ;
					if ( isset ( $selectedPreferenceID ) )
					{
						if ( $records[0] == $selectedPreferenceID )
						{
							$preferencetable .= "checked" ;
						}
					}
					else if ( $i == 1 )
					{
						$preferencetable .= "checked" ;
					}						
					$preferencetable .= "> \n" ;
					$preferencetable .= "$records[1]" ;
				}
				$preferencetable .= "</td> \n" ;
			}
			$preferencetable .= "</tr> \n" ;
		}

		$preferencetable .= "</table> \n" ;
		return $preferencetable ;			
	}			
}	

function verify_Registration_Exist( $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $reviewerSQL = " SELECT R.Organisation FROM " . $GLOBALS["DB_PREFIX"] . "Member M , " . $GLOBALS["DB_PREFIX"] . "Registration R ";
	$reviewerSQL .= " WHERE M.RegisterID = R.RegisterID AND M.MemberName = '".$_SESSION["valid_user"]."'";
	
	$reviewerResult = $db -> Execute($reviewerSQL);
	if ( !$reviewerResult )
	{
		$err_message .= " Could not get records from Member Table in \"verify_Registration_Exist\". <br>\n ";	// Exception has occurred
		return NULL ;
	}
	
    $row = $reviewerResult -> FetchNextObj();
	$reviewerInfo = $row -> Organisation;
	
	if ( empty ( $reviewerInfo ) )
		return false;
	else
		return true;
}

#Registration Function
function register_Reviewer($firstname,$middlename,$lastname,$org,$address1,$address2,$city,$state,$postalcode,
				$country, $email, $emailHome, $phoneno, $phonenoHome, $faxno , $err_message = "" )
				
// register new person with db
// return true or error message
{

	//global $_SESSION;
	
	// connect to db
	$db = adodb_connect( &$err_message );
		
  
	if (!$db)
	{
    	$err_message .= "Could not connect to database server - please try later. <br>\n";
		return false ;
	}

  	// if ok, put in db
	$firstname = addslashes($firstname);
  	$middlename = addslashes($middlename);
  	$lastname = addslashes($lastname);
  	$org = addslashes($org);
	$address1 = addslashes( $address1 );
 	$address2 = addslashes( $address2 );
  	$email = addslashes($email);
  	$emailHome = addslashes($emailHome);
	$postalcode = addslashes($postalcode) ;
	$country = addslashes ( $country ) ;
	$phoneno = addslashes ( $phoneno ) ;
	$phonenoHome = addslashes ( $phonenoHome ) ;
	$faxno = addslashes ( $faxno ) ;
	$city = addslashes ( $city ) ;
	$state = addslashes ( $state ) ;
  
  	//Insert into Registraion table first
  	$result = $db -> Execute("INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Registration(FirstName,MiddleName,LastName,Organisation,Address1,Address2,City,State,PostalCode,Country,Email,EmailHome,PhoneNumber,PhoneNumberHome,FaxNumber) VALUES 
                         ('$firstname','$middlename','$lastname','$org','$address1','$address2','$city','$state','$postalcode','$country','$email','$emailHome','$phoneno','$phonenoHome','$faxno')");
  	if (!$result)
	{
    	$err_message .= "Could not register you in database - please try again later. <br>\n";
		return false ;
	}	
  	else
  	{
		//Get the RegisterID from Registration table
		$registerID = $db -> Insert_ID();

		//Update the Member table
		$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Member";
		$updateSQL .= " SET RegisterID = '$registerID'";
		$updateSQL .= " WHERE MemberName = '".$_SESSION["valid_user"]."'";
		
		$updateResult = $db -> Execute($updateSQL);
		
		if(!$updateResult)
		{
			$err_message .= " Updating Reviewer information fail in \"register_Reviewer\". <br>\n ";		
			if ( delete_registration( $registerID , &$err_message ) )
			{
				$err_message .= " Call to \"delete_registration\" failed in \"register_Reviewer\". <br>\n ";
			}
			return false ;
		}
					
	  	return true ;		
	}
}

////////////// End Functions Shared by Reviewers //////////////////

////////////// Start Reviewers Bidding Functions //////////////////
/*
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
}*/

function get_result_of_paperid_selected( &$valid_user , &$dbprefix , $err_message = "" )
{
    //Establish connection with database
	$db = adodb_connect( &$err_message );
	
	//SQL Query to select all the papers
	$selectionSQL = " SELECT PP.PaperID" ;
	$selectionSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "Paper AS PP LEFT JOIN " . $GLOBALS["DB_PREFIX"] . "Selection AS S " ;
	$selectionSQL .= " USING (PaperID) " ;
	$selectionSQL .= " WHERE PP.Withdraw='false' AND S.MemberName=" . db_quote($db, $valid_user) ;
    $result = $db -> Execute( $selectionSQL ) ;
	
    if( !$result )
	{		
		$err_message .= " Could not query database in \"get_result_of_paperid_not_selected\". <br>\n" ;
		return NULL ;
	}	
	
	return $result ;
}

function get_result_of_paperid_not_selected ( $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	//global $_SESSION ;
	
	//SQL Query to select all the papers
	$selectionSQL = " SELECT PP.PaperID" ;
	$selectionSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "Paper AS PP LEFT JOIN " . $GLOBALS["DB_PREFIX"] . "Selection AS S " ;
	$selectionSQL .= " USING (PaperID) " ;
	$selectionSQL .= " WHERE PP.Withdraw='false' AND S.MemberName='".$_SESSION["valid_user"] ."'" ;

	$result = $db -> Execute( $selectionSQL ) ;
	if( !$result )
	{		
		$err_message .= " Could not query database in \"get_result_of_paperid_not_selected\". <br>\n" ;
		return NULL ;
	}	
	
	$paperid = "" ;
	if ( $id = $result -> FetchRow() )
	{
		$paperid = $id[0] ;

		while ( $id = $result -> FetchRow() )
		{
			$paperid .= " , " . $id[0] ;
		}
		$selectionSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper" ;
		$selectionSQL .= " WHERE PaperID NOT IN (" . $paperid . ")" ;
		$selectionSQL .= " AND Withdraw = 'false'";		
	}
	else
	{
		$selectionSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper" ;
		$selectionSQL .= " WHERE Withdraw = 'false'";
	}
		
	$result = $db -> Execute($selectionSQL);
	if( !$result )
	{		
		$err_message .= " Could not query database in \"get_result_of_paperid_not_selected\". <br>\n" ;
		return NULL ;
	}	
	
	return $result ;
}

//Function that update the selection table for reviewer
function select_paper($selection , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    //global $_SESSION;
	global $php_root_path ;
	global $showing ;  //pass on information to get back to right page
	global $sort ;
    
	while($choice = each($selection))
	{  
		$paperID = $choice[ "key" ];
		$preference = $choice[ "value" ];
		
		//Check whether the user has already been voted papers		
		if ( ( $result = check_selection($paperID, $_SESSION["valid_user"] , &$err_message ) ) !== NULL )
		{
			if ( $result )
			{				
				$sql = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Selection";
				$sql .= " VALUES('$paperID','".$_SESSION["valid_user"]."','$preference')";
                
				$result = $db -> Execute($sql);
                
				if(!$result)
				{
					$err_message .= "Could not update the selection information - please try again later. <br>\n" ;
					return false ;
				}
				
				//Collect the papers that are updated
				$update_str .= "PaperID #".$paperID."<br>";			
			}
			else
			{
				$notupdate_str .= "PaperID #".$paperID."<br>";
				//$num = strlen($not_str)-1;			
			}	
		}
		else
		{
			$err_message .= "Could not execute \"check_selection\" in \"select_paper\". <br>\n" ;
			return false ;
		}
	}//End of while loop
  
  //$not_str = substr($not_str,0,$num);
  //$notUpdates = array($not_str);
  
	if((strlen($update_str) != 0) && (strlen($notupdate_str) != 0))
	{
		return " Below are the papers that you have just bid on. <br><br>".$update_str."<br><br>".
			   " Below are the papers that you have already bid on.<br>Go to <a href='edit_paper_bids.php'>View Paper Bids</a> to change your preference instead.<br><br>".$notupdate_str;	
	}	
 	else if(strlen($update_str) != 0)
	{
  		$str = " Below are the papers that you have just bid on. <br><br>".$update_str;
$str .= "<br><br> What do you want to do now? <br><ul><li> <a href='bid_all_papers.php?sort=" .$sort. "&showing=" .$showing. "'" . "> Bid</a> another?</li> <br><li> <a href='edit_paper_bids.php'>View</a> your bids?</li> <br> <li><a href='$php_root_path/logout.php'>Logout</a> now?</li></ul>" ;
		
		return $str ;	
	}
	else if(strlen($notupdate_str) != 0)
	{
    	return " Sorry, you have already bid all the papers mentioned below.<br>Go to <a href='edit_paper_bids.php'>View Paper Bids</a> to change your preference instead.<br><br>".$notupdate_str;
	}  
}

function check_selection($paperID, $membername , $err_message = "" )
{	
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Selection";
	$sql .= " WHERE PaperID = '$paperID'";
	$sql .= " AND Membername = '".$membername."'";
	$result = $db -> Execute($sql);
	
	if ( !$result )
	{
		$err_message .= "Could not query the \"Selection\" table in database by \"check_selection\". <br>\n" ;
		return NULL ;		
	}
	
	$rows = $result -> RecordCount();
	
	if($rows > 0)
		return false;
	else
		return true;
}

//Function that update the selection table for reviewer
function update_selection($selection , $err_message = "" )
{
  	//Establish connection with database
	$db = adodb_connect( &$err_message );
    
    //global $_SESSION;
	global $php_root_path ;
	global $sort;		//Allows us to get back to the right page
	global $showing ;
	
	$update_str = "" ;
	$notupdate_str = "" ;
	$bool = false ;
	$some = false ;
	while($choice = each($selection))
	{  
		$paperID = $choice[ "key" ];
		$preference = $choice[ "value" ];
		
		$sql = " SELECT PreferenceName " ;
		$sql .= " FROM " . $GLOBALS["DB_PREFIX"] . "Preference " ;
		$sql .= " WHERE PreferenceID = '$preference' " ;
        
		$presult = $db -> Execute($sql);
        
		if(!$presult)
		{
			$err_message .= " Could not Query \"Preference\" table in database by \"update_selection\". <br>\n";
			return NULL ;
		}
		
        $prow = $presult -> FetchNextObj();
		$pname = $prow -> PreferenceName ;				
		
		$sql = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Selection";
		$sql .= " SET PreferenceID = ".db_quote($db, $preference);
		$sql .= " WHERE PaperID = ".db_quote($db, $paperID);
		$sql .= " AND MemberName = ".db_quote($db, $_SESSION["valid_user"]);
		
		$result = $db -> Execute($sql);
	  
		if(!$result)
		{
			$err_message .= " Could not Update \"Selection\" table in database by \"update_selection\". <br>\n";
			return NULL ;
		}
		
		if ( $db -> Affected_Rows() )
		{				
			$update_str .= "PaperID #".$paperID." : $pname <br>\n" ;
			$bool = true ;
		}
		else
		{
			$notupdate_str .= "PaperID #".$paperID." : $pname <br>\n" ;
			$some = true ;
		}				
	}

	if ( $bool )
	{
		$str = "Your new bids are updated successfully<br><br>\n\n" ;
		$str .= " Below are the papers that you have just updated. <br><br>\n\n".$update_str;
		if ( $some )
		{
			$str .= "<br>No changes are made to the paper below. <br><br>\n\n".$notupdate_str;
		}
		$str .= "<br><br> What do you want to do now? <br><ul><li> <a href='edit_paper_bids.php?sort=" .$sort. "&showing=" .$showing. "'" . ">Edit</a> your bids?</li> <br> <li><a href='$php_root_path/logout.php'>Logout</a> now?</li></ul>" ;			
		return $str ;	
	}
	
	return false ;	
}

////////////// End of Reviewers Bidding Functions //////////////////

//////// Start of Reviewer Review Functions ///////////////

function update_paper_status( $paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT PaperID FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE PaperID = $paperID" ;
	$countPaperResult = $db -> Execute( $sql ) ;
	
	if (!$countPaperResult)
	{
		$err_message .= "Could not query Paper Table in database. <br>\n";
		return false ;
	}
	
	if ( $countPaperResult -> RecordCount() == 0 )
	{
		$err_message .= " This Paper:#$paperID does not exist on Paper Table <br>\n " ;
		return false ;	
	}

	$sql = "SELECT PaperID FROM " . $GLOBALS["DB_PREFIX"] . "Review WHERE PaperID = $paperID" ;
	$countPaperResult = $db -> Execute( $sql ) ;
	
	if (!$countPaperResult)
	{
		$err_message .= "Could not query Paper Table in database. <br>\n";
		return false ;
	}
	
	if ( $countPaperResult -> RecordCount() == 0 )
	{
		$sql = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Paper SET " ;
		$sql .= " PaperStatusID=1 " ;	// Not reviewed
		$sql .= " WHERE PaperID=$paperID " ;
	
		$result = $db -> Execute($sql) ;
	
		if ( !$result )	// Roll back the changes
		{
			$err_message .= " Could not Update the Paper Status ID into the Paper Table <br>\n " ;
			return false ;	
		}
		else
		{	
			return true ;
		}			
	}

	$sql = " SELECT P.PaperID " ;
	$sql .= " FROM " . $GLOBALS["DB_PREFIX"] . "Paper AS P , " . $GLOBALS["DB_PREFIX"] . "Review AS R " ;
	$sql .= " WHERE R.PaperID = $paperID AND P.PaperID = R.PaperID AND
R.Objectives = 0 AND R.Validity = 0 AND R.Innovativeness = 0 AND
R.Presentation = 0 AND R.Bibliography = 0 AND R.AcceptPaper = 0 AND
R.AcceptPoster = 0 AND P.Withdraw='false'" ;	
//	echo "\$sql= " . $sql . "<br>\n" ;
	
	$countPaperResult = $db -> Execute( $sql ) ;
	
	if ( $countPaperResult -> RecordCount() == 0 )
	{
		$sql = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Paper SET " ;
		$sql .= " PaperStatusID=2 " ;	// Reviewed
		$sql .= " WHERE PaperID=$paperID " ;
	
		$result = $db -> Execute($sql) ;
	
		if ( !$result )	// Roll back the changes
		{
			$err_message .= " Could not Update the Paper Status ID into the Paper Table <br>\n " ;
			return false ;	
		}
		else
		{	
//			echo "<br>\n2 DONE!<br>\n" ;
			return true ;
		}			
	}
	else
	{
		$sql = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Paper SET " ;
		$sql .= " PaperStatusID=5 " ;	// Reviewing
		$sql .= " WHERE PaperID=$paperID " ;
	
		$result = $db -> Execute($sql) ;
	
		if ( !$result )	// Roll back the changes
		{
			$err_message .= " Could not Update the Paper Status ID into the Paper Table <br>\n " ;
			return false ;	
		}
		else
		{	
//			echo "<br>\n5 DONE!<br>\n" ;		
			return true ;
		}				
	}
}

function insert_review($paperID,$objectives,$validity,$innovativeness,$presentation,$bibliography,$acceptpaper,$acceptposter,$comments , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
    
    //global $_SESSION;
	
	global $php_root_path ;	
	
	$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Review";
	$updateSQL .= " SET Objectives = $objectives,";
	$updateSQL .= "Validity = $validity,";
	$updateSQL .= "Innovativeness=$innovativeness,";
	$updateSQL .= "Presentation=$presentation,";
	$updateSQL .= "Bibliography =$bibliography,";			
	$updateSQL .= "AcceptPaper =$acceptpaper,";			
	$updateSQL .= "AcceptPoster =$acceptposter,";			
	$updateSQL .= "Comments = '$comments'";
	$updateSQL .= " WHERE PaperID = $paperID";
	$updateSQL .= " AND MemberName = '".$_SESSION["valid_user"]."'";
	
	$resultUpdate1 = $db -> Execute($updateSQL);
	
	$avgSQL = "SELECT ";
	$avgSQL .= " AVG(Objectives) as Objectives,";
	$avgSQL .= " AVG(Validity) as Validity,";
	$avgSQL .= " AVG(Innovativeness) as Innovativeness,";
	$avgSQL .= " AVG(Presentation) as Presentation,";
	$avgSQL .= " AVG(Bibliography) as Bibliography,";
	$avgSQL .= " AVG(AcceptPaper) as AcceptPaper,";
	$avgSQL .= " AVG(AcceptPoster) as AcceptPoster";
	$avgSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "Review";
	$avgSQL .= " WHERE PaperID = $paperID"; 
	$avgSQL .= " AND Objectives <> 0";

	$resultAvg = $db -> Execute($avgSQL);
	
	// Calculate Evaluation out of 10
	// Thus 5.0 is a marginal paper (every setting set to marginal)
	// Weighting is 80% on OverallEvaluation and 20% on Other Factors Combined
	
	if ($resultAvg){
		$rowAvg = $resultAvg -> FetchNextObj();
		$overall = (($rowAvg -> AcceptPaper
			+ $rowAvg -> AcceptPoster)/2- 1)/4; // normalised range 0-1
		$other = (($rowAvg -> Objectives
			+ $rowAvg -> Validity
			+ $rowAvg -> Innovativeness
			+ $rowAvg -> Presentation
			+ $rowAvg -> Bibliography)/5 - 1)/4; // normalised range 0-1
		$evaluation = 10*(0.8*$overall + 0.2*$other); // weight and make evaluation out of 10
	}
	// echo "eval = $evaluation";
	
	$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Paper";
	$updateSQL .= " SET OverallRating = $evaluation";
	$updateSQL .= " WHERE PaperID = $paperID";
	
	if ($evaluation > 0)	$resultUpdate2 = $db -> Execute($updateSQL); // don't execute if average fails
	
	if(!$resultUpdate1||!resultAvg||!resultUpdate2)
	{
		$err_message .= " Could not Update the \"Review\" or \"Paper\" table in the database by \"insert_review\". <br>\n" ;
		return false ;
	}
	else
	{
		$str = " The Review on Paper #".$paperID." is updated successfully.";
		$str .= "<br><br> What do you want to do now? <br><ul><li> <a href='view_assigned_papers.php'>Review</a> another?</li> <br> <li><a href='$php_root_path/logout.php'>Logout</a> now?</li></ul>" ;
		return $str ;
	}
}

function get_review($paperID , $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    //global $_SESSION;
	
	//Retrieve the information from Review Table
	$reviewSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Review";
	$reviewSQL .= " WHERE PaperID='$paperID'";
	$reviewSQL .= " AND Membername='".$_SESSION["valid_user"]."'";
	$reviewResult = $db -> Execute($reviewSQL);
	
	if ( !$reviewResult )
	{
		$err_message .= " Could not query \"Review\" table of database in \"get_review\" <br>\n" ;
		return false ;
	}
	
	$reviewInfo = $reviewResult -> FetchNextObj();
	
	return $reviewInfo;
}

/////////// End of Reviewer Review Functions ///////////////

?>
