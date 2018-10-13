<?php
/**
 * FreestyleSolutions_BizSyncXL_Utils
 *
 * @author Gary MacDougall
 * @version $Id$
 * @copyright FreeportWeb, Inc., 19 February, 2012
 * @package FreestyleSolutions_BizSyncXL
 **/

define ('ENABLE_LOGGING', true);

/**
 * Define DocBlock
 **/

function writeXmlDeclaration()
{
	echo "<?xml version=\"1.0\" standalone=\"yes\" ?>";
}

// $this->write the open xml tag 
function writeStartTag($tag)
{
	echo '<' . $tag . '>';
}

// $this->write closing xml tag
function writeCloseTag($tag)
{
	echo '</' . $tag . '>';
}

// Output the given tag\value pair
function writeElement($tag, $value)
{
	writeStartTag($tag);
	echo htmlspecialchars($value);
	writeCloseTag($tag);
}

// Function used to output an error and quit.
function RestResultError($code, $message, $method)
{	

	writeStartTag("StatusCode");
	writeElement("Code", $code);
	writeElement("Name", $message);
	writeElement("Method", $method);
	writeCloseTag("StatusCode");
	// we always start with an opening BizSync tag, close it because we've ended output on an error.
	writeCloseTag("BizSync");
	die();
}	

/**
 * escapeInventoryFileData function.
 * 
 * @access public
 * @param mixed $str_data
 * @return void
 */
function escapeInventoryFileData($str_data) 
{
	$str_data = strip_tags($str_data);
	$searched_data = array("\r\n", "\n", "\r", "\t", "\\");
	$replaced_data = ' ';
	return str_replace($searched_data, $replaced_data, $str_data);
}


/**
 * toGmt function.
 * 
 * @access public
 * @param mixed $dateSql
 * @return void
 */
function toGmt($dateSql)
{
	$pattern = "/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2}):(\d{2})$/i";

	if (preg_match($pattern, $dateSql, $dt)) 
	{
		$dateUnix = mktime($dt[4], $dt[5], $dt[6], $dt[2], $dt[3], $dt[1]);
		return gmdate("Y-m-d H:i:s", $dateUnix);
	}

	return $dateSql;
}

/**
 * toLocalSqlDate function.
 * 
 * @access public
 * @param mixed $dateUnix
 * @return void
 */
function toLocalSqlDate($dateUnix)
{					       
    return date("Y-m-d H:i:s", $dateUnix);
}

/**
 * catalog_findStatAbbrev function
 *
 * @return void
 * @author Gary MacDougall
 **/
function catalog_findStateAbbrev( $state )
{
	$USStateCodes = array (
		"ALABAMA"        => "AL",
		"ALASKA"         => "AK",
		"AMERICAN SAMOA" => "AS",
		"ARIZONA"        => "AZ",
		"ARKANSAS"       => "AR",
		"CALIFORNIA"     => "CA",
		"COLORADO"       => "CO",
		"CONNECTICUT"    => "CT",
		"DELAWARE"       => "DE",
		"DISTRICT OF COLUMBIA" => "DC",
		"FEDERATED STATES OF MICRONESIA" => "FM",
		"FLORIDA"        => "FL",
		"GEORGIA"        => "GA",
		"GUAM"           => "GU",
		"HAWAII"         => "HI",
		"IDAHO"          => "ID",
		"ILLINOIS"       => "IL",
		"INDIANA"        => "IN",
		"IOWA"           => "IA",
		"KANSAS"         => "KS",
		"KENTUCKY"       => "KY",
		"LOUISIANA"      => "LA",
		"MAINE"          => "ME",
		"MARSHALL ISLANDS" => "MH",
		"MARYLAND"       => "MD",
		"MASSACHUSETTS"  => "MA",
		"MICHIGAN"       => "MI",
		"MINNESOTA"      => "MN",
		"MISSISSIPPI"    => "MS",
		"MISSOURI"       => "MO",
		"MONTANA"        => "MT",
		"NEBRASKA"       => "NE",
		"NEVADA"         => "NV",
		"NEW HAMPSHIRE"  => "NH",
		"NEW JERSEY"     => "NJ",
		"NEW MEXICO"     => "NM",
		"NEW YORK"       => "NY",
		"NORTH CAROLINA" => "NC",
		"NORTH DAKOTA"   => "ND",
		"NORTHERN MARIANA ISLAND" => "MP",
		"OHIO"           => "OH",
		"OKLAHOMA"       => "OK",
		"OREGON"         => "OR",
		"PALAU ISLAND"   => "PW",
		"PENNSYLVANIA"   => "PA",
		"PUERTO RICO"    => "PR",
		"RHODE ISLAND"   => "RI",
		"SOUTH CAROLINA" => "SC",
		"SOUTH DAKOTA"   => "SD",
		"TENNESSEE"      => "TN",
		"TEXAS"          => "TX",
		"UTAH"           => "UT",
		"VERMONT"        => "VT",
		"VIRGIN ISLANDS" => "VI",
		"VIRGINIA"       => "VA",
		"WASHINGTON"     => "WA",
		"WEST VIRGINIA"  => "WV",
		"WISCONSIN"      => "WI",
		"WYOMING"        => "WY",
	);

	$code = $state;
	$state = strtoupper($state);
	if( array_key_exists($state,$USStateCodes) )
	{
		$code = $USStateCodes[$state];
	}

	return $code ;
}


/**
 * logger function.
 * 
 * @access public
 * @param mixed $data
 * @param boolean
 * @return void
 */
function logger ($data, $bLogOverride = false)
{
	if (ENABLE_LOGGING == true || $bLogOverride)
	{
		$date = "[" . date("D M j G:i:s T Y") . "] ";
		$array_dump = '';
		if (is_array ($data))
		{
			foreach ($data as $key=>$value)
			{
				$array_dump .= 'key =>[' . $key . '] value=>[' . $value . ']\n';
			}		
		} else {
			$new_data = $date . $data;
		}
		if ($array_dump != '')
		{
			$new_data = $data . $array_dump;
		}
		$fp = fopen (ERROR_LOG_PATH, "a+");
		fwrite ($fp, $new_data . "\n");
		fclose($fp);
		$this->LastMessage = $data;
	}
}

/**
 * ParseGiftMessage
 *
 * @emits XML tags Greeting1 through Greeting6 for MOM
 * @return void
 * @author Paul Quirnbach
 */
function ParseGiftMessage( &$order )
{

	// 2011Oct24 PJQ - parse giftmessage into Greeting1 thru 6
	$message = Mage::getModel('giftmessage/message');
	$gift_message_id = $order->getGiftMessageId();
	if(!is_null($gift_message_id)) {
		$message->load((int)$gift_message_id);
		$gift_sender = $message->getData('sender');
		$gift_recipient = $message->getData('recipient');
		$giftmessage = $message->getData('message');
	}

	//$giftmessage = str_replace ("\n", " ", $giftmessage); //
	$giftmessage = str_replace ("\r", " ", $giftmessage); //
	$giftmessage = str_replace ("\m", " ", $giftmessage); //
	$giftmessage = str_replace ("\t", " ", $giftmessage); //

	$giftmessage = strip_tags ($giftmessage); /// anyone trying to get cute?

	// try the simple case - and preserve newlines...
	$gift_array_easy = explode("\n", $giftmessage); /// split on a newline. fill the array.

	$is_easy = 1;
	if( count($gift_array_easy) < 7 )
	{
		foreach($gift_array_easy as $aLine)
		{
			if(strlen($aLine) > 35)
			{
				$is_easy = 0;
			}
		}
	}
	else
	{
		$is_easy = 0;
	}


	$line_array[0] = "";

	if( $is_easy )
	{
		// if we are still easy, then slam the lines right into the line_array, they can go directly into Greeting1 to 6
		$line_array = array_merge($line_array, $gift_array_easy);
	}
	else
	{
		// we are back to the original parsing, just trying to get it to fit into MOM's Greeting1 to 6,
		// its too big to preserve newlines
		$giftmessage = str_replace ("\n", " ", $giftmessage); //
		$gift_array = explode(" ", $giftmessage); /// split on a space. fill the array.
		//$line_array[0] = "";
		$line=1;
		$temp = "";
		$newline = true;
		for ($index=0; $index < count($gift_array); $index++)
		{
			if ( strlen($temp)+strlen($gift_array[$index]) < 35 )
			{
				if (!$newline)
				{
					$temp .= " ";
				}
				$newline = false;
				$temp .= $gift_array[$index];
			}
			else
			{
				$line_array[$line] = $temp;
				$line++;
				$temp = $gift_array[$index];
				//if ($line == 6)
				//    break;
			}
		}
		if ($line <= 6)
		{
			$line_array[$line] = $temp;
		}
	}
	writeElement("Greeting1", $line_array[1]);
	writeElement("Greeting2", $line_array[2]);
	writeElement("Greeting3", $line_array[3]);
	writeElement("Greeting4", $line_array[4]);
	writeElement("Greeting5", $line_array[5]);
	writeElement("Greeting6", $line_array[6]);


	// end parse giftmessage

}

function GetLatinLongDescription ()
{
	return "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?";
}

function GetLatinShortDescription ()
{
	return "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
}
function GetLatinProductName ()
{
	return "Lorem Ipsum Dolor Sit Amet";
}
///////////////////////////////////////////////////////////////////////////////////
// XML tools
///////////////////////////////////////////////////////////////////////////////////
/*
	Original source from: http://codes.myfreewares.com/php/XML/XML%20@=@%20Associative%20Array.php-file.html
	Modified by Chris Jean (http://gaarai.com/)

	Version History
		1.0 - 2009-01-12
			Original modification
		1.1 - 2009-02-11
			Added var testing to remove warnings from ArrayToXML code
			Fixed incorrect parent nesting in ArrayToXML
			Added xmlLibTest.php file to archive

	Examples
		XMLToArray -- Convert XML to Associative Array
			Usage:		$xml = new XMLToArray( xmldata (string), ignorefields (array 1,2,3), replacefields(array OLD => NEW), show attributes?, convert to upper );
						$array = $xml->getArray();
			Example:	$xml = new XMLToArray( 'http://www.slashdot.org/slashdot.xml', array( 'backslash' ), array( 'story' => '_array_' ), true, false );
						print_r( $xml->getArray() );
						print_r( $xml->getReplaced() );
						print_r( $xml->getAttributes() );

		ArrayToXML -- Convert Associative Array to XML
			Usage:		$array = new ArrayToXML( $xml->getArray(), $xml->getReplaced(), $xml->getAttributes() )
						$xml = $array->getXML();
*/

class ArrayToXML {
	var $_data;
	var $_name = Array();
	var $_rep  = Array();
	var $_parser = 0;
	var $_ignore, $_err, $_errline, $_replace, $_attribs, $_parent;
	var $_level = 0;

        // php 7 or newer requires to use __construct instead
        // Deprecated: Methods with the same name as their class will not be constructors in a future version of PHP
        //function ArrayToXML( &$data, $replace = Array(), $attribs = Array() ) {
        function __construct( &$data, $replace = Array(), $attribs = Array() ) {
		$this->_attribs = $attribs;
		$this->_replace = $replace;
		$this->_data = $this->_processArray( $data );
	}

	function & getXML() {
		return $this->_data;
	}

	function _processArray( &$array, $level = 0, $parent = '' ) {
		//ksort($array);
		$return = '';
		foreach ( (array) $array as $name => $value ) {
			$tlevel = $level;
			$isarray = false;
			$attrs = '';

			if ( is_array( $value ) && ( sizeof( $value ) > 0 ) && array_key_exists( 0, $value ) ) {
				$tlevel = $level - 1;
				$isarray = true;
			}
			elseif ( ! is_int( $name ) ) {
				if ( ! isset( $this->_rep[$name] ) )
					$this->_rep[$name] = 0;
				$this->_rep[$name]++;
			}
			else {
				$name = $parent;
				if ( ! isset( $this->_rep[$name] ) )
					$this->_rep[$name] = 0;
				$this->_rep[$name]++;
			}

			if ( ! isset( $this->_rep[$name] ) )
				$this->_rep[$name] = 0;

			if ( isset( $this->_attribs[$tlevel][$name][$this->_rep[$name] - 1] ) && is_array( $this->_attribs[$tlevel][$name][$this->_rep[$name] - 1] ) ) {
				foreach ( (array) $this->_attribs[$tlevel][$name][$this->_rep[$name] - 1] as $aname => $avalue ) {
					unset( $value[$aname] );
					$attrs .= " $aname=\"$avalue\"";
				}
			}
			if ( $this->_replace[$name] )
				$name = $this->_replace[$name];

			is_array( $value ) ? $output = $this->_processArray( $value, $tlevel + 1, $name ) : $output = htmlspecialchars( $value );

			$isarray ? $return .= $output : $return .= "<$name$attrs>$output</$name>\n";
		}
		return $return;
	}
}

class XMLToArray {
	var $_data = Array();
	var $_name = Array();
	var $_rep  = Array();
	var $_parser = 0;
	var $_ignore = Array(), $_replace = Array(), $_showAttribs;
	var $_level = 0;

	function XMLToArray( $data, $ignore = Array(), $replace = Array(), $showattribs = false, $toupper = false) {
		if ( preg_match( '@^(https?|ftp)://@', $data ) ) {
			if ( $stream = fopen( $data, 'r' ) ) {
				$data = stream_get_contents( $stream );
				fclose( $stream );
			}
			else
				return false;
		}
		if ( file_exists( $data ) )
			$data = file_get_contents( $data );

		$this->_showAttribs = $showattribs;
		$this->_parser  = xml_parser_create();

		xml_set_object( $this->_parser, $this );
		if ( $toupper ) {
			foreach ( (array) $ignore as $key => $value )
				$this->_ignore[strtoupper( $key )]  = strtoupper( $value );
			foreach ( (array) $replace as $key => $value)
				$this->_replace[strtoupper( $key )] = strtoupper( $value );
			xml_parser_set_option( $this->_parser, XML_OPTION_CASE_FOLDING, true);
		}
		else {
			$this->_ignore  = &$ignore;
			$this->_replace = &$replace;
			xml_parser_set_option( $this->_parser, XML_OPTION_CASE_FOLDING, false);
		}
		xml_set_element_handler( $this->_parser, '_startElement', '_endElement' );
		xml_set_character_data_handler( $this->_parser, '_cdata');

		$this->_data = array();
		$this->_level = 0;
		if( ! xml_parse( $this->_parser, $data, true ) ) {
			//new Error("XML Parse Error: ".xml_error_string(xml_get_error_code($this->_parser))."n on line: ".xml_get_current_line_number($this->_parser),true);
			return false;
		}
		xml_parser_free( $this->_parser );
	}

	function & getArray() {
		return $this->_data[0];
	}

	function & getReplaced() {
		return $this->_data['_Replaced_'];
	}

	function & getAttributes() {
		return $this->_data['_Attributes_'];
	}

	function _startElement( $parser, $name, $attrs ) {
		if ( ! isset( $this->_rep[$name] ) )
			$this->_rep[$name] = 0;
		if ( ! in_array( $name, $this->_ignore ) ) {
			$this->_addElement( $name, $this->_data[$this->_level], $attrs, true );
			$this->_name[$this->_level] = $name;
			$this->_level++;
		}
	}

	function _endElement( $parser, $name ) {
		if ( ! in_array( $name, $this->_ignore ) && isset( $this->_name[$this->_level - 1] ) ) {
			if ( isset( $this->_data[$this->_level] ) )
				$this->_addElement( $this->_name[$this->_level - 1], $this->_data[$this->_level - 1], $this->_data[$this->_level], false );

			unset( $this->_data[$this->_level] );
			$this->_level--;
			$this->_rep[$name]++;
		}
	}

	function _cdata( $parser, $data ) {
		if ( ! empty( $this->_name[$this->_level - 1] ) )
			$this->_addElement( $this->_name[$this->_level - 1], $this->_data[$this->_level - 1], str_replace( array( '&gt;', '&lt;', '&quot;', '&amp;' ), array( '>', '<', '"', '&' ), $data ), false );
	}

	function _addElement( &$name, &$start, $add = array(), $isattribs = false ) {
		if ( ( ( sizeof( $add ) == 0 ) && is_array( $add ) ) || ! $add ) {
			if ( ! isset( $start[$name] ) )
				$start[$name] = '';
			$add = '';
			//if (is_array($add)) return;
			//return;
		}
		if ( ! empty( $this->_replace[$name] ) && ( '_ARRAY_' === strtoupper( $this->_replace[$name] ) ) ) {
			if ( ! $start[$name] )
				$this->_rep[$name] = 0;
			$update = &$start[$name][$this->_rep[$name]];
		}
		elseif ( ! empty( $this->_replace[$name] ) ) {
			if ( $add[$this->_replace[$name]] ) {
				$this->_data['_Replaced_'][$add[$this->_replace[$name]]] = $name;
				$name = $add[$this->_replace[$name]];
			}
			$update = &$start[$name];
		}
		else
			$update = &$start[$name];

		if ( $isattribs && ! $this->_showAttribs )
			return;
		elseif ( $isattribs )
			$this->_data['_Attributes_'][$this->_level][$name][] = $add;
		elseif ( is_array( $add ) && is_array( $update ) )
			$update += $add;
		elseif ( is_array( $update ) )
			return;
		elseif ( is_array( $add ) )
			$update = $add;
		elseif ( $add )
			$update .= $add;
	}
}
?>
