<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	

	do_html_header("View All Users");

	//Establish connection with database
	$db = adodb_connect();
	
	if (!$db){
		echo "Could not connect to database server - please try later.";
		exit;
	}	
	
	$memberSQL = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Member M," . $GLOBALS["DB_PREFIX"] . "PrivilegeType P," . $GLOBALS["DB_PREFIX"]."Registration R";
	$memberSQL .= " WHERE M.RegisterID <> 0";
	$memberSQL .= "	AND M.PrivilegeTypeID = P.PrivilegeTypeID";
	$memberSQL .= "	AND M.RegisterID = R.RegisterID";	
	
	//Check the sorting by Title
	switch($HTTP_GET_VARS["sort"]){
			case 1:
				$memberSQL .= " ORDER BY M.MemberName ASC";
				$sortStr = "UserName - Ascending";
				break;
			case 2:
				$memberSQL .= " ORDER BY M.MemberName DESC";
				$sortStr = "UserName - Descending";
				break;
			case 3:
				$memberSQL .= " ORDER BY R.FirstName ASC";
				$sortStr = "FullName - Ascending";
				break;
			case 4:
				$memberSQL .= " ORDER BY R.FirstName DESC";
				$sortStr = "FullName - Descending";
				break;
			case 5:
				$memberSQL .= " ORDER BY R.Organisation ASC";
				$sortStr = "Organisation - Ascending";
				break;
			case 6:
				$memberSQL .= " ORDER BY R.Organisation DESC";
				$sortStr = "Organisation - Descending";
				break;																
			case 7:
				$memberSQL .= " ORDER BY P.PrivilegeTypeID ASC";
				$sortStr = "Privilege - Ascending";
				break;
			case 8:
				$memberSQL .= " ORDER BY P.PrivilegeTypeID DESC";
				$sortStr = "Privilege - Descending";
				break;								
			default:
				$memberSQL .= " ORDER BY P.PrivilegeTypeID";
				$sortStr = "Privilege - Ascending";
				break;							
	}						
			
	$memberResult = $db -> Execute($memberSQL);
	
	if(!$memberResult){
		echo "Could not retrieve the members' information - please try again later";
		exit;
	}	

?>
<form name="form1" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr> 
      <td><strong>Total Users:</strong>&nbsp;<?php echo $memberResult -> RecordCount(); ?></td>
      <td align="right"><strong>Order By:</strong>&nbsp;<?php echo $sortStr; ?></td>
    </tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
    <tr> 
      <td colspan="2"><table width="100%" border="1" cellspacing="2" cellpadding="1">
          <tr> 
            <td width="15%"><a href="view_all_users.php?sort=1"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;User 
              Name&nbsp;<a href="view_all_users.php?sort=2"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></td>
            <td width="30%"><a href="view_all_users.php?sort=3"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp;Full Name&nbsp;<a href="view_all_users.php?sort=4"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></td>
            <td width="40%"><a href="view_all_users.php?sort=5"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a>&nbsp;Organization&nbsp;<a href="view_all_users.php?sort=6"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></td>
            <td width="15%"><a href="view_all_users.php?sort=7"><img src="<?php echo $php_root_path ; ?>/images/up.gif" border=0></a>&nbsp; 
              Privilege&nbsp;<a href="view_all_users.php?sort=8"><img src="<?php echo $php_root_path ; ?>/images/down.gif" border=0></a></td>
          </tr>
          <?php while( $memberInfo = $memberResult -> FetchNextObj()) {?>
          <tr> 
            <td><?php echo $memberInfo -> MemberName; ?></td>
            <td><?php echo getMemberFullName($memberInfo -> MemberName);?></td>
            <td><?php echo ($memberInfo -> Organisation !="") ? stripslashes( $memberInfo -> Organisation ): "N/A"; ?></td>
            <td><?php echo $memberInfo -> PrivilegeTypeName; ?></td>
          </tr>
          <?php }/*end of while loop*/?>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
</form>

<?php do_html_footer(); ?>
