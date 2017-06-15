<?php 




?>
<html>
<head>
<title>
Author Index
</title>
<style type="text/css">
  .PageTitle { text-align: center ; font: bolder xx-large "Comic Sans MS" ; color: teal }
  .Alpha { padding-bottom: 10 ; font: bold large "Comic Sans MS" ; color: teal }
  .Authors { padding-bottom: 20 }
  .Author {  }
  .Paper { font-family: monospace }
</style>
</head>
<body>

<div class="PageTitle">Author Index</div>

<?php 
$authorList = get_author_papers();
ksort($authorList);
$alpha = array();
foreach (range('A','Z') as $letter)
	$alpha[$letter] = array();

foreach ($authorList as $author => $papers)
{
	$alpha[strtoupper(substr($author,0,1))][$author] = $papers;
}

foreach ($alpha as $letter => $authors)
{
?>
<div class="Alpha">
<?php echo $letter ?>
</div>
<?php
	foreach ($authors as $author => $papers)
	{
?>

<div class="Authors">
<span class="Author"><?php echo $author ?></span>
<span class="Paper">
<?php 
$i=0; //paper counter
asort($papers,SORT_NUMERIC); //papers in ascending page number order
foreach ($papers as $paper => $page)
{
	$fileInfo = get_latestFile($paper, &$err_message );
	$fileEnding = strstr($fileInfo -> FileName, '.');
	if ($i) echo ", "; $i++; //punctuation
?>
	<a href="papers/<?php echo $paper . $fileEnding ?>">
	<?php echo $page."</a>"; 
 } ?>
</span>
</div>

<?php 
} 
}
?>

</body>
</html>
