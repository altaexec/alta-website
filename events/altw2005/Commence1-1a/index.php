<?php

$php_root_path = ".";

require_once("$php_root_path/includes/include_all_fns.php");
global $homepage ;	
$error_array = array() ;
$exempt_array = array() ;
$err_message = " Unable to process your request due to the following problems: <br>\n" ;

check_form( $HTTP_POST_VARS , $error_array , &$exempt_array ) ;

if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
{
	// connect to db
	$db = adodb_connect( &$err_message );
	if (!$db)
	{
		$homepage->showmenu = 0 ;	
		do_html_header("Login Failed");	
		$err_message .= " Unable to connect to database. <br>\n" ;
	}	
	else if ( $PrivilegeTypeID = login( $HTTP_POST_VARS["username"], $HTTP_POST_VARS["logpassword"] , &$err_message ) )
	{
		session_start();		
		// if they are in the database register the user id
		$_SESSION["valid_user"] = $HTTP_POST_VARS["username"] ;
//		$_SESSION["phase"] ;	// 4.0.6
		$_SESSION["phase"] ;
//		session_register("valid_user");
//		echo "<br>\nSession: " . $_SESSION["valid_user"] . "<br>\n" ;
		
		if ( !check_conference_phase( &$err_message, $PrivilegeTypeID ) )
		{
			$homepage->showmenu = 0 ;		
			do_html_header("Login Failed");	
//			session_unregister("valid_user");
			unset ( $_SESSION["valid_user"] ) ;
			$err_message .= " Unable to connect to conference database. <br>\n" ;					
		}
		else
		{		
//			session_register("phase") ;
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
//					if ( !session_is_registered ( "s_reviewer" ) )
//					{
//						session_register ( "s_reviewer" ) ;
//					}					
//					$_SESSION["s_reviewer"] = array() ;
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
					$homepage->showmenu = 0 ;				
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
		$homepage->showmenu = 0 ;		
		do_html_header("Login Failed" , &$err_message );
		$err_message .= " Please re-enter your username and password. <br>\n" ;
//			echo $err_message . "<br><br> Try <a href='index.php'>again</a>?" ;
//      		do_html_footer();
	}      
}
else
{
	$homepage->showmenu = 0 ;
	//Call the function to get the conference information
	// $confName = $conferenceInfo -> ConferenceCodeName;
	$conferenceInfo = get_conference_info();		
	do_html_header("Online Submission and Reviewing" , &$err_message );
}

?>

  <form action="index.php" method="post" name="loginForm" id="loginForm">
  <table width="80%" border="0" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="24" colspan="2">The submission deadline and review
process has now passed.  If your submission was accepted, you can log
in now to upload your camera ready submission.</td>
    </tr>
<!--      <td height="24" colspan="2"><a href="<?php echo $php_root_path ; ?>/user/registration.php">First submission or want to register your interest? Sign 
        up for an account.</a></td>
    </tr>
    <tr> 
      <td height="20" colspan="2">Hint: Use your email address as UserName.</td>
    </tr> -->
    <tr> 
      <td width="20%">&nbsp;</td>
      <td width="80%" height="24">&nbsp;</td>
    </tr>
    <tr> 
      <td>UserName</td>
      <td><input name="username" type="text" id="username" value="<?php echo $HTTP_POST_VARS["username"] ; ?>" size="25" maxlength="50"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["username"][0] . "</font>" ?></td>
    </tr>
    <tr> 
      <td>Password</td>
      <td><input name="logpassword" type="password" id="logpassword2" value="<?php echo $HTTP_POST_VARS["logpassword"] ; ?>" size="25" maxlength="50"> 
        <?php echo "<font color=\"#FF0000\">" . $error_array["logpassword"][0] . "</font>" ?></td>
    </tr>
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
    <tr> 
      <td colspan="2">If you have forgotten your password,<a href="<?php echo $php_root_path ; ?>/user/forget_pwd.php">
        click here.</a></td>
    </tr>
    <tr> 
      <td colspan="2">If you have any problems or questions about this
system please contact the <a href="mailto:workshop@alta.asn.au">ALTA
Workshop 2005 organisers</a>.</td>
    </tr>
  </table>
</form>	
<?php

do_html_footer( &$err_message );

?>
