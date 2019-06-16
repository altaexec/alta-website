<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	$name = & $HTTP_GET_VARS["name"];	

 ?>
<html>
<head>
<title>Commence Conference System</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php echo "<link href=\"$php_root_path/stylesheets/CommentStyle.css\" rel=\"stylesheet\" type=\"text/css\">\n"; ?>
</head>
<h1>Reviewer Information</h1>
 <?php
	
	//Establish database connection
  	$db = adodb_connect();
  
  	if (!$db){
   		echo "Could not connect to database server - please try later.";
		exit;
	}
	
	$reviewerSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Member M," . $GLOBALS["DB_PREFIX"]."Registration R";
	$reviewerSQL .= " WHERE M.RegisterID = R.RegisterID";
	$reviewerSQL .= " AND MemberName = '$name'";
	$reviewerResult = $db -> Execute($reviewerSQL);
	$reviewerInfo = $reviewerResult -> FetchNextObj();
	

?>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr> 
    <td width="30%"><strong>Reviewer Name:</strong></td>
    <td width="70%"><?php echo $reviewerInfo -> MemberName; ?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><strong>Fullname:</strong></td>
    <td><?php echo  $reviewerInfo -> FirstName." ".$reviewerInfo -> MiddleName." ". $reviewerInfo -> LastName; ?></td>
  </tr>
  <tr> 
    <td><strong>Organisation:</strong></td>
    <td><?php echo  $reviewerInfo -> Organisation; ?></td>
  </tr>
  <tr> 
    <td><strong>Email:</strong></td>
    <td><?php echo $reviewerInfo -> Email; ?></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><input name="Button" type="button" onClick="JavaScript:window.close()" value="Close"></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
