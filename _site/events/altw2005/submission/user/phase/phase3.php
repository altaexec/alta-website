<?php

global $php_root_path ;
require_once ("$php_root_path/user/phase/phase2.php") ;

class phase3 extends phase2
{
	function phase3()
	{
		$this->phaseID = 3 ;
		$this->records = array() ;		
		$this->headers = array() ;		
	}
	
	function set_view_records ( $err_message = "" )
	{
		global $rows ;
		phase2::set_view_records ( &$err_message ) ;
		
		$this -> paperResult -> MoveFirst() ;	

		reset ( $this->records ) ;
		list ($id) = each ( $this->headers ) ;		
		list ($title) = each ( $this->headers ) ;
		list ($upLoadTime) = each ( $this->headers ) ;
		list ($status) = each ( $this->headers ) ;	// Get pointer to status field
//		list ($viewDetails) = each ( $this->headers ) ;
		for( $i = 0 ; $i < $rows ; $i++ )
		{		
			$data = $this -> paperResult -> FetchNextObj();			
//			$this->records[$status][$i] = $this->get_numberOfReviews( $data->PaperID , &$err_message ) . " Review" ;
			$reviews = $this->get_numberOfReviews( $data->PaperID , &$err_message ) ;
			switch ( $reviews )
			{
		  	  case 0 :
				$this->records[$status][$i] = "No Reviews" ;
				break ;
		  	  case 1 :
				$this->records[$status][$i] = "1 Review" ;
				break ;
		  	  case 2 :
				$this->records[$status][$i] = "2 Reviews" ;
				break ;
		  	  default :
				$this->records[$status][$i] = "All Reviews " ;
				break ;
			}			
		} 	
		reset ( $this->records ) ;							
	}

	function get_numberOfReviews( $paperID , $err_message = "" )
	{
		//Establish connection with database
		$db = adodb_connect( &$err_message );
		
		$paperSQL = "SELECT PaperID FROM " . $GLOBALS["DB_PREFIX"] .  "Review WHERE PaperID = $paperID AND Objectives <> 0 AND Validity <> 0 AND Innovativeness <> 0 AND Presentation <> 0 AND Bibliography <> 0 AND AcceptPaper <> 0 AND AcceptPoster <> 0" ;
		$countPaperResult = $db -> Execute( $paperSQL ) ;
		
	  	if (!$countPaperResult)
		{
			do_html_header("View Papers Failed" , &$err_message ) ;
			$err_message .= "Could not query View Paper database. <br>\n";
			$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "'>again</a>?" ;					
			exit ;
		}
		
		return ( $countPaperResult -> RecordCount() ) ;		
	}

}

?>
