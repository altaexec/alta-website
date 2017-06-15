<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	//Establish database connection
  	$db = adodb_connect();
  
  	if (!$db){
   		echo "Could not connect to database server - please try later.";
		exit;
	}
	
	if($HTTP_POST_VARS["Submit"] == "Cancel"){
		$sort = $_SESSION["sort"] ; // retrieve current sort and showing setings
		$showing = $_SESSION["showing"] ;
		$url = "Location: view_all_papers.php?sort=$sort&showing=$showing";
		header($url);
		exit;
	}	
	
	if($HTTP_POST_VARS["paperstatus"] == "Accepted"){
		//paper is going to accept, redirect to accept_reject page
		$url = "Location: accept_reject_paper.php?paperID=".$HTTP_POST_VARS["paperID"]."&status=Accepted&type=".$HTTP_POST_VARS["PresentationType"];
		header($url);
		exit;
	}
	else if($HTTP_POST_VARS["paperstatus"] == "Rejected"){
		//paper is going to accept, redirect to accept_reject page
		$url = "Location: accept_reject_paper.php?paperID=".$HTTP_POST_VARS["paperID"]."&status=Rejected";
		header($url);
		exit;
	}
	
	//Get the paper information
	$paperInfo = get_paper_info($HTTP_POST_VARS["paperID"]);
	
	if($HTTP_POST_VARS["Submit"] == "Back"){
		$url = "Location: evaluate_paper_status.php?paperID=".$HTTP_POST_VARS["paperID"];
		$url .= "&status=".$HTTP_POST_VARS["status"];
		header($url);
		exit;
	}	
	
	if($HTTP_POST_VARS["Submit"] == "Confirm" && ($paperInfo -> PaperStatusName =="Accepted" ? remove_paper_presentation($HTTP_POST_VARS["paperID"]): true) )
	{
		//Update the paper status
		if(update_PaperStatus($HTTP_POST_VARS["paperID"],$HTTP_POST_VARS["status"])){
			do_html_header("Successful Update");
			echo "<p>The following paper is set to <strong>".$HTTP_POST_VARS["status"]."</strong> status.<br><br>";
			echo "<strong>PaperID:</strong> #".$HTTP_POST_VARS["paperID"]."<br>";
			echo "<strong>Previous Status:</strong> ".$paperInfo -> PaperStatusName."<br>";			
			echo "<strong>Now:</strong> ".$HTTP_POST_VARS["status"]."<br><br>";
			$sort = $_SESSION["sort"] ; // retrieve current sort and showing setings
			$showing = $_SESSION["showing"] ;		
			echo "<br>Go back to <a href='view_all_papers.php?sort=".$sort."&showing=".$showing."'>View All Papers</a> page.<br><br>";
			//echo "Go back to <a href=\"view_all_papers.php\">view all papers</a></p>";
			do_html_footer();
			exit;
		}		
	}	
	
	//The paper is set to Marginal so update the information
	do_html_header("Confirm Paper Status");

?>
<form action="process_evaluate_paper_status.php" method="post" name="form1">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td colspan="2"><strong>Below is the paper information that you are going 
        to change status. Click Confirm to proceed.</strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><?php echo stripslashes("<h3>".$paperInfo -> Title."</h3>"); ?></td>
    </tr>
    <input type="hidden" name="paperID" value="<?php echo $paperInfo->PaperID; ?>">
    <tr> 
      <td colspan="2"><strong>Paper #<?php echo $paperInfo->PaperID; ?></strong></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td width="15%"><strong>Authors:</strong> </td>
      <td width="85%"><?php echo retrieve_authors($paperInfo -> PaperID);?></td>
    </tr>
    <tr> 
      <td><strong>Keywords:</strong> </td>
      <td><?php echo  getSelectedCategoryCommaSeparated($paperInfo -> PaperID , &$err_message );?></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Current Status:</strong></td>
      <td><?php echo $paperInfo -> PaperStatusName; ?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Change to:</strong></td>
      <td><?php echo $HTTP_POST_VARS["paperstatus"]; ?></td>
      <input type="hidden" name="status" value="<?php echo $HTTP_POST_VARS["paperstatus"]; ?>">
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="Submit" type="submit" id="Submit" value="Confirm"> 
        <input name="Submit" type="submit" id="Submit" value="Back"></td>
    </tr>
  </table>
</form>
<?php 
	
	do_html_footer();

?>
