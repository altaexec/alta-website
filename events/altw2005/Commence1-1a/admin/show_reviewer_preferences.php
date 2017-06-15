<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	
	$name = & $HTTP_GET_VARS["name"];	
	
	//do_html_header("Reviewer Preferences");
	
	//Establish database connection
  	$db = adodb_connect();
  
  	if (!$db){
   		echo "Could not connect to database server - please try later.";
		exit;
	}
	
	if(!verify_Preference_Exist($name)){
		echo "No preferences have been given by the reviewer <strong>".$name."</strong>.";
		echo "<p><input type=\"button\" name=\"close\" value=\"Close\" onClick=\"JavaScript:window.close()\"></p>";
		exit;
	}
	
	//Get the preferences
	$preferenceSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Preference";
	$preferenceResult = $db -> Execute($preferenceSQL);
		
?>
<html>
<head>
<title>Commence Conference System</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="stylesheets/CommentStyle.css" rel="stylesheet" type="text/css">
<?php echo "<link href=\"$php_root_path/stylesheets/CommentStyle.css\" rel=\"stylesheet\" type=\"text/css\">\n"; ?>
</head>
<h1>Reviewer <?php echo $name; ?>'s Preferences</h1>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td>Reviewer: <strong><?php echo $name; ?></strong></td>
  </tr>
  <?php while($preferenceInfo = $preferenceResult -> FetchNextObj()){
  
  		//Retrieve the preferences on the paper
		$paperSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Selection";
		$paperSQL .= " WHERE PreferenceID = ".$preferenceInfo -> PreferenceID;
  		$paperSQL .= " AND MemberName = '$name'";
		$paperResult = $db -> Execute($paperSQL);

		if($paperResult -> RecordCount() != 0){ ?>
  <tr> 
    <td><hr></td>
  </tr>
  <?php }/*end of if statement*/ ?>
  <tr> 
    <td> 
      <?php if($paperResult -> RecordCount() != 0) echo "<strong>".$preferenceInfo -> PreferenceName."</strong><br>"; ?>
    </td>
  </tr>
  <?php  while($paperIDInfo = $paperResult -> FetchNextObj()){
  			
		//Call the function to get the paperinfo
		$paperInfo = get_paper_info($paperIDInfo -> PaperID);	
  ?>
  <tr> 
    <td><?php echo "Paper#".$paperInfo -> PaperID." - ".stripslashes($paperInfo -> Title); ?></td>
  </tr>
  <?php }/*end of inner while loop*/?>
  <?php
  }/*end of while statement*/?>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><input type="button" name="Close" value="Close" onClick="JavaScript:window.close()"></td>
  </tr>
</table>
</body>
</html>
