<?php 

define("MAX_EMAILS",10);

function verify_ConferencePhase_Set( $err_message = "", $db = NULL)
{
	if (!$db)
        $db = adodb_connect();
	
	$adminSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "ConferencePhase WHERE StartDate = 0 OR EndDate = 0";
	$adminResult = $db -> Execute($adminSQL);
	
    if ( !$adminResult )
	{
		$err_message .= " Could not get records from Conference Table in \"verify_Conference_Exist\". <br>\n ";	// Exception has occurred
		return NULL ;
	}
	
	return $adminResult -> RecordCount() ;
}

function verify_Conference_Exist( $err_message = "", $db = NULL)
{
    if (!$db)
        $db = adodb_connect();
	
    $adminSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Conference";
	$adminResult = $db -> Execute($adminSQL);
	
    if ( !$adminResult )
	{
		$err_message .= " Could not get records from Conference Table in \"verify_Conference_Exist\". <br>\n ";	// Exception has occurred
		return NULL ;
	}
	
	return $adminResult -> RecordCount() ;
}

function getAllPhases( $err_message = "", $db = NULL )
{
	if (!$db)
        $db = adodb_connect();
	
    $phasesSQL = "SELECT PhaseID,PhaseName, ".dbdf_out($db, "StartDate").", ";
	$phasesSQL .= dbdf_out($db, "EndDate").", Status";
    $phasesSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
	$phasesResult = $db -> Execute($phasesSQL);
		
	if (!$phasesResult)
	{		
		$err_message .= "Could not retrieve the phase information - please try again later.";
		return NULL ;
	}	
		
	if ($phasesResult -> RecordCount()  == 0)
	{
		$err_message .= "There are no phases setup for the conference - please setup the conference phases first.";
		return false ;
	}
		
	return $phasesResult ;
}

// Unused function
/*
function get_all_reviews($paperID)
{
	//Establish database connection
	$db = adodb_connect();
    
	if (!$db)
		return "Could not connect to database server - please try later.";
    
	//Retrieve the information from Review Table
	$reviewSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Review";
	$reviewSQL .= " WHERE PaperID='$paperID'";
	$reviewResult = $db -> Execute($reviewSQL);
	$reviewInfo = $reviewResult -> FetchNextObj();
	
	return $reviewInfo;
}
*/


function getMemberEmail($MemberName){

	
	//Establish database connection
	$db = adodb_connect();
  
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$sql  = "SELECT R.Email FROM " . $GLOBALS["DB_PREFIX"] . "Registration R," . $GLOBALS["DB_PREFIX"] . "Member M";
	$sql .= " WHERE M.RegisterID = R.RegisterID";
	$sql .= " AND M.MemberName = '".$MemberName."'";
	$result = $db -> Execute($sql);
	
    if(!$result)
		return "Could not retrieve the Member email - pls try again later";
		
	
	$emailInfo = $result -> FetchNextObj();
	
	return $emailInfo -> Email;


}

// Unused function
/*
function getReviewedPapers(){
	
	//Establish database connection
	$db = adodb_connect();
  
	if (!$db)
		return "Could not connect to database server - please try later.";

	$papersSQL = "SELECT *";
	$papersSQL .= " From " . $GLOBALS["DB_PREFIX"] . "Paper P," . $GLOBALS["DB_PREFIX"] . "PaperStatus PS";
	$papersSQL .= " WHERE P.PaperStatusID = PS.PaperStatusID";
	$papersSQL .= " AND PaperStatusName = 'Reviewed'";
	
	$papersResult = $db -> Execute($papersSQL);
	
	for ($i=0; $i < mysql_num_rows($papersResult) ; $i++){
		$papersArray[$i] = mysql_fetch_object($papersResult);
	}
	
	return $papersArray;
}
*/

//Function that will insert categories into the table
function insert_category($arrCategoryName)
{	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	for($i = 0; $i < count($arrCategoryName); $i++)
    {
		$insertSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Category";
		$insertSQL .= "(CategoryName)";
		$insertSQL .= " VALUES(".db_quote($db,$arrCategoryName[$i]).")";
		$insertResult = $db -> Execute($insertSQL);
		
		if(!$insertResult )
			return "Could not insert the Category information";
	}
	return "The Category information has setup successfully";
}

//Function that will update the conference phases	
function updateConferencePhase($arrPhaseName,$arrStartDate,$arrEndDate)
{
	$numPhases = count($arrPhaseName);
    
	//Establish connection with database
	$conn = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	for($i = 0; $i < $numPhases ; $i++){
	
		//Make the sql to insert value
		$insertPhaseSQL = "INSERT INTO ".$GLOBALS["DB_PREFIX"]."ConferencePhase ";
		$insertPhaseSQL .= "(PhaseName,StartDate,EndDate) VALUES(";
        $insertPhaseSQL .= db_quote($db,$arrPhaseName[$i]).", ";
        $insertPhaseSQL .= db_quote($db,$arrStartDate[$i]).", ";
        $insertPhaseSQL .= db_quote($db,$arrEndDate[$i]).")";
		$insertPhaseResult = $db -> Execute($insertPhaseSQL);
		
		if(!$insertPhaseResult)
			return "Could not insert the phases information - please try again later.";
	
	}
		
	return "The Phases information is updated successfully.";
}

function activate_Phase($phaseID){
		
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$activateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
	$activateSQL .= " SET Status = 'true'";
	$activateSQL .= " WHERE PhaseID = ".db_quote($db,$phaseID);
	$activateResult = $db -> Execute($activateSQL);
	
	if(!$activateResult)
		return false;
	else{
		switch ( $phaseID )
		{
			case 1:
				$_SESSION["phase"] = new phase1();
				break ;
			case 2:
				$_SESSION["phase"] = new phase2();
				break ;
			case 3:
				$_SESSION["phase"] = new phase3();
				break ;
			case 4:
				$_SESSION["phase"] = new phase4();
				break ;
			default :
			{
				return " Unknown New Phase of Conference in database. <br>\n" ;
				break ;
			}		
		return true;
		}
	}
}

function deactivate_Phase($phaseID)
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$deactivateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
	$deactivateSQL .= " SET Status = 'false'";
	$deactivateSQL .= " WHERE PhaseID = ".db_quote($db,$phaseID);
	$deactivateResult = $db -> Execute($deactivateSQL);
	
	if(!$deactivateResult)
		return false;
	else
		return true;
}


function updateUserPrivilege($arrPrivilegeName)
{
	$numPrivileges = count($arrPrivilegeName);
    
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	for($i = 0; $i < $numPrivileges ; $i++)
    {
		//Make the sql to insert value
		$insertPrivilegeSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "PrivilegeType";
		$insertPrivilegeSQL .= " (PrivilegeTypeName)";
		$insertPrivilegeSQL .= " VALUES(".db_quote($db,$arrPrivilegeName[$i]).")";
		$insertPrivilegeResult = $db -> Execute($insertPrivilegeSQL);
		
		if(!$insertPrivilegeResult)
			return "Could not insert the privileges information - please try again later.";
	
	}
	return "The Privilege information is updated successfully.";
}

function get_Privilege_TypeID($privilegeName)
{
    //Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	$privilegeSQL = "SELECT PrivilegeTypeID FROM " . $GLOBALS["DB_PREFIX"] . "PrivilegeType";
	$privilegeSQL .= " WHERE PrivilegeTypeName = ".db_quote($db,$privilegeName);
	$privilegeResult = $db -> Execute($privilegeSQL);
	if(!$privilegeResult)
		return "Could not retrieve the privilege type - please try again later.";
	
	$privilegeInfo = $privilegeResult -> FetchNextObj();
	
	return $privilegeInfo -> PrivilegeTypeID;
}

function setup_new_account($arrAccountInfo,$password)
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	//Insert the email of the reviewer into register table
	$emailSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"]."Registration(FirstName,MiddleName,LastName,Email)";
	$emailSQL .= " VALUES(";
    $emailSQL .= db_quote($db, $arrAccountInfo["firstname"]).", ";
    $emailSQL .= db_quote($db, $arrAccountInfo["middlename"]).", ";
    $emailSQL .= db_quote($db, $arrAccountInfo["lastname"]).", ";
    $emailSQL .= db_quote($db, $arrAccountInfo["email"]).")";
	$emailResult = $db -> Execute($emailSQL);
	
	if(!$emailResult)
		return "Could not insert email information - try again";
	
	//Get the newly inserted RegisterID
	$registerID = $db -> Insert_ID();
		
	//Get the privilege ID of reviewer
	$privilegeID = get_Privilege_TypeID($arrAccountInfo["accountType"]);	
	
	$insertSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Member";
	$insertSQL .= " (MemberName,Password,PrivilegeTypeID,RegisterID)";
	$insertSQL .= " VALUES(";
    $insertSQL .= db_quote($db, $arrAccountInfo["loginname"]).", ";
    $insertSQL .= db_quote($db, sha1($password)).", "; // previously used MySQL's PASSWORD()
    $insertSQL .= db_quote($db, $privilegeID).", ";
    $insertSQL .= db_quote($db, $registerID).")";
    $insertResult = $db -> Execute($insertSQL);
	
	if(!$insertResult)
		return "Could not update the reviewer information - please try again later";

	return true;

}

function delete_Newly_Setup_Account($userName)
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	//Get the user information
	$userInfo = getMemberInfo($userName);
	
	//Delete the registration
	$result = delete_registration($userInfo -> RegisterID);
	
	if($result) {
		//Then delete the account
		$deleteSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"]."Member";
		$deleteSQL .= " WHERE MemberName = ".db_quote($db,$userName);
		$deleteResult = $db -> Execute($deleteSQL);
		
		if($deleteResult)
			return true;
		else
			return false;
	}
	else
		echo "Error";	
}

function generateReviewerInputTable($paperID)
{
	//global $arrReviewers;
	$arrReviewers = get_Reviewers_Of_Paper($paperID);
	global $arrEditReviewers;
    
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		

	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"] . "PrivilegeType P";
	$sql .=" WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
	$sql .=" AND PrivilegeTypeName = 'Reviewer' ORDER BY MemberName" ;
	$result = $db -> Execute($sql);				
		
	if(!$result)
		return " Could not retrieve the Reviewers information - please try again later.";	// Exception has occurred
			
	$reviewerTable = "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\"> \n" ;
	$rows = $result -> RecordCount() ;			
			
	for ( $i = 0 ; $i < $rows ; )
	{
		$reviewerTable .= "<tr> \n" ;
		for ( $j = 0 ; $j < 4 ; $i++ , $j++ )
		{
			// $reviewerTable .= "<td" ;
			if ( $records = $result -> FetchNextObj() )
            {
				// Change background colour based on preference
				$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Selection " ;
				$sql .= "WHERE MemberName = " . db_quote($db,$records -> MemberName);
				$sql .= "AND PaperID = " . db_quote($db,$paperID);				
				$prefResult = $db -> Execute( $sql );
				if(!$prefResult)
					return " Could not retrieve the Reviewer's preferences - please try again later.";	// Exception has occurred				
				$prefInfo = $prefResult -> FetchNextObj() ;
				$pref = $prefInfo -> PreferenceID ;
				switch($pref){
				case 1: 
					$reviewerTable .= "<td bgcolor=#AAFFAA> " ;
					break ;
				case 2: 
					$reviewerTable .= "<td bgcolor=#E0FFE0> " ;
					break ;
				case 3: 
					$reviewerTable .= "<td bgcolor=#FFE0E0> " ;
					break ;
				case 4: 
					$reviewerTable .= "<td bgcolor=#FFBBBB> " ;
					break ;
				default: 
					$reviewerTable .= "<td bgcolor=#EEEEEE> " ; // No pref
					break ;
				}
				$reviewerTable .= "<input type=\"checkbox\" name=\"paper".$paperID."[]\" value=\"".$records -> MemberName."\" " ;
				
				//The back button is pressed and come back
				if(count($arrReviewers) > 0){			
					for($k=0;$k < count($arrReviewers);$k++){
							if ($records -> MemberName == $arrReviewers[$k])
								$reviewerTable .=  " checked ";
					}//for loop
				}//end of if
				else if(count($arrEditReviewers) > 0){
					for($m=0;$m < count($arrEditReviewers);$m++){
						if($records -> MemberName == $arrEditReviewers[$m])
							$reviewerTable .=  " checked ";
					}
				}
				// Get number of paper assignments as measure of reviewer load
				$sql = "SELECT COUNT(*) AS assTotal FROM " . $GLOBALS["DB_PREFIX"] . "Review " ;
				$sql .= "WHERE MemberName = ".db_quote($db,$records -> MemberName) ;
				
				$assResult = $db -> Execute($sql);
				if(!$assResult)
					return " Could not retrieve the Reviewers assignments - please try again later.";	// Exception has occurred

				$assInfo = $assResult -> FetchNextObj() ;
				$numAssign = $assInfo -> assTotal ;
				$reviewerTable .= " > \n" ;
				$reviewerTable .= "<a href=\"#\" onClick=\"JavaScript:window.open('show_reviewer_preferences.php?name=".$records -> MemberName."',null,'height=400,width=600,left=150,top=250,status=yes,toolbar=no,menubar=no,scrollbars=yes,location=no')\">".$records -> MemberName." ($numAssign) </a>" ;
				} else {  // no record fetched
				$reviewerTable .= "<td bgcolor=#EEEEEE>&nbsp;" ;
				}
					$reviewerTable .= "</td> \n" ;
		} 
		$reviewerTable .= "</tr> \n" ;
	}

	$reviewerTable .= "</table> \n" ;
	return $reviewerTable ;						
}	

function get_Number_Of_Reviews($paperID){
	
		//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$countSQL = "SELECT COUNT(*) AS Number FROM " . $GLOBALS["DB_PREFIX"] . "Review";
	$countSQL .= " WHERE PaperID = $paperID" ;
	$countSQL .= " AND Objectives <> 0";
	
	$countResult = $db -> Execute($countSQL);
	if(!$countResult)
		return "Count not retrieve the number of reviews - please try again later";
	
	$countInfo = $countResult -> FetchNextObj();
	
	return $countInfo -> Number;

}

function get_Num_of_Reviewers(){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
		
	$numReviewerSQL = "SELECT COUNT(*) AS NumReviewer FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"] . "PrivilegeType P";
	$numReviewerSQL .= " WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
	$numReviewerSQL .= " AND PrivilegeTypeName = 'Reviewer'";
	$numReviewerResult = $db -> Execute($numReviewerSQL);
	
	if(!$numReviewerResult)
		return "Count not retrive the number of reviewers - please try again later";
	
	$numReviewerInfo = $numReviewerResult -> FetchNextObj();
	
	return $numReviewerInfo -> NumReviewer;

}

function get_Paperstatus_ID($paperStatus){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$paperStatusSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "PaperStatus";
	$paperStatusSQL .= " WHERE PaperStatusName = ".db_quote($db,$paperStatus);
	$paperStatusResult = $db -> Execute($paperStatusSQL);
	$paperStatusInfo = $paperStatusResult -> FetchNextObj();
	
	return $paperStatusInfo -> PaperStatusID;

}

function assign_paper($paperID,$arrReviewers){
	if ($arrReviewers == "") //Check for no data
		return "No reviewers have been specified";
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	for($i=0;$i< count($arrReviewers);$i++){
				
		$insertReviewSQL  = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Review";
		$insertReviewSQL .= "(PaperID,MemberName)";
		$insertReviewSQL .= " VALUES(".db_quote($db,$paperID).", ";
        $insertReviewSQL .= db_quote($db,$arrReviewers[$i]).")";
		$insertReviewResult = $db -> Execute($insertReviewSQL);
		if(!$insertReviewResult)
			return "Could not assign the paper to Reviewer - please try again later";
			
	}//End of for loop
		
	if(!update_PaperStatus($paperID,'Reviewing'))
		return "Could not assign the paper to Reviewer - please try again later";
	
	return true;
		
}

//Function that will edit the reviewers of the papers
function edit_Assigned_Reviewers($paperID,$arrChangeReviewers){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";

	if ($arrChangeReviewers == "") //Check for no data
	{
		$arrRemoveReviewers = get_Reviewers_Of_Paper($paperID);
		if(count($arrRemoveReviewers) > 0){
		//Remove the unwanted reviewers
			foreach($arrRemoveReviewers as $reviewername){
				$deleteSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Review";
				$deleteSQL .= " WHERE PaperID = ".$paperID;
				$deleteSQL .= " AND MemberName = ".db_quote($db,$reviewername);
				$deleteResult = $db -> Execute($deleteSQL);
			
				if(!deleteResult)
					return "Could not delete the previous reviewers - please try again";
			}//end of for
			if(!update_PaperStatus($paperID,'Not Reviewed'))
			return "Could not reset paper status - please try again later";
		}//end of inner if	
		return true; //successful resetting of reviewers
	} //end of outer if
			
	//	return "No reviewers have been specified";
	

	//Get the current reviews	
	$arrCurrentReviewers = get_Reviewers_Of_Paper($paperID);		

	//Get the arrary of new reviewers to insert
	$arrNewReviewers = array_diff($arrChangeReviewers,$arrCurrentReviewers);
		
	if(count($arrNewReviewers) > 0 ){
		//Insert into Review table
		foreach($arrNewReviewers as $reviewername){
			$insertSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Review";
			$insertSQL .= "(PaperID,MemberName)";
			$insertSQL .= " VALUES(".db_quote($db,$paperID).", ";
            $insertSQL .= db_quote($db,$reviewername).")";
			$insertResult = $db -> Execute($insertSQL);
				
			if(!$insertResult)
				return "Could not insert new Reviewers into database - please try again.";
		}//end of for
	}//end of if
		
	//Finish inserting, get the reviewers to remove
	$arrRemoveReviewers = array_diff($arrCurrentReviewers,$arrChangeReviewers);
		
	if(count($arrRemoveReviewers) > 0){
		//Remove the unwanted reviewers
		foreach($arrRemoveReviewers as $reviewername){
			$deleteSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Review";
			$deleteSQL .= " WHERE PaperID = ".$paperID;
			$deleteSQL .= " AND MemberName = ".db_quote($db,$reviewername);
			$deleteResult = $db -> Execute($deleteSQL);
			
			if(!deleteResult)
				return "Could not delete the previous reviewers - please try again";
		}//end of for
	}//end of if	e
	
	return true;
}

function update_PaperStatus($paperID,$status){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return false;

	$paperStatusID = get_Paperstatus_ID($status);
	
	//Update the status of paper to reviewing state
	$updatePaperStatusSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Paper";
	$updatePaperStatusSQL .= " SET PaperStatusID = $paperStatusID";
	$updatePaperStatusSQL .= " WHERE PaperID =".$paperID;
	$updatePaperStatusResult = $db -> Execute($updatePaperStatusSQL);
	
	if(!$updatePaperStatusResult)
		return false;
		
	return true;
}

// Gets Current Phase from either session variable or database as appropriate. Optional
// $database flag forces query to database and ignores session variable.
function getCurrentPhase($database=false){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	$currentPhaseSQL = "SELECT PhaseID,PhaseName,".dbdf_out($db,"StartDate");
    $currentPhaseSQL .= ",".dbdf_out($db,"EndDate").",Status";
	$currentPhaseSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
	if (isset($_SESSION["real_user"]) && !$database) //check if su'ed to administrator
	{
		$currentPhaseSQL .= " WHERE PhaseID = ".$_SESSION["phase"]->phaseID; //su'ed, use session phase
	}else{
		$currentPhaseSQL .= " WHERE Status = 'true'"; //normal login, use default database phase
	}
	
	$currentPhaseResult = $db -> Execute($currentPhaseSQL);
	
	if(!$currentPhaseResult)
		return "Could not retrieve the current phase - please try later";
	
	if($currentPhaseResult -> RecordCount() == 0)
		return false;	
	else
	{
		$currentPhaseInfo = $currentPhaseResult -> FetchNextObj();	
		return $currentPhaseInfo;
	}

}

function checkPhase($phaseName){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	$phaseSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
	$phaseSQL .= " WHERE PhaseName = ".db_quote($db,$phaseName);
	$phaseResult = $db -> Execute($phaseSQL);
	
	if(!$phaseResult)
		return "Could not retrieve the phase information from the database - please try again later.";
	
	if($phaseResult -> RecordCount() == 0)
		return "There is not phase with the name provided inside the database - please try again later.";
	
	$phaseInfo = $phaseResult -> FetchNextObj();
	
	if($phaseInfo -> Status == "true")
		return true; //It is the current running phase
	else
		return false; //This is not the current running phase
	
}

//Function that format the conference phase status
function get_Phase_Status($status){

if($status == "true")
	return "Active";
else
	return "Not Active";
}

function update_phases($arrPhaseID,$arrStartDate,$arrEndDate){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	for($i=0;$i < count($arrPhaseID);$i++){
	
		//list($day,$month,$year) = split('[/.-]',$arrStartDate[$i]);
		//$startDate = "$year-$month-$day";
		$startDate = $arrStartDate[$i];
		
		//list($day,$month,$year) = split('[/.-]',$arrEndDate[$i]);
		//$endDate = "$year-$month-$day";
		$endDate = $arrEndDate[$i];
		
		//SQL to update the phase informaiton
		$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
		$updateSQL .= " SET StartDate = ".db_quote($db,$startDate).",";
		$updateSQL .= "EndDate = ".db_quote($db,$endDate);
		$updateSQL .= " WHERE PhaseID = ".db_quote($db,$arrPhaseID[$i]);
		
		$updateResult = $db -> Execute($updateSQL);
		
		if(!$updateResult)
			return "Could not update the phase information - please try again later.";
	}
	
	return true;

}

//Function that take the membername and return the Priviledge Name of the member
function get_Privilege_Name($memberName){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$privilegeSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"] . "PrivilegeType P";
	$privilegeSQL .= " WHERE MemberName =".db_quote($db,$memberName);
	$privilegeSQL .= " AND M.PrivilegeTypeID = P.PrivilegeTypeID";
	$privilegeResult = $db -> Execute($privilegeSQL);
	
	if(!$privilegeResult)
		return "Could not retrieve the privilege type - please try later.";
		
	$privilegeInfo = $privilegeResult -> FetchNextObj();
	
	return $privilegeInfo -> PrivilegeTypeName;

}

//Function that update the member name for the name tag
function update_Member_Name($registerID,$firstname,$middlename,$lastname,$org){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Registration";
	$updateSQL .= " SET FirstName = ".db_quote($db,$firstname).",";
	$updateSQL .= "MiddleName = ".db_quote($db,$middlename).",";
	$updateSQL .= "LastName = ".db_quote($db,$lastname).",";
	$updateSQL .= "Organisation = ".db_quote($db,$org);	
	$updateSQL .= " WHERE RegisterID = ".$registerID;
	
	$updateResult = $db -> Execute($updateSQL);
	
	if(!$updateResult)
		return "Could not update the member name - please try later";
		
	return true;

}

//Function that insert the conference informaiton to the conference table
function setup_conference($arrConferenceInfo,$arrLogoInfo)
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	
	define("MAX_WIDTH",$settingInfo -> MaxLogoWidth);
	define("MAX_HEIGHT",$settingInfo -> MaxLogoHeight);				
	
	//Extract the conference array
	extract($arrConferenceInfo);
	
	//There is logo file to insert
	if(!empty($arrLogoInfo["logofile"]["name"])){
				
		//Get the width and height of image
		$size = getimagesize($arrLogoInfo["logofile"]["tmp_name"]);
		$width = $size[0];
		$height = $size[1];
		
		
		//Check whether the file need to resize
		if(($width > MAX_WIDTH) || ($height > MAX_HEIGHT)){
			$result = resize_Image($arrLogoInfo);
			if(!$result)
				return "Could not resize the file";
		
			$tmpDir = get_cfg_var("upload_tmp_dir");
			$realname = $arrLogoInfo["logofile"]["name"];		
			
			//Read the file
			$file = addslashes(fread(fopen("$tmpDir/$realname","r"),$arrLogoInfo["logofile"]["size"])) ;		
			//unlink("$tmpDir/$realname");			
		}else
			$file = addslashes(fread(fopen($arrLogoInfo["logofile"]["tmp_name"],"r"),$arrLogoInfo["logofile"]["size"])) ;

		$filename = $arrLogoInfo["logofile"]["name"];
		$filesize = $arrLogoInfo["logofile"]["size"];
		$filetype = $arrLogoInfo["logofile"]["type"];				
		
	$insertSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Conference";
	$insertSQL .= "(ConferenceName,ConferenceCodeName,ConferenceStartDate,ConferenceEndDate,ConferenceLocation,ConferenceHostName,ConferenceContact,LogoFile,FileName,FileSize,FileType)";
	$insertSQL .= " VALUES(";
    $insertSQL .= db_quote($db,$arrConferenceInfo["name"]).",";
    $insertSQL .= db_quote($db,$arrConferenceInfo["codename"]).",";
    $insertSQL .= db_quote($db,$arrConferenceInfo["ConferenceStartDate"]).",";
    $insertSQL .= db_quote($db,$arrConferenceInfo["ConferenceEndDate"]).",";
    $insertSQL .= db_quote($db,$arrConferenceInfo["location"]).",";
    $insertSQL .= db_quote($db,$arrConferenceInfo["hostname"]).",";
    $insertSQL .= db_quote($db,$arrConferenceInfo["email"]).",";
    $insertSQL .= db_quote($db,$file).",";
    $insertSQL .= db_quote($db,$filename).",";
    $insertSQL .= db_quote($db,$filesize).",";
    $insertSQL .= db_quote($db,$filetype).")";		
		
	}else{
        //No logo file to insert
        $insertSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Conference";
        $insertSQL .= "(ConferenceName,ConferenceCodeName,ConferenceStartDate,ConferenceEndDate,ConferenceLocation,ConferenceHostName,ConferenceContact)";
        $insertSQL .= " VALUES(";
        $insertSQL .= db_quote($db,$arrConferenceInfo["name"]).",";
        $insertSQL .= db_quote($db,$arrConferenceInfo["codename"]).",";
        $insertSQL .= db_quote($db,$arrConferenceInfo["ConferenceStartDate"]).",";
        $insertSQL .= db_quote($db,$arrConferenceInfo["ConferenceEndDate"]).",";
        $insertSQL .= db_quote($db,$arrConferenceInfo["location"]).",";
        $insertSQL .= db_quote($db,$arrConferenceInfo["hostname"]).",";
        $insertSQL .= db_quote($db,$arrConferenceInfo["email"]).")";
	}
	
	//Execute the query
	$insertResult = $db -> Execute($insertSQL);
	
	if(!$insertResult)
		return "Could not insert Conference Information - please try again later";
		
	return true;


}

//Function that insert the conference informaiton to the conference table
function edit_conference_info($arrConferenceInfo,$arrLogoInfo){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	//Retrieve the setting information
	$settingInfo = get_Conference_Settings();
	
	define("MAX_WIDTH",$settingInfo -> MaxLogoWidth);
	define("MAX_HEIGHT",$settingInfo -> MaxLogoHeight);		
	
	//$file = addslashes(fread(fopen($logofile,"r"),filesize($logofile))) ;
		
	$editSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Conference"
	 		. " SET ConferenceName=".db_quote($db,$arrConferenceInfo["name"])
			.",ConferenceCodeName=".db_quote($db,$arrConferenceInfo["codename"])
			.",ConferenceStartDate=".db_quote($db,$arrConferenceInfo["ConferenceStartDate"])
			.",ConferenceEndDate=".db_quote($db,$arrConferenceInfo["ConferenceEndDate"])
			.",ConferenceLocation=".db_quote($db,$arrConferenceInfo["location"])
			.",ConferenceHostName=".db_quote($db,$arrConferenceInfo["hostname"])
			.",ConferenceContact=".db_quote($db,$arrConferenceInfo["email"]);

	if(!empty($arrLogoInfo["logofile"]["name"])){
	
		$size = getimagesize($arrLogoInfo["logofile"]["tmp_name"]);
		$width = $size[0];
		$height = $size[1];
		
		//return "Width: ".$width."<br>Height: ".$height."<br>Max Width: ".MAX_WIDTH."<br>Height: ".MAX_HEIGHT;
		
		//Check whether the file need to resize
		if(($width > MAX_WIDTH) || ($height > MAX_HEIGHT)){
			$result = resize_Image($arrLogoInfo);
			if(!$result)
				return "Could not resize the file";
		
			$tmpDir = get_cfg_var("upload_tmp_dir");
			$realname = $arrLogoInfo["logofile"]["name"];		
			
			//Read the file
			$file = addslashes(fread(fopen("$tmpDir/$realname","r"),$arrLogoInfo["logofile"]["size"])) ;		
			//unlink("$tmpDir/$realname");			
		}else
			$file = addslashes(fread(fopen($arrLogoInfo["logofile"]["tmp_name"],"r"),$arrLogoInfo["logofile"]["size"])) ;		
		
		$editSQL .= ",LogoFile = ".db_quote($db,$file).",".
					"FileName = ".db_quote($db,$arrLogoInfo["logofile"]["name"]).",".
					"FileSize = ".db_quote($db,$arrLogoInfo["logofile"]["size"]).",".
					"FileType = ".db_quote($db,$arrLogoInfo["logofile"]["type"]);										
		
	}
	
	$editSQL .= " WHERE ConferenceID = ".db_quote($db,$arrConferenceInfo["conferenceID"]);
	
	//return $editSQL;
	$editResult = $db -> Execute($editSQL);
	
	if(!$editResult)
		return "Could not update Conference Information - please try again later";
	
	return true;

}

//Function that will resize the image
function resize_Image($arrLogoInfo){

	//Get the image size
	$imgsize = getimagesize($arrLogoInfo["logofile"]["tmp_name"]);
	$width = $imgsize[0];
	$height = $imgsize[1];
	
	//Get the ratio of the images
  	$x_ratio = MAX_WIDTH / $width;
	$y_ratio = MAX_HEIGHT / $height;
  
  	//Evaluate the new width and height
  	if(($width <= MAX_WIDTH) && ($height <= MAX_HEIGHT)){
  		$newWidth = $width;
		$newHeight = $height;
	}
	else if(($x_ratio * $height) < MAX_HEIGHT){
		$newHeight = ceil($x_ratio * $height);
		$newWidth = MAX_WIDTH;
	}
	else{
		$newWidth = ceil($y_ratio * $width);
		$newHeight = MAX_HEIGHT;
	}
	
	$tmpDir = get_cfg_var("upload_tmp_dir");
	$realname = $arrLogoInfo["logofile"]["name"];	
	
	switch($arrLogoInfo["logofile"]["type"]){
		//Gif file come here
		case "image/gif":
			$src = imagecreatefromgif($arrLogoInfo["logofile"]["tmp_name"]);
			
			if(function_exists("imagecreatetruecolor"))
				$dst = imagecreatetruecolor($newWidth,$newHeight);
			else
				$dst = imagecreate($newWidth,$newHeight);
			
			if(function_exists("imagecopyresampled"))
				imagecopyresampled($dst,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			else			
				imagecopyresized($dst,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			
			imagegif($dst,$arrLogoInfo["logofile"]["name"]);
			break;
		case "image/pjpeg":
			//JPG file are here
			$src = imagecreatefromjpeg($arrLogoInfo["logofile"]["tmp_name"]);
			
			if(function_exists("imagecreatetruecolor"))
				$dst = imagecreatetruecolor($newWidth,$newHeight);
			else
				$dst = imagecreate($newWidth,$newHeight);
			
			if(function_exists("imagecopyresampled"))
				imagecopyresampled($dst,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			else			
				imagecopyresized($dst,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			
			imagejpeg($dst,"$tmpDir/$realname",90);
			break;
		case "image/x-png":
			//PNG Welcome here
			$src = imagecreatefrompng($arrLogoInfo["logofile"]["tmp_name"]);
			
			if(function_exists("imagecreatetruecolor"))
				$dst = imagecreatetruecolor($newWidth,$newHeight);
			else
				$dst = imagecreate($newWidth,$newHeight);
			
			if(function_exists("imagecopyresampled"))
				imagecopyresampled($dst,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			else			
				imagecopyresized($dst,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			
			imagepng($dst,"$tmpDir/$realname");
			break;
		/*default:
			$src = imagecreatefromjpeg($arrLogoInfo["logofile"]["tmp_name"]);
			
			if(function_exists("imagecreatetruecolor"))
				$dst = imagecreatetruecolor($newWidth,$newHeight);
			else
				$dst = imagecreate($newWidth,$newHeight);
			
			if(function_exists("imagecopyresampled"))
				imagecopyresampled($dst,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			else			
				imagecopyresized($dst,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);
			
			imagejpeg($dst,"$tmpDir/$realname",100);
			break;*/
	
	}
	
	//Destroy the image
	imagedestroy($src);
	imagedestroy($dst);
	
	return true;
}

//Function that will delete the selected papers
// Check for linked papers in other tables!!!
function purge_Selected_Paper($arrPaperID){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	foreach($arrPaperID as $paperID){
		
		$deleteFileSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "File WHERE PaperID = ".$paperID;
		$deleteResult = $db -> Execute($deleteFileSQL);
	
		if(!$deleteResult)
			return "Could not delete the File";
	
		$deletePaperSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE PaperID = ".$paperID;
		$deleteResult = $db -> Execute($deletePaperSQL);
	
		if(!$deleteResult)
			return "Could not delete the Paper Information";

		$deletePaperSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Review WHERE PaperID = ".$paperInfo -> PaperID;
		$deleteResult = $db -> Execute($deletePaperSQL);
	
		if(!$deleteResult)
			return "Could not delete the Paper Information";
	}
	
	return true;
}

//Function that will delete all the withdrawns papers
// Check for linked papers in other tables!!!
function purge_Withdrawn_Papers(){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$withdrawnPapersSQL = "SELECT *";
	$withdrawnPapersSQL .= " From " . $GLOBALS["DB_PREFIX"] . "Paper";
	$withdrawnPapersSQL .= " WHERE Withdraw = 'true'";
	$withdrawnPapersResult = $db -> Execute($withdrawnPapersSQL);		
		
	while( $paperInfo = $withdrawnPapersResult  -> FetchNextObj()){
		
		$deleteFileSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "File WHERE PaperID = ".$paperInfo -> PaperID;
		$deleteResult = $db -> Execute($deleteFileSQL);
	
		if(!$deleteResult)
			return "Could not delete the File";
	
		$deletePaperSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE PaperID = ".$paperInfo -> PaperID;
		$deleteResult = $db -> Execute($deletePaperSQL);
	
		if(!$deleteResult)
			return "Could not delete the Paper Information";

		$deletePaperSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Review WHERE PaperID = ".$paperInfo -> PaperID;
		$deleteResult = $db -> Execute($deletePaperSQL);
	
		if(!$deleteResult)
			return "Could not delete the Paper Information";

	}
	
	return true;
}

function get_Unsended_EmailList($letterID,$recipientGroupName){

	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	//Get the letter information
	$letterInfo = get_Letter_Info($letterID);		
	
	//Get the membername from MailLog table
	$memberSQL = "SELECT MemberName FROM " . $GLOBALS["DB_PREFIX"]."MailLog";
	$memberSQL .= " WHERE LetterID = ".$letterID;
	$memberResult = $db -> Execute($memberSQL);
	

	//Get all the member from Member table
	$allmemberSQL  = get_Recipient_Group_SQL($recipientGroupName);
	$allmemberResult = $db -> Execute($allmemberSQL);
	
	//Get all member info as an array
	while($memberInfo = $allmemberResult -> FetchNextObj()){
		if(($letterInfo -> Title == "Paper Acceptance") || ($letterInfo -> Title == "Paper Rejection"))
			$arrAllMembers[] = $memberInfo -> PaperID;
		else
			$arrAllMembers[] = $memberInfo -> MemberName;
	}
	
	//Check if there is any records return from mail Log
	if($memberResult -> RecordCount() == 0){
	
		//Make the array that contain member names as Index
		reset($arrAllMembers);
		
		if(($letterInfo -> Title == "Paper Acceptance") || ($letterInfo -> Title == "Paper Rejection")){
				//The email is by paper			
				//The result return paperID instead of usename, get the email of the paper owner
				foreach($arrAllMembers as $paperID){			
					$paperInfo = get_paper_info($paperID);
					$arrAllEmails[$paperID] = getMemberEmail($paperInfo -> MemberName);
				}				
		
		}else {
				//The email is by member name
				foreach($arrAllMembers as $memberName){
					$arrAllEmails[$memberName] = getMemberEmail($memberName);
				}
		}
		
		//No record return, return the array of all members as an array
		return $arrAllEmails;
	}
	
	//Since there is come records from MailLog, Get the array from MaiLog
	while($memberInfo = $memberResult -> FetchNextObj()){	
		$arrMailLogMembers[] = $memberInfo -> MemberName;
	}	
	
	//Get the members that has not receive any email yet
	$arrUnsendMembers = array_diff($arrAllMembers,$arrMailLogMembers);
	
	if(($letterInfo -> Title == "Paper Acceptance") || ($letterInfo -> Title == "Paper Rejection")){
		//The email is by paper
		//The result return paperID instead of usename, get the email of the paper owner
		foreach($arrUnsendMembers as $paperID){			
			$paperInfo = get_paper_info($paperID);
			$arrUnsendEmails[$paperID] = getMemberEmail($paperInfo -> MemberName);
		}			
		
	}else{	
		//Loop the array and get the member email
		foreach($arrUnsendMembers as $memberName){
			//Make the array
			$arrUnsendEmails[$memberName] = getMemberEmail($memberName);
		}
	}
	
	return $arrUnsendEmails;
}

function get_Already_Sent_EmailList($letterID){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$letterInfo = get_Letter_Info($letterID);		
		
	$memberSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."MailLog";
	$memberSQL .= " WHERE LetterID = ".$letterID;
	$memberResult = $db -> Execute($memberSQL);
	
	if($memberResult -> RecordCount() > 0){
		while($memberInfo = $memberResult -> FetchNextObj()){
		
			if(($letterInfo -> Title == "Paper Acceptance") || ($letterInfo -> Title == "Paper Rejection")){
			
			//The result return paperID instead of usename, get the email of the paper owner
			$paperInfo = get_paper_info($memberInfo -> MemberName);
			$arrEmails[$memberInfo -> MemberName] = getMemberEmail($paperInfo -> MemberName);
		}else 		
			//Get the member email and insert into array
			$arrEmails[$memberInfo -> MemberName] = getMemberEmail($memberInfo -> MemberName);
		}
	}
	else
		$arrEmails = array();
		
	return $arrEmails;
}

function display_track_table(){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$categorySQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Track";
	$categoryResult = $db -> Execute($categorySQL);
	
	if(!$categoryResult)
		return "Could not retrieve the track information - please try again later";	

	echo '<table width="100%" border="1" cellspacing="2" cellpadding="2">';
  		echo "<tr>";
    		//echo '<td width="10%">&nbsp;</td>';
    		echo '<td width="70%"><strong>Track</strong></td>';
    		echo '<td width="15%">&nbsp;</td>';
    		echo '<td width="15%">&nbsp;</td>';
	  	echo "</tr>";
	
 	while($categoryInfo = $categoryResult -> FetchNextObj()){
  		echo "<tr>";
    		// echo "<td>".++$i.".</td>";
    		echo "<td>".$categoryInfo -> TrackName."</td>";
    		echo '<td><a href="edit_track.php?catID='.$categoryInfo -> TrackID.'">edit</a></td>';
    		echo '<td><a href="confirm_delete_track.php?catID='.$categoryInfo -> TrackID.'">delete</a></td>';
  		echo "</tr>\n";
 	}
	
	echo "</table>";

}

function display_category_table(){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$categorySQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Category";
	$categoryResult = $db -> Execute($categorySQL);
	
	if(!$categoryResult)
		return "Could not retrieve the topic information - please try again later";	

	echo '<table width="100%" border="1" cellspacing="2" cellpadding="2">';
  		echo "<tr>";
    		// echo '<td width="10%">&nbsp;</td>';
    		echo '<td width="70%"><strong>Topic</strong></td>';
    		echo '<td width="15%">&nbsp;</td>';
    		echo '<td width="15%">&nbsp;</td>';
	  	echo "</tr>";
	
 	while($categoryInfo = $categoryResult -> FetchNextObj()){
  		echo "<tr>";
    		// echo "<td>".++$i.".</td>";
    		echo "<td>".$categoryInfo -> CategoryName."</td>";
    		echo '<td><a href="edit_category.php?catID='.$categoryInfo -> CategoryID.'">edit</a></td>';
    		echo '<td><a href="confirm_delete_category.php?catID='.$categoryInfo -> CategoryID.'">delete</a></td>';
  		echo "</tr>\n";
 	}
	
	echo "</table>";

}
function get_track_info($catID){
	//Get the Track information to edit
	$catSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Track";
	$catSQL .= " WHERE TrackID = $catID";
	$catResult = db_quick_query($catSQL); // connect and query db
    $categoryInfo = $catResult -> FetchNextObj();
    return $categoryInfo;
}

function get_tracks( $err_message = "" )
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Track";
	$result = $db -> Execute($sql);
	
	if(!$result)
		$err_message .= "Could not retrieve the track information - please try again later";
	
	$tracks = array();
	while ($track = $result -> FetchNextObj())
	{
		$tracks[] = $track;
	}
	return $tracks;
}

function get_category_info($catID){
	//Get the Category information to edit
	$catSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Category";
	$catSQL .= " WHERE CategoryID = $catID";
	$catResult = db_quick_query($catSQL); // connect and query db
	$categoryInfo = $catResult -> FetchNextObj();
	return $categoryInfo;		
}
function update_Track($catID,$catName){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return false;
		
	$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Track";
	$updateSQL .= " SET TrackName = ".db_quote($db,$catName);
	$updateSQL .= " WHERE TrackID = $catID";
	$updateResult = $db -> Execute($updateSQL);
	
	if(!$updateResult)
		return false;
	else
		return true;

}
function update_Category($catID,$catName){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return false;
		
	$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"] . "Category";
	$updateSQL .= " SET CategoryName = ".db_quote($db,$catName);
	$updateSQL .= " WHERE CategoryID = $catID";
	$updateResult = $db -> Execute($updateSQL);
	
	if(!$updateResult)
		return false;
	else
		return true;

}

function delete_Track($catID){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return false;
		
	$deleteSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Track";
	$deleteSQL .= " WHERE TrackID = ".db_quote($db,$catID);
	$deleteResult = $db -> Execute($deleteSQL);
	
	if(!$deleteResult)
		return false;
	else
		return true;

}
function delete_Category($catID){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return false;
		
	$deleteSQL = "DELETE FROM " . $GLOBALS["DB_PREFIX"] . "Category";
	$deleteSQL .= " WHERE CategoryID = '$catID'";
	$deleteResult = $db -> Execute($deleteSQL);
	
	if(!$deleteResult)
		return false;
	else
		return true;

}

function add_new_track($catName){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return false;
		
	$insertSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Track(TrackName)";
	$insertSQL .= " VALUES('$catName')";
	$insertResult = $db -> Execute($insertSQL);
	
	if(!$insertResult)
		return false;
	else
		return true;

}

function add_new_category($catName){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return false;
		
	$insertSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"] . "Category(CategoryName)";
	$insertSQL .= " VALUES('$catName')";
	$insertResult = $db -> Execute($insertSQL);
	
	if(!$insertResult)
		return false;
	else
		return true;

}

function display_phase_table(){
	
    // Get settings info
    $settingsInfo = get_Conference_Settings();
    
    // Connect to database
    $db = adodb_connect($err_message);
    
    $phasesSQL = "SELECT PhaseID, PhaseName, ".dbdf_out($db,"StartDate").", ".dbdf_out($db,"EndDate").", Status";
	$phasesSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "ConferencePhase";
	$phasesResult = $db -> Execute($phasesSQL);
	
	if(!$phasesResult)
		return "Could not retrieve the phase information - please try again later";	
	
	echo '<table width="100%" border="1" cellspacing="2" cellpadding="2">';
  		echo "<tr>";
    		echo '<td width="10%">&nbsp;</td>';
    		echo '<td width="40%"><strong>Phase Name</strong></td>';
    		echo '<td width="30%"><strong>Start Date</strong></td>';
    		echo '<td width="30%"><strong>End Date</strong></td>';
	  	echo "</tr>";
	
 	while($phaseInfo = $phasesResult -> FetchNextObj()){
  		echo "<tr>";
    		echo "<td>".++$i.".</td>";
    		echo "<td>".$phaseInfo -> PhaseName."</td>";
    		echo "<td>".format_date($settingsInfo -> DateFormatShort,$phaseInfo -> StartDate)."</td>";
    		echo "<td>".format_date($settingsInfo -> DateFormatShort,$phaseInfo -> EndDate)."</td>";
  		echo "</tr>\n";
 	}
	
	echo "</table>";

}


function verify_Preference_Exist($reviewer){
	
	//Retrieve the preferences on the paper
	$selectionSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Selection";
  	$selectionSQL .= " WHERE MemberName = '$reviewer'";
	$selectionResult = db_quick_query($selectionSQL);
	
	if (!$selectionResult)
		return "Could not retrieve the preference information - please try again later";
	
	if (($selectionResult -> RecordCount()) == 0)
		return false;
	else
		return true;
}

function get_Reviewers_Of_Paper($paperID){
	
	$reviewerSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Review";
	$reviewerSQL .= " WHERE PaperID = ".$paperID;
	$reviewerResult = db_quick_query($reviewerSQL);
	
	for( $i = 0 ; $i < $reviewerResult -> RecordCount() ; $i++ )
    {
		$reviewerInfo = $reviewerResult -> FetchNextObj();
		$arrReviewers[$i] = $reviewerInfo -> MemberName;
	}
	return $arrReviewers;
}

function get_Paper_Reviewing_Statistic(){

	
	//Establish connection with database
	$db = adodb_connect($err_message);
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	//Get the paperID of reviewing papers
	$getPaperReviewingSQL = "SELECT PaperID,COUNT(*) AS Number FROM ".$GLOBALS["DB_PREFIX"]."Review";
	$getPaperReviewingSQL .= " GROUP BY PaperID";
	$getPaperReviewingResult = $db -> Execute($getPaperReviewingSQL);
	
	//Go through each paper and evaluate the data
	
	for($i = 0;$i < $getPaperReviewingResult -> RecordCount();$i++){
		
		//Get the review record
		$reviewPaperInfo = $getPaperReviewingResult -> FetchNextObj();
		
		//Evaluate the maximum reviews of papers
		if($i == 0)
			$maxReviews = $reviewPaperInfo -> Number;
		else
			if($reviewPaperInfo -> Number > $maxReviews )
				$maxReviews  = $reviewPaperInfo -> Number;
		
		//Get the reviews of this paper
		$paperReviewSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Review";
		$paperReviewSQL .= " WHERE PaperID = ".$reviewPaperInfo -> PaperID;
		$paperReviewSQL .= " AND Objectives <> 0";
		$paperReviewResult = $db -> Execute($paperReviewSQL);
		
		//Go through each review of paper and evaluate
		/*$countReviews = 0;
		while($paperReviewInfo = $paperReviewResult -> FetchNextObj()){
			
			//Count the number of reviews
			//if(!is_null($paperReviewInfo -> Comments))
				++$countReviews;
		}*/
		$countReviews = $paperReviewResult -> RecordCount();
        
		//If the maxReviews is less than 3,assume 3 minimum reviewers
		if($maxReviews < 3)
			$maxReviews = 3;
			
		//Loop until maxReviews and create the array
		for($j = 0; $j <= $maxReviews; $j ++){
			if($j == $countReviews)
				++$arrCount[$j];
			else
				if (!isset($arrCount[$j]))
					$arrCount[$j] = 0;
				else
					$arrCount[$j] = $arrCount[$j];
				
		}//end of inner for loop		
		
	}//end of for loop
	
	return $arrCount;

}

// Functions currently not used - (don't know why)
/*
function display_RecipientGroup_SelectionList(){
	
	
	global $arrLetterInfo;
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$recipientgroupSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."RecipientGroup";
	$recipientgroupSQLResult = $db -> Execute($recipientgroupSQL);
	
	if(!$recipientgroupSQLResult)
		return "Could not retrieve the recipientgroup information";
	
	$selecttag = "<select name=\"recipientgroup\">\n";
	while($recipientgroupInfo = mysql_fetch_object($recipientgroupSQLResult)){
		//Genearate the option tag according to the value
		if(count($arrLetterInfo) > 0)
			if($arrLetterInfo["recipientgroup"] == $recipientgroupInfo -> RecipientGroupName)
				$strSelected = "selected";
			else
				$strSelected = "";
			
		$selecttag .= "<option value=\"".$recipientgroupInfo -> RecipientGroupName."\" $strSelected >".$recipientgroupInfo -> RecipientGroupName."</option>\n";
	
	}
	$selecttag .= "</select>\n";
	
	return $selecttag;

}


function get_RecipientGroupID($recipientGroupName){

	

	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	$recipientgroupSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."RecipientGroup";
	$recipientgroupSQL .= " WHERE RecipientGroupName = '".$recipientGroupName."'";
	$recipientgroupResult = $db -> Execute($recipientgroupSQL);
	$recipientgroupInfo = mysql_fetch_object($recipientgroupResult);
	
	return $recipientgroupInfo -> RecipientGroupID;

}
*/

function get_RecipientGroupName($recipientGroupID){

	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	$recipientgroupSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."RecipientGroup";
	$recipientgroupSQL .= " WHERE RecipientGroupID = '".$recipientGroupID."'";
	$recipientgroupResult = $db -> Execute($recipientgroupSQL);
	$recipientgroupInfo = $recipientgroupResult -> FetchNextObj();
	
	return $recipientgroupInfo -> RecipientGroupName;

}

function get_Letter_Info($letterID){

	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	//Make the SQL to retrieve the letters
	$letterSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Letter L," . $GLOBALS["DB_PREFIX"]."RecipientGroup R";
	$letterSQL .= " WHERE L.RecipientGroupID = R.RecipientGroupID";	
	$letterSQL .= " AND LetterID = ".$letterID;	
	$letterResult = $db -> Execute($letterSQL);
	$letterInfo = $letterResult -> FetchNextObj();	

	return $letterInfo;

}

function get_LetterInfo_By_Title($letterTitle){
	
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";

	//Retrieve the letter from database
	$letterSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Letter L," . $GLOBALS["DB_PREFIX"]."RecipientGroup R";
	$letterSQL .= " WHERE L.RecipientGroupID = R.RecipientGroupID";
	$letterSQL .= " AND L.Title = '".$letterTitle."'";
	$letterResult = $db -> Execute($letterSQL);

	if(!$letterResult)
		return "Could not retrieve the letter information - please try again";
	
	$letterInfo = $letterResult -> FetchNextObj();
	
	return $letterInfo;


}

function update_LetterInfo($arrLetterInfo){
	

	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	//get the letter information
	$letterInfo  = get_Letter_Info($arrLetterInfo["letterID"]);	
	
	$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"]."Letter"
				 ." SET Subject = '".$arrLetterInfo["subject"]."',"
				 ."BodyContent = '".addslashes($arrLetterInfo["bodycontent"])."',"
				 ."RecipientGroupID = '".$letterInfo -> RecipientGroupID."'"
				 . " WHERE Title = '".$letterInfo -> Title."'";
	$updateResult = $db -> Execute($updateSQL);
	
	if(!$updateResult)
		return "Could not update the letter information - please try again";
		
	return true;
				  
}

function evaluate_Lettertype_BackURL($lettertype){

	switch($lettertype){
		case "reviewerinvite":
			$url = "Location: mail_reviewer_invitation.php?lettertype=".$lettertype;
			break;
		case "useraccount":
			$url = "Location: mail_account_info.php?lettertype=".$lettertype;				
			break;
		case "revieweraccount":
			$url = "Location: mail_account_info.php?lettertype=".$lettertype;
			break;
		case "adminaccount":
			$url = "Location: mail_account_info.php?lettertype=".$lettertype;
			break;								
		case "paperacceptance":
			$url = "Location: mail_acceptance_rejection_paper.php?lettertype=".$lettertype;
			break;			
		case "paperrejection":
			$url = "Location: mail_acceptance_rejection_paper.php?lettertype=".$lettertype;				
			break;
		case "newlettertype":
			$url = "Location: mail_new_lettertype.php?lettertype=".$lettertype;						
			break;
	}
	
	return $url;

}

function evaluate_Letter_URL($letterTitle,$letterID){

	switch($letterTitle){
		case "Reviewer Invitation and Instructions":
			$arrURL["edit"] = "mail_reviewer_invitation.php?lettertype=reviewerinvite";
			$arrURL["send"] = "<a href=\"send_reviewer_invitation.php?lettertype=reviewerinvite\">Send Letter</a>";
			$arrURL["view"] = "<a href=\"view_each_letter.php?letterID=".$letterID."&lettertype=reviewerinvite\">View Letter</a>";			
			break;
		case "User Account Info":		
			$arrURL["edit"] = "mail_account_info.php?lettertype=useraccount";				
			$arrURL["send"] = "<a href=\"send_account_info.php?lettertype=useraccount\">Send Letter</a>";
			$arrURL["view"] = "<a href=\"view_each_letter.php?letterID=".$letterID."&lettertype=useraccount\">View Letter</a>";						
			break;
		case "Reviewer Account Info":
			$arrURL["edit"] = "mail_account_info.php?lettertype=revieweraccount";
			$arrURL["send"] = "<a href=\"send_account_info.php?lettertype=revieweraccount\">Send Letter</a>";
			$arrURL["view"] = "<a href=\"view_each_letter.php?letterID=".$letterID."&lettertype=revieweraccount\">View Letter</a>";						
			break;
		case "Admin Account Info":
			$arrURL["edit"]= "mail_account_info.php?lettertype=adminaccount";
			$arrURL["send"] = "<a href=\"send_account_info.php?lettertype=adminaccount\">Send Letter</a>";
			$arrURL["view"] = "<a href=\"view_each_letter.php?letterID=".$letterID."&lettertype=adminaccount\">View Letter</a>";						
			break;								
		case "Paper Acceptance":
			$arrURL["edit"] = "mail_acceptance_rejection_paper.php?lettertype=paperacceptance";
			$arrURL["send"] = "<a href=\"send_paper_info.php?lettertype=paperacceptance\">Send Letter</a>";
			$arrURL["view"] = "<a href=\"view_each_letter.php?letterID=".$letterID."&lettertype=paperacceptance\">View Letter</a>";						
			break;			
		case "Paper Rejection":
			$arrURL["edit"] = "mail_acceptance_rejection_paper.php?lettertype=paperrejection";				
			$arrURL["send"] = "<a href=\"send_paper_info.php?lettertype=paperrejection\">Send Letter</a>";
			$arrURL["view"] = "<a href=\"view_each_letter.php?letterID=".$letterID."&lettertype=paperrejection\">View Letter</a>";			
			break;						
		default:
			$arrURL["edit"] = "<a href=\"mail_new_lettertype.php?letterID=".$letterID."\">Edit Letter</a>";						
			break;
	}	
	
	return $arrURL;

}

function evaluate_Letter_Constants($lettertype){

	switch($lettertype){
		case "reviewerinvite":
			$arrConstants = array("\$fullname","\$url","\$confname","\$confcode","\$contact");
			break;
		case "useraccount":
		case "revieweraccount":
		case "adminaccount":				
			$arrConstants = array("\$fullname","\$username","\$password","\$url","\$confname","\$confcode","\$contact");						
			break;								
		case "paperacceptance":
			$arrConstants = array("\$fullname","\$paperID","\$papertitle","\$authors","\$papercat","\$url","\$confname","\$confcode","\$contact","\$presType");					
			break;
		case "paperrejection":		
			$arrConstants = array("\$fullname","\$paperID","\$papertitle","\$authors","\$papercat","\$url","\$confname","\$confcode","\$contact");					
			break;
		default:		
			$arrConstants = array("\$fullname","\$username","\$url","\$confname","\$confcode","\$contact");					
			break;			
			
	}
	
	return $arrConstants;

}

function deleteMailLog($memberName,$letterID){
	
	

	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	$deleteMailLog = "DELETE FROM " . $GLOBALS["DB_PREFIX"]."MailLog";	
	$deleteMailLog .= " WHERE MemberName = '".$memberName."'";
	$deleteMailLog .= " AND LetterID= ".$letterID."";
	$deleteResult = $db -> Execute($deleteMailLog);
	
	if(!$deleteResult)
		return "Could not delete the mail log information.";
	
	return true;

}

function updateMailLog($memberName,$letterID)
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	//Find the record with this member name
	$selectSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."MailLog";
	$selectSQL .= " WHERE MemberName = '".$memberName."'";
	$selectSQL .= " AND LetterID =".$letterID;	
	$selectResult = $db -> Execute($selectSQL);
	
	if(!$selectResult)
		return "Could not retrieve the maillog information - please try agian";
		
	if($selectResult -> RecordCount() == 0) {
		
		$insertSQL = "INSERT INTO " . $GLOBALS["DB_PREFIX"]."MailLog";
		$insertSQL .= " VALUES('".$memberName."','".$letterID."')";
		$insertResult = $db -> Execute($insertSQL);
		
		if(!$insertResult)
			return "Could not log the mail information";
		
	}

	return true;
}

//This function updates the password of the user
function updateUserPassword($userName,$password)
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$updateSQL = "UPDATE " . $GLOBALS["DB_PREFIX"]."Member";
	$updateSQL .= " SET Password = '".sha1($password)."'";
	$updateSQL .= " WHERE MemberName = '".$userName."'";
	$updateResult = $db -> Execute($updateSQL);
	
	if(!$updateResult)
		return "Could not update the account information.";

	return true;
}

//This function update the General Settings of conference
function updateSettings($arrSettings)
{
	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
    // Remove "Submit" from the array
    $arrSettings = array_diff_assoc($arrSettings, array("Submit"=>"Submit"));
    
    // Calculate the MaxUploadSize and MaxLogoSize
	$arrSettings["MaxUploadSize"] = $arrSettings["MaxUploadSize"] * pow(2,20);
	$arrSettings["MaxLogoSize"] = $arrSettings["MaxLogoSize"] * pow(2,20);
	
    foreach ($arrSettings as $key => $value)
    {
        $updateSQL = "REPLACE INTO " . $GLOBALS["DB_PREFIX"]."Settings ";
        $updateSQL .= "(Name, Value) VALUES ";
        $updateSQL .= "(".db_quote($db,$key).",".db_quote($db,$value).")";
        
        $updateResult = $db -> Execute($updateSQL);
        
        if(!$updateResult)
            return "Could not update the setting information - try again : $updateSQL";
    }
	return true;
	
}

function highlight_Dynamic_Values($arrConstants,$strContent)
{
	//Loop the array and get the constants
	foreach($arrConstants as $constant){
		//Replace the constants with highlight strings
		$strReplace = "<font color=\"#0000FF\"><strong>".$constant."</strong></font>";
		//$strFind = substr($constant,1);
		$arrReplace = array("$constant" => $strReplace);
		//$strContent = ereg_replace($constant,$strReplace,$strContent);
		$strContent = strtr($strContent,$arrReplace);
	}
	
	return $strContent ;

}

function replace_Dynamic_Values($arrConstants,$arrReplaceInfo,$strContent)
{
	//Retrieve the constatn one by one
	foreach($arrConstants as $constantName){
		//Take away the dollarsign "$" to process easier
		//$constantName= substr($constantName,1);
		switch(substr($constantName,1)){
			case "fullname":
				$arrReplace ["$constantName"] = $arrReplaceInfo["fullname"];							
				break;
			case "username":
				$arrReplace ["$constantName"] = $arrReplaceInfo["username"];							
				break;
			case "password":
				$arrReplace ["$constantName"] = $arrReplaceInfo["password"];							
				break;			
			case "paperID":
				$arrReplace ["$constantName"] = $arrReplaceInfo["paperID"];			
				break;
			case "papertitle":
				$arrReplace ["$constantName"] = $arrReplaceInfo["papertitle"];
				break;
			case "authors":
				$arrReplace ["$constantName"] = $arrReplaceInfo["authors"];
				break;
			case "papercat":
				$arrReplace ["$constantName"] = $arrReplaceInfo["papercat"];
				break;
			case "url":
				$arrReplace ["$constantName"] = $arrReplaceInfo["url"];
				break;
			case "confname":
				$arrReplace ["$constantName"] = $arrReplaceInfo["confname"];
				break;
			case "confcode":
				$arrReplace ["$constantName"] = $arrReplaceInfo["confcode"];
				break;
			case "contact":
				$arrReplace ["$constantName"] = $arrReplaceInfo["contact"];
				break;
			case "presType":
				$arrReplace ["$constantName"] = $arrReplaceInfo["presType"];
				break;
		
		}//end of switch		

	}//end of for each
	
	//Replace the content with generated value
	$strContent = strtr($strContent,$arrReplace);	
	
	return $strContent;
}

function get_Recipient_Group_SQL($recipientGroupName){
	

	switch($recipientGroupName){
		case "Users":
			//Retrieve all the user information
			$emailSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"]."PrivilegeType P," . $GLOBALS["DB_PREFIX"] . "Registration R";
			$emailSQL .= " WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
			$emailSQL .= " AND M.RegisterID = R.RegisterID";
			$emailSQL .= " AND P.PrivilegeTypeName = 'User'";
			$emailType = "per User";
			break;
		case "Reviewers":
			//Retrieve all the reviewer information
			$emailSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"]."PrivilegeType P," . $GLOBALS["DB_PREFIX"] . "Registration R";
			$emailSQL .= " WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
			$emailSQL .= " AND M.RegisterID = R.RegisterID";
			$emailSQL .= " AND P.PrivilegeTypeName = 'Reviewer'";
			$emailType = "per User";			
			break;		
		case "Administrators":
			//Retrieve all the adminstrators information
			$emailSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"]."PrivilegeType P," . $GLOBALS["DB_PREFIX"] . "Registration R";
			$emailSQL .= " WHERE M.PrivilegeTypeID = P.PrivilegeTypeID";
			$emailSQL .= " AND M.RegisterID = R.RegisterID";
			$emailSQL .= " AND P.PrivilegeTypeName = 'Administrator'";
			$emailType = "per User";			
			break;		
		case "Accepted Users":
			//Retrieve all the accepted users
			$emailSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Paper PP," . $GLOBALS["DB_PREFIX"]."PaperStatus PS," . $GLOBALS["DB_PREFIX"]."Member M," . $GLOBALS["DB_PREFIX"]."Registration R";
			$emailSQL .= " WHERE PP.PaperStatusID = PS.PaperStatusID";
			$emailSQL .= " AND PP.MemberName = M.MemberName";
			$emailSQL .= " AND M.RegisterID = R.RegisterID";			
			$emailSQL .= " AND PS.PaperStatusName = 'Accepted'";			
			$emailSQL .= " AND PP.Withdraw = 'false'";									
			break;
		case "Rejected Users":
			//Retrieve all the rejected users
			$emailSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Paper PP," . $GLOBALS["DB_PREFIX"]."PaperStatus PS," . $GLOBALS["DB_PREFIX"]."Member M," . $GLOBALS["DB_PREFIX"]."Registration R";
			$emailSQL .= " WHERE PP.PaperStatusID = PS.PaperStatusID";
			$emailSQL .= " AND PP.MemberName = M.MemberName";
			$emailSQL .= " AND M.RegisterID = R.RegisterID";			
			$emailSQL .= " AND PS.PaperStatusName = 'Rejected'";			
			$emailSQL .= " AND PP.Withdraw = 'false'";					
			break;
	}
	
	return $emailSQL;
}

function arrayKeyExists($key, $search) {
   if (in_array($key, array_keys($search))) {
       return true;
   } else {
       return false;
   }
}

function make_Popup_Window($url,$parameterList = "",$height,$width,$scrollbars){

	if(strlen($parameterList) > 0 )
		$str = "JavaScript: window.open('".$url."?".$parameterList."',null,'height=".$height.",width=".$width.",status=yes,toolbar=no,menubar=no,scrollbars=".$scrollbars.",location=no');";
	else
		$str = "JavaScript: window.open('".$url."',null,'height=".$height.",width=".$width.",status=yes,toolbar=no,menubar=no,scrollbars=".$scrollbars.",location=no');";
		
	return $str;

}

// Unused function
/*
function get_Paper_Owner_Email($paperID)
{
	//Establish database connection
	$db = adodb_connect();
    
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	$sql  = "SELECT R.Email FROM " . $GLOBALS["DB_PREFIX"] . "Paper P," . $GLOBALS["DB_PREFIX"]."Registration R," . $GLOBALS["DB_PREFIX"] . "Member M";
	$sql .= " WHERE P.MemberName = M.MemberName";	
	$sql .= " AND M.RegisterID = R.RegisterID";
	$sql .= " AND P.PaperID = ".$paperID."";
	$result = $db -> Execute($sql);
	
	if(!$result)
		return "Could not retrieve the Member email - pls try again later";
	
	$emailInfo = mysql_fetch_object($result);
	
	return $emailInfo -> Email;
}
*/

function display_Letter_Recipients($strTitle,$arrEmails)
{
	$strTable = "";
	$i = 0;

	//Display the successful send emails list
	if(count($arrEmails) > 0){
		$strTable =  "<p>".$strTitle."<p>";
		$strTable .= "<table width=\"60%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
		$strTable .=  "<tr>";
		$strTable .=	"<td width=\"5%\"><strong>&nbsp;</strong></td>";
		$strTable .=	"<td width=\"25%\"><strong>User Name</strong></td>";
		$strTable .=	"<td width=\"35%\"><strong>Full Name</strong></td>";
		$strTable .=	"<td width=\"35%\"><strong>Email</strong></td>";
		$strTable .=  "</tr>";
						
		while(list($memberName,$email) = each($arrEmails)){
			$memberFullName = getMemberFullName($memberName);
			$strTable .=  "<tr>";
			$strTable .=	"<td>".++$i.". </td>";
			$strTable .=	"<td>".$memberName."</td>";
			$strTable .=	"<td>".$memberFullName."</td>";
			$strTable .=	"<td>".$email."</td>";
			$strTable .=  "</tr>";
		}
		
		$strTable .= "</table></p>";		
	}

	return $strTable;
}

function display_Letter_Recipients_Per_Paper($strTitle,$arrEmails)
{
	$strTable = "";
	$i = 0;

	//Display the successful send emails list
	if(count($arrEmails) > 0){
		$strTable =  "<p>".$strTitle."<p>";
		$strTable .= "<table width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
		$strTable .=  "<tr>";
		$strTable .=	"<td width=\"5%\"><strong>&nbsp;</strong></td>";
		$strTable .=	"<td width=\"10%\"><strong>PaperID</strong></td>";
		$strTable .=	"<td width=\"30%\"><strong>Title</strong></td>";				
		$strTable .=	"<td width=\"15%\"><strong>User Name</strong></td>";
		$strTable .=	"<td width=\"15%\"><strong>Email</strong></td>";
		$strTable .=	"<td width=\"25%\"><strong>Full Name</strong></td>";		
		$strTable .=  "</tr>";
						
		while(list($paperID,$email) = each($arrEmails)){
			
			$paperInfo = get_paper_info($paperID);
			$memberFullName = getMemberFullName($paperInfo -> MemberName);
			
			$strTable .=  "<tr>";
			$strTable .=	"<td>".++$i.". </td>";
			$strTable .=	"<td>#".$paperID."</td>";
			$strTable .=	"<td>".stripslashes($paperInfo -> Title)."</td>";						
			$strTable .=	"<td>".$paperInfo -> MemberName."</td>";
			$strTable .=	"<td>".$email."</td>";
			$strTable .=	"<td>".$memberFullName."</td>";
			$strTable .=  "</tr>";
		}
		
		$strTable .= "</table></p>";		
	}

	return $strTable;
}

function format_Letter_Subject($strSubject)
{
	//Get the conference information
	$conferenceInfo = get_conference_info();

	//Format the subject of the letter
	$arrConstants = array("\$confname","\$confcode");	
	$arrReplaceInfo = array("confname" => $conferenceInfo -> ConferenceName,"confcode" => $conferenceInfo -> ConferenceCodeName);
	$strSubject = replace_Dynamic_Values($arrConstants,$arrReplaceInfo,$strSubject);
	
	return $strSubject;
}

function array_merge_assoc($array1,$array2)
{
	//Intersect both arrays
	$arrIntersect = array_intersect_assoc($array1,$array2);
	$arrDiffFirst = array_diff_assoc($array1,$array2);
	$arrDiffSecond = array_diff_assoc($array2,$array1);

	while(list($key,$value) = each($arrIntersect))
		$arrReturn[$key] = $value;
		
	while(list($key,$value) = each($arrDiffFirst))
		$arrReturn[$key] = $value;
		
	while(list($key,$value) = each($arrDiffSecond))
		$arrReturn[$key] = $value;				
		
	return $arrReturn;
}

function check_Letter_Already_Sent($memberName,$letterID)
{
    //Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
		
	//Find the record with this member name
	$selectSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."MailLog";
	$selectSQL .= " WHERE MemberName = '".$memberName."'";
	$selectSQL .= " AND LetterID =".$letterID;	
	$selectResult = $db -> Execute($selectSQL);
	
	if(!$selectResult)
		return "Could not retrieve the maillog information - please try again";
		
	if( $selectResult -> RecordCount() == 0) 
		return false;
	else
		return true;
}

function get_reviewer_info($reviewerID){

	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db)
		return "Could not connect to database server - please try later.";
	
	//Make the SQL to retrieve the letters
	$reviewerSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Registration R";
	$reviewerSQL .= " WHERE R.RegisterID = $reviewerID";	
	$reviewerResult = $db -> Execute($reviewerSQL);
	$reviewerInfo = $reviewerResult -> FetchNextObj();	

	return $reviewerInfo;
}

?>
