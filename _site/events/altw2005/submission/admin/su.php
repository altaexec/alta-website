<?php

$php_root_path = "..";
$privilege_root_path = "/admin" ;
require_once("includes/include_all_fns.php");
session_start();
	
$error_array = array() ;
$exempt_array = array ( "logpassword" ) ;
$err_message = " Unable to process your request due to the following problems: <br>\n" ;
// do_html_header("Change User" , &$err_message );  // Causes redirect errors
		
//Check for Administrator privileges, exit otherwise

check_form ( $HTTP_POST_VARS , $error_array , &$exempt_array ) ; 

// connect to db
$db = adodb_connect( &$err_message );

if (!$db){
    echo "Could not connect to database server - please try later.";		
    exit;
}
$phasesSQL = " SELECT PhaseID, PhaseName FROM ".$GLOBALS["DB_PREFIX"]."ConferencePhase";
$phasesResult = $db -> Execute($phasesSQL);
    
if(!$phasesResult){
    echo "Could not retrieve the phase information - please try again later.";
    exit;
}

if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
{
	if (!$db)
	{
		$homepage->showmenu = 0 ;	
		do_html_header("Change User" , &$err_message );
		do_html_header("Login Failed");	
		$err_message .= " Unable to connect to database. <br>\n" ;
	}	
	else if ( $PrivilegeTypeID = suLogin( $HTTP_POST_VARS["username"], $HTTP_POST_VARS["logpassword"] , &$err_message ) )
	{
		$adminUser = $_SESSION["valid_user"] ; // Record current user for later
		//unset ( $_SESSION["valid_user"] ) ; // Logout as Administrator
		// unset ( $_SESSION ) ;
		//$result_dest = session_destroy();
		//session_start() ;			// Begin Login as new user
		// if they are in the database register the user id
		$_SESSION["valid_user"] = $HTTP_POST_VARS["username"] ;
		$_SESSION["phase"] ;
		$_SESSION["real_user"] = $adminUser ;
		
		$newPhaseID = $HTTP_POST_VARS["newPhaseID"];

		if ( !check_conference_phase( &$err_message, $PrivilegeTypeID, $newPhaseID ) )
		{
			$homepage->showmenu = 0 ;		
			// do_html_header("Change User" , &$err_message );
			do_html_header("Login Failed");	
			unset ( $_SESSION["valid_user"] ) ;
			$err_message .= " Unable to connect to conference database. <br>\n" ;					
		}
		else
		{		
			switch ( $PrivilegeTypeID )
			{
				case 1:
				{
					$str = "Location: $php_root_path/user/view_papers.php";
					header( $str ); // Redirect browser
					exit; // Make sure that code below does not get executed when we redirect. 					
					break ;
				}
				case 2:
				{	

					$str = "Location: $php_root_path/reviewer/reviewer_home.php";
					header( $str ); // Redirect browser
					exit; // Make sure that code below does not get executed when we redirect. 					
					break ;
				}
				case 3:
				{
					$str = "Location: $php_root_path/admin/admin_home.php";
					header( $str ); // Redirect browser
					exit; // Make sure that code below does not get executed when we redirect. 
					break ;
				}
				default :
				{
					$homepage->showmenu = 1 ;				
					// do_html_header("Change User" , &$err_message );
					do_html_header("Login Failed" , &$err_message );
					$err_message .= " Unknown User's PrivilegeTypeID. <br>\n" ;
					break ;
				}			
			}
		}
///////////////// Debug //////////////
//		echo gettype( $_SESSION["phase1"] ) . "<br>\n" ;	// 4.1.1
//		echo gettype( $_SESSION["phase2"] ) . "<br>\n" ;	// 4.1.1
//		echo gettype( $_SESSION["phase"]3 ) . "<br>\n" ;	// 4.0.6
//		echo "Login phaseID 1: " . $_SESSION["phase1"]->phaseID . "<BR>\n" ;	// 4.1.1
//		echo "Login phaseID 2: " . $_SESSION["phase2"]->phaseID . "<BR>\n" ;	// 4.1.1
//		echo "Login phaseID: " . $_SESSION["phase"]->phaseID . "<br>\n";	// 4.0.6		
//////////////////////////////////////
	}  
	else
	{
		// unsuccessful login
		$homepage->showmenu = 1 ;		
		// do_html_header("Change User" , &$err_message );
		do_html_header("Login Failed" , &$err_message );
		$err_message .= " Please re-enter your username and password. <br>\n" ;
	}      
}
else
{
	$homepage->showmenu = 1 ;
	// do_html_header("Change User" , &$err_message );
	do_html_header("Login to Another Account" , &$err_message );
}
?>


  <form action="su.php" method="post" name="loginForm" id="loginForm">
  <table border="0" cellpadding="0" cellspacing="0">
      <tr> 
      </tr>


    <tr> 
      <td>UserName &nbsp;</td><td>
      <input name="username" type="text" id="username" value="<?php echo $HTTP_POST_VARS["username"] ; ?>" size="25" maxlength="50"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["username"][0] . "</font>" ?></td>
    </tr>

	 <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    </tr>
	<tr> 
	<td></td>
      <td ><strong> Select Phase:</strong></td>
      
    </tr>

	  <?php for($i=0;$i < $phasesResult -> RecordCount();$i++){
		  
		  	$phaseInfo = $phasesResult -> FetchNextObj();
		  ?>
          <tr> 
            <td></td><td align="left"> <input name="newPhaseID" type="radio" value="<?php echo $phaseInfo -> PhaseID; ?>" 
			<?php if($arrPhaseInfo["newPhaseID"] == $phaseInfo -> PhaseID) echo "checked"; else if($i== 0) echo "checked";?>>
			
			<?php echo $phaseInfo -> PhaseName; ?>
			</td>         
          </tr>
          <?php } /*end of while loop*/ ?>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Submit"> <input type="reset" name="reset" value="Reset"></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    </table>
</form>	
<?php

do_html_footer( &$err_message );

?>
