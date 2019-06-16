<?php
$php_root_path = "..";
$privilege_root_path = "/admin" ;
require_once("includes/include_all_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;

$sessions = get_sessions($HTTP_GET_VARS["sortby"],$HTTP_GET_VARS["desc"]);


$settingsInfo = get_Conference_Settings();

do_html_header("Conference Sessions" , &$err_message);
?>
<a href="session.php">Add new session</a>
<table width="100%" border=0>
	<tr><td colspan=3><hr /></td></tr>
	<tr>
		<td width="10%" style="text-align: center"><strong>ID</strong></td>
		<td colspan=2>
		Group by: 
		<?php 
		$sorts = array(
			"SessionID" => "ID",
			"SessionName" => "Name",
			"TrackID" => "Track",
			"StartTime" => "Start Time",
			"RoomID" => "Room",
			"ChairID" => "Chairperson"
			);
		$firstField = true;
		foreach ($sorts as $fieldName => $fieldDesc) {
			if ($firstField) $firstField = false;
			else echo "&nbsp;|&nbsp;";
		?>
		<a href="sessions.php?sortby=<?php echo $fieldName ?>&desc=1">
		<img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0>
		</a>
		<?php echo $fieldDesc ?>
		<a href="sessions.php?sortby=<?php echo $fieldName ?>&desc=0">
		<img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0>
		</a>
		<?php } ?>
		</td>
	</tr>
	<tr><td colspan=3><hr /></td></tr>
<?php

foreach ($sessions as $session)
{
	$track = get_track_info($session -> TrackID);
	$type = get_presentation_info($session -> PresentationTypeID);
	$sessionSlotsInfo = get_session_slots_info($session -> SessionID);
	$chairInfo = get_member_info_with_id($session -> ChairID);
	$room = get_room_info($session -> RoomID);
?>
<tr>
	<td style="text-align: center; vertical-align: text-top">
	#<?php echo $session -> SessionID ?>
	</td>
	<td>
	<strong><?php echo $session -> SessionName ?></strong><br />
	<i>
	<?php echo $track -> TrackName ?> - 
	<?php echo $type -> PresentationTypeName ?><br />
	</i>
	<strong>Chair:</strong>
	<?php 
	$fullName = getMemberFullName($chairInfo->MemberName); echo $fullName
	?>
	<br />
	
	<strong>Start Time : </strong>
	<?php echo format_date($settingsInfo -> DateFormatShort, $session -> StartTime) ?>
	@
	<?php echo format_date("g:i a", $session -> StartTime) ?>
	<br />
	
	<strong>Finish Time : </strong>
	<?php echo format_date($settingsInfo -> DateFormatShort, $session -> EndTime) ?>
	@
	<?php echo format_date("g:i a", $session -> EndTime) ?>
	<br />
	
	<strong>Room : </strong>
	<?php echo $room -> RoomName ?>
	<br />
	
	<strong>Slots : </strong>
	<?php echo count($sessionSlotsInfo -> Slots)." / ".($sessionSlotsInfo -> MaxSlots); ?>
	</td>
	<td>
	<a href="session.php?id=<?php echo $session -> SessionID ?>">
	Edit Session Details
	</a><br />
	<a href="session.php?id=<?php echo $session -> SessionID ?>&delete=1">
	Delete Session
	</a><br />
	<a href="reschedule_session_slots.php?id=<?php echo $session -> SessionID ?>">
	Manually Reschedule Session Slots
	</a><br />
	</td>
</tr>
<tr><td colspan=3><hr /></td></tr>
<?php 
} ?>
</table>

<?php 
do_html_footer(&$err_message); 
?>
