<?php

$php_root_path = ".." ;
require_once("$php_root_path/includes/include_all_fns.php");
require_once("$php_root_path/includes/page_includes/page_fns.php");

$db = adodb_connect();

if (!$db)
    return "Could not connect to database server - please try later.";

$result = $db -> Execute("SELECT * FROM ".$GLOBALS["DB_PREFIX"]."Settings");

$data = $result -> FetchRow();

$result = $db -> Execute("DROP TABLE ".$GLOBALS["DB_PREFIX"]."Settings");
if (!$result) 
    echo "Migration Failed";
$sql = "CREATE TABLE ".$GLOBALS["DB_PREFIX"]."Settings ";
$sql .= "( Name VARCHAR(128) NOT NULL, Value BLOB NULL, PRIMARY KEY (Name) );";
$result = $db -> Execute($sql);
if (!$result) 
    echo "Migration failed in unstable state. Possible loss of settings.<br>";

foreach ($data as $key => $value)
{
    $sql = "INSERT INTO ".$GLOBALS["DB_PREFIX"]."Settings (Name, Value) ";
    $sql .= "VALUES ('$key', '$value')";
    $result = $db -> Execute($sql);
    if (!$result) 
        echo "Migration failed in unstable state. Possible loss of settings.<br>";
}

echo "If no errors are noted above, then migration was successful.";

?>