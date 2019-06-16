<?php

require_once("$php_root_path/includes/globals.php");

// ADOdb includes
require_once("$php_root_path/includes/adodb/adodb.inc.php");

function adodb_connect($err_message = "")
{
    $db = ADONewConnection("mysql");
	$connected = $db->Connect( $GLOBALS["DB_HOSTNAME"], 
                               $GLOBALS["DB_USERNAME"], 
                               $GLOBALS["DB_PASSWORD"], 
                               $GLOBALS["DB_DATABASE"]);
    if (!$connected)
    {
		$err_message .= " Unable to connect to database in \"db_query\". <br>\n" ;
		return false;
	}
    return $db;
}

function db_quick_query($sql, $err_message = "")
{
    $db = adodb_connect(&$err_message);
    $result = $db->Execute($sql);
    return $result;
}


// DB Formatting Functions

// Date formatting for SELECT statements
function dbdf_out($db, $fieldName, $newFieldName = "")
{
	if (!$db) $db = ADONewConnection("mysql");
    $sql = $db -> SQLDate("Y-m-d", $fieldName);
    if ($newFieldName)
        return "$sql as $newFieldName";
    else
        return "$sql as $fieldName";
}

// Date formatting for INSERT, UPDATE and REPLACE statements
function dbdf_in($db, $date)
{
	if (!$db) $db = ADONewConnection("mysql");
    return $db -> DBDate($date);
}

// Returns a quoted string for use in INSERT, REPLACE and UPDATE queries
function db_quote($db, $value)
{
    // This method safely handles "Magic Quotes"
    // * Magic quotes -> is addslashes done automatically?
    // * Magic quotes are evil!
    //   http://www.webmasterstop.com/tutorials/magic-quotes.shtml
	if (!$db) $db = ADONewConnection("mysql");
    return $db->qstr($value,get_magic_quotes_gpc());
}

?>