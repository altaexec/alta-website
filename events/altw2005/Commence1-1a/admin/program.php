<?php
$php_root_path = "..";
$privilege_root_path = "/admin" ;
require_once("includes/include_all_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;

$sessions = get_sessions("StartTime", false);

$settingsInfo = get_Conference_Settings();

do_html_header("Conference Program" , &$err_message);

$days = array();
$tracks = get_tracks();
$trackIDs = array();
foreach ($tracks as $track)
	$trackIDs[$track -> TrackID] = array();
foreach ($sessions as $session)
{
	$date = format_date('Y-m-d',$session -> StartTime);
	if (!$days[$date])
		$days[$date] = $trackIDs;
	$days[$date][$session -> TrackID][] = $session;
}

?>
<table width="100%" style="border-collapse: collapse" >
<?php
foreach ($days as $date => $track_sessions)
{
	?>
	<tr>
	<td style="padding-top: 20 ; border-bottom: solid medium black ">
	<strong>
	<?php echo format_date($settingsInfo -> DateFormatLong,$date);  ?>
	</strong>
	</td>
	</tr>
	<?php
	
	// Track headings
	?><tr><?php
	foreach ($tracks as $track)
	{
		?>
		<td style="border-bottom: solid thin black ">
		<b>
		<?php echo $track -> TrackName ?>
		</b>
		</td>
		<?php
	}
	?></tr><?php
	
	// Sessions
	?><tr><?php
	foreach ($track_sessions as $track => $sessions)
	{
		?><td style="vertical-align: top"><?php
		foreach ($sessions as $index => $session)
		{
			// Print session info, and if not last, underline
			?>
			<div style="	padding-top: 10 ; 
							padding-bottom: 10 ;
							padding-left: 5;
							padding-rigt:5;
							<?php if ($index + 1 < count($sessions)) { ?>
							border-bottom: dashed thin black
							<?php } ?>
							">
			<b>
			<?php echo $session -> SessionName; ?>
			</b>
			<br>
			<i>
			<?php 
				$chairInfo = get_member_info_with_id($session -> ChairID);
				$fullName = getMemberFullName($chairInfo->MemberName);
				echo "&nbsp;Chair: $fullName"; 
			?>
			<br>
			<?php echo "&nbsp;".format_date('g:i a',$session -> StartTime) ?> - 
			<?php echo format_date('g:i a',$session -> EndTime) ?>,
			<?php 
			$room = get_room_info($session -> RoomID); 
			echo $room -> RoomName;
			?>
			<br />
			
			</i>
			<table>
			<?php 
			$session_slots = get_session_slots_info($session -> SessionID);
			foreach ( $session_slots -> Slots as $slot ) {
				?><tr><?php
				$paper = get_paper_info($slot -> PaperID);
				echo "<td valign=\"top\" align=\"right\"><b>".$paper -> PaperID."</b></td>";
				echo "<td>".$paper -> Title."</td>";
				?></tr><?php
			}
			?>
			</table>
			</div>
			<?php
		}
		?></td><?php
	}
	?></tr><?php
}
?>
</table>
<?php
do_html_footer(&$err_message); 
?>
