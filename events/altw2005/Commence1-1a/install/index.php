<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php

//	$conn_id = ftp_connect("www.itee.uq.edu.au");
//	$conn_id = @ftp_connect("localhost");

//	if ( $login_result )
	//ftp.members.lycos.co.uk
//	$mode= @ftp_pasv( $conn_id , 1 );
//	$conn_id = ftp_connect("www.members.lycos.co.uk");
//	$ftp = "ftp://shaggy78:shaggysg@ftp.members.lycos.co.uk" ;
//	$ftp = "ftp.members.lycos.co.uk" ;

	$ftp = "ftp.members.lycos.co.uk" ;
	$conn_id = ftp_ssl_connect("$ftp") ;
	$login_result = ftp_login ( $conn_id , "shaggy78" , "shaggysg" ) ;
/*
	$ftp = "ftp.members.lycos.co.uk" ;
	$conn_id = ftp_connect("$ftp") ;
	$login_result = ftp_login ( $conn_id , "shaggy78" , "shaggysg" ) ;
*/	
	$mode= ftp_pasv( $conn_id , 1 );	
	
	if ( $conn_id )
	{
		echo "<br>\nCorrect!<br>\n" ;
	}
	else
	{
		echo "<br>\nNot Correct!<br>\n" ;	
	}

	if ( $login_result )
	{
		echo "<br>\nLogin Correct!<br>\n" ;
	}
	else
	{
		echo "<br>\nLogin Not Correct!<br>\n" ;	
	}

	if ( $mode )
	{
		echo "<br>\nMode Correct!<br>\n" ;
	}
	else
	{
		echo "<br>\nMode Not Correct!<br>\n" ;	
	}	
	
//	$filename = addslashes ( "ftp://shaggy78:shaggysg@ftp.members.lycos.co.uk/index.html/" ) ;
//	$filename = addslashes ( "http://www.itee.uq.edu.au/~tahliang/WebComments/index.php/" ) ;
//	$filename = "sql/Database.sql" ;
//	$filename = addslashes ( "ftp://shaggy78:shaggysg@ftp.members.lycos.co.uk/index.html/" ) ;
//	$filename = addslashes ( "http://members.lycos.co.uk/shaggy78/WebComments/install/install.php/" ) ;
//	$filename = addslashes ( "http://www.itee.uq.edu.au/~tahliang/WebComments/index.php" ) ;
//	$filename = addslashes ( "http://members.lycos.co.uk/shaggy78/WebComments/install/install.php" ) ;
/*
//Works!
//	$filename = "../includes/db_connect.php" ;
//	$filename = addslashes ( $_SERVER["DOCUMENT_ROOT"]."/WebComments/install/install.php" ) ;	
//	$filename = addslashes ( $_SERVER["SCRIPT_URI"] ) ;	
//	$filename = addslashes ( $_SERVER["SCRIPT_URI"] . "install.php" ) ;	
//	$filename = "http://www.itee.uq.edu.au/~tahliang/WebComments/install/install.php" ;
	echo $filename ;
	if ( fopen ( $filename , "r" ) ) 
	{
		echo "<br>\n\"" . stripslashes($filename) . "\" is open!<br>\n" ;	
//		if ( file_exists ( $filename ) )
		{
//			echo "<br>\n\"" . stripslashes($filename) . "\" exist!<br>\n" ;			
			if ( is_writeable ( $filename ) )
			{
				echo "<br>\nWritable!<br>\n" ;			
			}
			else
			{
				echo "<br>\nNot Writable!<br>\n" ;
			}
		}
//		else
//		{
//			echo "<br>\n\"" . stripslashes($filename) . "\" does not exist!<br>\n" ;	
//		}
	}
	else
	{
		echo "<br>\n\"" . stripslashes ( $filename) ."\" Could not be open!<br>\n" ;		
	}	
*/
?>
</body>
</html>
