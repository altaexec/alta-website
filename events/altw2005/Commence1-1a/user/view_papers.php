<?php
	$php_root_path = "..";
	require_once("$php_root_path/includes/include_all_fns.php");

	session_start();
	$header = "View Papers" ;
	$accepted_privilegeID_arr = array ( 1 => "" ) ;
	$accepted_phaseID_arr = array ( 1 => "" , 2 => "" , 3 => "" , 4 => "" ) ;
	authentication( $header , $accepted_privilegeID_arr , $accepted_phaseID_arr , $homepage , $php_root_path , $dbprefix , &$err_message ) ;	
	
	$sort =& $HTTP_GET_VARS["sort"] ;	

	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	//Call function to evaluate showing
	$showing = evaluate_showing($HTTP_GET_VARS["showing"]);


	$_SESSION["phase"]->set_view_paper( &$err_message ) ;	// 4.0.6	
	//Call the function to display the range of records
	$from = evaluate_records_range($showing,$totalPapers);		
		
	//Call the function to evaluate prev
	$prev = evaluate_prev ( $HTTP_GET_VARS["sort"] , $showing ) ;
	$next = evaluate_next ( $HTTP_GET_VARS["sort"] , $showing , $totalPapers ) ;

	echo "User Name:<strong> " . stripslashes ( $_SESSION["valid_user"] )  . "</strong><p>";		
?>	

  <table width="100%" border="0" cellspacing="2" cellpadding="0">			
	<tr>
		<td>From: <?php echo "<strong>$from</strong>";	?></td>	
        <td align="right">Total Papers : <strong><?php echo $totalPapers; ?></strong></td>
	</tr>
  </table>		
<?php
	$_SESSION["phase"] -> set_view_headers() ;
        $_SESSION["phase"] -> set_view_records ( &$err_message ) ;
	$_SESSION["phase"] -> GenerateViewPaperTable() ;
?>  
  <table width="100%" border="0" cellspacing="2" cellpadding="5">
  	<tr>    				
      <td>&nbsp;</td>
      <td align="right">		
		<?php echo $prev; ?>
				| 
		<?php echo $next; ?>			
      </td>
   </tr>
  </table>
<?php			
do_html_footer( &$err_message );
      
?>
