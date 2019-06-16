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

$conferenceInfo = get_conference_info();

$memberInfo = getMemberInfo($_SESSION["valid_user"]);
if ($memberInfo == null) exit;

if (!array_key_exists('formid', $HTTP_GET_VARS))
{
	header( 'Location: payment_forms.php' );
	exit;
}

$selectionXml = retrieve_selection_xml($HTTP_GET_VARS['formid'], &$err_message);

if ($selectionXml == null)
{
	header( 'Location: payment_forms.php' );
	exit;
}

header('Content-Type: text/html; charset=utf-8');
$htmlStatement = get_registration_statement($selectionXml->Form);
?>
<html>
<head>
<title></title>
</head>
<body>
	<h1><?php echo $conferenceInfo->ConferenceName?> Registration</h1>
	<h2>#<?php echo $HTTP_GET_VARS['formid']?></h2>
	<div>
		<div style="font-weight: bold">
			<?php echo $memberInfo->FirstName?>
			<?php echo $memberInfo->MiddleName?>
			<?php echo $memberInfo->LastName?>
			<i>(<?php echo $memberInfo->MemberName?>)</i>
		</div>
		<div style="font-weight: bold">
			<?php echo $memberInfo->Organisation?>
		</div>
		<div>
			<span style="font-weight: bold">Ph:</span>
			<span><?php echo $memberInfo->PhoneNumber?></span>
		</div>
		<div>
			<span style="font-weight: bold">Email:</span>
			<span><?php echo $memberInfo->Email?></span>
		</div>
	</div>
	<div>
		<?php
		// Output the resulting HTML
		echo $htmlStatement;
		?>
	</div>
</body>
</html>

