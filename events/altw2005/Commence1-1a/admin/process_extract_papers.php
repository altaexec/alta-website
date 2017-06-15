<?php 
	define(START, 0); //First record to retrieve
	define (FINISH, 10000); // Last record to retrieve

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");		
	session_start();	
	require_once("includes/libzip.php");	
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
		

	$db = adodb_connect();
  
	if (!$db){
   		echo "Could not connect to database server - please try later.";	
		exit;
	}

	// Get the temp directory to zip to	
	$tmpDir = get_cfg_var("upload_tmp_dir");
    if (!($tmpDir)) $tmpDir = "/tmp";

	if ( $HTTP_POST_VARS["download"] === "Start Download" )
	{
		$saveasname = $HTTP_POST_VARS['zipfilename'] ;
		$filename = $tmpDir."/".$saveasname ;
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
			exit ; 
		}
	}
	
	//Make the SQL statement accroding to user selection
	switch($HTTP_POST_VARS["extractType"]){
		case 1:
			//All papers from paper table
			$papersSQL	= "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper P, " . $GLOBALS["DB_PREFIX"]."Track T ";
			$papersSQL	.= "WHERE P.TrackID = T.TrackID ";
			$papersSQL	.= "ORDER BY P.TrackID ASC, P.PAPERID ASC";
			$dirName = "papers";
			break;	
		case 2:
			//Accepted papers
			$papersSQL	= "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper P," . $GLOBALS["DB_PREFIX"]."PaperStatus PS, " . $GLOBALS["DB_PREFIX"]."Track T ";
			$papersSQL .= " WHERE P.PaperStatusID = PS.PaperStatusID";	
			$papersSQL .= " AND P.TrackID = T.TrackID";	
			$papersSQL .= " AND PS.PaperStatusName = 'Accepted'";	
			$papersSQL .= " ORDER BY P.TrackID ASC, P.PAPERID ASC";	
			$dirName = "accepted_papers";			
			break;
		case 3:
			//Rejected papers
			$papersSQL	= "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper P," . $GLOBALS["DB_PREFIX"]."PaperStatus PS, " . $GLOBALS["DB_PREFIX"]."Track T ";
			$papersSQL .= " WHERE P.PaperStatusID = PS.PaperStatusID";
			$papersSQL .= " AND P.TrackID = T.TrackID";	
			$papersSQL .= " AND PS.PaperStatusName = 'Rejected'";	
			$papersSQL .= " ORDER BY P.TrackID ASC, P.PAPERID ASC";
			$dirName = "rejected_papers";			
			break;
		case 4:
			//All papers except withdrawn
			$papersSQL	= "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper P, " . $GLOBALS["DB_PREFIX"]."Track T";
			$papersSQL	.= " WHERE P.TrackID = T.TrackID ";
			$papersSQL	.= " AND P.Withdraw = 'false' ";
			$papersSQL	.= " ORDER BY P.TrackID ASC, P.PAPERID ASC";
			$dirName = "papers_not_withdrawn";			
			break;			
		case 5:
			//Withdrawn papers
			$papersSQL	= "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper P, " . $GLOBALS["DB_PREFIX"]."Track T";
			$papersSQL	.= " WHERE P.TrackID = T.TrackID ";
			$papersSQL	.= " AND P.Withdraw = 'true' ";
			$papersSQL	.= " ORDER BY P.TrackID ASC, P.PAPERID ASC";
			$dirName = "withdrawn_papers";			
			break;
	
	}
	
	//Execute the query
	$papersResult = $db -> Execute($papersSQL);
	
	if(!$papersResult){
		die ("Could not retrieve papers from database - please try again");
	}
	
	if( ( $totalpapers = $papersResult -> RecordCount() ) == 0){
		do_html_header("No Requested Papers");
		echo "<p>There are no papers of the requested type. <br>You may wish to try to extract other types.</p><a href=\"extract_papers.php\">Back</a>";
		do_html_footer();
		exit;
	}
	
	//Create a new class of zip library
	$zipfile = new zipfile();
	
	// add the subdirectory ... important! 
	$zipfile -> add_dir("$dirName/"); 
	
	//Make the zip file name
	if(!empty($HTTP_POST_VARS["zipfilename"]))
		$zipfilename = $HTTP_POST_VARS["zipfilename"].".zip";
	else
	{
		$conferenceInfo = get_conference_info();
		$zipfilename = $conferenceInfo -> ConferenceCodeName.".zip";
	}					
	
	do_html_header("Extract Papers");
?>
<form name="downloadForm" id="downloadForm" method="post" action="process_extract_papers.php">
	<p>File extraction in progress...</p>
	<input name="status" READONLY type="text" id="status" value="0 % Completed" size="20" maxlength="0">
  <input type="hidden" name="zipfilename" value="<?php echo $zipfilename ; ?>">
  <p>
  <input type="submit" name="download" DISABLED id="download" value="Start Download">
  </p>
</form>	
<?php	
	do_html_footer();			
	
	// Set the timeout to infinity
	set_time_limit ( 0 ) ;

	// Set up TableOfContents
	$TableOfContents = "<html><head>\n<title> Commence Conference System </title>\r\n" ;
	$TableOfContents .= "<meta http-equiv='Content-Type' content='text/html'; charset='iso-8859-1'>\r\n </head>\n" ;
	$TableOfContents .= "<h1>Commence Conference System</h1><br>\r\n" ;
	$TableOfContents .= "<table width='100%' border='1' cellspacing='0' cellpadding='4'>\r\n" ;
	$TableOfContents .= "<tr> <td width='70%'> </td> <td width='30%'> </td> </tr>\r\n" ;
	
	//Loop each paper and add to zip file
	$currentPage = 1;  //Current Page
	$currentTrack = 0; //Current Track
	$i = 0; //Set loop counter

	while($paperInfo = $papersResult -> FetchNextObj())
	{	
		//Call the function to get the lastest file of the paper
		$fileInfo = get_latestFile($paperInfo -> PaperID , &$err_message );			
		
		$data = $fileInfo -> File;
		$name = $fileInfo -> FileName;
		
		//Get the file type by fetchinglast 4 characters
		//$fileType = substr($name,strlen($name) - 4); //Fails for .ps
		$fileType = strstr($name,'.') ;
				
		// Prepare the file structure
		$currentFileName = "paper_".$paperInfo -> TrackID."_".$paperInfo -> PaperID.$fileType;
		$filePath = "$tmpDir/$currentFileName";	
		$fileStructure = $dirName."/".basename($filePath);

		//add entry to TableOfContents
		$authors = retrieve_authors($paperInfo -> PaperID , &$err_message );
	
		
		// Output Table of Contents Entries
		if ($paperInfo -> TrackID > $currentTrack){ // Output Group Header
			$currentTrack = $paperInfo -> TrackID;
			$TableOfContents .= "<tr><td colspan=2 align=\"center\"><H2>".$TRACK_NAME.": ".$paperInfo -> TrackName."</H2></td></tr>\r\n";
		}
 		$TableOfContents .= "<tr> <td> <a href=$currentFileName> #";
		$TableOfContents .= $paperInfo -> PaperID ;
		$TableOfContents .= " " ;
		$TableOfContents .= stripslashes($paperInfo -> Title);
		$TableOfContents .= "</a><br>";
		$TableOfContents .= $authors ;
		$TableOfContents .= "</td> <td>page $currentPage </td></tr>\r\n" ;
		
		// Increment current page
		$currentPage +=  $paperInfo -> NumberOfPages ; 

		// add the binary data stored in the string 'data' 
		$zipfile -> add_file($data,$fileStructure);

/*		// Debug use only
		echo "\$totalpapers= $totalpapers<br>" ;
		echo "\$i= $i<br>" ;
		echo "<br><br><br>" . $percentage_completed . "%<br>";
		exit ;
*/
		// Update status
		$percentage = number_format(++$i / $totalpapers * 100, 1) ;	
		$percentage_completed = $percentage . " % Done" ;
		echo '<script language="JavaScript" type="text/javascript" >' ;						
		echo "document.downloadForm.status.value='$percentage_completed'" ;
		echo '</script>' ;															
	}	// End while loop

	//Close TableOfContents
	$TableOfContents .= "</table></html>" ;
	// Prepare the file structure
	$filePath = $tmpDir."/index.htm";	
	$fileStructure = $dirName."/".basename($filePath);
	// add the binary data stored in the string 'TableOfCOntents' 
	$zipfile -> add_file($TableOfContents,$fileStructure);

	
	$fp=fopen( $tmpDir."/".$zipfilename , 'wb' ) ;
	
	if ($fp) // ok, push the data
	{
		echo '<script language="JavaScript" type="text/javascript" >' ;						
		echo "document.downloadForm.status.value='Creating Zip archive'" ;
		echo '</script>' ;																	
		if ( !fwrite($fp, $zipfile -> file() ) )
		{
			echo "Problem in writing to file '$zipfilename' - please try again.";
			exit;		
		}
		else
		{
			echo '<script language="JavaScript" type="text/javascript" >' ;						
			echo "document.downloadForm.status.value='Finished'" ;
			echo '</script>' ;																				
			echo '<script language="JavaScript" type="text/javascript" >' ;						
			echo "document.downloadForm.download.disabled=false" ;
			echo '</script>' ;				
		}
	}	// End if $fp condition				
?>
