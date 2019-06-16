<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	
	$new_include_path = ini_get('include_path').":$php_root_path/includes/pear";
	ini_set('include_path', $new_include_path);
	
	require_once($php_root_path."/includes/pear/HTML/Progress.php");
	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Call the function to get the conference information
	$conferenceInfo = get_conference_info();		
    
	require_once($php_root_path."/includes/pear/Tar.php");
	require_once($php_root_path."/admin/includes/libzipfile.php");
		
	class File_Archiver
	{
		function File_Archiver( $filename ) {}
		function AddString( $archival_path, $data ) {}
	}
	
	class Tar_Archiver extends File_Archiver
	{
		var $COMPRESSION_TYPE = false;
		var $archive_file;
		
		function Tar_Archiver( $filename )
		{
			echo $COMPRESSION_TYPE;
			$this -> $archive_file = 
				new Archive_Tar($filename, $COMPRESSION_TYPE);
			$this -> $archive_file -> create(array());
		}
		
		function AddString( $archival_path, $data )
		{
			$this -> $archive_file -> addString( $archival_path, $data );
		}
	}
	
	class BZip2_Archiver extends Tar_Archiver
	{
		var $COMPRESSION_TYPE = 'bz2';
	}
	
	class GZip_Archiver extends Tar_Archiver
	{
		var $COMPRESSION_TYPE = 'gz';
	}
	
	class Zip_Archiver extends File_Archiver
	{
		var $filename;
		var $archive_file;
		
		function Zip_Archiver( $filename )
		{
			$this -> filename = $filename;
			$this -> archive_file = new zipfile();
		}
		
		function AddString( $archival_path, $data )
		{
			$this-> archive_file -> add_file( $data , $archival_path);
			$file = fopen($this -> filename, 'w');
			fwrite($file, $this -> archive_file -> file());
		}
	}
	
	
	if (!$HTTP_POST_VARS["Submit"])
	{
		/* START Select Archive Type page */
		do_html_header("Build CD Structure");
?>


<form name="form1" method="post">

<div style="padding-top: 3mm">
Select the type of archive should be exported:
</div>

<div>
<table cellpadding="1">
	<tr>
		<td><input type="radio" name="enctype" value="bz2" checked /></td>
		<td>bzip2</td>
		<td>(*.tar.bz2)</td>
	</tr>
	<tr>
		<td><input type="radio" name="enctype" value="gz" /></td>
		<td>gzip</td>
		<td>(*.tar.gz)</td>
	</tr>
	<tr>
		<td><input type="radio" name="enctype" value="zip" /></td>
		<td>zip</td>
		<td>(*.zip)</td>
	</tr>
</table>
</div>

<div style="padding-top: 3mm">
Base filename: <input name="filename"><br />
(conference name will be used if none given)
</div>

<div style="padding-top: 3mm">
<input type=submit name="Submit" value="Build Archive">
</div>

</form>

<?php
		exit;
		/* END Select Archive Type page */
	}
	if ( $HTTP_POST_VARS["Submit"] == "Build Archive" ) {
		/* START Compiling Archive Structure page */
		
		$archive_papers = get_papers_in_order();
		
		// If there's no papers, tell the user.
		if (count($archive_papers) == 0)
		{
			do_html_header("No Papers Available");
			?>
			<br />
			<div style="padding-top: 3mm">
			No papers have been accepted and scheduled yet. Without any
			scheduling data, the CD structure cannot be built.
			</div>
			<div style="padding-top: 3mm">
			If you still wish
			to export papers, use the "Extract All Papers" function instead.
			</div>
			<?php
			do_html_footer();
			exit;
		}
		
		// Set up progress bar
		$bar = new HTML_Progress();
		$bar->setAnimSpeed(100);
		$bar->setIncrement(1);
		$bar->setBorderPainted(true);
		$ui =& $bar->getUI();
		$ui->setCellAttributes('active-color=#000084 inactive-color=#3A6EA5 width=4 spacing=0');
		$ui->setBorderAttributes('width=1 style=inset color=white');
		$ui->setStringAttributes(array(
			'width' => 200,
			'height' => 20,
			'font-size' => 14,
			/*'background-color' => '#C3C6C3',*/
			'valign' => 'top'
		));
		$ui->setCellCount(100);		
		
		// Start building archive
		require_once($php_root_path."/includes/pear/Tar.php");
		require_once($php_root_path."/includes/pear/Zip.php");
		
		// Get the temp directory to put the archive in
		$tmpDir = get_cfg_var("upload_tmp_dir");
		if (!($tmpDir)) $tmpDir = "/tmp";
		
		
		if ($HTTP_POST_VARS["filename"])
			$basename = $HTTP_POST_VARS["filename"];
		else
			$basename = $conferenceInfo -> ConferenceCodeName;
		
		
		// Create the archive file
		switch ($HTTP_POST_VARS["enctype"])
		{
			case 'bz2':
				$saveasname = "$basename.tar.bz2";
				$filename = $tmpDir."/".$saveasname ;
				$tarFile = new BZip2_Archiver($filename);
				break;
			case 'gz':
				$saveasname = "$basename.tar.gz";
				$filename = $tmpDir."/".$saveasname ;
				$tarFile = new GZip_Archiver($filename);
				break;
			case 'zip':
				$saveasname = "$basename.zip";
				$filename = $tmpDir."/".$saveasname ;
				$tarFile = new Zip_Archiver($filename);
				break;
			default:
				$saveasname = "$basename.tar";
				$filename = $tmpDir."/".$saveasname ;
				$tarFile = new Tar_Archiver($filename);
				break;
		}
		
		$cdindex_path = $php_root_path.$privilege_root_path."/cdindex/";
		ob_start();
		include($cdindex_path."technical_program.php");
		$htmlFile = ob_get_contents();
		ob_end_clean();
		$tarFile -> AddString("technical_program.html",$htmlFile);
		
		ob_start();
		include($cdindex_path."author_index.php");
		$htmlFile = ob_get_contents();
		ob_end_clean();
		$tarFile -> AddString("author_index.html",$htmlFile);
		
		// Tell progress bar how many papers need to be processed.
		$bar->setMaximum(count($archive_papers));
		
		// Define archiving proceedure for papers
		function archive_next_paper($percent, &$bar)
		{
			global $archive_papers;
			global $tarFile;
			$paperInfo = $archive_papers[$bar -> getValue()];
			if (!$paperInfo) return; // Skip empty entries (applies to 100% too)
			$fileInfo = get_latestFile($paperInfo -> PaperID , &$err_message );
			$fileEnding = strstr($fileInfo -> FileName, '.');
			$data = $fileInfo -> File;
			$name = $paperInfo -> PaperID .$fileEnding;
			$tarFile -> AddString("papers/".$name, $data);
		}
		
		// Tell progress bar to archive a paper for each 'tick'
		$bar->setProgressHandler('archive_next_paper');
		
		// Start building head info
		ob_start();
?>
<style type="text/css">
<?php echo $bar->getStyle(); ?>
</style>
<script type="text/javascript">
<?php echo $bar->getScript(); ?>
</script>
<?php
		// Attach head info to page
		$homepage -> AddExtraHeadData(ob_get_contents());
		ob_end_clean();
		
		do_html_header("Compiling CD Structure...");
?>

<center>
<span class="ProgressBar">
<?php 
echo $bar->toHtml(); 
?>
</span>
<style type="text/css">
/* This line hides the download link initially */
.DownloadLink {display: none}
</style>
<span class="DownloadLink">
<br /><br />
<form name="form1" method="post">
<input type=hidden name="SaveAsName" value="<?php echo $saveasname ?>">
<input type=hidden name="FileName" value="<?php echo $filename ?>">
<input type=submit name="Submit" value="Download File">
</form>
</span>
</center>

<?php
		do_html_footer();
		
		// Set the timeout to infinity [run() will take a while]
		set_time_limit ( 0 ) ;
		
		// Page has reached "end", but now code is added to move progress bar.
		$bar->run();
?>
<style type="text/css">
/* The progress bar must have completed, so show the download link */
.ProgressBar  {display: none}
.DownloadLink {display: block}
</style>
<?php
		exit;
		/* END Compiling Archive Structure page */
	}
	
	/* Archive download at the top, so extra characters don't interfer */
	if ( $HTTP_POST_VARS["Submit"] == "Download File" ) {
		$saveasname = $HTTP_POST_VARS["SaveAsName"];
		$filename = $HTTP_POST_VARS["FileName"];
		/* START Archive Download */
		if ( file_exists ( $filename ) )
		{
		   // Send binary filetype HTTP header 
			header('Content-Type: application/octet-stream'); 
			// Send content-length HTTP header 
			header('Content-Length: '.filesize($filename)); 
			// Send content-disposition with save file name HTTP header 
			header('Content-Disposition: attachment; filename="'.$saveasname.'"'); 
			// Output file 
			readfile($filename); 
			// Done 
			exit; 
		}
		/* END Archive Download */
	}
?>
