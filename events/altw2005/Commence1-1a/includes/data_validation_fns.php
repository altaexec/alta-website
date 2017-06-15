<?php // require_once ("debugarray.inc");

function filled_out($form_vars)	// Will be phased out by checkform.
{
  // test that each variable has a value
	foreach ($form_vars as $key => $value)
	{
//		echo "Start key= [" . $key . "] value= " . $value . " <br>\n";	// Debug
		if ( !isset($key) || ($value == "") )
		{
//			echo "Failed key= [" . $key . "] value= " . $value . " <br>\n";	// Debug
			return false;
		}
		else if ( is_array( $value ) )
		{
//			echo "Array Start key= [" . $key . "] value= " . $value . " <br>\n";	// Debug
			if ( !filled_out ( $value ) )
			{
//				echo "Failed array key= [" . $key . "] value= " . $value . "<br>\n";	// Debug
				return false ;
			}
//			echo "Array success key= [" . $key . "] value= " . $value . " <br>\n";	// Debug
		}
//		echo "Success key= [" . $key . "] value= " . $value . " <br>\n";	// Debug
  	}
	return true;
}

/*
check_form ( &$value , &$error_array , $exempt_array = array() , $key = NULL )
Returns: Does not return anything.
&$value: $HTTP_POST_VARS from form.
&$error_array:	Multidimensional Error array that stores any error
$exempt_array:	Takes a one dimensional array of strings to exempt from checking
$key:	Takes the array key of the one dimensional associative array of $value as the array key of error[$array_key] = array(0=>"...",1=>"...",...)
E-mail: ( Woon Tah Liang ) "shaggy@yahoo.com.sg"
*/
function check_form ( &$value , &$error_array , $exempt_array = array() , $key = NULL )
{
	$collect_error_array = array() ;
	$index = 0 ;
//	display ( $exempt_array ) ;
//	display ( $value ) ;

	foreach ($value as $array_key => $array_value)
	{
		if ( is_array ( $array_value ) )
		{
//			echo "Is ARRAY<BR>\n" ;
			check_form ( $array_value , $error_array , &$exempt_array , &$array_key ) ;
		}
		else
		{
			$vars = trim ( $array_value ) ;

			if ( $key === NULL )	// First
			{
//				echo "Non array <br>\n" ;
				if ( isset($array_key) && $vars !="" )	// If the key is set and value is not empty
				{
					validate ( $array_key , $vars , &$collect_error_array , &$index ) ;	// If the value is valid
				}
				else
				{
					if ( !in_array ( $array_key , $exempt_array ) )
					{
	//					echo "Error \$key: " . $array_key . " \$value: " . $array_value . " <br>\n" ;
						$collect_error_array[$index] = " This entry cannot be empty.<br>\n" ;
					}
				}
			}
			else
			{
				if ( isset($array_key) && $vars !="" )	// If the key is set and value is not empty
				{
//					echo "ARRAY key passed <br>\n" ;
					validate ( $key , $vars , &$collect_error_array , &$index , &$array_key ) ;	// If the value is valid
				}
				else
				{
					if ( !in_array ( $key , $exempt_array ) )
					{
	//					echo "Error \$key: " . $key . " \$value: " . $array_value . " <br>\n" ;
						$collect_error_array[$index] = " This entry cannot be empty.<br>\n" ;
					}
				}
			}
		}

		if ( $key === NULL )
		{
			$index = 0 ;

			if ( count ( $collect_error_array ) > 0 )
			{
//				echo "Upper error<br>\n" ;
				$error_array[$array_key] = $collect_error_array ;
				$collect_error_array = array() ;
			}
		}
		else
		{
			$index++ ;
		}
	}

	if ( $key !== NULL )
	{
		if ( count ( $collect_error_array ) > 0 )
		{
//			echo "ARRAY error<br>\n" ;
			$error_array[$key] = $collect_error_array ;
		}
	}
}

function validate ( &$key , &$value , $error_array = array() , $index = 0 , $array_key = NULL )
{
//	echo "Switch Test \$key: " . $key . " \$value: " . $value . " <br>\n" ;
	switch ( $key ) // Lesson: if $key is numeric zero or true, then the first case would be always selected
	{
		case "numauthors":
		case "numpages":
		{
//			echo "Switch Number \$key: " . $key . " \$value: " . $value . " <br>\n" ;
			isIntegerMoreThanZero ( $value , &$error_array , &$index ) ;
			break ;
		}
		case "email":
		case "emailHome":
		case "ConferenceContact":
		{
//			echo "Switch Email \$key: " . $key . " \$value: " . $value . " <br>\n" ;
			valid_email ( $value , &$error_array , &$index ) ;
			break ;
		}
		case "faxno":
		case "phoneno":
		{
//			echo "Switch Phone \$key: " . $key . " \$value: " . $value . " <br>\n" ;
			isValidPhoneNumber ( $value , &$error_array , &$index );
			break ;
		}
			case "phonenoHome":
		{
//			echo "Switch Phone \$key: " . $key . " \$value: " . $value . " <br>\n" ;
			isValidPhoneNumber ( $value , &$error_array , &$index );
			break ;
		}
		case "userfile":
		case "state":
		case "commentfile":
		{
//			echo "Switch File \$key: " . $key . " \$value: " . $value . " <br>\n" ;
			isValidFile ( $value , &$error_array , &$index , &$array_key );
			break ;
		}
		case "logofile":
		{
			isValidLogoFile ( $value , &$error_array , &$index , &$array_key );
			break ;
		}
		case "country":
		{
			isValidCountryCode ( $value , &$error_array , &$index ) ;
			break ;
		}
		case "password":
		case "newpwd":
		{
			isValidPassword ( $value , &$error_array , &$index ) ;
			break ;
		}
		case "date":
		case "ConferenceStartDate":
		case "ConferenceEndDate":
		case "arrStartDate":
		case "arrEndDate":
		{
			if ( isValidDate( $value , &$error_array , &$index ) )
			{
				//is_date_expired( $value , date ( "j/m/Y" , time() ) , &$error_array , &$index ) ;
                is_date_expired( $value , date ( "Y-m-d" , time() ) , &$error_array , &$index ) ;
			}
			break ;
		}
		default:
		{
//			echo "Default \$key: " . $key . " \$value: " . $value . " <br>\n" ;
			break ;
		}
	}
}

function valid_email( &$value , $error_array = array() , $index = 0 )
{
//	echo "Email index: " . $index . "<br>\n" ;

	//Check out email address is possibly valid
	if (ereg("^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\.\-]+$", $value))
	{
		return true;
	}
	else
	{
//		echo "Failed \$string: " . $value . " <br>\n" ;
//		echo "Failed \$index: " . $index . " <br>\n" ;
		$error_array[$index] = " This entry must be a valid email.<br>\n" ;
		return false;
	}
}

function isIntegerMoreThanZero ( &$string , $error_array = array() , $index = 0 )
{
	if ( is_numeric ( $string ) && $string > 0 )
	{
		if ( ereg ( "(^[0-9]+$)|(^[0-9]+\.[0]+$)" , $string ) )
		{
			return true ;
		}
		else
		{
//			echo "Failed \$string: " . $string . " <br>\n" ;
			$error_array[$index] = " This entry must be a positive integer that is more than zero.<br>\n" ;
			return false ;
		}
	}
	else if ( ereg ( "[[:alpha:]]+" , $string ) )
	{
//		echo "Failed \$string: " . $string . " <br>\n" ;
		$error_array[$index] = " This entry must not contain alphabets.<br>\n" ;
		return false ;
	}
	else
	{
//		echo "Failed \$string: " . $string . " <br>\n" ;
		$error_array[$index] = " This entry must be a positive integer that is more than zero.<br>\n" ;
		return false ;
	}
}

function isValidPhoneNumber ( &$string , $error_array = array() , $index = 0 )
{
	if ( ereg ( "^[/+]?(\([0-9]+\))*[\" \"]*([0-9]+\-)*([0-9]+[\" \"]*[0-9]+[\" \"]*)+$" , $string ) )
	{
		return true ;
	}
	else if ( ereg ( "[[:alpha:]]+" , $string ) )
	{
//		echo "Failed \$string: " . $string . " <br>\n" ;
		$error_array[$index] = "This entry must not contain letters.<br>\n" ;
		return false ;
	}
	else
	{
//		echo "Failed \$string: " . $string . " <br>\n" ;
		$error_array[$index] = " Use formats +12 3 456789, (123)456-789, 123456789 or 123-456-789.<br>\n" ;
		return false ;
	}
}

/*
Lesson:
numeric zero is empty
*/

function isValidFile ( &$file , $error_array = array() , $index = 0 , $array_key = NULL )
{
	static $hasvalue = "false" ;

	switch ( $array_key )
	{
		case "name" :
		{
			$hasvalue = "true" ;
			break ;
		}
		case "type" :
		{
			break ;
		}
		case "tmp_name": break ;
		case "size":
		{
			if ( $file == 0 && $hasvalue == "true" )
			{
		//		echo "Failed \$string: " . $string . " <br>\n" ;

				$error_array[$index] = " Please upload a valid file.<br>\n" ;
				return false ;
			}
			else
			{
				$err_message =& $GLOBALS["err_message"] ;
				if ( ( $setobj = get_Conference_Settings( &$err_message ) ) === NULL )
				{
					global $HTTP_SERVER_VARS ;
					do_html_header("Data Validation Failed" , &$err_message) ;
					$err_message .= " Could not execute \"get_Conference_Settings\" in \"data_validation_fns.php\". <br>\n" ;
					$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $HTTP_SERVER_VARS["QUERY_STRING"] . "'>again</a>?" ;
					do_html_footer(&$err_message);
					exit ;
				}

				$maxfilesize = ( intval ( ini_get ( "upload_max_filesize" ) ) * 1000000 ) ;
				if ( $setobj )
				{
					$maxfilesize = $setobj->MaxUploadSize ;
				}

				if ( $file > $maxfilesize )
				{
					$error_array[$index] = " Please keep file size to the limit of $maxfilesize bytes.<br>\n" ;
					return false ;
					break ;
				}

				return true ;
			}
			break ;
		}
		case "error":
		{
			if ( $file == 2 )
			{
				$error_array[$index] = " Please keep file size under the limit.<br>\n" ;
				return false ;
			}
			else
			{
				return true ;
			}
			break ;
		}
		default: break ;
	}
}

function isValidLogoFile ( &$file , $error_array = array() , $index = 0 , $array_key = NULL )
{
	static $hasvalue = "false" ;

	switch ( $array_key )
	{
		case "name" :
		{
			$hasvalue = "true" ;
			break ;
		}
		case "type" :
		{
			if ( $file == "image/pjpeg" || $file == "image/x-png" )
			{

			}
			else
			{
				$error_array[$index] = " This \"$file\" file type is not supported.<br>\n" ;
				return false ;
			}
			break ;
		}
		case "tmp_name": break ;
		case "size":
		{
			if ( $file == 0 && $hasvalue == "true" )
			{
		//		echo "Failed \$string: " . $string . " <br>\n" ;

				$error_array[$index] = " Please upload a valid file.<br>\n" ;
				return false ;
			}
			else
			{
				$err_message =& $GLOBALS["err_message"] ;
				if ( ( $setobj = get_Conference_Settings( &$err_message ) ) === NULL )
				{
					global $HTTP_SERVER_VARS ;
					do_html_header("Data Validation Failed" , &$err_message) ;
					$err_message .= " Could not execute \"get_Conference_Settings\" in \"data_validation_fns.php\". <br>\n" ;
					$err_message .= "<br><br> Try <a href='" . $_SERVER["PHP_SELF"] . "?" . $HTTP_SERVER_VARS["QUERY_STRING"] . "'>again</a>?" ;
					do_html_footer(&$err_message);
					exit ;
				}

				$maxfilesize = ( intval ( ini_get ( "upload_max_filesize" ) ) * 1000000 ) ;
				if ( $setobj )
				{
					$maxfilesize = $setobj->MaxLogoSize ;
				}

				if ( $file > $maxfilesize )
				{
					$error_array[$index] = " Please keep file size to the limit of $maxfilesize bytes.<br>\n" ;
					return false ;
					break ;
				}

				return true ;
			}
			break ;
		}
		case "error":
		{
			if ( $file == 2 )
			{
				$error_array[$index] = " Please keep file size under the limit.<br>\n" ;
				return false ;
			}
			else
			{
				return true ;
			}
			break ;
		}
		default: break ;
	}
}

function isValidCountryCode ( &$string , $error_array = array() , $index = 0 )
{
	switch ( $string )
	{
		case "AF" : return "Afghanistan" ; break ;
		case "AL" : return "Albania" ; break ;
		case "DZ" : return "Algeria" ; break ;
		case "AS" : return "American Samoa" ; break ;
		case "AD" : return "Andorra" ; break ;
		case "AO" : return "Angola" ; break ;
		case "AI" : return "Anguilla" ; break ;
		case "AQ" : return "Antarctica" ; break ;
		case "AG" : return "Antigua And Barbuda" ; break ;
		case "AR" : return "Argentina" ; break ;
		case "AM" : return "Armenia" ; break ;
		case "AW" : return "Aruba" ; break ;
		case "AU" : return "Australia" ; break ;
		case "AT" : return "Austria" ; break ;
		case "AZ" : return "Azerbaijan" ; break ;
		case "BS" : return "Bahamas" ; break ;
		case "BH" : return "Bahrain" ; break ;
		case "BD" : return "Bangladesh" ; break ;
		case "BB" : return "Barbados" ; break ;
		case "BY" : return "Belarus" ; break ;
		case "BE" : return "Belgium" ; break ;
		case "BZ" : return "Belize" ; break ;
		case "BJ" : return "Benin" ; break ;
		case "BM" : return "Bermuda" ; break ;
		case "BT" : return "Bhutan" ; break ;
		case "BO" : return "Bolivia" ; break ;
		case "BA" : return "Bosnia and Herzegovina" ; break ;
		case "BW" : return "Botswana" ; break ;
		case "BV" : return "Bouvet Island" ; break ;
		case "BR" : return "Brazil" ; break ;
		case "IO" : return "British Indian Ocean Territory" ; break ;
		case "BN" : return "Brunei" ; break ;
		case "BG" : return "Bulgaria" ; break ;
		case "BF" : return "Burkina Faso" ; break ;
		case "BI" : return "Burundi" ; break ;
		case "KH" : return "Cambodia" ; break ;
		case "CM" : return "Cameroon" ; break ;
		case "CA" : return "Canada" ; break ;
		case "CV" : return "Cape Verde" ; break ;
		case "KY" : return "Cayman Islands" ; break ;
		case "CF" : return "Central African Republic" ; break ;
		case "TD" : return "Chad" ; break ;
		case "CL" : return "Chile" ; break ;
		case "CN" : return "China" ; break ;
		case "CX" : return "Christmas Island" ; break ;
		case "CC" : return "Cocos (Keeling) Islands" ; break ;
		case "CO" : return "Colombia" ; break ;
		case "KM" : return "Comoros" ; break ;
		case "CG" : return "Congo" ; break ;
		case "CK" : return "Cook Islands" ; break ;
		case "CR" : return "Costa Rica" ; break ;
		case "CI" : return "Cote D'Ivoire (Ivory Coast)" ; break ;
		case "HR" : return "Croatia (Hrvatska)" ; break ;
		case "CU" : return "Cuba" ; break ;
		case "CY" : return "Cyprus" ; break ;
		case "CZ" : return "Czech Republic" ; break ;
		case "CD" : return "Dem Rep of Congo (Zaire)" ; break ;
		case "DK" : return "Denmark" ; break ;
		case "DJ" : return "Djibouti" ; break ;
		case "DM" : return "Dominica" ; break ;
		case "DO" : return "Dominican Republic" ; break ;
		case "TP" : return "East Timor" ; break ;
		case "EC" : return "Ecuador" ; break ;
		case "EG" : return "Egypt" ; break ;
		case "SV" : return "El Salvador" ; break ;
		case "C3" : return "England" ; break ;
		case "GQ" : return "Equatorial Guinea" ; break ;
		case "ER" : return "Eritrea" ; break ;
		case "EE" : return "Estonia" ; break ;
		case "ET" : return "Ethiopia" ; break ;
		case "FK" : return "Falkland Islands (Malvinas)" ; break ;
		case "FO" : return "Faroe Islands" ; break ;
		case "FJ" : return "Fiji" ; break ;
		case "FI" : return "Finland" ; break ;
		case "FR" : return "France" ; break ;
		case "GF" : return "French Guiana" ; break ;
		case "PF" : return "French Polynesia" ; break ;
		case "TF" : return "French Southern Territories" ; break ;
		case "GA" : return "Gabon" ; break ;
		case "GM" : return "Gambia" ; break ;
		case "GE" : return "Georgia" ; break ;
		case "DE" : return "Germany" ; break ;
		case "GH" : return "Ghana" ; break ;
		case "GI" : return "Gibraltar" ; break ;
		case "GR" : return "Greece" ; break ;
		case "GL" : return "Greenland" ; break ;
		case "GD" : return "Grenada" ; break ;
		case "GP" : return "Guadeloupe" ; break ;
		case "GU" : return "Guam" ; break ;
		case "GT" : return "Guatemala" ; break ;
		case "GN" : return "Guinea" ; break ;
		case "GW" : return "Guinea-Bissau" ; break ;
		case "GY" : return "Guyana" ; break ;
		case "HT" : return "Haiti" ; break ;
		case "HM" : return "Heard and McDonald Islands" ; break ;
		case "HN" : return "Honduras" ; break ;
		case "C4" : return "Hong Kong" ; break ;
		case "HU" : return "Hungary" ; break ;
		case "IS" : return "Iceland" ; break ;
		case "IN" : return "India" ; break ;
		case "ID" : return "Indonesia" ; break ;
		case "IR" : return "Iran" ; break ;
		case "IQ" : return "Iraq" ; break ;
		case "IE" : return "Ireland" ; break ;
		case "IL" : return "Israel" ; break ;
		case "IT" : return "Italy" ; break ;
		case "JM" : return "Jamaica" ; break ;
		case "JP" : return "Japan" ; break ;
		case "JO" : return "Jordan" ; break ;
		case "KZ" : return "Kazakhstan" ; break ;
		case "KE" : return "Kenya" ; break ;
		case "KI" : return "Kiribati" ; break ;
		case "KR" : return "Korea" ; break ;
		case "KP" : return "Korea (D.P.R.)" ; break ;
		case "KW" : return "Kuwait" ; break ;
		case "KG" : return "Kyrgyzstan" ; break ;
		case "LA" : return "Lao" ; break ;
		case "LV" : return "Latvia" ; break ;
		case "LB" : return "Lebanon" ; break ;
		case "LS" : return "Lesotho" ; break ;
		case "LR" : return "Liberia" ; break ;
		case "LY" : return "Libya" ; break ;
		case "LI" : return "Liechtenstein" ; break ;
		case "LT" : return "Lithuania" ; break ;
		case "LU" : return "Luxembourg" ; break ;
		case "MO" : return "Macao" ; break ;
		case "MK" : return "Macedonia" ; break ;
		case "MG" : return "Madagascar" ; break ;
		case "MW" : return "Malawi" ; break ;
		case "MY" : return "Malaysia" ; break ;
		case "MV" : return "Maldives" ; break ;
		case "ML" : return "Mali" ; break ;
		case "MT" : return "Malta" ; break ;
		case "MH" : return "Marshall Islands" ; break ;
		case "MQ" : return "Martinique" ; break ;
		case "MR" : return "Mauritania" ; break ;
		case "MU" : return "Mauritius" ; break ;
		case "YT" : return "Mayotte" ; break ;
		case "MX" : return "Mexico" ; break ;
		case "FM" : return "Micronesia" ; break ;
		case "MD" : return "Moldova" ; break ;
		case "MC" : return "Monaco" ; break ;
		case "MN" : return "Mongolia" ; break ;
		case "MS" : return "Montserrat" ; break ;
		case "MA" : return "Morocco" ; break ;
		case "MZ" : return "Mozambique" ; break ;
		case "MM" : return "Myanmar" ; break ;
		case "NA" : return "Namibia" ; break ;
		case "NR" : return "Nauru" ; break ;
		case "NP" : return "Nepal" ; break ;
		case "NL" : return "Netherlands" ; break ;
		case "AN" : return "Netherlands Antilles" ; break ;
		case "NC" : return "New Caledonia" ; break ;
		case "NZ" : return "New Zealand" ; break ;
		case "NI" : return "Nicaragua" ; break ;
		case "NE" : return "Niger" ; break ;
		case "NG" : return "Nigeria" ; break ;
		case "NU" : return "Niue" ; break ;
		case "NF" : return "Norfolk Island" ; break ;
		case "MP" : return "Northern Mariana Islands" ; break ;
		case "NO" : return "Norway" ; break ;
		case "OM" : return "Oman" ; break ;
		case "C1" : return "Other" ; break ;
		case "PK" : return "Pakistan" ; break ;
		case "PW" : return "Palau" ; break ;
		case "PA" : return "Panama" ; break ;
		case "PG" : return "Papua new Guinea" ; break ;
		case "PY" : return "Paraguay" ; break ;
		case "PE" : return "Peru" ; break ;
		case "PH" : return "Philippines" ; break ;
		case "PN" : return "Pitcairn Island" ; break ;
		case "PL" : return "Poland" ; break ;
		case "PT" : return "Portugal" ; break ;
		case "PR" : return "Puerto Rico" ; break ;
		case "QA" : return "Qatar" ; break ;
		case "RE" : return "Reunion" ; break ;
		case "RO" : return "Romania" ; break ;
		case "RU" : return "Russia" ; break ;
		case "RW" : return "Rwanda" ; break ;
		case "KN" : return "Saint Kitts And Nevis" ; break ;
		case "LC" : return "Saint Lucia" ; break ;
		case "VC" : return "Saint Vincent And The Grenadines" ; break ;
		case "WS" : return "Samoa" ; break ;
		case "SM" : return "San Marino" ; break ;
		case "ST" : return "Sao Tome and Principe" ; break ;
		case "SA" : return "Saudi Arabia" ; break ;
		case "C5" : return "Scotland" ; break ;
		case "SN" : return "Senegal" ; break ;
		case "SC" : return "Seychelles" ; break ;
		case "SL" : return "Sierra Leone" ; break ;
		case "SG" : return "Singapore" ; break ;
		case "SK" : return "Slovak Republic" ; break ;
		case "SI" : return "Slovenia" ; break ;
		case "SB" : return "Solomon Islands" ; break ;
		case "SO" : return "Somalia" ; break ;
		case "ZA" : return "South Africa" ; break ;
		case "GS" : return "South Georgia, Sth Sandwich Islands" ; break ;
		case "ES" : return "Spain" ; break ;
		case "LK" : return "Sri Lanka" ; break ;
		case "SH" : return "St Helena" ; break ;
		case "PM" : return "St Pierre and Miquelon" ; break ;
		case "SD" : return "Sudan" ; break ;
		case "SR" : return "Suriname" ; break ;
		case "SJ" : return "Svalbard And Jan Mayen Islands" ; break ;
		case "SZ" : return "Swaziland" ; break ;
		case "SE" : return "Sweden" ; break ;
		case "CH" : return "Switzerland" ; break ;
		case "SY" : return "Syria" ; break ;
		case "TW" : return "Taiwan" ; break ;
		case "TJ" : return "Tajikistan" ; break ;
		case "TZ" : return "Tanzania" ; break ;
		case "TH" : return "Thailand" ; break ;
		case "TG" : return "Togo" ; break ;
		case "TK" : return "Tokelau" ; break ;
		case "TO" : return "Tonga" ; break ;
		case "TT" : return "Trinidad And Tobago" ; break ;
		case "TN" : return "Tunisia" ; break ;
		case "TR" : return "Turkey" ; break ;
		case "TM" : return "Turkmenistan" ; break ;
		case "TC" : return "Turks And Caicos Islands" ; break ;
		case "TV" : return "Tuvalu" ; break ;
		case "UG" : return "Uganda" ; break ;
		case "UA" : return "Ukraine" ; break ;
		case "AE" : return "United Arab Emirates" ; break ;
		case "UK" : return "United Kingdom" ; break ;
		case "US" : return "United States" ; break ;
		case "UM" : return "United States Minor Outlying Islands" ; break ;
		case "UY" : return "Uruguay" ; break ;
		case "UZ" : return "Uzbekistan" ; break ;
		case "VU" : return "Vanuatu" ; break ;
		case "VA" : return "Vatican City State (Holy See)" ; break ;
		case "VE" : return "Venezuela" ; break ;
		case "VN" : return "Vietnam" ; break ;
		case "VG" : return "Virgin Islands (British)" ; break ;
		case "VI" : return "Virgin Islands (US)" ; break ;
		case "C6" : return "Wales" ; break ;
		case "WF" : return "Wallis And Futuna Islands" ; break ;
		case "EH" : return "Western Sahara" ; break ;
		case "YE" : return "Yemen" ; break ;
		case "YU" : return "Yugoslavia" ; break ;
		case "ZM" : return "Zambia" ; break ;
		case "ZW" : return "Zimbabwe" ; break ;
		default :
		{
			$error_array[$index] = " You must choose a country. <br>\n" ;
			return false ;
			break ;
		}
	}
}

function isValidPassword ( $string , $error_array = array() , $index = 0 )
{
	if ( ereg ( "([[:alnum:]]){6,}" , $string ) && ereg ( "[[:alpha:]]+" , $string ) && ereg ( "[0-9]+" , $string ) )
	{
		return true ;
	}
	else
	{
		$error_array[$index] = " Passwords must have a length of at least 6 and contain a combination of characters and numbers. <br>\n" ;
		return false ;
	}
}

function isValidDate( &$date , $error_array = array() , $index = 0 )
{
//	echo "<br>\nDATE: " . $date . "<br>\n" ;
	list( $year , $month , $day ) = split('[/.-]', $date ) ;

	if ( isIntegerMoreThanZero ( $day , &$error_array , &$index ) && isIntegerMoreThanZero ( $month , &$error_array , &$index ) && isIntegerMoreThanZero ( $year , &$error_array , &$index ) )
	{
		if ( checkdate ( $month , $day , $year ) )
		{
			return true ;
		}
		else
		{
			$error_array[$index] = " Invalid date input. Please check a calender for valid date. <br>\n" ;
			return false ;
		}
	}
	else
	{
		$error_array[$index] = " Invalid date input." ;
		return false ;
	}
}

/*
    Computes whether the deadline has passed for a given date.
    Date parameters are given in ISO 8601 format - "Y-m-d" in PHP.
*/
function is_date_expired( &$deadline , &$date_now , $error_array = array() , $index = 0 )
{
//	echo "<br>\nDATE1: " . $date1 . "<br>\n" ;
//	echo "<br>\nDATE2: " . $date2 . "<br>\n" ;

    //list( $day1 , $month1 , $year1 ) = split('[/.-]', $deadline ) ;
	//list( $day2 , $month2 , $year2 ) = split('[/.-]', $date_now ) ;
	list( $year1 , $month1 , $day1 ) = split('[/.-]', $deadline ) ;
	list( $year2 , $month2 , $day2 ) = split('[/.-]', $date_now ) ;

	$myDate1 = mktime( "" , "" , "" , $month1 , $day1 , $year1 ) ;
	$myDate2 = mktime( "" , "" , "" , $month2 , $day2 , $year2 ) ;

	if($myDate1 >= $myDate2 )
	{
		return false;
	}
	else
	{
		$error_array[$index] = " This date has already expired. <br>\n" ;
		return true;
	}
}

?>