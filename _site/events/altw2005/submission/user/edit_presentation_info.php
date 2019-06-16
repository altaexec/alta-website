<?php
$php_root_path = ".." ;
require_once("$php_root_path/includes/include_all_fns.php");
require_once("$php_root_path/includes/page_includes/page_fns.php");
session_start();

$paper = get_paper_info($HTTP_GET_VARS["paperid"]);
$session_slot = get_session_slot_info($HTTP_GET_VARS["paperid"]);
if ($session_slot)
{
	$session = get_session_info($session_slot -> SessionID);
}
$presentation_type = 
		    get_presentation_info(
			get_presentation_type_for_paper($paper -> PaperID));
$presenter = get_presenter_info($HTTP_GET_VARS["paperid"]);


if ($HTTP_POST_VARS["Submit"]=="Change")
{
	$member = getMemberInfo($HTTP_POST_VARS["MemberName"]);
	if ($member)
	{
		$new_presenter = new Presenter();
		$new_presenter -> PaperID = $HTTP_GET_VARS["paperid"];
		$new_presenter -> RegisterID = $member -> RegisterID;
		$presenter = get_presenter_info($HTTP_GET_VARS["paperid"]);
		if ($presenter)
		{
			update_presenter($new_presenter);
		} else {
			add_presenter($new_presenter); // Needs to be fixed
		}
		autoschedule_waiting_papers();
	} else {
		$err_MemberName = "Invalid username";
	}
	// Refresh variables
	$session_slot = get_session_slot_info($HTTP_GET_VARS["paperid"]);
	$presenter = get_presenter_info($HTTP_GET_VARS["paperid"]);
	if ($session_slot)
	{
		$session = get_session_info($session_slot -> SessionID);
	}
	$presentation_type = 
			get_presentation_info(
				get_presentation_type_for_paper($paper -> PaperID));
}

$member = get_member_info_with_id($presenter -> RegisterID);

do_html_header("Presentation Details" , &$err_message );		
?>
<br />
<table style="margin-left: 10%">
	<tr>
		<td colspan="2">
		<h3>
		#<?php echo $paper -> PaperID ?>
		- <?php echo $paper -> Title ?>
		</h3>
		</td>
	</tr>
	<tr>
	<td colspan="2">
	&nbsp;
	</td>
	</tr>
	<tr>
		<td style="padding-right: 2cm">
		<strong>
		Presenter Username :
		</strong>
		</td>
		<td>
		<form name="frmEdit" method="post" action="edit_presentation_info.php?paperid=<?php echo $HTTP_GET_VARS["paperid"] ?>">
		<input type="text" name="MemberName" value="<?php echo $member -> MemberName ?>">
		<input type="submit" name="Submit" value="Change">
		<input type="reset" value="Undo">
		<font color="FF0000"><?php echo $err_MemberName ?></font>
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<strong>
		Presentation Type :
		</strong>
		</td>
		<td>
		<?php echo $presentation_type -> PresentationTypeName ?>
		</td>
	</tr>
</table>

<?php do_html_footer( &$err_message ); ?>
