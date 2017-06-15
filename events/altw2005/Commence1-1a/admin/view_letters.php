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
	
	//Make the SQL to retrieve the letters
	$lettersSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"]."Letter L," . $GLOBALS["DB_PREFIX"]."RecipientGroup R";
	$lettersSQL .= " WHERE L.RecipientGroupID = R.RecipientGroupID";
	
	//Check the sorting by Title
	switch($HTTP_GET_VARS["sort"]){
			case 1: 
				$lettersSQL .= " ORDER BY LetterID ASC";
				$strSort = "LetterID - Ascending";
				break;
			case 2:
				$lettersSQL .= " ORDER BY LetterID DESC";
				$strSort = "LetterID - Descending";
				break;	
			case 3:
				$lettersSQL .= " ORDER BY Title ASC";
				$strSort = "Title - Ascending";
				break;
			case 4:
				$lettersSQL .= " ORDER BY Title DESC";
				$strSort = "Title - Descending";
				break;				
			default:
				$lettersSQL .= " ORDER BY LetterID ASC";
				$strSort = "LetterID - Ascending";
				break;							
	}					
	
	$lettersResult = $db -> Execute($lettersSQL);	
		
	do_html_header("View Formatted Letters");
	
	//Check if there are any letters already setup
	if($lettersResult -> RecordCount() == 0){
		echo "<p>There are no formatted letters to display.<br><br>";
		echo "<a href=\"add_new_lettertype.php\">Add a new Letter?</a><br></p>";
		do_html_footer();
		exit;
	}
	
?>
  <table width="100%" border="0" cellspacing="2" cellpadding="0">
    <tr> 
      <td width="50%"><strong>Total Letters:</strong>&nbsp;<?php echo $lettersResult -> RecordCount(); ?></td>
      <td width="50%" align="right">Order By:&nbsp;<strong><?php echo $strSort;  ?></strong></td>
    </tr>
	<tr><td colspan="2">&nbsp;</td></tr>
  </table>
 <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td width="5%"><strong>ID</strong></td>
      <td width="65%"><a href="view_letters.php?sort=1"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>LetterID</strong>&nbsp;<a href="view_letters.php?sort=2"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>&nbsp;<a href="view_letters.php?sort=3"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;<strong>Title</strong>&nbsp;<a href="view_letters.php?sort=4"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></td>
      
    <td width="30%" align="right">&nbsp;</td>
    </tr>
	<tr><td colspan="3">&nbsp;</td></tr>
    <?php while($lettersInfo = $lettersResult -> FetchNextObj()){
			
			//Get the url for edit letter
			$arrURL = evaluate_Letter_URL($lettersInfo -> Title,$lettersInfo -> LetterID);			
	?>
    <tr> 
      <td valign="top">#<?php echo $lettersInfo -> LetterID; ?></td>
      <td valign="top"><p><strong>Title:</strong>&nbsp;<?php echo $lettersInfo -> Title; ?><br>
	  	<?php if ($lettersInfo -> Subject != "") 
       		echo "<strong>Subject: </strong>".stripslashes($lettersInfo -> Subject); ?>
		<br><br>
	  	<?php if ($lettersInfo -> RecipientGroupName != "") 
       		echo "<strong>Recipient Group: </strong>".$lettersInfo -> RecipientGroupName; ?>
        </p></td>
      <td valign="top">
	  	<ul>
          <?php if ($lettersInfo -> Subject != "") 		echo "<li>".$arrURL ["view"]."</li>"; ?>	
          <li><?php echo "<a href=\"".$arrURL ["edit"]."\">Edit Letter</a>"; ?></li>		
		 <?php if (($lettersInfo -> Subject != "") && ($lettersInfo -> Title != "User Account Info"))
       		echo "<li>".$arrURL ["send"]."</li>"; ?>  
        </ul></td>
    </tr>
    <tr> 
      <td colspan="3"><hr></td>
    </tr>
    <?php } ?>
</table>
<?php do_html_footer(); ?>
