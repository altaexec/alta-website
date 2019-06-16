<?php

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
/*
	do_html_header("Edit Phase" , &$err_message ) ;
	echo "This page is under maintainance by Shaggy.<br><br>\n\n" ;
	do_html_footer( &$err_message ) ;
	exit ;
*/
	//Establish connection with database
	$db = adodb_connect();

	if (!$db)
	{
		do_html_header("Edit Phases Failed" , &$err_message) ;
		echo "Could not connect to database server - please try later.";
		do_html_footer(&$err_message);
		exit;
	}

	function redisplay ( &$dbprefix , $err_message = "" )
	{
		global $HTTP_SERVER_VARS ;

		if ( ( $phasesResult = getAllPhases ( &$err_message ) ) === NULL )
		{
			do_html_header("Edit Phases Failed" , &$err_message) ;
			$err_message .= " Could not execute \"getAllPhases\" in \"edit_phases.php\". <br>\n" ;
			$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $HTTP_SERVER_VARS['QUERY_STRING'] . "'>again</a>?" ;
			do_html_footer(&$err_message);
			exit ;
		}

		$array = array() ;
		$r = 0 ;
		while( $phaseInfo = $phasesResult -> FetchNextObj() )
		{
			$array["arrPhaseID"][$r] = $phaseInfo -> PhaseID ;
			$array["arrPhaseName"][$r] = $phaseInfo -> PhaseName ;
			$array["arrStartDate"][$r] = $phaseInfo -> StartDate ;
			$array["arrEndDate"][$r] = $phaseInfo -> EndDate ;
			$array["arrStatus"][$r] = $phaseInfo -> Status ;
			$r++ ;
		}//end of while loop

		return $array ;
	}

//	$confirm = "" ;
	$button = "Submit" ;
	$option = "Undo Changes" ;

	if ( count ( $HTTP_POST_VARS ) > 0 )
	{
		if ( $HTTP_POST_VARS["Submit"] == "Submit" )
		{
			$error_array = array() ;
			$exempt_array = array() ;
			check_form ( $HTTP_POST_VARS , $error_array , &$exempt_array ) ;

			if ( count ( $error_array ) == 0 )
			{
				//Submit Button is pressed
				$result = update_phases($HTTP_POST_VARS["arrPhaseID"],$HTTP_POST_VARS["arrStartDate"],$HTTP_POST_VARS["arrEndDate"]);

				if($result === true)
				{
					header("Location: view_phases.php");
					exit;
				}
				else
				{
					do_html_header("Process Edit Phase Failed" , &$err_message );
					$err_message .= $result ;
				}
			}
			else
			{
				if ( $error_array["arrStartDate"] || $error_array["arrEndDate"] )
				{
					do_html_header("Confirm Edit Phase" , &$err_message );
	//				$confirm = "<input type=\"hidden\" value=\"\" name=\"process\">\n" ;
					$button = "Confirm" ;
					$option = "Edit" ;
				}
				else
				{
					do_html_header("Edit Phase" , &$err_message );
				}
			}
		}
		else if ( $HTTP_POST_VARS["Submit"] == "Confirm" )
		{
			$result = update_phases($HTTP_POST_VARS["arrPhaseID"],$HTTP_POST_VARS["arrStartDate"],$HTTP_POST_VARS["arrEndDate"]);

			if($result === true)
			{
				header("Location: view_phases.php");
				exit;
			}
			else
			{
				do_html_header("Process Edit Phase Failed" , &$err_message );
				$err_message .= $result ;
				$button = "Confirm" ;
				$option = "Edit" ;
			}
		}
		else if ( $HTTP_POST_VARS["Submit"] == "Edit" )
		{
			do_html_header("Edit Phase" , &$err_message );
		}
		else
		{
			do_html_header("Edit Phase" , &$err_message );
			$HTTP_POST_VARS = redisplay ( $GLOBALS["DB_PREFIX"] , &$err_message ) ;
		}
	}
	else
	{
		do_html_header("Edit Phase" , &$err_message );
		$HTTP_POST_VARS = redisplay ( $GLOBALS["DB_PREFIX"] , &$err_message ) ;
	}

?>
<script language="JavaScript" src="script/popcalendar.js"></script>
<form name="frmEdit" method="post" action="edit_phases.php">
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="2" cellpadding="1">
          <tr>
            <td width="20%"><strong>Phase Name</strong></td>
            <td width="30%"><strong>Start Date</strong> </td>
            <td width="30%"><strong>End Date</strong> </td>
            <td width="15%"><strong>Current Status</strong></td>
          </tr>
		  <tr><td colspan="4">&nbsp;</td></tr>
<?php
	$r = count ( $HTTP_POST_VARS["arrPhaseID"] ) ;

	if ( $button == "Submit" )
	{
		for ( $i=0 ; $i < $r ; $i++ )
		{
			$startDate =  $HTTP_POST_VARS["arrStartDate"][$i];
			$endDate = $HTTP_POST_VARS["arrEndDate"][$i];
			if (!$startDate) $startDate = format_date('Y-m-d');
			if (!$endDate) $endDate = format_date('Y-m-d');
?>
          <tr>
		  	<input type="hidden"  name="arrPhaseID[]" value="<?php echo $HTTP_POST_VARS["arrPhaseID"][$i] ; ?>">
            <td><input type="hidden"  name="arrPhaseName[]" value="<?php echo $HTTP_POST_VARS["arrPhaseName"][$i] ; ?>">
			<?php echo $HTTP_POST_VARS["arrPhaseName"][$i] ; ?>
			</td>
            <td>
            <input name="arrStartDate[]" type=text id="arrStartDate[]" value="<?php if ($startDate != '0000-00-00') echo $startDate; else echo date('Y-m-d',time()); ?>" onclick='showCalendar(this, this, "yyyy-mm-dd","en",1)'>
            <br><font color="#FF0000"><?php echo $error_array["arrStartDate"][$i] ; ?></font></td>
            <td>
            <input name="arrEndDate[]" type=text id="arrEndDate[]" value="<?php if ($endDate != '0000-00-00') echo $endDate; else echo date('Y-m-d',time()); ?>" onclick='showCalendar(this, this, "yyyy-mm-dd","en",1)'>
            <br><font color="#FF0000"><?php echo $error_array["arrEndDate"][$i] ; ?></font></td>
            <td><?php echo get_Phase_Status( $HTTP_POST_VARS["arrStatus"][$i] ); ?></td>
          </tr>
		  <tr><td colspan="4">&nbsp;</td></tr>
<?php
		}//end of while loop
	}
	else if ( $button == "Confirm" )
	{
		for ( $i=0 ; $i < $r ; $i++ )
		{
?>
          <tr>
		  	<input type="hidden"  name="arrPhaseID[]" value="<?php echo $HTTP_POST_VARS["arrPhaseID"][$i] ; ?>">
            <td><input type="hidden"  name="arrPhaseName[]" value="<?php echo $HTTP_POST_VARS["arrPhaseName"][$i] ; ?>">
			<?php echo $HTTP_POST_VARS["arrPhaseName"][$i] ; ?>
			</td>
            <td><input name="arrStartDate[]" type="hidden" id="arrStartDate[]" value="<?php echo $HTTP_POST_VARS["arrStartDate"][$i] ; ?>" >
<?php
			echo $HTTP_POST_VARS["arrStartDate"][$i] ;
?>
			</td>
            <td><input name="arrEndDate[]" type="hidden" id="arrEndDate[]" value="<?php echo $HTTP_POST_VARS["arrEndDate"][$i] ; ?>" >
<?php
			echo $HTTP_POST_VARS["arrEndDate"][$i] ;
?>
			</td>
            <td><?php echo get_Phase_Status( $HTTP_POST_VARS["arrStatus"][$i] ); ?></td>
          </tr>
		  <tr><td colspan="4">&nbsp;</td></tr>
<?php
		}

		if ( count ( $error_array ) )
		{
?>
		<tr><td colspan="4"><strong>The following dates are incorrect:</strong></td></tr>
		<tr><td colspan="4"></td></tr>
<?php
			for ( $i=0 ; $i < $r ; $i++ )
			{
				if ( $error_array["arrStartDate"][$i] )
				{
?>
		<tr><td colspan="4">
			<font color="#FF0000"><?php echo $error_array["arrStartDate"][$i] . $HTTP_POST_VARS["arrPhaseName"][$i] . " : Start Date : " . $HTTP_POST_VARS["arrStartDate"][$i] ; ?></font>
		</td></tr>
<?php
				}
				if ( $error_array["arrEndDate"][$i] )
				{
?>
		<tr><td colspan="4">
			<font color="#FF0000"><?php echo $error_array["arrEndDate"][$i] . $HTTP_POST_VARS["arrPhaseName"][$i] . " : End Date : " . $HTTP_POST_VARS["arrEndDate"][$i] ; ?></font>
		</td></tr>
<?php
				}
			}
?>
		<tr><td colspan="4"><strong>Are you sure you want to accept them?</strong></td></tr>
<?php
		}
	}
?>
        </table>
      </td>
  </tr>
  <tr>
      <td>&nbsp;</td>
  </tr>
  <tr>
    <td><input type="submit" name="Submit" value="<?php echo $button ; ?>"> <input name="Submit" type="submit" id="Submit" value="<?php echo $option ; ?>"></td>
  </tr>
</table>
</form>

<?php

do_html_footer( &$err_message );

?>
