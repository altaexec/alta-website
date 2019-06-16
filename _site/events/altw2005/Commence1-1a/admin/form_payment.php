<?php
$php_root_path = "..";
$privilege_root_path = "/admin" ;
require_once("includes/include_all_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;

if (array_key_exists("formid",$HTTP_GET_VARS))
{
	$selectionXmlObj = retrieve_selection_xml($HTTP_GET_VARS["formid"]);
	if ($selectionXmlObj != null)
	{
		if (array_key_exists("paid",$HTTP_POST_VARS))
		{
			set_payment_status($selectionXmlObj->FormID,$HTTP_POST_VARS["paid"],&$err_message);
			$selectionXmlObj = retrieve_selection_xml($HTTP_GET_VARS["formid"]);
		}
		$memberInfo = get_member_info_with_id($selectionXmlObj->RegisterID);
		do_html_header("Form #".$HTTP_GET_VARS["formid"],&$err_message);
		$paidSelectors = array();
		$paidSelector["Paid"] = ($selectionXmlObj->Paid) ? 'checked' : '';
		$paidSelector["Unpaid"] = (!$selectionXmlObj->Paid) ? 'checked' : '';
?>
		<div>
		<form action="form_payment.php?formid=<?php echo $HTTP_GET_VARS["formid"]?>" method="post">
			<div style="padding: 5">
			This form is currently marked as 
			<span style="font-weight: bold; color: <?php echo ($selectionXmlObj->Paid)?'green':'red'?>">
			<?php echo ($selectionXmlObj->Paid)?'paid':'unpaid'?>
			</span>.
			Change this to :
			</div>
			<div style="padding: 5">
			<input name="paid" type="radio" value="1" <?php echo $paidSelector["Paid"]?>>
				Paid
			</input>
			<br/>
			<input name="paid" type="radio" value="0" <?php echo $paidSelector["Unpaid"]?>>
				Unpaid
			</input>
			</div>
			<div style="padding: 5">
			<input type="submit" name="submit" value="Change"/>
			</div>
		</form>
		</div>
		<div style="width: 98% ; margin: 1%">
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
				<?php echo get_registration_statement($selectionXmlObj->Form)?>
			</div>
		</div>
<?php
		do_html_footer(&$err_message);
		exit;
	} else {
		$err_message .= "Form with given FormID does not exist.";
	}
}
do_html_header("Enter FormID Number",&$err_message);
?>
<div style="padding: 20; text-align: center">
<form action="form_payment.php" method="get">
	<input type="text" name="formid"></input>
	<input type="submit" name="submit" value="Get Details">
</form>
</div>
<?php
do_html_footer(&$err_message);
?>
