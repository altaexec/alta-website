<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	do_html_header("Extract Papers");

?>
<form name="form1" method="post" action="process_extract_papers.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td><strong>What would you like to extract?</strong></td>
    </tr>
    <tr> 
      <td><table width="40%">
          <tr> 
            <td><label> 
              <input type="radio" name="extractType" value="4" checked>
              all papers except withdrawn</label></td>
          </tr>		  
          <tr> 
            <td><label> 
              <input name="extractType" type="radio" value="1">
              all papers</label></td>
          </tr>	  
          <tr> 
            <td><label> 
              <input name="extractType" type="radio" value="2">
              accepted papers</label></td>
          </tr>
          <tr> 
            <td><label> 
              <input type="radio" name="extractType" value="3">
              rejected papers</label></td>
          </tr>   
          <tr> 
            <td><label> 
              <input type="radio" name="extractType" value="5">
              withdrawn papers</label></td>
          </tr>       
        </table></td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><strong>Zipfile Name:</strong> (If you leave this field blank, the Conference 
        code name will be used as the zip file name)</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td>Zipfile name:&nbsp;<input name="zipfilename" type="text" id="zipfilename" size="25" maxlength="30">
        (e.g., WDIC2003)</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><input name="Submit" type="submit" id="Submit" value="Extract Papers"></td>
    </tr>
  </table>
</form>
<?php do_html_footer(); ?>
