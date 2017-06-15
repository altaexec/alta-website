<?php 
//////////// REVIEWER PHASE 3 ///////////////
	$php_root_path = ".." ;
	$privilege_root_path = "/reviewer" ;
//extract ( $HTTP_GET_VARS , EXTR_REFS ) ;
//extract ( $HTTP_POST_VARS , EXTR_REFS ) ;
//extract ( $HTTP_POST_FILES , EXTR_REFS ) ;

	require_once("includes/include_all_fns.php");		
	session_start() ;
//	extract ( $_SESSION , EXTR_REFS ) ;
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	$header = "Review Form" ;
	$accepted_privilegeID_arr = array ( 2 => "" ) ;
	$accepted_phaseID_arr = array ( 3 => "" ) ;
	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $GLOBALS["DB_PREFIX"] , &$err_message ) ;		
	
	
	if ( count ( $HTTP_POST_VARS ) > 0 )
	{		
		$error_array = array() ;		
		$exempt_array = array ( "commentfile" ) ;

		$vars = array_merge ( $HTTP_POST_VARS , $HTTP_POST_FILES ) ;
		check_form ( $vars , $error_array , &$exempt_array ) ;		
		if($HTTP_POST_VARS["submitType"] == "file" && !$error_array["commentfile"] )
		{
			//Read the file contents and display
			$HTTP_POST_VARS["comments"] = addslashes(fread(fopen($HTTP_POST_FILES["commentfile"]["tmp_name"],"r"),filesize($HTTP_POST_FILES["commentfile"]["tmp_name"])));
		}					
	}	
//	display ( $error_array ) ;
	if ( count ( $error_array ) == 0 && count ( $HTTP_POST_VARS ) > 0 )
	{
		if ( $HTTP_POST_VARS["Submit"] == "Submit" )
		{	
			//Process the normal form submission here
			if ( $returnResult = insert_review($HTTP_POST_VARS["paperid"],$HTTP_POST_VARS["objectives"],$HTTP_POST_VARS["validity"],$HTTP_POST_VARS["innovativeness"],$HTTP_POST_VARS["presentation"],$HTTP_POST_VARS["bibliography"],$HTTP_POST_VARS["acceptpaper"],$HTTP_POST_VARS["acceptposter"],addslashes($HTTP_POST_VARS["comments"]) , &$err_message ) )
			{
				if ( update_paper_status( $HTTP_POST_VARS["paperid"] , &$err_message) )
				{
					do_html_header("Review Accepted" , &$err_message );						
					echo $returnResult ;
					do_html_footer(&$err_message);
					exit;
					
				}
				else
				{
					do_html_header("Show Review Form Failed" , &$err_message );					
					$err_message .= " Could not update paper status in Paper Table<br>\n" ;
					$err_message .= "<br><br> Try <a href='review_form.php?paperid=".$HTTP_POST_VARS["paperid"]."'>again</a>?" ;
				}
			}
			else
			{
				do_html_header("Show Review Form Failed" , &$err_message );	
				$err_message .= " Could not execute \"insert_review\" in \"review_form.php\". <br>\n" ;
				$err_message .= "<br><br> Try <a href='review_form.php?paperid=".$HTTP_POST_VARS["paperid"]."'>again</a>?" ;
			}			
		}
		else
		{
			do_html_header("Review Form" , &$err_message );
		}
	}
	else 
	{
		if ( count ( $HTTP_POST_VARS ) == 0 )
		{	
			$HTTP_POST_VARS["paperid"] = $HTTP_GET_VARS["paperid"] ;
		}
		do_html_header("Review Form" , &$err_message );
	}

	//Retrieve the paper info	
	if ( ( $paperInfo = get_paper_info( $HTTP_POST_VARS["paperid"] , &$err_message ) ) === false )
	{
		do_html_header("Show Review Form Failed" , &$err_message );	
		$err_message .= " Cannot retrieve information from database. <br>\n" ;
		$err_message .= "<br><br> Try <a href='review_form.php?paperid=".$HTTP_POST_VARS["paperid"]."'>again</a>?" ;
		do_html_footer(&$err_message);
		exit;	
	}
	
?>
<script language="JavaScript">
function loadFileContent(){

	//Change the form submition type
	document.frmReview.submitType.value = "file";
	
	//Retrieve the value of file path
	var str = document.frmReview.commentfile.value;
	//Extract the file type to vefiry
	var fileType = str.substring(str.length - 3);
	
	//Check whehter the file is text file
	if(fileType != "txt"){
		alert ("Sorry,You can select only plain text file.");
		document.frmReview.commentfile.focus();
		document.frmReview.commentfile.select();
	}
	else{
		//alert ("File Type is valid");
		document.frmReview.submit();
	}

}

</script>
 <form enctype="multipart/form-data" action="review_form.php" method="post" name="frmReview">
<!--<form enctype="multipart/form-data" action="phpinfo.php" method="post" name="frmReview">  --> <br><br>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td align="center"><h3>#<?php echo $paperInfo -> PaperID; ?>&nbsp;&nbsp;<?php echo stripslashes($paperInfo -> Title); ?></h3></td>
    </tr>
    <tr> 
      <td align="center"><h4><?php if ( $authors = retrieve_authors( $paperInfo -> PaperID , &$err_message ) )
	{
		echo $authors ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read author table. Try <a href='review_form.php?paperid=".$HTTP_POST_VARS["paperid"]."'>again</a>?</font>" ;
	}
	  ?></h4></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Reviewer: </strong><?php echo $_SESSION["valid_user"] ; ?></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><hr></td>
    </tr>
    <tr> 
      <td><strong>Numerical Ranking</strong></td>
    </tr>
    <tr> 
      <td><table width="90%" border="1" cellpadding="0" cellspacing="2">
          <tr> 
            <td rowspan="2">&nbsp;<strong>Ranking Criteria</strong></td>
            <td align="center">&nbsp;<strong>Strongly disagree</strong></td>
            <td align="center">&nbsp;<strong>Weakly disagree</strong></td>
            <td align="center">&nbsp;<strong>Undecided</strong></td>
            <td align="center">&nbsp;<strong>Weakly agree</strong></td>
            <td align="center">&nbsp;<strong>Strongly agree</strong></td>
          </tr>
          <tr> 
            <td align="center">1</td>
            <td align="center">2</td>
            <td align="center">3</td>
            <td align="center">4</td>
            <td align="center">5</td>
          </tr>
          <tr> 
            <td>The paper fits in with the objectives of this workshop</td>
            <td align="center"><input name="objectives" type="radio" value="1"
			<?php if ( isset ( $HTTP_POST_VARS["objectives"] ) )
				{
					if ($HTTP_POST_VARS["objectives"] == 1)
						echo "checked";
				}
				else
				{
					echo "checked";
				}
			?>></td>			
            <td align="center"><input type="radio" name="objectives" value="2" <?php if ($HTTP_POST_VARS["objectives"] == 2) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="objectives" value="3" <?php if ($HTTP_POST_VARS["objectives"] == 3) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="objectives" value="4" <?php if ($HTTP_POST_VARS["objectives"] == 4) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="objectives" value="5" <?php if ($HTTP_POST_VARS["objectives"] == 5) echo "checked"; ?>></td>
          </tr>
          <tr> 
				<td>All reported claims and results are correct and valid,
and there are no technical and/or methodological flaws</td>
            <td align="center"><input name="validity" type="radio" value="1" 
			<?php if ( isset ( $HTTP_POST_VARS["validity"] ) )
				{
					if ($HTTP_POST_VARS["validity"] == 1)
						echo "checked";
				}
				else
				{
					echo "checked";
				}
			?>></td>
            <td align="center"><input type="radio" name="validity" value="2" <?php if ($HTTP_POST_VARS["validity"] == 2) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="validity" value="3" <?php if ($HTTP_POST_VARS["validity"] == 3) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="validity" value="4" <?php if ($HTTP_POST_VARS["validity"] == 4) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="validity" value="5" <?php if ($HTTP_POST_VARS["validity"] == 5) echo "checked"; ?>></td>
          </tr>
          <tr> 
            <td>The paper is innovative and makes a genuine
contribution to the field</td>
            <td align="center"><input name="innovativeness" type="radio" value="1" <?php if ( isset ( $HTTP_POST_VARS["innovativeness"] ) )
				{
					if ($HTTP_POST_VARS["innovativeness"] == 1)
						echo "checked";
				}
				else
				{
					echo "checked";
				}
			?>></td>
            <td align="center"><input type="radio" name="innovativeness" value="2" <?php if ($HTTP_POST_VARS["innovativeness"] == 2) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="innovativeness" value="3" <?php if ($HTTP_POST_VARS["innovativeness"] == 3) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="innovativeness" value="4" <?php if ($HTTP_POST_VARS["innovativeness"] == 4) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="innovativeness" value="5" <?php if ($HTTP_POST_VARS["innovativeness"] == 5) echo "checked"; ?>></td>
          </tr>
          <tr> 
            <td>The objectives, methodology and contributions of the
paper are clearly described</td>
            <td align="center"><input name="presentation" type="radio" value="1" checked
			<?php if ( isset ( $HTTP_POST_VARS["presentation"] ) )
				{
					if ($HTTP_POST_VARS["presentation"] == 1)
						echo "checked";
				}
				else
				{
					echo "checked";
				}
			?>></td>
            <td align="center"><input type="radio" name="presentation" value="2" <?php if ($HTTP_POST_VARS["presentation"] == 2) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="presentation" value="3" <?php if ($HTTP_POST_VARS["presentation"] == 3) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="presentation" value="4" <?php if ($HTTP_POST_VARS["presentation"] == 4) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="presentation" value="5" <?php if ($HTTP_POST_VARS["presentation"] == 5) echo "checked"; ?>></td>
          </tr>
          <tr> 
            <td>The bibliography is relevant and exhaustive</td>
            <td align="center"><input name="bibliography" type="radio" value="1" 
			<?php if ( isset ( $HTTP_POST_VARS["bibliography"] ) )
				{
					if ($HTTP_POST_VARS["bibliography"] == 1)
						echo "checked";
				}
				else
				{
					echo "checked";
				}
			?>></td>
            <td align="center"><input type="radio" name="bibliography" value="2" <?php if ($HTTP_POST_VARS["bibliography"] == 2) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="bibliography" value="3" <?php if ($HTTP_POST_VARS["bibliography"] == 3) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="bibliography" value="4" <?php if ($HTTP_POST_VARS["bibliography"] == 4) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="bibliography" value="5" <?php if ($HTTP_POST_VARS["bibliography"] == 5) echo "checked"; ?>></td>
          </tr>
          <tr> 
            <td>The paper should be accepted as a regular paper</td>
            <td align="center"><input name="acceptpaper" type="radio" value="1" 
			<?php if ( isset ( $HTTP_POST_VARS["acceptpaper"] ) )
				{
					if ($HTTP_POST_VARS["acceptpaper"] == 1)
						echo "checked";
				}
				else
				{
					echo "checked";
				}
			?>></td>
            <td align="center"><input type="radio" name="acceptpaper" value="2" <?php if ($HTTP_POST_VARS["acceptpaper"] == 2) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="acceptpaper" value="3" <?php if ($HTTP_POST_VARS["acceptpaper"] == 3) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="acceptpaper" value="4" <?php if ($HTTP_POST_VARS["acceptpaper"] == 4) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="acceptpaper" value="5" <?php if ($HTTP_POST_VARS["acceptpaper"] == 5) echo "checked"; ?>></td>
          </tr>
          <tr> 
            <td>The paper should be accepted as a poster</td>
            <td align="center"><input name="acceptposter" type="radio" value="1" 
			<?php if ( isset ( $HTTP_POST_VARS["acceptposter"] ) )
				{
					if ($HTTP_POST_VARS["acceptposter"] == 1)
						echo "checked";
				}
				else
				{
					echo "checked";
				}
			?>></td>
            <td align="center"><input type="radio" name="acceptposter" value="2" <?php if ($HTTP_POST_VARS["acceptposter"] == 2) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="acceptposter" value="3" <?php if ($HTTP_POST_VARS["acceptposter"] == 3) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="acceptposter" value="4" <?php if ($HTTP_POST_VARS["acceptposter"] == 4) echo "checked"; ?>></td>
            <td align="center"><input type="radio" name="acceptposter" value="5" <?php if ($HTTP_POST_VARS["acceptposter"] == 5) echo "checked"; ?>></td>
          </tr>
        </table> </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><hr></td>
    </tr>
    <tr> 
      <td><p><strong>Detailed Comments</strong></p>
        <p>Please supply detailed comments to back up your rankings (the more 
          detailed your comments, the better). To do this, pick one of the following 
          two options: </p>
        <ul>
          <li>You can pre-edit your comments in a separate text file, and then 
            just upload the contents into the textbox below. (The file should contain plain ascii text, 
            i.e., a .txt file, NOT .doc, .ps, .pdf, .wp, .tex, etc.). </li>
          <li>Or you can type (or paste) your comments directly in the textbox.</li>
        </ul></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Upload Comments from File:</strong><input type="hidden" name="submitType" value="normal"> <input type="file" name="commentfile" onChange="loadFileContent()" ><font color="#FF0000"><?php echo ( $error_array["commentfile"][0] ? $error_array["commentfile"][0] : $error_array["commentfile"][4] ) ; ?></font></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><p><strong>Comments for Authors:</strong><font color="#FF0000"><?php echo $error_array["comments"][0] ?></font></p>
        <p> 
          <textarea name="comments" cols="100" rows="15"><?php 
	echo stripslashes ( $HTTP_POST_VARS["comments"] ) ;		
		  ?></textarea>
        </p></td>
    </tr>
       <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><hr></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>
	  <input type="hidden" name="paperid" value="<?php echo $paperInfo -> PaperID; ?>">
	  <input type="submit" name="Submit" value="Submit"> <input type="reset" name="Submit2" value="Reset"></td>
    </tr>
  </table>
</form>
<?php 

do_html_footer(&$err_message);

?>
