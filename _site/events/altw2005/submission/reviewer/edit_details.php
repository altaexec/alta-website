<?php 

//////////// REVIEWER PHASE 1 ///////////////
//////////// REVIEWER PHASE 2 ///////////////
//////////// REVIEWER PHASE 3 ///////////////
//////////// REVIEWER PHASE 4 ///////////////	

$php_root_path = ".." ;
$privilege_root_path = "/reviewer" ;
// extract ( $HTTP_POST_VARS , EXTR_REFS ) ;

require_once("includes/include_all_fns.php");
require_once("$php_root_path/includes/page_includes/page_fns.php");
session_start() ;
// extract ( $_SESSION , EXTR_REFS ) ;

$err_message = " Unable to process your request due to the following problems: <br>\n" ;
$header = "Edit Personal Details" ;
$accepted_privilegeID_arr = array ( 2 => "" ) ;
$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;	

if ($HTTP_POST_VARS["submit"] == "Undo Changes")
{
	//Redirect to the same page to undo changes
	$str = "Location: edit_details.php";
	header($str);
	exit;
}

$error_array = array() ;
$exempt_array = get_user_details_form_exemptions();

check_form( $HTTP_POST_VARS , $error_array , &$exempt_array ) ;

if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
{
	if($HTTP_POST_VARS["submit"] == "Update")
	{		
		if ( $result = update_details( $HTTP_POST_VARS, &$err_message ) )
		{		
			do_html_header("Updating Personal Details Successful" , &$err_message );
			echo " Your personal information was updated successfully. <br><br><a href='edit_details.php'>View</a> your updated details?<br>\n" ;
			do_html_footer( $err_message );
			exit ;			
		}
		else
		{
			do_html_header("Updating Personal Details Failed..." , &$err_message );
			$err_message .= "<br><br> Try <a href='edit_details.php'>again</a>?<br>\n" ;
		}
	}
}
else 
{
	if ( count ( $HTTP_POST_VARS ) == 0 )
	{	
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
		if (!$db)
		{
			$homepage->showmenu = 0 ;		
			do_html_header("Edit Personal Details Failed" , &$err_message );	
			$err_message .= " Could not connect to database server - please try later. <br>\n" ;
			$err_message .= "<br><br> Try <a href='edit_details.php'>again</a>?" ;
			do_html_footer(&$err_message);
			exit;
		}
		
			
		
		$result = $db -> Execute("SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M, " . $GLOBALS["DB_PREFIX"] . "Registration R 
							   WHERE M.RegisterID = R.RegisterID 
							   and M.MemberName = " . db_quote($db, $_SESSION["valid_user"]));	
	
		if(!$result)
		{
			$homepage->showmenu = 0 ;		
			do_html_header("Edit Personal Details Failed" , &$err_message );	
			$err_message .= " Cannot retrieve information from database <br>\n" ;					 
			$err_message .= "<br><br> Try <a href='edit_details.php'>again</a>?" ;
			do_html_footer(&$err_message);
			exit;
		}
		
		if($result -> RecordCount() == 0)
		{
			$homepage->showmenu = 0 ;		
			do_html_header("Edit Personal Details Failed" , &$err_message );	
			$err_message .= " The user is invalid <br>\n";
			$err_message .= "<br><br> Try <a href='edit_details.php'>again</a>?" ;
			do_html_footer(&$err_message);
			exit;
		}
		
		$info = $result -> FetchRow();
		
        foreach (get_user_details_field_map() as $key => $value)
        {
            $HTTP_POST_VARS[$value] = stripslashes ( $info[$key] ) ;
        }
	}
	
	do_html_header("Edit Personal Details" , &$err_message );
}

?>

<form name="frmRegister" method="post" action="edit_details.php">
        
  <table width="80%" border="0" cellspacing="0" cellpadding="0">
    <?php get_user_details_form($HTTP_POST_VARS, $error_array) ?>
    <tr> 
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top">&nbsp;</td>
      <td><input type="submit" name="submit" value="Update"> &nbsp; 
	  <input name="submit" type="submit" value="Undo Changes"></td>
    </tr>
  </table>
     </form>
<?php 

do_html_footer(&$err_message);

?>
