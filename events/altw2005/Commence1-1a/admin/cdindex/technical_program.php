<?php 
$sessions = get_sessions("StartTime");
$pageNumberOf = array_flip(get_paper_pages( ));

$days = array();
foreach ($sessions as $session)
{
	$date = format_date("Y-m-d", $session -> StartTime);
	if (!$days[$date])
		$days[$date] = array();
	$days[$date][] = get_session_slots_info($session -> SessionID);
}

?>
<html>
<head>
<title>
Technical Program
</title>
<style type="text/css">
  div { padding-top: 3 ; padding-bottom: 3 }
  .PageTitle { text-align: center ; font: bolder xx-large "Comic Sans MS" ; color: teal}
  .Day { font-style: italic }
  .Session { font-weight: bold ; font-family: "Comic Sans MS" }
  .Indent {float:left; width:2%}
  .LeftColumn {float:left; width:78%;}
  .RightColumn {float:left; width:20%; text-align: right;}
  Slot.PaperName {}
  Slot.Authors {}
  Slot.PageNumber {}
</style>
</head>
<body>

<div class="PageTitle">
Technical Program
</div>

<?php
ksort($days);
foreach ($days as $date => $sessions)
{
	?>
<div class="Day">
<?php echo format_date("l jS F",$date) ?>
</div>
	<?php
	foreach ($sessions as $session)
	{
		?>
		<div class="Session">
		<?php echo $session -> Session -> SessionName; ?>
		</div>
	<?php
		foreach ($session -> Slots as $slot)
		// Use 3 column layout to provide indent to prevent
		// Mozilla breaking on padding-left. Also
		// need <br clear=all> to get columns in Mozilla
		{
			$paper = get_paper_info($slot -> PaperID);
			$firstPage = $pageNumberOf[$slot -> PaperID];
			$authors = retrieve_authors($paper -> PaperID);
			$fileInfo = get_latestFile($paper -> PaperID , &$err_message );
			$fileEnding = strstr($fileInfo -> FileName, '.');
			?>
			<div class="Indent"><nbsp;></div>  
			<div class="LeftColumn">
				<span class="Slot.PaperName">
				<a href="papers/<?php echo $paper -> PaperID . $fileEnding ?>">
				<?php echo $paper -> Title ?>
				</a>
				</span>
				<br />
				<span class="Slot.Authors">
				<?php echo $authors ?>
				</span>
			</div>
			<div class="RightColumn">
				<span class="Slot.PageNumber">
				<?php echo $firstPage ?>
				</span>
			</div>
			<br clear=all>
		<?php
			}
		?>
		<?php
	}
}
?>
</body>
</html>
