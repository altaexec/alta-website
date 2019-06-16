<?php
$php_root_path = "..";
$privilege_root_path = "/admin" ;
require_once("includes/include_all_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;

if ($HTTP_POST_VARS["Submit"])
{
	if ($HTTP_POST_VARS["Submit"] == "Submit")
	{
		if ($HTTP_POST_VARS["PresentationTypeName"] != "")
		{
			$pt = new PresentationType;
			$pt -> PresentationTypeID = $HTTP_POST_VARS["PresentationTypeID"];
			$pt -> PresentationTypeName = $HTTP_POST_VARS["PresentationTypeName"];
			$pt -> SlotLength = $HTTP_POST_VARS["SlotLength"];
			if ($HTTP_POST_VARS["PresentationTypeID"])
			{
				$prevInfo = get_presentation_info($pt -> PresentationTypeID);
				// Update changed presentation types
				update_presentation_type($pt, &$err_message);
				// If the slot length has been increased, some may not fit
				if ($prevInfo -> SlotLength < $pt -> SlotLength)
					resolve_slot_length_change($pt -> PresentationTypeID);
			} else {
				// Add a new presentation type if given
				add_new_presentation_type($pt, &$err_message);
			}
		}
	}
	else if ($HTTP_POST_VARS["Submit"] == "Confirm")
	{
		delete_presentation_type($HTTP_POST_VARS["PresentationTypeID"]);
	}
}

$types = get_presentation_types();

?>
<?php
if ($HTTP_GET_VARS["add"]){
	do_html_header("Add Presentation Type" , &$err_message) ;
	?>
<!-- Start add section -->
<form name="frmEdit" method="post" action="presentation_types.php">
<table style="padding-top: 0.5cm; padding-bottom: 0.5cm">
<tr>
<td width="200">
Presentation Type Name:
</td>
<td>
<input name="PresentationTypeName" type="text" size="40" maxlength="50" value=""> 
</td>
</tr>
<tr>
<td>
Slot Length (in minutes):
</td>
<td>
<input name="SlotLength" type="text" size="5" maxlength="5" value="30"> 
</td>
</tr>
</table>
<div>
<input type="submit" name="Submit" value="Submit">
<input type="reset" name="Reset" value="Undo Changes">
</div>
</form>
<div style="text-align: right">
<a href="presentation_types.php">Back</a>
</div>
<hr />
<!-- Finish add section -->
	
	<?php
} else if ($HTTP_GET_VARS["edit"])
{
	do_html_header("Edit Presentation Type" , &$err_message) ;
	$type = get_presentation_info($HTTP_GET_VARS["edit"]);
	?>
<!-- Start edit section -->
<form name="frmEdit" method="post" action="presentation_types.php">
		
Tip: For all types of presentation, the maximum number of papers is always given by the session duration divided by the slot length. 
	 For example, a slot length of 5 minutes will allow scheduling of up to 24 posters in a 2 hr session.<p>
		 
<table style="padding-top: 0.5cm; padding-bottom: 0.5cm">
<tr>
<td width="200">
Presentation Type Name:
</td>
<td>
<input name="PresentationTypeID" type="hidden" value="<?php echo $type -> PresentationTypeID ?>">
<input name="PresentationTypeName" type="text" size="40" maxlength="50" value="<?php echo $type -> PresentationTypeName; ?>"> 
</td>
</tr>
<tr>
<td>
Slot Length (in minutes):
</td>
<td>
<input name="SlotLength" type="text" size="5" maxlength="5" value="<?php echo $type -> SlotLength; ?>"> 
</td>
</tr>
</table>
<div>
<input type="submit" name="Submit" value="Submit">
<input type="reset" name="Reset" value="Undo Changes">
</div>
</form>
<div style="text-align: right">
<a href="presentation_types.php">Back</a>
</div>
<hr />
<!-- Finish edit section -->
	<?php
} else if ($HTTP_GET_VARS["delete"]) {
	do_html_header("Delete Presentation Type" , &$err_message) ;
	$type = get_presentation_info($HTTP_GET_VARS["delete"]);
	$sessions = sessions_using_presentation_type($type -> PresentationTypeID);
	if (count($sessions) > 0)
	{
?>
<!-- Start "cannot delete" -->
<div style="padding-top: 0.5cm; padding-bottom: 0.5cm">
Cannot delete "<?php echo $type -> PresentationTypeName ?>" because the 
following sessions are still using it:
</div>
<div>
<table>
<?php foreach ($sessions as $session) { ?>
<tr>
<td><b>#<?php echo $session -> SessionID ?></b></td>
<td><?php echo $session -> SessionName ?></td>
</tr>
<?php } ?>
</table>
</div>
<div style="text-align: right">
<a href="presentation_types.php">Back</a>
</div>
<hr />
<!-- Finish "cannot delete" -->
	<?php
	} else {
	?>
<!-- Start delete section -->
<form name="frmEdit" method="post" action="presentation_types.php">
<div style="padding-top: 0.5cm">
Below is the presentation type that will be deleted. Press confirm to proceed.
</div>
<div style="padding-top: 0.5cm; padding-bottom: 0.5cm">
<span>
Presentation Type Name:
</span>
<span style="padding-left: 1cm">
<input name="PresentationTypeID" type="hidden" value="<?php echo $type -> PresentationTypeID ?>">
<?php echo $type -> PresentationTypeName ?>
</span>
</div>
<div>
<input type="submit" name="Submit" value="Confirm">
<input type="submit" name="Submit" value="Cancel">
</div>
</form>
<hr />
<!-- Finish delete section -->
	<?php	}
} else {
	do_html_header("Available Presentation Types" , &$err_message) ;
	?>
<p style="text-align: right">
<a href="presentation_types.php?add=1">
Add Presentation Type
</a>
</p>
	<?php
}

?>

<!-- Start rooms table -->
<table align="center" width="100%" border="1" cellspacing="2" cellpadding="1">
	<tr>
		<td>
		<strong>Presentation Type Name</strong>
		</td>
		<td>
		<strong>Slot Length</strong>
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
<?php foreach ($types as $type) { ?>
	<tr>
		<td>
		<?php echo $type -> PresentationTypeName ?>
		</td>
		<td>
		<?php echo $type -> SlotLength ?>
		</td>
		<td>
		<a href="presentation_types.php?edit=<?php echo $type -> PresentationTypeID ?>">
		Edit
		</a>
		</td>
		<td>
		<a href="presentation_types.php?delete=<?php echo $type -> PresentationTypeID ?>">
		Delete
		</a>
		</td>
	</tr>
<?php } ?>

</table>
<br>

<?php 

do_html_footer(&$err_message); 
?>
