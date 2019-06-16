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

if (! array_key_exists('xml',$HTTP_POST_VARS) ) exit;
$selectionXml = html_entity_decode($HTTP_POST_VARS['xml']);

// Retrieve the member information
$memberInfo = getMemberInfo($_SESSION["valid_user"]);
if ($memberInfo == null) exit;

// Retrieve the setting information
$settingInfo = get_Conference_Settings();

$formID = store_selection_xml($memberInfo->RegisterID, $selectionXml, &$err_message);

do_html_header($header, &$err_message);
?>
<div style="padding-top: 20">
<p>
Your registration form has been lodged as <b>#<?php echo $formID?></b>.
</p>

<div>
<?php echo stripslashes($settingInfo -> RegFinalInstruct); ?>
</div>

<p>
<a href="payment_forms.php">View Completed Registration Forms</a>
</p>
</div>
<?php
do_html_footer(&$err_message);
?>
