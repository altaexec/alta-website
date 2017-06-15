<?php #michael@creotec

######### function starts ##############

#function to return only part of a string - chop it phase only where there is a space
function datamanchopstring($text,$len,$breaker = " ") {
	$vertext = strrchr(substr("$text",0,$len),"$breaker");
	$mylen = strlen ($vertext);
	$mylen = $len-$mylen;
	$newtext = substr($text,0,$mylen);
	if (strlen($text) > $len) {
		$newtext = $newtext. " &nbsp;&nbsp;[... more]";
	}
	return $newtext;
}

function dbconnect($host,$uname,$passwd,$mydb) {
	global $dbarr;
	$db = mysql_connect($host,$uname,$passwd);
	$db_list = mysql_list_dbs($db);
	echo "\nDatabases: <select name=\"sel_db\" onChange=\"JavaScript:form.submit();\">\n";
	echo "<option value=\"\">select database ....</option>\n";
	while ($row = mysql_fetch_object($db_list)) {
		foreach ($dbarr as $myvalue) {
			if ($row->Database == $myvalue) {
				if ($mydb == "$row->Database") {
					echo "<option value=\"$row->Database\" selected>$row->Database</option>\n";
				} else {
					echo "<option value=\"$row->Database\">$row->Database</option>\n";
				}
			}
		}
	}
	echo "</select>";
}


function ddtables ($dbname){
	global $sel_table;
	if (!empty($dbname)) {
		$result = mysql_list_tables($dbname);
		$i = 0;
		echo "\nTables: <select name=\"sel_table\">\n";
		echo "<option value=\"\">select table ....</option>\n";
		while ($i < mysql_num_rows ($result)) {
		    $tb_names[$i] = mysql_tablename ($result, $i);
		    if ($tb_names[$i] == "$sel_table") {
		    	echo "<option value=\"$tb_names[$i]\" selected>$tb_names[$i]</option>\n";
			} else {
				echo "<option value=\"$tb_names[$i]\">$tb_names[$i]</option>\n";
			}
		    $i++;
		}
		echo "</select>";
	}
}

function ddfield($table) {
	if (!empty($table)) {
		$result = mysql_query("select * from $table");
		$fields = mysql_num_fields ($result);
		$rows   = mysql_num_rows ($result);
		$table = mysql_field_table ($result, $k);
		$k=0;
		echo "<b>Table Properties: Name: $table</b><br>";
		echo "<table border='1' cellpadding='4' cellspacing='2' width='80%'><tr><td><b>Field Name</b></td><td><b>Field Type</b></td><td><b>Field Length</b></td><td><b>Field Flags<b></td></tr>";
		while ($k < $fields) {
			echo "<tr>";
		    $name  = mysql_field_name  ($result, $k);
		    $type  = mysql_field_type  ($result, $k);
		    $len   = mysql_field_len   ($result, $k);
		    $flags = mysql_field_flags ($result, $k);
		    echo "<td>".$name."</td><td>".$type."</td><td>".$len."</td><td>".$flags."</td>";
		    $k++;
			echo "</tr>";
		}
		echo "</table>";
	}
}



#function get all fieldlist into selection dropdown
function ddfieldlist($table,$selfield=NULL) {
	if ($table) {
		$result = mysql_query("select * from $table");
		$fields = mysql_num_fields ($result);
		$output .= "<select name=\"myddfield\">";
		$output .= "<option value=\"\">$fname</option>";
		for ($k = 0; $k < $fields; $k++) {
			$fname = mysql_field_name($result, $k);
			$output .= "<option value=\"$fname\">$fname</option>";
		}
		$output .= "</select>";
		return $output;
	}
}

function ddrowlist($table,$mydb) {
	global $lmin,$lmax,$myddfield,$myorder;
	if (empty($lmin)) { $lmin = 0; }
	if (empty($lmax)) { $lmax = 50; }
	if (!empty($table) && ($lmax >= $lmin)) {

		if ($myddfield) {
			$sql = "select * from $table order by $myddfield $myorder limit $lmin,$lmax ";
		} else {
			$sql = "select * from $table limit $lmin,$lmax";
		}
		$result = mysql_query($sql);
		$fields = mysql_num_fields ($result);
		$rows   = mysql_num_rows ($result);
		$tblcol = $fields + 2;
		echo "<table border='1' cellpadding='4' cellspacing='2' width='80%'><tr>\n";
		echo "<tr><td colspan=$tblcol><b>Properties: </b>Table Name: $table | Number of rows: $rows | <a href=\"".$_SERVER['PHP_SELF']."?mode=addnew&ddtable=$table&sel_table=$table&sel_db=$mydb\">Add New Row</a></td></tr>";
		echo "<tr bgcolor=\"#cccccc\">";
		for ($k = 0; $k < $fields; $k++) {
			$fname = mysql_field_name($result, $k);
			echo "<td><b>$fname</b></td>\n";
		}
		echo "<td><b>&nbsp;</b></td><td><b>&nbsp;</b></td>";
		echo "</tr>\n";
		while ($row = mysql_fetch_object ($result)) {
			echo "<tr>\n";
				for ($i = 0; $i < $fields; $i++) {
					  $fname = mysql_field_name($result, $i);
					  $meta = mysql_fetch_field($result, $i);
					  echo "<td>";
					  if ($meta = $meta->primary_key || $type == "timestamp") {
						   echo "<a href=\"".$_SERVER['PHP_SELF']."?ddvalue=".$row->$fname."&mode=update&ddfield=".$fname."&ddtable=$table&sel_table=$table&sel_db=$mydb\">".$row->$fname."</a>";
						   $editlink = "<a href=\"".$_SERVER['PHP_SELF']."?ddvalue=".$row->$fname."&mode=update&ddfield=".$fname."&ddtable=$table&sel_table=$table&sel_db=$mydb\">edit</a>";
						   $passon = $row->$fname;
						   $passfname = $fname;
					  } else {
						   echo datamanchopstring(stripslashes($row->$fname),"150");
					  }
					  echo "</td>\n";
				}
			$strDelete = "ddvalue=".$passon."&mode=delete&ddfield=".$passfname."&ddtable=$table&sel_table=$table&sel_db=$mydb";
			echo "<td>$editlink</td>\n";
			echo "<td><a href=\"#\" onclick=\"javascript:confirmaction('".$_SERVER['PHP_SELF']."?$strDelete','Are you sure you wish to delete this entry?');\">delete</a></td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
}

function ddrowupdate($table,$field,$value,$mydb) {
	if (!empty($table) && !empty($field) && !empty($value)) {
		$result = mysql_query("select * from $table where $field='$value'");
		$fields = mysql_num_fields ($result);
		$rows   = mysql_num_rows ($result);
		$count = 0;
		echo "<table border='1' cellpadding='4' cellspacing='2' width='80%'>\n";
		echo "<tr><td colspan=4><b>Table Name:</b> $table | * marked fields require entry | <font color=red>Primary Key</font></td></tr>\n";
		echo "<tr bgcolor=\"#cccccc\"><td><b>Field</b></td><td><b>Value</b></td><td><b>Type</b></td><td><b>Length</b></td></tr>\n";
		echo "<input type=\"hidden\" name=\"table\" value=\"$table\">\n";
		echo "<input type=\"hidden\" name=\"myfield\" value=\"$field\">\n";
		echo "<input type=\"hidden\" name=\"myvalue\" value=\"$value\">\n";
		echo "<input type=\"hidden\" name=\"sel_db\" value=\"$mydb\">\n";
		echo "<input type=\"hidden\" name=\"mode\" value=\"update\">\n";
		while ($row = mysql_fetch_object ($result)) {
			for ($i = 0; $i < $fields; $i++) {
				  $meta = mysql_fetch_field ($result, $i);
				  $meta2 = mysql_fetch_field ($result, $i);
				  $type  = mysql_field_type  ($result, $i);
	    		  $len   = mysql_field_len   ($result, $i);
	    		  $fname = mysql_field_name($result, $i);
	    		  if ($len > 126) { $mylen = 126;} else { $mylen = $len;}
				  if ($meta2 = $meta->not_null) { $notnull = "*";} else { $notnull = "";}
				  if ($meta = $meta->primary_key || $type == "timestamp") {
					  echo "<tr><td><font color=red>$fname</font> $notnull</td><td>".stripslashes($row->$fname)."</td><td>$type</td><td>$len</td></tr>\n";
					  echo "<input type=\"hidden\" name=\"fd[$count]\" value=\"".$row->$fname."\">\n";
					  echo "<input type=\"hidden\" name=\"fn[$count]\" value=\"".$fname."\">\n";
				  } else {
					  if ($type == "blob") {
						  echo "<tr><td>$fname $notnull</td><td><textarea name=\"fd[$count]\" cols=\"95\" rows=\"10\">".stripslashes($row->$fname)."</textarea></td><td>$type</td><td>$len</td></tr>\n";
						  echo "<input type=\"hidden\" name=\"fn[$count]\" value=\"".$fname."\">\n";
					  } else {
					  echo "<tr><td>$fname $notnull</td><td><input type=\"text\" name=\"fd[$count]\" value=\"".stripslashes($row->$fname)."\" size=\"$mylen\" maxlength=\"$len\"></td><td>$type</td><td>$len</td></tr>\n";
					  echo "<input type=\"hidden\" name=\"fn[$count]\" value=\"".$fname."\">\n";
					  }
				  }
				$count++;
			}
		}
	echo "</table>\n";
	}
}


function ddrowadd($table,$mydb) {
if (!empty($table)) {
	$result = mysql_query("select * from $table");
	$fields = mysql_num_fields ($result);
	$rows   = mysql_num_rows ($result);
	$count = 0;
	echo "<table border='1' cellpadding='4' cellspacing='2' width='80%'>\n";
	echo "<tr><td colspan=4><b>Table Name:</b> $table | * marked fields require entry | <font color=red>Primary Key</font></td></tr>\n";
	echo "<tr><td><b>Field</b></td><td><b>Value</b></td><td><b>Type</b></td><td><b>Length</b></td></tr>\n";
	echo "<input type=\"hidden\" name=\"table\" value=\"$table\">\n";
	echo "<input type=\"hidden\" name=\"sel_db\" value=\"$mydb\">\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"addnew\">\n";
			for ($i = 0; $i < $fields; $i++) {
				  $meta = mysql_fetch_field ($result, $i);
				  $meta2 = mysql_fetch_field ($result, $i);
				  $type  = mysql_field_type  ($result, $i);
	    		  $len   = mysql_field_len   ($result, $i);
	    		  $fname = mysql_field_name($result, $i);
	    		  if ($len > 126) { $mylen = 126;} else { $mylen = $len;}
	    		  if ($meta2 = $meta->not_null) { $notnull = "*";} else { $notnull = "";}
				  if ($meta = $meta->primary_key || $type == "timestamp") {
					  echo "<tr><td><font color=red>$fname</font> $notnull</td><td>".$row->$fname."</td><td>$type</td><td>$len</td></tr>\n";
					  echo "<input type=\"hidden\" name=\"fd[$count]\" value=\"".$row->$fname."\">\n";
					  echo "<input type=\"hidden\" name=\"fn[$count]\" value=\"".$fname."\">\n";
				  } else {
					  if ($type == "blob") {
						  echo "<tr><td>$fname $notnull</td><td><textarea name=\"fd[$count]\" cols=\"95\" rows=\"10\"></textarea></td><td>$type</td><td>$len</td></tr>\n";
						  echo "<input type=\"hidden\" name=\"fn[$count]\" value=\"".$fname."\">\n";
					  } else {
					  echo "<tr><td>$fname $notnull</td><td><input type=\"text\" name=\"fd[$count]\" size=\"$mylen\" maxlength=\"$len\"></td><td>$type</td><td>$len</td></tr>\n";
					  echo "<input type=\"hidden\" name=\"fn[$count]\" value=\"".$fname."\">\n";
					  }
				  }
				  $count++;
			}
	echo "</table>\n";
	}
}


#function to log all queries passed to the database
#The result will be stored in the dataman db in the querylog table

function querylog($logquery,$tname,$dbname) {
	
	global $log_host,$log_username,$log_password,$log_db;
	$user_agent = getenv(HTTP_USER_AGENT);
	$path_info = getenv(PATH_INFO);
	$remote_address = getenv(REMOTE_ADDR);
	$query_string = getenv(QUERY_STRING);
	$remote_user = getenv(REMOTE_USER);
	$http_referer = getenv(HTTP_REFERER);
	$date = date ("Y-m-d", mktime (now));

	$logquery = addslashes($logquery);

	$sqlquerylog = "insert into " . $GLOBALS["DB_PREFIX"] . "querylog (dmdate,dmtablename,dmdbname,dmquery,dmbrowser,dmpath,dmremote,dmreferer,dmquerystring) values ('$date','$tname','$dbname','$logquery','$user_agent','$path_info','$remote_address','$http_referer','$query_string')";

	$logdb = mysql_connect($log_host,$log_username,$log_password);
	mysql_select_db($log_db,$logdb);
	mysql_query($sqlquerylog);
	mysql_close();

}

###### function ends #########

?>