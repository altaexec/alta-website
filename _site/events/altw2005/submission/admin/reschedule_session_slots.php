<?php
$php_root_path = "..";
$privilege_root_path = "/admin" ;
require_once("includes/include_all_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;

// Set colours for slots
$colPaid 	= "00A000";
$colUnpaid 	= "FF8000";
$colNone 	= "FF0000";

// Floating JavaScript
$javascript_header = '
<SCRIPT LANGUAGE="JavaScript">
<!--
floatX=20;
floatY=0;
layerwidth=100;
layerheight=130;
halign="left";
valign="center";
delayspeed=3;

// This script is copyright (c) Henrik Petersen, NetKontoret
// Feel free to use this script on your own pages as long as you do not change it.
// It is illegal to distribute the script as part of a tutorial / script archive.
// Updated version available at: http://www.echoecho.com/toolfloatinglayer.htm
// This comment and the 4 lines above may not be removed from the code.

NS6=false;
IE4=(document.all);
if (!IE4) {NS6=(document.getElementById);}
NS4=(document.layers);

function adjust() {
if ((NS4) || (NS6)) {
if (lastX==-1 || delayspeed==0)
{
lastX=window.pageXOffset + floatX;
lastY=window.pageYOffset + floatY;
}
else
{
var dx=Math.abs(window.pageXOffset+floatX-lastX);
var dy=Math.abs(window.pageYOffset+floatY-lastY);
var d=Math.sqrt(dx*dx+dy*dy);
var c=Math.round(d/10);
if (window.pageXOffset+floatX>lastX) {lastX=lastX+delayspeed+c;}
if (window.pageXOffset+floatX<lastX) {lastX=lastX-delayspeed-c;}
if (window.pageYOffset+floatY>lastY) {lastY=lastY+delayspeed+c;}
if (window.pageYOffset+floatY<lastY) {lastY=lastY-delayspeed-c;}
}
if (NS4){
document.layers[\'floatlayer\'].pageX = lastX;
document.layers[\'floatlayer\'].pageY = lastY;
}
if (NS6){
document.getElementById(\'floatlayer\').style.left=lastX;
document.getElementById(\'floatlayer\').style.top=lastY;
}
}
else if (IE4){
if (lastX==-1 || delayspeed==0)
{
lastX=document.body.scrollLeft + floatX;
lastY=document.body.scrollTop + floatY;
}
else
{
var dx=Math.abs(document.body.scrollLeft+floatX-lastX);
var dy=Math.abs(document.body.scrollTop+floatY-lastY);
var d=Math.sqrt(dx*dx+dy*dy);
var c=Math.round(d/10);
if (document.body.scrollLeft+floatX>lastX) {lastX=lastX+delayspeed+c;}
if (document.body.scrollLeft+floatX<lastX) {lastX=lastX-delayspeed-c;}
if (document.body.scrollTop+floatY>lastY) {lastY=lastY+delayspeed+c;}
if (document.body.scrollTop+floatY<lastY) {lastY=lastY-delayspeed-c;}
}
document.all[\'floatlayer\'].style.posLeft = lastX;
document.all[\'floatlayer\'].style.posTop = lastY;
}
setTimeout(\'adjust()\',50);
}

function define()
{
if ((NS4) || (NS6))
{
if (halign=="left") {floatX=ifloatX};
if (halign=="right") {floatX=window.innerWidth-ifloatX-layerwidth-20};
if (halign=="center") {floatX=Math.round((window.innerWidth-20)/2)-Math.round(layerwidth/2)};
if (valign=="top") {floatY=ifloatY};
if (valign=="bottom") {floatY=window.innerHeight-ifloatY-layerheight};
if (valign=="center") {floatY=Math.round((window.innerHeight-20)/2)-Math.round(layerheight/2)};
}
if (IE4)
{
if (halign=="left") {floatX=ifloatX};
if (halign=="right") {floatX=document.body.offsetWidth-ifloatX-layerwidth-20}
if (halign=="center") {floatX=Math.round((document.body.offsetWidth-20)/2)-Math.round(layerwidth/2)}
if (valign=="top") {floatY=ifloatY};
if (valign=="bottom") {floatY=document.body.offsetHeight-ifloatY-layerheight}
if (valign=="center") {floatY=Math.round((document.body.offsetHeight-20)/2)-Math.round(layerheight/2)}
}
}
//-->
</script>
	';
$homepage -> AddExtraHeadData($javascript_header);

$sessionID = $HTTP_GET_VARS["id"];
if ($HTTP_POST_VARS["Submit"] == "Change")
{
	// Split up NewOrder string
	$new_session_slots = array();
	foreach (explode(";", $HTTP_POST_VARS["NewOrder"]) as $s)
	{
		if ($s == "") continue; // no data, so don't process
		list($sID,$valStr) = explode("=",$s);
		$vals = explode(" ",$valStr);
		foreach ($vals as $key => $value)
		{
			if (!$value) continue;
			$slot = new SessionSlot;
			$slot -> SessionID = $sID;
			$slot -> SlotID    = $key;
			$slot -> PaperID   = $value;
			$new_session_slots[] = $slot;
		}
	}
	// Change data
	update_session_slots($new_session_slots);
}

$settingsInfo = get_Conference_Settings();

$sessions = get_similar_sessions($sessionID);
do_html_header("Rearrange Session Slots" , &$err_message);
?>

<script language="JavaScript" src="script/reschedule.js"></script> 

<form name="myForm" method="post" onsubmit="process(this)">
<table>
<tr>
<td width="80" rowspan="<?php echo count($sessions) + 2 ?>">

<!-- Start of floating controls -->
<script>
if (NS4) {document.write('<LAYER NAME="floatlayer" LEFT="'+floatX+'" TOP="'+floatY+'">');}
if ((IE4) || (NS6)) {document.write('<div id="floatlayer" style="position:absolute; left:'+floatX+'; top:'+floatY+';">');}
</script>
<center>
<input type=button name"Up" value="/\" onclick="moveup(document.myForm.ListBox,-1)">
<br />
<input type=button name"Down" value="\/" onclick="movedown(document.myForm.ListBox,1)">
<br />
<br />
</center>
<input type=submit name="Submit" value="Change">
<script>
if (NS4)
{
document.write('</LAYER>');
}
if ((IE4) || (NS6))
{
document.write('</DIV>');
}
ifloatX=floatX;
ifloatY=floatY;
define();
window.onresize=define;
lastX=-1;
lastY=-1;
adjust();
</script>
<!-- End of floating controls -->

<input type="hidden" name="NewOrder">
<input type="hidden" name="ListBox" id="0">
</td>
<td style="padding-top: 0.5cm ; padding-bottom: 0.5cm">
<?php 
$track = get_track_info($sessions[0] -> TrackID);
$type = get_presentation_info($sessions[0] -> PresentationTypeID);
?>
<b>Track:</b> <?php echo $track -> TrackName ?><br />
<b>Presentation Type:</b> <?php echo $type -> PresentationTypeName ?><br />
</td>
</tr>
<?php 
foreach ($sessions as $session) { 
	$session_slots = get_session_slots_info($session -> SessionID);
?>
<tr><td>
<select style="font-family: monospace" size=<?php echo $session_slots -> MaxSlots ?> name="ListBox" id="<?php echo $session -> SessionID ?>" onchange="clearothers(this, document.myForm.elements[this.name])"  >
<?php
$slots = array();
foreach ($session_slots -> Slots as $slot) { 
	$slots[$slot -> SlotID] = $slot;
	}
for ($i = 1; $i <= $session_slots -> MaxSlots; $i++)
{
	$maxLength = 40;
	$slot = $slots[$i];
	if ($slot)
	{
		$paper = get_paper_info($slot -> PaperID);
		$presenter = get_presenter_info($slot -> PaperID);
		if ($presenter == NULL) {
			$colour = $colNone;
		} else {
			if (has_paid_registration($presenter->RegisterID))
				$colour = $colPaid;
			else
				$colour = $colUnpaid;
		}
		
?>
	<option style="color: #<?php echo $colour ?>" value="<?php echo $paper -> PaperID ?>" >
	<?php 
	// Build ID/Title combo
	$idStr = "#".$paper -> PaperID;
	if (strlen($idStr)+strlen($paper -> Title) + 1 > $maxLength)
	{
		$truncLen = $maxLength - strlen($idStr) - strLen(" ...");
		echo $idStr." ".substr($paper -> Title,0,$truncLen)."...";
	} else 
		echo $idStr." ".$paper -> Title;
	?>
	</option>
<?php } else { ?>
	<option style="color: #808080" value="0" >
	<?php 
	$freeSlotStr = "Free Slot";
	$spaces = $maxLength - strlen($freeSlotStr);
	echo $freeSlotStr.str_repeat("&nbsp;",$spaces);
	?>
	</option>
<?php } ?>
<?php 
} ?>
</select>
</td>
<td style="vertical-align: top ; padding-left: 0.5cm">
	<strong><?php echo $session -> SessionName ?></strong><br />
	<i>
	<?php 
	$room = get_room_info($session -> RoomID);
	echo $room -> RoomName;
	?>
	</i>
	<br />
	
	<?php echo format_date($settingsInfo -> DateFormatShort, $session -> StartTime) ?>
	@
	<?php echo format_date("g:i a", $session -> StartTime) ?>
	to
	<?php echo format_date("g:i a", $session -> EndTime) ?>
	<br />
	
</td>
</tr>
<?php } ?>


</form>

<tr>
<td style="padding-top: 0.5cm">
<ul>
<li style="color: #<?php echo $colPaid ?>" type=disc>
Presenter has paid towards registration
</li>
<li style="color: #<?php echo $colUnpaid ?>" type=circle>
Presenter has not paid any registration
</li>
<li style="color: #<?php echo $colNone ?>" type=square>
Presenter has not been assigned
</li>
</ul>
</td>
</tr>
</table>


<?php
do_html_footer( &$err_message );
?>
