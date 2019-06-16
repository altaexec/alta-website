<?php

global $php_root_path ;
require_once("$php_root_path/includes/main_fns.php");
require_once("$php_root_path/includes/output_fns.php");

class phasebase
{
	var $phaseID ;
	var $headers ;
	var $records ;
	var $paperResult ;
	var $writtenResult ;
	var $fileData ;

	function display_menu( &$header_str , $err_message = "" )
	{
		//global $_SESSION ;
//		$_SESSION["valid_user"] ;

		global $homepage ;
		global $php_root_path ;

		$db = adodb_connect( &$err_message );
		if (!$db)
		{
			$homepage->showmenu = 0 ;
			do_html_header("Load User Page Failed" , &$err_message );
			$err_message .= " Could not connect to database server - please try later. <br>\n" ;
			$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			do_html_footer( &$err_message );
			exit;
		}

		if ( ( $status = check_privilege_type( 1 , &$err_message ) ) !== false )
		{
			if ( $status == 0 )
			{
				$homepage->showmenu = 0 ;
				do_html_header("Load User Page Failed" , &$err_message ) ;
				$err_message .= " You do not have the authority to access this page.<br>\n";
				$err = $err_message . "<br><br> Go to <a href='$php_root_path/index.php'>Login</a> page." ;
				do_html_footer(&$err);
				exit;
			}
		}
		else
		{
			$homepage->showmenu = 0 ;
			do_html_header("Load User Page Failed" , &$err_message ) ;
			$err_message .= " Could not connect to database.<br>\n";
			$err = $err_message . "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			do_html_footer(&$err);
			exit;
		}
	}

	function get_edit_paper_info_sql( &$paperid )
	{
		//global $_SESSION ;

		//Execute the retrieve of written table
		$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Written W , " . $GLOBALS["DB_PREFIX"] . "Paper P";
		$sql .=" WHERE W.PaperID=" . $paperid . " AND W.PaperID=P.PaperID AND P.Withdraw='false' AND P.MemberName='" . addslashes ( $_SESSION["valid_user"] ) . "'" ;
		return $sql ;
	}

	function set_edit_paper_info( &$paperid , &$array , $err_message = "" )
	{
		// connect to db
		$db = adodb_connect( &$err_message );

		$settingInfo = get_Conference_Settings();

		if (!$db)
		{
			do_html_header("Edit Paper Details Failed" , &$err_message );
			$err_message .= " Could not connect to database server - please try later. <br>\n";
			$err_message .= "<br><br> Try <a href='edit_paper_info.php?paperid=$paperid'>again</a>?" ;
			do_html_footer( &$err_message );
			exit;
		}

		//Execute the retrieve of written table
		$sql = $this->get_edit_paper_info_sql( &$paperid ) ;

	//	echo "\$sql: " . $sql . "<br>\n";
		$written_result = $db -> Execute($sql);

		if(!$written_result)
		{
			do_html_header("Edit Paper Details Failed" , &$err_message );
			$err_message .= " There is error reading written table. <br>\n";
			$err_message .= "<br><br> Try <a href='edit_paper_info.php?paperid=$paperid'>again</a>?" ;
			do_html_footer( &$err_message );
			exit ;
		}

		if ( !$numrows = $written_result -> RecordCount() )
		{
			do_html_header("Edit Paper Details Failed" , &$err_message );
			$err_message .= " The requested infomation is not available. <br>\n";
			$err_message .= "<br><br> Try <a href='edit_paper_info.php?paperid=$paperid'>again</a>?" ;
			do_html_footer( &$err_message );
			exit ;
		}

		$array["paperid"] = $paperid ;

		if ( $settingsInfo -> SESUG ) {
			if ( $selcat = GetSelectedLevelList( $paperid , &$err_message ) )
			{
				$array["level"] = $selcat ;
			}
			else
			{
				do_html_header("Edit Paper Details Failed" , &$err_message );
				$err_message .= "<br><br> Try <a href='edit_paper_info.php?paperid=$paperid'>again</a>?" ;
				do_html_footer( &$err_message );
				exit ;
			}
		}
		if ( $selcat = GetSelectedTrackList( $paperid , &$err_message ) )
		{
			$array["track"] = $selcat ;
		}
		else
		{
			do_html_header("Edit Paper Details Failed" , &$err_message );
			$err_message .= "<br><br> Try <a href='edit_paper_info.php?paperid=$paperid'>again</a>?" ;
			do_html_footer( &$err_message );
			exit ;
		}

		if ( $selcat = GetSelectedCategoryList( $paperid , &$err_message ) )
		{
			$array["category"] = $selcat ;
		}
		else
		{
			do_html_header("Edit Paper Details Failed" , &$err_message );
			$err_message .= "<br><br> Try <a href='edit_paper_info.php?paperid=$paperid'>again</a>?" ;
			do_html_footer( &$err_message );
			exit ;
		}

		$array["numauthors"] = $numrows ;

		$firstname ;
		$middlename ;
		$lastname ;
		$email ;

		for( $i = 0 ; $i < $numrows ; $i++ )
		{
			$writtenInfo = $written_result -> FetchNextObj();

			$author_sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Author WHERE AuthorID = ".$writtenInfo -> AuthorID;

			$author_result = $db -> Execute($author_sql);

			if(!$author_result)
			{
				do_html_header("Edit Paper Details Failed" , &$err_message );
				$err_message .= " Error in reading author table <br>\n";
				$err_message .= "<br><br> Try <a href='edit_paper_info.php?paperid=$paperid'>again</a>?" ;
				do_html_footer( &$err_message );
				exit ;
			}

			$authorInfo = $author_result -> FetchNextObj();

			$firstname [ $i ] = $authorInfo -> FirstName;
			$middlename [ $i ] = $authorInfo -> MiddleName;
			$lastname [ $i ] = $authorInfo -> LastName;
			$email [ $i ] = $authorInfo -> Email;
		}

		$array["firstname"] = $firstname ;
		$array["middlename"] = $middlename ;
		$array["lastname"] = $lastname ;
		$array["email"] = $email ;

		$papersql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Paper WHERE PaperID =".$paperid;
		$result = $db -> Execute($papersql);

		if(!$result)
		{
			do_html_header("Edit Paper Details Failed" , &$err_message );
			$err_message .= " There is error in retrieving information <br>\n";
			$err_message .= "<br><br> Try <a href='edit_paper_info.php?paperid=$paperid'>again</a>?" ;
			do_html_footer( &$err_message );
			exit ;
		}

		$paperInfo = $result -> FetchNextObj();

		$array["title"] = htmlentities ( stripslashes ( $paperInfo -> Title ) ) ;
		$array["numpages"] = $paperInfo -> NumberOfPages ;
		$array["abstract"] = stripslashes( $paperInfo -> PaperAbstract ) ;
		$array["presenterbio"] = stripslashes( $paperInfo -> PresenterBio ) ;
		$array["attended"] = $paperInfo -> SESUG_Attended ;
		$array["presented"] = $paperInfo -> SESUG_Presented ;
		$array["keyword1"] = stripslashes( $paperInfo -> Keyword1 ) ;
		$array["keyword2"] = stripslashes( $paperInfo -> Keyword2 ) ;
		$array["keyword3"] = stripslashes( $paperInfo -> Keyword3 ) ;
	}

	function set_view_paper_details( &$fileid , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );

        //global $_SESSION ;

		$papersql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "File F , " . $GLOBALS["DB_PREFIX"] . "Paper P , " . $GLOBALS["DB_PREFIX"] . "PaperStatus PS";
		$papersql .= " WHERE F.FileID = $fileid AND P.MemberName = '" . addslashes ( $_SESSION["valid_user"] ) . "' AND P.PaperStatusID = PS.PaperStatusID AND P.PaperID = F.PaperID AND P.Withdraw='false'" ;
		// echo "\$papersql: " . $papersql . "<br>\n" ;	// Debug

		//Execute the retrieving of paper table
		$this->paperResult = $db -> Execute($papersql);
		$rows = $this -> paperResult -> RecordCount();

		if ( !$this->paperResult )
		{
			do_html_header("View Paper Details Failed" , &$err_message );
			$err_message .= " There is error reading paper table. <br>\n";
			$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?" ;
			do_html_footer( &$err_message );
			exit ;
		}
		else if ( !$rows )
		{
			do_html_header("View Paper Details Failed" , &$err_message );
			$err_message .= " The requested infomation is not available. <br>\n";
			$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?" ;
			do_html_footer( &$err_message );
			exit ;
		}

	}

	function get_view_paper_details( &$fileid , $err_message = "" )
	{
        //Establish connection with database
        $db = adodb_connect( &$err_message );

        $paperInfo = $this -> paperResult -> FetchNextObj();
		$sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . "Written" ;
		$sql .= " WHERE PaperID=" . $paperInfo -> PaperID ;

        //Execute the retrieve of written table
		$this -> writtenResult = $db -> Execute($sql);

		if(!$this->writtenResult)
		{
			do_html_header("View Paper Details Failed" , &$err_message );
			$err_message .= " There is error reading written table. <br>\n";
			$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?" ;
			do_html_footer( &$err_message ) ;
			exit ;
		}

		//Get the latestfile according to the paperID
		if ( !( $this->fileData = get_latestFile( $paperInfo->PaperID , &$err_message ) ) )
		{
			do_html_header("View Paper Details Failed" , &$err_message );
			$err_message .= "<br><br> Try <a href='view_paper_details.php?fileid=$fileid'>again</a>?" ;
			do_html_footer( &$err_message ) ;
			exit ;
		}

		return $paperInfo ;
	}

	function set_view_paper( $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );

        global $sort ;
		global $showing ;
		global $totalPapers ;
		global $rows ;
		//global $_SESSION ;

//		$paperSQL = "SELECT DISTINCT ( P.PaperID ) , P.Title , PS.PaperStatusName FROM " . $GLOBALS["DB_PREFIX"] . "Paper P , " . $GLOBALS["DB_PREFIX"] . "PaperStatus PS , " . $GLOBALS["DB_PREFIX"] . "File F";
//		$paperSQL .= " WHERE P.PaperStatusID = PS.PaperStatusID AND P.PaperID = F.PaperID AND MemberName = '" . addslashes ( $valid_user ) . "' AND Withdraw='false'";

		$paperSQL = "SELECT P.PaperID , P.Title , PS.PaperStatusName , MAX( F.DateTime ) AS DateTime " ;
		$paperSQL .= " FROM " . $GLOBALS["DB_PREFIX"] . "File F , " . $GLOBALS["DB_PREFIX"] . "Paper P , " . $GLOBALS["DB_PREFIX"] . "PaperStatus PS " ;
		$paperSQL .= " WHERE P.PaperStatusID=PS.PaperStatusID AND P.PaperID=F.PaperID AND P.MemberName = '" . addslashes ( $_SESSION["valid_user"] ) . "' AND P.Withdraw='false' " ;
		$paperSQL .= " GROUP BY F.PaperID " ;
		$countPaperResult = $db -> Execute( $paperSQL ) ;

        if (!$countPaperResult)
		{
			do_html_header("View Papers Failed" , &$err_message ) ;
			$err_message .= "Could not query View Paper database. <br>\n";
			$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;
			do_html_footer( &$err_message ) ;
			exit ;
		}

		$totalPapers = $countPaperResult -> RecordCount() ;

		if ( $totalPapers <= 0 )
		{
			$this->display_header("View Papers" , &$err_message ) ;
			echo $message .= " There are no papers uploaded yet.<br>\n";
			if ( $this -> phaseID == 1 )
			{
				echo "<br><br><a href='upload_paper.php'>Add</a> a paper?" ;
			}
			do_html_footer( &$err_message ) ;
			exit;
		}

		//Check the sorting by Title
		switch($sort)
		{
			case 1:
			{
				$paperSQL .= " ORDER BY Title ASC";
				break;
			}
			case 2:
			{
				$paperSQL .= " ORDER BY Title DESC";
				break;
			}
			case 3:
			{
				$paperSQL .= " ORDER BY DateTime ASC";
				break;
			}
			case 4:
			{
				$paperSQL .= " ORDER BY DateTime DESC";
				break;
			}
			case 5:
			{
				$paperSQL .= " ORDER BY P.PaperID ASC";
				break;
			}
			case 6:
			{
				$paperSQL .= " ORDER BY P.PaperID DESC";
				break;
			}
			case 7:
			{
				$paperSQL .= " ORDER BY PS.PaperStatusName ASC";
				break;
			}
			case 8:
			{
				$paperSQL .= " ORDER BY PS.PaperStatusName DESC";
				break;
			}
			default:
				$paperSQL .= " ORDER BY P.PaperID";
				break;
		}

		$paperSQL .= " LIMIT ".$showing.",".MAX_PAPERS;

		$this->paperResult = $db -> Execute($paperSQL);
		$rows = $this -> paperResult -> RecordCount();

		$this->display_header("View Papers" , &$err_message ) ;
	}

	function display_header($title , $err_message = "" )
	{
		do_html_header($title , &$err_message );
	}

	function GenerateViewPaperTable()
	{
		global $showing ;
		global $rows ;

		reset ( $this->headers ) ;
		reset ( $this->records ) ;

	?>
		<table width="100%" border="1" cellspacing="3" cellpadding="3">
		  <tr align="center">
	<?php
		foreach ( $this->headers as $name => $width )
		{
/*
Name is either:
$this->headers["<a href=\"view_papers.php?sort=3&showing=$showing\"><img src=\"images/up.gif\" border=0></a>&nbsp;Upload Time&nbsp;<a href=\"view_papers.php?sort=4&showing=$showing\"><img src=\"images/down.gif\" border=0></a>"] = 25 ;
Or:
$this->headers["ID"] = 5 ;
*/
	?>
			<td width="<?php echo $width ; ?>%"><STRONG><?php echo $name ; ?></STRONG></td>
	<?php
		}

		reset ( $this->headers ) ;

		for( $i = 0 ; $i < $rows ; $i++ )
		{
			echo "<tr align='center'>" ;
		  	while ( list ( $field ) = each ( $this->headers ) )
			{
/*
$this->records[$field][$i] is either:
$this->records[$field][$i]["view_file.php?fileid=$FileData->FileID\" target=\"_blank"] = stripslashes($data -> Title) ;
Or:
$this->records[$field][$i]["view_paper_details.php?fileid=$FileData->FileID"] = "View" ;
Or:
$this->records[$field][$i] = $data->PaperStatusName ;
*/

				if ( is_array ( $this->records[$field][$i] ) )
				{
					list ( $urlname , $link ) = each ( $this->records[$field][$i] )
	?>
			<td><a href="<?php echo $urlname ; ?>"><?php echo $link ; ?></a></td>
	<?php
				}
				else
				{
	?>
			<td><?php echo $this->records[$field][$i] ; ?></td>
	<?php
				}
			}
			reset ( $this->headers ) ;
		}
	?>
		</table>
	<?php
	reset ( $this->headers ) ;
	reset ( $this->records ) ;

	}
}

?>
