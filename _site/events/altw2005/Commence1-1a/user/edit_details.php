<?php
$php_root_path = ".." ;
require_once("$php_root_path/includes/include_all_fns.php");
require_once("$php_root_path/includes/page_includes/page_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;
$header = "Edit Personal Details" ;
$accepted_privilegeID_arr = array ( 1 => "" ) ;
$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;

$error_array = array() ;
$exempt_array = get_user_details_form_exemptions();

check_form ( $HTTP_POST_VARS , $error_array , &$exempt_array ) ;

// echo dump_array($error_array);
if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
{	
	if($HTTP_POST_VARS["submit"] == "Update")
	{	
		if ( $result = update_details( $HTTP_POST_VARS, &$err_message ) )
		{		
			do_html_header("Personal Details Updated Successfully" , &$err_message );
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
	else if($HTTP_POST_VARS["submit"] == "Undo Changes")
	{
		//Redirect to the same page to undo changes
		$str = "Location: edit_details.php";
		header($str);
		exit;
	}
}
else 
{
	if ( count ( $HTTP_POST_VARS ) == 0 )
	{	
		$db = adodb_connect( &$err_message );
		
		if (!$db)
		{
			do_html_header("Edit Personal Details Failed" , &$err_message );	
			$err_message .= " Could not connect to database server - please try later. <br>\n" ;
			$err_message .= "<br><br> Try <a href='edit_details.php'>again</a>?" ;
			do_html_footer(&$err_message);
			exit;
		}
		
			
		
		$result = $db -> Execute("SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M, " . $GLOBALS["DB_PREFIX"] . "Registration R 
							   WHERE M.RegisterID = R.RegisterID 
							   and M.MemberName = '" . addslashes ( $_SESSION["valid_user"] ) . "' ");	
	
		if(!$result)
		{
			do_html_header("Edit Personal Details Failed" , &$err_message );	
			$err_message .= " Cannot retrieve information from database <br>\n" ;					 
			$err_message .= "<br><br> Try <a href='edit_details.php'>again</a>?" ;
			do_html_footer(&$err_message);
			exit;
		}
		
		if($result -> RecordCount() == 0)
		{
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

	do_html_footer( &$err_message );
?>
