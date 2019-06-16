<?php

global $php_root_path ;
global $privilege_root_path ;

require_once ("$php_root_path/includes/page_includes/pagebase.php") ;

class Page extends PageBase
{	
	function DisplayHeader( $err_message = "" )
	{
		global $php_root_path ;
		
		header('Content-Type: text/html; charset=utf-8');		
		$header_str = "<html>\n" ;  // Display
		$header_str .= "<head>\n" ;	// Display
		$this->DisplayTitle( $header_str ) ;	// Display		
		$this->GetExtraHeadData( $header_str );
		$this->GetMetaHeader( $header_str ) ; // Display
		$header_str .= "</head>\n" ;	// Display		
		$this->Style( $header_str ) ;	// Display		
		$header_str .= "<body bgcolor=" . $this->bgcolor . ">\n" ;	// Display
				
		$this->DisplayMenu ( $header_str , &$err_message ) ;	// Layout
		
		echo "<div style=\"width: 100%\">\n" ;
		echo "<h1> $this->header </h1>\n" ;
		echo "</div>\n" ;	
		
		echo "<div style=\"width: 100% ; margin: 0 ; padding: 0\">\n" ;	// Layout
	}
	
	function DisplayLayout ()
	{
		echo $this->content ;  	
	}		
	
	function DisplayFooter( $err_message = "" )
	{
		if ( !empty ( $err_message ) )
		{
			$confer = get_conference_info() ;
		}
		
		echo "<font color=\"#FF0000\">" . $err_message . "</font>" ;		
		?>
		</div>
		
		<div style="width: 100% ; border: none; margin: 0 ; padding: 0">
			<p class=foot>
			<a href="http://iaprcommence.sourceforge.net">Powered by Commence</a>
			</p>
		</div>
				
		</body>
		</html>
		<?php		
	}
	
	function Display()
	{
		$this->DisplayHeader() ;
		$this->DisplayLayout();
		$this->DisplayFooter() ;
	}
}
?>