<?php

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;
	$id = & $HTTP_GET_VARS["id"];	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Check whether the user has login to view this page.
if ( !check_valid_user( &$err_message ) )
{
	//This user is not logged in
	do_html_header("View Abstract Failed" , &$err_message ) ;			
	$err_message .= " Sorry, You must login to edit this paper. <br>\n";
	$err = $err_message . "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
	do_html_footer($err);
	exit;
}	
		
	 //Establish database connection
  	$db = adodb_connect( &$err_message );
	
  
if (!$db)
{
	do_html_header("Connection to View Abstract Failed" , &$err_message );	
	$err_message .= " Could not connect to database server - please try later. <br>\n" ;
	$err = $err_message . "<br><br> Try <a href='view_abstract.php?id=$id'>again</a>?" ;
	do_html_footer( &$err );
	exit;
}						
/*		
	if ( ( $status = check_privilege_type ( 3 , &$err_message ) ) !== false )
	{
		if ( $status == 0 )
		{
			$homepage->showmenu = 0 ;
			do_html_header("View Abstract Failed" , &$err_message );	
			$err_message .= " Sorry, You do not have the authority to view this abstract. <br>\n" ;
			$err = $err_message . "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
			do_html_footer(&$err);
			exit;
		}
	}
	else
	{
		do_html_header("View Abstract Failed" , &$err_message );	
		$err_message .= " Cannot retrieve information from database. <br>\n" ;
		$err = $err_message . "<br><br> Try <a href='view_abstract.php?id=$id'>again</a>?" ;
		do_html_footer(&$err);
		exit;
	}		
*/		
	//Get the paper information
	if ( ( $paperInfo = get_paper_info($id , &$err_message ) ) === false )
	{
		do_html_header("View Abstract Failed" , &$err_message );	
		$err_message .= " Cannot retrieve information from database. <br>\n" ;
		$err = $err_message . "<br><br> Try <a href='view_abstract.php?id=$id'>again</a>?" ;
		do_html_footer(&$err);
		exit;	
	}
		
	//Get the lastest file of the paper				
	if ( ( $FileIDData = get_latestFile($id , &$err_message ) ) === false )
	{
		do_html_header("View Abstract Failed" , &$err_message );		
		$err_message .= " Could not execute \"get_latestFile\" in \"view_abstract.php\". <br>\n" ;
		$err = $err_message . "<br><br> Try <a href='view_abstract.php?id=$id'>again</a>?" ;
		do_html_footer(&$err);
		exit;			
	}
	
	
	do_html_header("View Abstract" , &$err_message );
?>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
    <td><h4> #<?php echo $paperInfo -> PaperID; ?> <nbsp> <?php echo stripslashes($paperInfo -> Title); ?></h4></td>
    </tr>
    <tr> 
    <td><strong>Authors:</strong> 
      <?php
 	if ( $authors = retrieve_authors($id , &$err_message ) )
	{
		echo $authors ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read author table. Try <a href='view_abstract.php?id=$id'>again</a>?</font>" ;
	}		
?>
    </td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><strong>Track:</strong> 
      <?php 
	if ( $catcomsep = getSelectedTrackText($id , &$err_message ) )
	{
		echo $catcomsep ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read Paper table. Try <a href='view_abstract.php?id=$id'>again</a>?</font>" ;
	}		  
	?>
    </td>
  </tr>
  <tr> 
    <td><strong>Topic(s):</strong> 
      <?php 
	if ( $catcomsep = getSelectedCategoryCommaSeparated($id , &$err_message ) )
	{
		echo $catcomsep ;
	}
	else
	{
		echo " <font color=\"#FF0000\"> Could not read Paper Category table. Try <a href='view_abstract.php?id=$id'>again</a>?</font>" ;
	}		  
	?>
    </td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><a href='view_file.php?fileid=<?php echo $FileIDData -> FileID ; ?>'>View Paper</a>&nbsp;|&nbsp;<a href='download_file.php?fileid=<?php echo $FileIDData -> FileID ; ?>'>Download Paper</a></td>
  </tr>
  <tr> 
    <td><hr></td>
  </tr>
  <tr> 
    <td><p><strong>Abstract:</strong></p>
      <p> 
        <?php 
 		echo nl2br( ereg_replace( "  " , "&nbsp;&nbsp;" , stripslashes($paperInfo -> PaperAbstract )));
	  ?>
      </p></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?php 

	do_html_footer( &$err_message );
?>
