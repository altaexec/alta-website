<?php 
global $php_root_path ;
//include_once("$php_root_path/includes/db_connect.php"); already included in main_fns.php
require_once("$php_root_path/includes/main_fns.php");

function authentication( &$header , &$accepted_privilegeID_arr , &$accepted_phaseID_arr , &$homepage , $php_root_path , $dbprefix , $err_message= "" )
{		
	if ( !check_valid_user( &$err_message ) )
	{
		//This user is not login
		$homepage->showmenu = 0 ;			
		do_html_header( $header . " Page Failed" , &$err_message ) ;			
		$err_message .= " Sorry, you must login to perform this task. <br>\n";
		$err_message .= "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
		$err_message .= " Otherwise you may <a href='$php_root_path/logout.php'>Logout</a> now and try again later." ;
		do_html_footer($err_message);
		exit;
	}
	
  	$db = adodb_connect( &$err_message );
	
	if (!$db)
	{
		do_html_header("Connection to " . $header . " Page Failed" , &$err_message );	
		$err_message .= " Could not connect to database server by \"authentication\"- please try later. <br>\n" ;
		$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
		$err_message .= "<br> Otherwise you may <a href='$php_root_path/logout.php'>Logout</a> now and try again later." ;		
		do_html_footer( &$err_message );
		exit;
	}						

	$result = $db -> Execute("SELECT PrivilegeTypeID FROM " . $GLOBALS["DB_PREFIX"] . "Member WHERE MemberName = '" . addslashes ( $_SESSION["valid_user"] ) . "'" ) ;
	if(!$result)
	{
		$homepage->showmenu = 0 ;			
		do_html_header($header . " Page Failed" , &$err_message ) ;				
		$err_message .= " Cannot retrieve information from Member Table in database by \"authentication\". <br>\n" ;
		$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
		$err_message .= "<br> Otherwise you may <a href='$php_root_path/logout.php'>Logout</a> now and try again later." ;				
		do_html_footer($err_message);		
		exit ;
	}
	
	$numrows = $result -> RecordCount() ;
	//Check whether the user has login to view this page.		
	if ( $numrows < 1 )
	{
		$homepage->showmenu = 0 ;			
		do_html_header( $header . " Page Failed" , &$err_message ) ;			
		$err_message .= " Sorry, You must register as a user to view this page. <br>\n";
		$err_message .= "<br><br> Go to <a href='$php_root_path/user/registration.php'>Register</a> page." ;
		$err_message .= "<br> Otherwise you may <a href='$php_root_path/logout.php'>Logout</a> now and try again later." ;						
		do_html_footer(&$err_message);
		exit;		
	}
	else if ( $numrows > 1 )
	{
		$homepage->showmenu = 0 ;			
		do_html_header( $header . " Page Failed" , &$err_message ) ;
		$confer = get_conference_info() ;			
		$err_message .= " Database User Table Inconsistent Error by \"authentication\". Please report this error to the admininstrator<br>\n";
		$err_message .= "<br><br> Email this error to administrator at <a href='mailto:$confer->ConferenceContact'>$confer->ConferenceContact</a> of $confer->ConferenceName.<br>\n" ;
		$err_message .= "<br> Go to <a href='$php_root_path/index.php'>Login</a> page.<br>\n" ;
		$err_message .= "<br> Otherwise you may <a href='$php_root_path/logout.php'>Logout</a> now and try again later." ;						
		do_html_footer(&$err_message);
		exit;				
	}
	
	$privID = $result -> FetchRow();
    
	$privIDpass = false ;
	$err_mess = "" ;	
	while ( list ( $acceptedPrivID , $contents_arr ) = each ( $accepted_privilegeID_arr ) )
	{
//			echo "In $acceptedPrivID" ;
		if ( $acceptedPrivID == $privID["PrivilegeTypeID"] )
		{
//				echo "done" ;
			$privIDpass = true ;
			break ;			
		}
		$err_mess .= $contents_arr ;		
	}
	
	reset ( $accepted_privilegeID_arr ) ;		
	
	if ( !$privIDpass )
	{
		$homepage->showmenu = 0 ;	
		do_html_header( "View " . $header . " Page Failed" , &$err_message ) ;	
		$err_message .= " You do not have the authority to access this page.<br>\n";	
		$err_message .= $err_mess ;		
		$err_message .= "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
		$err_message .= "<br> Otherwise you may <a href='$php_root_path/logout.php'>Logout</a> now and try again later." ;						
		do_html_footer( &$err_message );		
		exit;
	}
	
	$phasepass = false ;
	while ( list ( $acceptedPhaseID , $contents_arr ) = each ( $accepted_phaseID_arr ) )
	{
		if ( $_SESSION["phase"]->phaseID == $acceptedPhaseID )
		{ 
			$phasepass = true ;
			break ;
		}	
		$err_mess .= $contents_arr ;
	}
	
	reset ( $accepted_phaseID_arr ) ;			
	
	if ( !$phasepass )
	{
		$homepage->showmenu = 0 ;
		do_html_header( $header . " Page Failed" , &$err_message );	
		$err_message .= " The requested infomation is not available at this phase. <br>\n";
		$err_message .= $err_mess ;
		$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
		$err_message .= "<br> Otherwise you may <a href='$php_root_path/logout.php'>Logout</a> now and try again later." ;						
		do_html_footer( &$err_message );		
		exit ;
	}
}	

function login($username, $password , $err_message = "" )
// check username and password with db
// if yes, return true
// else return false
{
	$db = adodb_connect($err_message);
    
	// Try SHA1 first
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member WHERE MemberName=".db_quote($db,$username)." AND Password=".db_quote($db,sha1($password))."" ;
	$result = $db -> Execute($sql) ;
	// Check if there was a match for the user/pass
    if ($result!=false && $result -> RecordCount() > 0)
	{
		$row = $result -> FetchNextObj() ;
		return $row -> PrivilegeTypeID ;
	}
    
    // May be old password. Fall back to MySQL password().
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member WHERE MemberName=".db_quote($db,$username)." AND Password=password(".db_quote($db,$password).")" ;
	$result =  $db -> Execute( $sql ) ;
	if (!$result)
	{
		$err_message .= " Unable to connect to User database. <br>\n" ;
		return false ;
	}
	
    // Check if user/pass worked with old-style passwords
    if ($result -> RecordCount() > 0)
	{
        $sql = "UPDATE ".$GLOBALS["DB_PREFIX"]."Member SET Password=".db_quote($db,sha1($password))." WHERE MemberName = ".db_quote($db,$username);
		$sha1_update = $db -> Execute($sql);
        $row = $result -> FetchNextObj() ;
		return $row -> PrivilegeTypeID ;
	}
    else 
	{
		$err_message .= "The username or password does not match the database. <br>\n" ;
		return false ;
	}
}

function suLogin($username, $password , $err_message = "" )
// check username only with db, used for su function
// if yes, return true
// else return false
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
	$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member WHERE MemberName=" . db_quote($db, $username);
//	echo "\$sql= " . $sql . "<br>\n" ;
// check if username is unique
	$result = $db -> Execute( $sql ) ;
	if (!$result)
	{
		$err_message .= " Unable to connect to User database. <br>\n" ;
		return false ;
	}
	  
	if ($result -> RecordCount() > 0)
	{
		$row = $result -> FetchNextObj() ;
		return $row -> PrivilegeTypeID ;
	}
	else 
	{
		$err_message .= "The username does not match the database. <br>\n" ;
		return false ;
	}
}

function check_valid_user( $err_message = "" )
// see if somebody is logged in and notify them if not
{
//	if (session_is_registered("valid_user"))
	//global $_SESSION ;
	if ( isset ( $_SESSION["valid_user"] ) && !empty ( $_SESSION["valid_user"] ) )
	{
		return true;
	}
	else
	{
		$err_message .= " This user's session is not registered. <br>\n" ;
		return false;
	}
}
/*
function check_conference_phase( $err_message = "", $priv = 1, $newPhaseID = 0)
{
	
	$result = mysql_query( "SELECT PhaseID FROM " . $GLOBALS["DB_PREFIX"] . "ConferencePhase WHERE Status='true'" ) ;
	
	if (!$result)
	{
		$err_message .= " Unable to connect to Conference database. <br>\n" ;
		return false ;
	}	
	
//	global $_SESSION["phase"] ;	// 4.0.6
//	global $_SESSION ;
	if ( $newPhaseID )
	{
		switch ( $newPhaseID )
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
				$err_message .= " Unknown New Phase of Conference in database. <br>\n" ;
				return false ;
				break ;
			}		
		}
	}
	else
	{
		switch ( mysql_result ( $result , 0 ) )
		{
			case 1:
			{
				$_SESSION["phase"] = new phase1();
				break ;
			}
			case 2:
			{	
				if (isset($_SESSION["real_user"]) & ($priv == 1)) //Check for su login to user
					$_SESSION["phase"] = new phase1(); //su user, phase 1, submission
				else
					$_SESSION["phase"] = new phase2(); //not su, phase 2, bidding
				break ;
			}
			case 3:
			{
				if (isset($_SESSION["real_user"]) & ($priv == 1)) //Check for su login to user
					$_SESSION["phase"] = new phase1(); //su user, phase 1, submission
				else
					$_SESSION["phase"] = new phase3(); //not su, phase 3, reviewing
				break ;
			}
			case 4:
			{

				if (isset($_SESSION["real_user"]) & ($priv == 1)) //Check for su login to user
					$_SESSION["phase"] = new phase4(); //su user, phase 4, resubmission
				else if (isset($_SESSION["real_user"]) & ($priv == 2)) //Check for su login to reviewer
					$_SESSION["phase"] = new phase3(); //su reviewer, phase 3, reviewing
				else
					$_SESSION["phase"] = new phase4(); //not su, phase 4, final submission
				break ;
			}				
			default :
			{
				$err_message .= " Unknown Phase of Conference in database. <br>\n" ;
				return false ;
				break ;
			}	
		}
	}			
/////////////// Debug ///////////////////////////////	
//	echo "C phaseID 1: " . $_SESSION["phase1"]->phaseID . "<BR>\n" ;
//	echo "C phaseID 2: " . $_SESSION["phase2"]->phaseID . "<BR>\n" ;																
//	echo "C phaseID 3: " . $_SESSION["phase"]->phaseID . "<br>\n";
//////////////////////////////////////////////////////
	return true ;
}
*/

function check_conference_phase( $err_message = "", $priv = 1, $newPhaseID = 0)
{
	//Establish connection with database
	$db = adodb_connect( &$err_message );
	
    $result = $db -> Execute( "SELECT PhaseID FROM ".$GLOBALS["DB_PREFIX"]."ConferencePhase WHERE Status='true'" ) ;
	
	if (!$result)
	{
		$err_message .= " Unable to connect to Conference database. <br>\n" ;
		return false ;
	}	
	
//	global $_SESSION["phase"] ;	// 4.0.6
//	global $_SESSION ;
	if ( $newPhaseID )
	{
		switch ( $newPhaseID )
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
				$err_message .= " Unknown New Phase of Conference in database. <br>\n" ;
				return false ;
				break ;
			}		
		}
	 }
	else
	{
        $row = $result -> FetchNextObj();
		switch ( $row -> PhaseID ) //code below is easily edited to provide manual control over phases for users and reviewers.
		{
			case 1:
			{
				switch ( $priv )
				{
					case 1: //user
						$_SESSION["phase"] = new phase1();
						break;
					case 2: //reviewer
						$_SESSION["phase"] = new phase1();
						break;
					case 3: //admin
						$_SESSION["phase"] = new phase1();
						break;
					default: //admin
						$err_message .= " Unknown user privilege. <br>\n" ;
						return false ;
						break;
				}
				break ;
			}
			case 2:
			{
				switch ( $priv )
				{
					case 1: //user
						$_SESSION["phase"] = new phase2();
						break;
					case 2: //reviewer
						$_SESSION["phase"] = new phase2();
						break;
					case 3: //admin
						$_SESSION["phase"] = new phase2();
						break;
					default: //admin
						$err_message .= " Unknown user privilege. <br>\n" ;
						return false ;
						break;
				}
				break ;
			}
			case 3:
			{
				switch ( $priv )
				{
					case 1: //user
						$_SESSION["phase"] = new phase3();
						break;
					case 2: //reviewer
						$_SESSION["phase"] = new phase3();
						break;
					case 3: //admin
						$_SESSION["phase"] = new phase3();
						break;
					default: //admin
						$err_message .= " Unknown user privilege. <br>\n" ;
						return false ;
						break;
				}
				break ;
			}
			case 4:
			{
				switch ( $priv )
				{
					case 1: //user
						$_SESSION["phase"] = new phase4();
						break;
					case 2: //reviewer
						$_SESSION["phase"] = new phase4();
						break;
					case 3: //admin
						$_SESSION["phase"] = new phase4();
						break;
					default: //admin
						$err_message .= " Unknown user privilege. <br>\n" ;
						return false ;
						break;
				}
				break ;
			}	
			default :
			{
				$err_message .= " Unknown Phase of Conference in database. <br>\n" ;
				return false ;
				break ;
			}	
		}
	}			
/////////////// Debug ///////////////////////////////	
//	echo "C phaseID 1: " . $_SESSION["phase1"]->phaseID . "<BR>\n" ;
//	echo "C phaseID 2: " . $_SESSION["phase2"]->phaseID . "<BR>\n" ;																
//	echo "C phaseID 3: " . $_SESSION["phase"]->phaseID . "<br>\n";
//////////////////////////////////////////////////////
	return true ;
}

function change_password( $username , $old_password , $new_password , $confirmpwd , $err_message = "" )
// change password for username/old_password to new_password
// return true or false
{
  // if the old password is right 
  // change their password to new_password and return true
  // else return false
	  
  
	if ( $confirmpwd != $new_password )
	{
		$err_message .= " Your new password and confirmation password are inconsistent. <br>\n" ;
		return false ; 		
	}	
  
	if (!($db = adodb_connect( &$err_message )))
	{
		$err_message .= " Could not connect to the database. <br>\n" ;
		return false;
	}
  
	if (login( $username, $old_password , &$err_message ))
	{			  
		$result = $db -> Execute("UPDATE " . $GLOBALS["DB_PREFIX"] . "Member SET Password = '" . sha1 ( $new_password ) . "' 
								 WHERE MemberName = '" . addslashes ( $username ) . "'");
		if (!$result)
		{
			$err_message .= " Could not update with your new password. Please try again. <br>\n";  // not changed
			return false ;
		}
		else
			return true ;  // changed successfully
	}
	else
	{
		$err_message .= " You have entered an incorrect password. <br>\n"; // old password was wrong
		return false ;
	}
}

function forget_password( $username , $err_message = "" )
{
	  // connect to db
	$db = adodb_connect( &$err_message );
  	if (!$db)
	{
		$err_message .= "Could not connect to database server - please try later.";
		return false ;
	}

	
	global $privilege_root_path ;

	$sql = "SELECT R.FirstName , R.MiddleName , R.LastName , R.Email FROM " . $GLOBALS["DB_PREFIX"] . "Member M , " . $GLOBALS["DB_PREFIX"] . "Registration R WHERE M.RegisterID = R.RegisterID and M.MemberName = '" . addslashes ( $username ) . "'";
	
	$result = $db -> Execute($sql);
	
	if($result -> RecordCount() > 0)
	{		
		//Get the user information
		$userInfo = $result -> FetchNextObj();
		
		//The Username exists in our table
		$password = generate_password();
		$confer = get_conference_info() ;
		//$uri = $_SERVER["SCRIPT_URI"] ;
		$uri = "http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"] ;
		//echo "<!-- $uri -->";
		$replacefrom = ( strpos( $uri , $privilege_root_path ) + strlen ( "/" ) ) ;
		$url = substr_replace ( $uri , "index.php" , $replacefrom ) ;	
		$content = "Dear $userInfo->FirstName $userInfo->MiddleName $userInfo->LastName ,\n\n" .
				   "Here is your new password for $confer->ConferenceName.\n" .
				   "Name: " . stripslashes ( $username ) . "\n" .
				   "Password: ".$password."\n\n".
				   "You may now login at ( " . $url . " ).\n\n" .
				   "We would be happy to listen to your inquiries at our e-mail ( mailto:" . $confer->ConferenceContact . " ).\n\n";
	
		//Create the instance  of mail
		$mail = new Mail();
		
		$mail -> Organization( $confer->ConferenceName );
		$mail -> ReplyTo( $confer->ConferenceContact );
		
		$mail -> From( $confer->ConferenceContact );
		$mail -> To( stripslashes( $userInfo->Email ) );
		$mail -> Subject( $confer->ConferenceCodeName . " Account Information" );
		$mail -> Body( $content );
		$mail -> Send();		
/*		
		//Send the password to the user email account
		$subject = "Web Comment System login password";
		$toaddress = stripslashes ( $userInfo->Email ) ;
//		echo "\$email=" . stripslashes ( $userInfo->Email ) . "<br>\n" ;
//		echo "\$password=" . $password . "<br>\n" ;
		$content = "This is your new password\n\n".
					"User Name: ". stripslashes ( $username ) ."\n".
				   "Password: ".$password."\n\n";
		$fromaddress = "webmaster@webcomment.com";
		
		$result = mail($toaddress,$subject,$content,$fromaddress);
		
		if(!$result)
		{
			$err_message .= "Email sending fail. Please try again. <br>\n";
			return false ;
		}	
		else
*/		{
			//Update the Member Password
			$result = $db -> Execute("UPDATE " . $GLOBALS["DB_PREFIX"] . "Member 
								   SET Password = '" . sha1( $password ) . "'
								   WHERE MemberName = '" . addslashes ( $username ) . "'");
			
			if (!$result)
			{
				$err_message .= "Could not update password in database - please try again later. <br>\n" ;
				return false ;
			}
			else
			{
				return true ;
			}
		}	
	}
	else
	{
		$err_message .= " The Username entered is invalid. <br>\n";
		return false ;
	}
}
?>
