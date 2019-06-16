<?php 
	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	extract ( $HTTP_GET_VARS , EXTR_REFS ) ;
	extract ( $HTTP_POST_VARS , EXTR_REFS ) ;
	extract ( $HTTP_POST_FILES , EXTR_REFS ) ;

	require_once("includes/include_all_fns.php");
	require_once("includes/dataman_config.php");
	require_once("includes/dataman_functions.php");	
	session_start() ;
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;

?>

<script language="javascript">
<!--//
function confirmaction(myURL,myMES){
	var res = confirm(myMES);
	if(res){
		location.href=myURL;
	}
}
//-->
</script>
<?php #michael@creotec

do_html_header("Database Management");

echo "<span class=\"myheader\">DataMan - Quick Database Content Manager</span><br />Release Version $buildversion<br />";


echo "<a href=\"dataman.php\">clear all</a>";

if ($sel_table) {
	echo"| <a title=\"click here to export data as CSV\" target=\"exportit\" href=\"dataman_export.php?exp=".base64_encode($sel_table)."&dbsel=".base64_encode($sel_db)."\">export data</a>";
}

echo "<form name='dataman' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

dbconnect("$datahost","$datauser","$datapasswd","$sel_db");

ddtables ("$sel_db");
if ($sel_table) {
	echo " sort by ".ddfieldlist($sel_table,$myddfield)." in <select name=\"myorder\"><option value=\"asc\">ascending</option><option value=\"desc\">descending</option></select>";
}
echo " set limits min:<input type=\"text\" name=\"lmin\" value=\"$lmin\" size=\"3\"> and max:<input type=\"text\" name=\"lmax\" value=\"$lmax\" size=\"3\">&nbsp;&nbsp;<input type=\"submit\" value=\"submit\">";
echo "<hr />";
echo "</form>";

if (!empty($ddvalue) && $mode == "update") {
	echo "<p>\n";
	echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>\n";
	ddrowupdate("$ddtable","$ddfield","$ddvalue","$sel_db");
	echo "<input type=\"submit\" value=\"update row\">\n";
	echo "</form>\n";

}

if (!empty($ddtable) && $mode == "addnew") {
	echo "<p>\n";
	// echo "<form method='post' action=\"$_SERVER[\"PHP_SELF\"]\">\n";
	ddrowadd("$ddtable","$sel_db");
	echo "<input type=\"submit\" value=\"add new row\">\n";
	echo "</form>\n";

}

echo "<p>\n";
ddrowlist("$sel_table","$sel_db");

#update query
if (!empty($fn) && $mode == "update") {
	$query .="update $table set";
		$k = 0;
		while (list($id,$value) = each($fn)){
		list($idd,$valued) = each($fd);
		  $k = $k;
		  if ($k > 0) {
		  $query = $query. ",";
		  }
		  $query = $query. " $value = '$valued'\n";
		  $k++;
		}

	$query .=" where $myfield = '$myvalue'";
	mysql_query($query);
	echo "$table in $sel_db updated";
	ddrowlist("$table","$sel_db");
	querylog("$query","$table","$sel_db");
}

#delete query
if ($mode == "delete") {
	$delquery = "delete from $ddtable where $ddfield = '$ddvalue'";
	mysql_query($delquery);
	querylog("$delquery","$ddtable","$sel_db");
	echo "$ddtable in $sel_db updated";
	@header("Location:".$_SERVER['PHP_SELF']."?sel_db=$sel_db&sel_table=$ddtable");
//	exit ;
}

#insert query
if (!empty($fn) && $mode == "addnew") {
	$insertquery .=" insert into $table (";
	    $k = 0;
	    while (list($id,$value) = each($fn)){
	      $k = $k;
	      if ($k > 0) {
	      $insertquery = $insertquery. ",";
	      }
	      $insertquery = $insertquery. " $value";
	      $k++;
	    }
	$insertquery .=") values (";
	    $k = 0;
	    while (list($id,$value) = each($fd)){
	      $k = $k;
	      if ($k > 0) {
	      $insertquery = $insertquery. ",";
	      }
	      $insertquery = $insertquery. " '$value'";
	      $k++;
	    }
	$insertquery .=")";
	mysql_query($insertquery);
	echo "$table in $sel_db updated";
	ddrowlist("$table","$sel_db");
	querylog("$insertquery","$table","$sel_db");
	}

if ($showproperties == "yes") {
	echo "<p />";
	ddfield("$sel_table");
}

do_html_footer();
?>
