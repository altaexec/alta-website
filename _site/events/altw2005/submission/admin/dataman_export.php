<?php #michael@creotec

$php_root_path = ".." ;
$privilege_root_path = "/admin" ;
require_once("includes/include_all_fns.php");
require_once("includes/dataman_config.php");

$exp = &$HTTP_GET_VARS["exp"];
$dbsel = &$HTTP_GET_VARS["dbsel"];

if ($exp && $dbsel) {
	$t_name = base64_decode($exp);
	$db_name = base64_decode($dbsel);
	
	//Establish database connection
  	$db = adodb_connect();
  
  	if (!$db){
   		echo "Could not connect to database server - please try later.";
		exit;
	}

	$date_stamp = date("Ymd");

	$file_name = "ExportedData_"."$t_name" . "_" . "$date_stamp" . ".".$exportension;

	if ($exportension == "tsv") {
		header("Content-type: text/tab-separated-values");
	} else {
		header("Content-type: application/x-msexcel");
	}
	header("Content-Disposition: attachment; filename=$file_name");

	#Specify the SELECT query (you will need to change this)...
	$query = "SELECT * FROM $t_name";

	#Execute the query...
	$result = $db -> Execute($query);

	#Format data as tab-separated values...
	while($row = $result -> FetchRow()) {
	  while (list($key, $value) = each($row)) {
		echo ("$exportenclose"."$value"."$exportenclose"."$exportseparator");
	  }
	  echo ("\n");
	}
	
} else {
	echo "No data was selected to export.";
}


?>