<?php

	function GenerateAuthorInputTable( $number )
	{
		$data = "";
		global $firstname ;
		global $middlename ;
		global $lastname ;
		global $email ;								
		
		global $firstname_error_array ;
		global $middlename_error_array ;
		global $lastname_error_array ;
		global $email_error_array ;
		
		if($number > 0)
		{
			$data = "<table width='80%' border='0' cellspacing='0' cellpadding='1'>" .
			"<caption align='left'><font size='2'><strong>Author Information</strong></font></caption>" .
          "<tr> " .
            "<td width='10%'>&nbsp;</td>" .		  	
            "<td width='20%'><STRONG>First Name</STRONG></td>" .
            "<td width='20%'><STRONG>Middle Name</STRONG></td>" .
            "<td width='20%'><STRONG>Last Name</STRONG></td>" .
            "<td width='30%'><STRONG>Author Email</STRONG></td>" .
          "</tr> \n";	
					
			for ( $i=0 ; $i < $number ; $i++ )
			{				
				$data = $data .
				"<tr>" .
					"<td></td>" .
					"<td>" . "<font color=\"#FF0000\">" . $firstname_error_array[$i] . "</font></td>" .
					"<td>" . "<font color=\"#FF0000\">" . $middlename_error_array[$i] . "</font></td>" .
					"<td>" . "<font color=\"#FF0000\">" . $lastname_error_array[$i] . "</font></td>" .
					"<td>" . "<font color=\"#FF0000\">" . $email_error_array[$i] . "</font></td>" .													
				"</tr>\n" .		
				
			  "<tr>".
			  	"<td align='center'>". ( $i + 1 ) .".</td>".

				"<td><input name=" . "'firstname[]'" .
				" type=" . "'text'" .
				" value=\"" . htmlentities ( stripslashes ( $firstname[$i] ) ) . "\"" .
				 " size=" . "'15'" .
				 " maxlength=" . "'30'" . ">" .
				 "</td>" .
	
				"<td><input name=" . "'middlename[]'" .
				" type=" . "'text'" .
				" value=\"" . htmlentities ( stripslashes ( $middlename[$i] ) ) . "\"" .				
				 " size=" . "'15'" . 
				 " maxlength=" . "'30'" . ">" .
				 "</td>" .
	
				"<td><input name=" . "'lastname[]'" .
				" type=" . "'text'" .
				" value=\"" . htmlentities ( stripslashes ( $lastname[$i] ) ) . "\"" .				
				 " size=" . "'15'" .
				 " maxlength=" . "'30'" . ">" .
				 "</td>" .
	
				"<td><input name=" . "'email[]'" .
				" type=" . "'text'" . 
				" value=\"" . htmlentities ( stripslashes ( $email[$i] ) ) . "\"" .				
				 " size=" . "'20'" .
				 " maxlength=" . "'30'" . ">" .
				 "</td>" .			 			 			 
	  
			  "</tr>" . "\n" ;
			}
	
			$data = $data .  "</table> \n";
			
			return $data ;
		
		}
	}

	function GenerateSelectedCategoryInputTable( &$selectedCategoryList , $err_message = "", $blocked = 0, $table = "Category" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
        $sql = "SELECT * FROM " . $GLOBALS["DB_PREFIX"] . $table ;	
		$result = $db -> Execute($sql);
		$categorytable = "";
		
		if(!$result)
		{
			$err_message .= " Could not get the $table Name from the $table Table <br>\n ";	// Exception has occurred
			return false ;
		}
		else
		{			
			$categorytable = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\"> \n" ;
			$rows = $result -> RecordCount() ;			

			$num = count ( $selectedCategoryList ) ;
						
			for ( $i = 0 ; $i < $rows ; )
			{
				$categorytable .= "<tr> \n" ;
				for ( $j = 0 ; $j < 2 ; $i++ , $j++ )
				{
					$categorytable .= "<td>" ;
					if ( $records = $result -> FetchRow() )
					{
						if ($table == "Level") //If Level use checkbox, If Category use checkbox, if Track use radio
						{
							$categorytable .= "<input type=\"checkbox\" name=\"level[]\" value=\"$records[0]\" " ;
						}
						elseif ($table == "Category") // Category
						{
							$categorytable .= "<input type=\"checkbox\" name=\"category[]\" value=\"$records[0]\" " ;
						}
						else // Track
						{
							$categorytable .= "<input type=\"radio\" name=\"track[]\" value=\"$records[0]\" " ;
						}
						for ( $k = 0 ; $k < $num ; $k++ )
						{						
							if ( $records[0] == $selectedCategoryList[$k] )
							{
								$categorytable .= "checked" ;
								break ;
							}
							else if ($blocked)  // disable selection buttons
							{
								$categorytable .= "disabled" ;
								break ;
							}
						}
						$categorytable .= "> \n" ;
						$categorytable .= "$records[1]" ;
					}
					$categorytable .= "</td> \n" ;
				}
				$categorytable .= "</tr> \n" ;
			}

			$categorytable .= "</table> \n" ;
			return $categorytable ;			
		}			
	}	

	function GetSelectedTrack( &$paperID , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
		$sql = "SELECT TrackID FROM " . $GLOBALS["DB_PREFIX"] . "Paper " ;	
		$sql .= " WHERE PaperID = $paperID " ;
		$result = $db -> Execute($sql);
		$categorytable ;
		
		if(!$result)
		{
			$err_message .= " Could not get records from the PaperTable <br>\n ";	// Exception has occurred
			return false ;
		}
		else
		{						
			$categorytable = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\"> \n" ;
			$rows = $result -> RecordCount() ;
			for ( $i = 0 ; $i < $rows ; )
			{
				$categorytable .= "<tr> \n" ;
				for ( $j = 0 ; $j < 2 ; $i++ , $j++ )
				{
					$categorytable .= "<td>" ;
					if ( $records = $result -> FetchNextObj() )
					{
						$sql_cat = "SELECT TrackName FROM " . $GLOBALS["DB_PREFIX"] . "Track " ;	
						$sql_cat .= " WHERE TrackID = ".$records -> TrackID;
						$catResult = $db -> Execute($sql_cat);
						
						if(!$catResult)
						{
							$err_message .= " Could not get records from the Track Table <br>\n ";	// Exception has occurred
							return $err_message ;
						}
						
						$catRecords = $catResult -> FetchNextObj() ;						
						$categorytable .= $catRecords -> TrackName ." \n" ;
					}
					else
					{
						$categorytable .= "&nbsp \n" ;
					}
					$categorytable .= "</td> \n" ;
				}
				$categorytable .= "</tr> \n" ;
			}

			$categorytable .= "</table> \n" ;
			return $categorytable ;			
		}			
	}
	
	function GetSelectedCategory( &$paperID , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
		$sql = "SELECT CategoryID FROM " . $GLOBALS["DB_PREFIX"] . "PaperCategory " ;	
		$sql .= " WHERE PaperID = $paperID " ;
		$result = $db -> Execute($sql);
		$categorytable ;
		
		if(!$result)
		{
			$err_message .= " Could not get records from the PaperCategory Table <br>\n ";	// Exception has occurred
			return false ;
		}
		else
		{						
			$categorytable = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\"> \n" ;
			$rows = $result -> RecordCount() ;
			for ( $i = 0 ; $i < $rows ; )
			{
				$categorytable .= "<tr> \n" ;
				for ( $j = 0 ; $j < 2 ; $i++ , $j++ )
				{
					$categorytable .= "<td>" ;
					if ( $records = $result -> FetchNextObj() )
					{
						$sql_cat = "SELECT CategoryName FROM " . $GLOBALS["DB_PREFIX"] . "Category " ;	
						$sql_cat .= " WHERE CategoryID = " . $records -> CategoryID ;
						$catResult = $db -> Execute($sql_cat);
						
						if(!$catResult)
						{
							$err_message .= " Could not get records from the Category Table <br>\n ";	// Exception has occurred
							return $err_message ;
						}
						
						$catRecords = $catResult -> FetchNextObj() ;						
						$categorytable .= $catRecords -> CategoryName ." \n" ;
					}
					else
					{
						$categorytable .= "&nbsp \n" ;
					}
					$categorytable .= "</td> \n" ;
				}
				$categorytable .= "</tr> \n" ;
			}

			$categorytable .= "</table> \n" ;
			return $categorytable ;			
		}			
	}

	function GetSelectedLevel( &$paperID , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
		$sql = "SELECT LevelID FROM " . $GLOBALS["DB_PREFIX"] . "PaperLevel " ;	
		$sql .= " WHERE PaperID = $paperID " ;
		$result = $db -> Execute($sql);
		$categorytable ;
		
		if(!$result)
		{
			$err_message .= " Could not get records from the PaperLevel Table <br>\n ";	// Exception has occurred
			return false ;
		}
		else
		{						
			$categorytable = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\"> \n" ;
			$rows = $result -> RecordCount() ;
			for ( $i = 0 ; $i < $rows ; )
			{
				$categorytable .= "<tr> \n" ;
				for ( $j = 0 ; $j < 2 ; $i++ , $j++ )
				{
					$categorytable .= "<td>" ;
					if ( $records = $result -> FetchNextObj() )
					{
						$sql_cat = "SELECT LevelName FROM " . $GLOBALS["DB_PREFIX"] . "Level " ;	
						$sql_cat .= " WHERE LevelID = ".$records -> LevelID ;
						$catResult = $db -> Execute($sql_cat);
						
						if(!$catResult)
						{
							$err_message .= " Could not get records from the Level Table <br>\n ";	// Exception has occurred
							return $err_message ;
						}
						
						$catRecords = $catResult -> FetchNextObj() ;						
						$categorytable .= $catRecords -> LevelName ." \n" ;
					}
					else
					{
						$categorytable .= "&nbsp \n" ;
					}
					$categorytable .= "</td> \n" ;
				}
				$categorytable .= "</tr> \n" ;
			}

			$categorytable .= "</table> \n" ;
			return $categorytable ;			
		}			
	}
	
	function GetSelectedTrackList ( &$paperID , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
	    
		$sql = "SELECT TrackID FROM " . $GLOBALS["DB_PREFIX"] . "Paper " ;	
		$sql .= " WHERE PaperID = $paperID " ;
		$result = $db -> Execute($sql);
		$categoryList = array() ;
		
		if(!$result)
		{
			$err_message .= " Could not get records from the Paper Table <br>\n ";	// Exception has occurred
			return false ;
		}
		else
		{						
			while ( $record = $result -> FetchNextObj() )
			{
				$categoryList[] = $record -> TrackID ;
			}
		}
		
		return $categoryList ;
	}

	function GetSelectedCategoryList( &$paperID , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
		$sql = "SELECT CategoryID FROM " . $GLOBALS["DB_PREFIX"] . "PaperCategory " ;	
		$sql .= " WHERE PaperID = $paperID " ;
		$result = $db -> Execute($sql);
		$categoryList = array() ;
		
		if(!$result)
		{
			$err_message .= " Could not get records from the PaperCategory Table <br>\n ";	// Exception has occurred
			return false ;
		}
		else
		{						
			while ( $record = $result -> FetchNextObj() )
			{						
				$categoryList[] = $record -> CategoryID ;
			}			
		}
		
		return $categoryList ;
	}	

	function GetSelectedLevelList( &$paperID , $err_message = "" )
	{
		//Establish connection with database
        $db = adodb_connect( &$err_message );
        
		$sql = "SELECT LevelID FROM " . $GLOBALS["DB_PREFIX"] . "PaperLevel " ;	
		$sql .= " WHERE PaperID = $paperID " ;
		$result = $db -> Execute($sql);
		$levelList = array() ;
		
		if(!$result)
		{
			$err_message .= " Could not get records from the PaperLevel Table <br>\n ";	// Exception has occurred
			return false ;
		}
		else
		{						
			while ( $record = $result -> FetchNextObj() )
			{						
				$levelList[] = $record -> LevelID ;
			}			
		}
		
		return $levelList ;
	}	

	function GetCountryDropDownBox( $selected = false )
	{
		// Lesson: An empty string passed in will be evaluated as false 
		$string = "<select name=\"country\" class=\"form_style\" >
			<option value=\"__\" >Choose your country...</option>
			<option value=\"AF\" >Afghanistan</option>	
			<option value=\"AL\" >Albania</option>	
			<option value=\"DZ\" >Algeria</option>	
			<option value=\"AS\" >American Samoa</option>	
			<option value=\"AD\" >Andorra</option>	
			<option value=\"AO\" >Angola</option>	
			<option value=\"AI\" >Anguilla</option>	
			<option value=\"AQ\" >Antarctica</option>	
			<option value=\"AG\" >Antigua And Barbuda</option>	
			<option value=\"AR\" >Argentina</option>	
			<option value=\"AM\" >Armenia</option>	
			<option value=\"AW\" >Aruba</option>	
			<option value=\"AU\" >Australia</option>	
			<option value=\"AT\" >Austria</option>	
			<option value=\"AZ\" >Azerbaijan</option>	
			<option value=\"BS\" >Bahamas</option>
			<option value=\"BH\" >Bahrain</option>	
			<option value=\"BD\" >Bangladesh</option>	
			<option value=\"BB\" >Barbados</option>	
			<option value=\"BY\" >Belarus</option>	
			<option value=\"BE\" >Belgium</option>	
			<option value=\"BZ\" >Belize</option>	
			<option value=\"BJ\" >Benin</option>	
			<option value=\"BM\" >Bermuda</option>	
			<option value=\"BT\" >Bhutan</option>	
			<option value=\"BO\" >Bolivia</option>	
			<option value=\"BA\" >Bosnia and Herzegovina</option>	
			<option value=\"BW\" >Botswana</option>	
			<option value=\"BV\" >Bouvet Island</option>	
			<option value=\"BR\" >Brazil</option>	
			<option value=\"IO\" >British Indian Ocean Territory</option>	
			<option value=\"BN\" >Brunei</option>	
			<option value=\"BG\" >Bulgaria</option>	
			<option value=\"BF\" >Burkina Faso</option>	
			<option value=\"BI\" >Burundi</option>	
			<option value=\"KH\" >Cambodia</option>	
			<option value=\"CM\" >Cameroon</option>	
			<option value=\"CA\" >Canada</option>	
			<option value=\"CV\" >Cape Verde</option>	
			<option value=\"KY\" >Cayman Islands</option>	
			<option value=\"CF\" >Central African Republic</option>	
			<option value=\"TD\" >Chad</option>	
			<option value=\"CL\" >Chile</option>	
			<option value=\"CN\" >China</option>	
			<option value=\"CX\" >Christmas Island</option>	
			<option value=\"CC\" >Cocos (Keeling) Islands</option>	
			<option value=\"CO\" >Colombia</option>	
			<option value=\"KM\" >Comoros</option>	
			<option value=\"CG\" >Congo</option>	
			<option value=\"CK\" >Cook Islands</option>	
			<option value=\"CR\" >Costa Rica</option>	
			<option value=\"CI\" >Cote D'Ivoire (Ivory Coast)</option>	
			<option value=\"HR\" >Croatia (Hrvatska)</option>	
			<option value=\"CU\" >Cuba</option>	
			<option value=\"CY\" >Cyprus</option>	
			<option value=\"CZ\" >Czech Republic</option>	
			<option value=\"CD\" >Dem Rep of Congo (Zaire)</option>	
			<option value=\"DK\" >Denmark</option>	
			<option value=\"DJ\" >Djibouti</option>	
			<option value=\"DM\" >Dominica</option>	
			<option value=\"DO\" >Dominican Republic</option>	
			<option value=\"TP\" >East Timor</option>	
			<option value=\"EC\" >Ecuador</option>	
			<option value=\"EG\" >Egypt</option>	
			<option value=\"SV\" >El Salvador</option>	
			<option value=\"C3\" >England</option>	
			<option value=\"GQ\" >Equatorial Guinea</option>	
			<option value=\"ER\" >Eritrea</option>	
			<option value=\"EE\" >Estonia</option>	
			<option value=\"ET\" >Ethiopia</option>	
			<option value=\"FK\" >Falkland Islands (Malvinas)</option>	
			<option value=\"FO\" >Faroe Islands</option>	
			<option value=\"FJ\" >Fiji</option>	
			<option value=\"FI\" >Finland</option>	
			<option value=\"FR\" >France</option>	
			<option value=\"GF\" >French Guiana</option>	
			<option value=\"PF\" >French Polynesia</option>	
			<option value=\"TF\" >French Southern Territories</option>	
			<option value=\"GA\" >Gabon</option>	
			<option value=\"GM\" >Gambia</option>	
			<option value=\"GE\" >Georgia</option>	
			<option value=\"DE\" >Germany</option>	
			<option value=\"GH\" >Ghana</option>	
			<option value=\"GI\" >Gibraltar</option>	
			<option value=\"GR\" >Greece</option>	
			<option value=\"GL\" >Greenland</option>	
			<option value=\"GD\" >Grenada</option>	
			<option value=\"GP\" >Guadeloupe</option>	
			<option value=\"GU\" >Guam</option>	
			<option value=\"GT\" >Guatemala</option>	
			<option value=\"GN\" >Guinea</option>	
			<option value=\"GW\" >Guinea-Bissau</option>	
			<option value=\"GY\" >Guyana</option>	
			<option value=\"HT\" >Haiti</option>	
			<option value=\"HM\" >Heard and McDonald Islands</option>	
			<option value=\"HN\" >Honduras</option>	
			<option value=\"C4\" >Hong Kong</option>	
			<option value=\"HU\" >Hungary</option>	
			<option value=\"IS\" >Iceland</option>	
			<option value=\"IN\" >India</option>	
			<option value=\"ID\" >Indonesia</option>	
			<option value=\"IR\" >Iran</option>	
			<option value=\"IQ\" >Iraq</option>	
			<option value=\"IE\" >Ireland</option>	
			<option value=\"IL\" >Israel</option>	
			<option value=\"IT\" >Italy</option>	
			<option value=\"JM\" >Jamaica</option>	
			<option value=\"JP\" >Japan</option>	
			<option value=\"JO\" >Jordan</option>	
			<option value=\"KZ\" >Kazakhstan</option>	
			<option value=\"KE\" >Kenya</option>	
			<option value=\"KI\" >Kiribati</option>	
			<option value=\"KR\" >Korea</option>	
			<option value=\"KP\" >Korea (D.P.R.)</option>	
			<option value=\"KW\" >Kuwait</option>	
			<option value=\"KG\" >Kyrgyzstan</option>	
			<option value=\"LA\" >Lao</option>	
			<option value=\"LV\" >Latvia</option>	
			<option value=\"LB\" >Lebanon</option>	
			<option value=\"LS\" >Lesotho</option>	
			<option value=\"LR\" >Liberia</option>	
			<option value=\"LY\" >Libya</option>	
			<option value=\"LI\" >Liechtenstein</option>	
			<option value=\"LT\" >Lithuania</option>
			<option value=\"LU\" >Luxembourg</option>	
			<option value=\"MO\" >Macao</option>	
			<option value=\"MK\" >Macedonia</option>	
			<option value=\"MG\" >Madagascar</option>	
			<option value=\"MW\" >Malawi</option>	
			<option value=\"MY\" >Malaysia</option>	
			<option value=\"MV\" >Maldives</option>	
			<option value=\"ML\" >Mali</option>	
			<option value=\"MT\" >Malta</option>	
			<option value=\"MH\" >Marshall Islands</option>	
			<option value=\"MQ\" >Martinique</option>	
			<option value=\"MR\" >Mauritania</option>	
			<option value=\"MU\" >Mauritius</option>	
			<option value=\"YT\" >Mayotte</option>	
			<option value=\"MX\" >Mexico</option>	
			<option value=\"FM\" >Micronesia</option>	
			<option value=\"MD\" >Moldova</option>	
			<option value=\"MC\" >Monaco</option>	
			<option value=\"MN\" >Mongolia</option>	
			<option value=\"MS\" >Montserrat</option>	
			<option value=\"MA\" >Morocco</option>	
			<option value=\"MZ\" >Mozambique</option>	
			<option value=\"MM\" >Myanmar</option>	
			<option value=\"NA\" >Namibia</option>	
			<option value=\"NR\" >Nauru</option>	
			<option value=\"NP\" >Nepal</option>	
			<option value=\"NL\" >Netherlands</option>	
			<option value=\"AN\" >Netherlands Antilles</option>	
			<option value=\"NC\" >New Caledonia</option>	
			<option value=\"NZ\" >New Zealand</option>	
			<option value=\"NI\" >Nicaragua</option>	
			<option value=\"NE\" >Niger</option>	
			<option value=\"NG\" >Nigeria</option>	
			<option value=\"NU\" >Niue</option>	
			<option value=\"NF\" >Norfolk Island</option>	
			<option value=\"MP\" >Northern Mariana Islands</option>	
			<option value=\"NO\" >Norway</option>	
			<option value=\"OM\" >Oman</option>
			<option value=\"C1\" >Other</option>	
			<option value=\"PK\" >Pakistan</option>	
			<option value=\"PW\" >Palau</option>	
			<option value=\"PA\" >Panama</option>	
			<option value=\"PG\" >Papua new Guinea</option>	
			<option value=\"PY\" >Paraguay</option>	
			<option value=\"PE\" >Peru</option>	
			<option value=\"PH\" >Philippines</option>
			<option value=\"PN\" >Pitcairn Island</option>	
			<option value=\"PL\" >Poland</option>	
			<option value=\"PT\" >Portugal</option>	
			<option value=\"PR\" >Puerto Rico</option>	
			<option value=\"QA\" >Qatar</option>	
			<option value=\"RE\" >Reunion</option>	
			<option value=\"RO\" >Romania</option>	
			<option value=\"RU\" >Russia</option>	
			<option value=\"RW\" >Rwanda</option>	
			<option value=\"KN\" >Saint Kitts And Nevis</option>	
			<option value=\"LC\" >Saint Lucia</option>	
			<option value=\"VC\" >Saint Vincent And The Grenadines</option>	
			<option value=\"WS\" >Samoa</option>	
			<option value=\"SM\" >San Marino</option>	
			<option value=\"ST\" >Sao Tome and Principe</option>	
			<option value=\"SA\" >Saudi Arabia</option>	
			<option value=\"C5\" >Scotland</option>	
			<option value=\"SN\" >Senegal</option>	
			<option value=\"SC\" >Seychelles</option>	
			<option value=\"SL\" >Sierra Leone</option>	
			<option value=\"SG\" >Singapore</option>	
			<option value=\"SK\" >Slovak Republic</option>	
			<option value=\"SI\" >Slovenia</option>	
			<option value=\"SB\" >Solomon Islands</option>	
			<option value=\"SO\" >Somalia</option>	
			<option value=\"ZA\" >South Africa</option>	
			<option value=\"GS\" >South Georgia, Sth Sandwich Islands</option>	
			<option value=\"ES\" >Spain</option>	
			<option value=\"LK\" >Sri Lanka</option>	
			<option value=\"SH\" >St Helena</option>	
			<option value=\"PM\" >St Pierre and Miquelon</option>	
			<option value=\"SD\" >Sudan</option>	
			<option value=\"SR\" >Suriname</option>	
			<option value=\"SJ\" >Svalbard And Jan Mayen Islands</option>	
			<option value=\"SZ\" >Swaziland</option>	
			<option value=\"SE\" >Sweden</option>	
			<option value=\"CH\" >Switzerland</option>	
			<option value=\"SY\" >Syria</option>	
			<option value=\"TW\" >Taiwan</option>	
			<option value=\"TJ\" >Tajikistan</option>	
			<option value=\"TZ\" >Tanzania</option>	
			<option value=\"TH\" >Thailand</option>	
			<option value=\"TG\" >Togo</option>	
			<option value=\"TK\" >Tokelau</option>	
			<option value=\"TO\" >Tonga</option>	
			<option value=\"TT\" >Trinidad And Tobago</option>	
			<option value=\"TN\" >Tunisia</option>	
			<option value=\"TR\" >Turkey</option>	
			<option value=\"TM\" >Turkmenistan</option>	
			<option value=\"TC\" >Turks And Caicos Islands</option>	
			<option value=\"TV\" >Tuvalu</option>	
			<option value=\"UG\" >Uganda</option>	
			<option value=\"UA\" >Ukraine</option>	
			<option value=\"AE\" >United Arab Emirates</option>
			<option value=\"UK\" >United Kingdom</option>	
			<option value=\"US\" >United States</option>	
			<option value=\"UM\" >United States Minor Outlying Islands</option>	
			<option value=\"UY\" >Uruguay</option>	
			<option value=\"UZ\" >Uzbekistan</option>	
			<option value=\"VU\" >Vanuatu</option>	
			<option value=\"VA\" >Vatican City State (Holy See)</option>	
			<option value=\"VE\" >Venezuela</option>	
			<option value=\"VN\" >Vietnam</option>	
			<option value=\"VG\" >Virgin Islands (British)</option>	
			<option value=\"VI\" >Virgin Islands (US)</option>	
			<option value=\"C6\" >Wales</option>	
			<option value=\"WF\" >Wallis And Futuna Islands</option>	
			<option value=\"EH\" >Western Sahara</option>	
			<option value=\"YE\" >Yemen</option>	
			<option value=\"YU\" >Yugoslavia</option>	
			<option value=\"ZM\" >Zambia</option>	
			<option value=\"ZW\" >Zimbabwe</option>
		</select>" ;
		
		if ( $selected )
		{
			if ( $pos = strpos ( $string , $selected ) )
			{			
				$len = strlen ( $selected ) + 2 ;
				return substr_replace ( $string , "SELECTED " , $pos + $len , 0 ) ;
			}
			else
			{
				return $string ;
			}
		}
		else
		{
			return $string ;
		}
	}

?>
