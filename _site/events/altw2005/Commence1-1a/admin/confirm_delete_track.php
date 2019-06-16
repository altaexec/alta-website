<?php
	
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	$catID = &$HTTP_GET_VARS["catID"];
	
	do_html_header("Delete Track");

	//Call the funtion to get the track information
	$editCatInfo = get_track_info($catID);


?>
<form name="form1" method="post" action="process_delete_track.php">
  <table width="100%" border="0" cellpadding="1" cellspacing="0">
    <tr> 
      <td colspan="2">Below is the track that will be 
        deleted. Press confirm to proceed.</td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    
    	<tr> 
      <td width="20%">Track Name:</td>
      <td width="80%"><?php echo $editCatInfo -> TrackName; ?></td>
      <input type="hidden" name="catID" value="<?php echo $editCatInfo -> TrackID; ?>">
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"><input name="Submit2" type="submit" id="Submit2" value="Confirm"> 
        <input name="Submit" type="submit" id="Submit3" value="Cancel"></td>
    </tr>
    <tr> 
      <td colspan="2"><hr></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <?php 
			//Call the function that display the categories
			echo display_track_table();
		?>
      </td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
</form>
