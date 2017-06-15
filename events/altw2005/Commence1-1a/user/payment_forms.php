<?php

$php_root_path = ".." ;
require_once("$php_root_path/includes/include_all_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;
$header = "Registration Forms" ;
$accepted_privilegeID_arr = array ( 1 => "" ) ;
$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;	

$memberInfo = getMemberInfo($_SESSION["valid_user"]);
if ($memberInfo == null) exit;

if (has_paid_registration($memberInfo->RegisterID))
{
	$paid = get_paid_registration($memberInfo->RegisterID);
	do_html_header("Paid Registration - #".$paid->FormID , &$err_message );
?>
<div style="padding: 20">
	<?php echo get_registration_statement($paid->Form) ?>
</div>
<?php
} else {
	do_html_header($header , &$err_message );
	$forms = retrieve_selection_xml_for_registerid($memberInfo->RegisterID);
	if (count($forms) > 0)
	{
?>
		<p>
		Previously filled forms:
		<ul>
<?php
		foreach ($forms as $form)
		{
			?>
			<li>
			<a href="printable_statement.php?formid=<?php echo $form->FormID?>">
			<?php echo $form->FormID?>
			</a>
			</li>
			<?php
		}
?>
		</ul>
		</p>
<?php
	}
?>
	<a href="payment_form.php">Fill out new form</a>
<?php
}
do_html_footer( &$err_message );
?>
