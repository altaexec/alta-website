<?php

/*
JEdit Editor Preferences
:tabSize=4:indentSize=4:noTabs=false:wrap=soft
:maxLineLen=120:folding=explicit:collapseFolds=1:
*/

$php_root_path = ".." ;
require_once("$php_root_path/includes/include_all_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;
$header = "Registration Form" ;
$accepted_privilegeID_arr = array ( 1 => "" ) ;
$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;	

// Retrieve the setting information
$settingInfo = get_Conference_Settings();

if (array_key_exists('submit', $HTTP_POST_VARS))
{
	do_html_header("Registration Statement" , &$err_message );
	
	$exclusionList = array('submit' => 'Submit');
	$selectionList = array_diff_assoc($HTTP_POST_VARS, $exclusionList);
	$selectionXml = get_selection_xml($selectionList);
	
	$htmlStatement = get_registration_statement($selectionXml);
?>
	<form action="lodge_payment_form.php" method="post">
	<div style="width: 98% ; margin: 1%">
		<?php
		// Output the resulting HTML
		echo $htmlStatement;
		?>
	</div>
	<input type="hidden" name="xml" value="<?php echo htmlentities($selectionXml); ?>" />
	<input type="submit" name="submit" value="Confirm Form" />
	</form>
	
<?
} else {
	do_html_header("Registration Form" , &$err_message );
	
	$htmlForm = get_registration_form();
?>
	<div style="padding-top: 20">
		<?php echo $settingInfo->RegPreamble?>
	</div>
	<div style="padding-top: 20">
		<form action="payment_form.php" method="post">
		<?php echo $htmlForm?>
		<input type="submit" name="submit" value="Submit">
		</form>
	</div>
<?php
}

do_html_footer( &$err_message );
?>
