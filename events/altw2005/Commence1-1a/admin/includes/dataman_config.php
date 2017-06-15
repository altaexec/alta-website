<?php #michael@creotec

#release version - please do not remove
$buildversion = "4.0 Beta";

#set your database connection information below
$datahost = $GLOBALS["DB_HOSTNAME"] ;
$datauser = $GLOBALS["DB_USERNAME"] ;
$datapasswd = $GLOBALS["DB_PASSWORD"] ;

#enter the database names you want accessiable via dataman web interface
$dbarr = array( $GLOBALS["DB_DATABASE"] ) ;

#change the db connection properties below for the logging function
#this logs all queries sent to the database - don't forget to create the required log table
$log_host = $GLOBALS["DB_HOSTNAME"] ;
$log_username = $GLOBALS["DB_USERNAME"] ;
$log_password = $GLOBALS["DB_PASSWORD"] ;
$log_db = $GLOBALS["DB_DATABASE"] ;

#fields and properties - to show properties
$showproperties = "no"; #(yes or no)

#export file data separator (e.g. "," or "\t" tab);
$exportseparator = ",";

#export field enclosure (e.g "\"","'" or "" for none);
$exportenclose = "\"";

#export file extension (e.g. "csv","tsv")
$exportension = "csv";

?>