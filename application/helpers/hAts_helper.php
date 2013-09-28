<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * hArpanet Template System (hATS) helper for CodeIgniter 2.3.x
 *
 * @package		hATS
 * @author		DH
 * @copyright	Copyright (c) 2012+, hArpanet
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://harpanet.com
 * @version		Version 1.6.2 / 130928
 *
 * @requires	GLOBAL PROPERTY ARRAY $hAtsData[] to be present and set in your controller (see _checkTplVar below)
 *
 * @method		tplSetTemplate	( $theme, $parts )						- v1.5.0
 * @method		tplGetPath		( $file, $in_theme, $use_baseurl )		- v1.5.0 modified v1.5.10
 * @method		tplStylesheet	( $file, $in_theme, $in_parts_subdir )	- v1.5.0 modified v1.5.6 @TODO: change in_parts_subdir to TRUE
 * @method		tplStylesheets	( )										- v1.5.2
 * @method		tplAddStylesheet( $css )								- v1.5.2
 * @method		tplJavascript	( $file, $in_theme, $in_parts_subdir )	- v1.5.0 modified v1.5.6 @TODO: change in_parts_subdir to TRUE
 * @method		tplJavascriptParsed	( $file, $in_theme, $in_parts_subdir )	- v1.6.0 added
 * @method		tplJavascripts	( )										- v1.5.3
 * @method		tplAddJavascript( $js )									- v1.5.3
 * @method		tplImage		( $file, $in_theme, $in_parts_subdir )	- v1.5.0 modified v1.5.6 @TODO: change in_parts_subdir to TRUE
 * @method		tplSet			( $var, $val, $elem )					- v1.1.0 modified v1.5.7
 * @method		tplAdd			( $var, $val, $nl )						- v1.1.0
 * @method		tplGet			( $var [,$elem] )						- v1.1.0
 * @method		tplGetOr		( $var, $or )							- v1.5.11 added
 * @method		tplAddPart		( $part )								- v1.5.1
 * @method		tplGetPart		( $part )								- v1.5.0 modified v1.5.4
 * @method		tplGetPartAsHtml( $part )								- v1.5.4
 * @method		tplGetParts		( )										- v1.5.1
 * @method		tplResponse_message( )									- v1.5.x modified v1.6.1 added jQuery fadout to messages
 *
 * @method		_tplGetThemeFile($type="", $file="", $in_parts_subdir)	- v1.2.0 modified in v1.5.6 @TODO: change in_parts_subdir to TRUE
 * @method		_get_parts_dir( $in_parts_subdir )						- v1.5.6
 *
 * @example		In your Controller, use: 	tplSet('varName',	'var content');
 * @example		In your Views, use: 		<?php echo tplGet('varName'); ?>
 *
 * @note		RESERVED TEMPLATE VARIABLES for tplSet / tplGet
 * @note			['theme']		- specifies location of theme in use
 * @note			['parts']		- specifies location of parts in use
 * @note			['viewPart']	- array containing names of view parts to display - use tplAddPart() & tplGetPart()
 * @note			['css']			- array containing names of css files to display - use tplAddStylesheet()
 * @note			['js']			- array containing names of js  files to output  - use tplAddJavascript()
 */

/*
 * GENERAL FUNCTIONS
 * =================
 */

define("TPLVAR", 		'hATS');	// the array key (in $hAtsData) used to store all template values
define("hAts_DEBUG",	/**/ FALSE /*/ TRUE /**/);		// either use tplSet('debug', true) or set it manually here

/*
 * INTERNAL FUNCTIONS - used by other hATS functions
 * ==================
 */

if (!function_exists('_tplDebug'))
{
	function _tplDebug( $msg="" )
	/**
	 * Enable/Disable output of template debug messages
	 * @version	28-Sep-2013
	 *
	 * @return	bool	current setting 	(True = enabled, False = disabled)
	 */
	{
		// set debugging on or off
		if (tplGet('debug') === TRUE) {
			// enabled via template var
			$ENABLED = tplGet('debug');

		} else {
			// use global setting
			$ENABLED = hAts_DEBUG;
		}

		if ( $ENABLED && $msg ) {
			echo "<div style='color:grey; clear:both;'>$msg</div>";
		}

		return $ENABLED;
	}
}

if (!function_exists('_checkTplVar'))
{
	/**
	 * Check if the tplvar array element exists and create it if it doesn't
	 * @version	11-Sept-2012
	 *
	 * @return			$this
	 */
	function _checkTplVar()
	{
		$ci=& get_instance();

		if ( !isset($ci->hAtsData) ) exit('ERROR: GLOBAL PROPERTY ARRAY $hAtsData[] DOES NOT EXIST IN YOUR CONTROLLER. hATS CANNOT CONTINUE. Add: var $hAtsData = array(); to fix.');

		if ( !isset($ci->hAtsData[TPLVAR]) ) {
			$ci->hAtsData[TPLVAR] = array();
		}

		//		return $this;
	}
}

/*
 * PUBLIC FUNCTIONS - available to Controllers, Views, etc.
 * ================
 */

if (!function_exists('tplSetTemplate'))
{
	/**
	 * Set names of theme and parts to use
	 * @version	11-Sept-2012
	 *
	 * @param	string	$theme	name of theme
	 * @param	string	$parts	name of parts
	 * @return			$this
	 *
	 * NOTE:
	 * Although the default folders are specified here as '/theme/'
	 * and '/parts/' they can be overridden in the application
	 * controller by specifying new ones (using tplSet lines similar
	 * to those used below)
	 */
	function tplSetTemplate($theme = "default", $parts="default")
	{
		tplSet('theme', '/theme/'.$theme);
		tplSet('parts', '/parts/'.$parts);

		//		return $this;
	}
}

if (!function_exists('tplGetPath'))
{
	/**
	 * Get the path to a specified PARTS file using traversal
	 * @version	13-Nov-2012
	 *
	 * @param	string	$file			filename requested, including path
	 * @param	bool	$inTheme		(default TRUE) flag indicating whether the parts path is located within
	 * 									the theme folder. If TRUE, parts path is appended to
	 * 									theme path; if False, parts path is used as-is
	 * @param	bool	$use_baseurl	(default TRUE) If $in_theme is FALSE and $use_baseurl is TRUE, base_url() is prepended to $file path
	 *
	 * @return	string			path to found file
	 */
	function tplGetPath( $file="", $in_theme = TRUE, $use_baseurl = FALSE )
	{
		$newfile = "";

		// build filename: add theme and parts path to filename specified, and tag .php onto the end
		if ( $in_theme )
		{
			// parts are located within the theme folder, so prepend theme folder
			// Note: modified on 15-Aug-2012 : moved parts folder out of named theme and placed
			//								   in root of /theme folder - much more logical
			$newfile .= "." ."/theme";	// .tplGet('theme');
		}

		_tplDebug( "THEME PATH:".$newfile );
		_tplDebug( "PARTS PATH:".tplGet('parts') );

		if ($use_baseurl)
		{
			// if baseurl is requested, prepend it and assume that a full path to
			// a resource is specified (ie. don't use parts path)
			$CI =& get_instance();
			$newfile = $CI->config->base_url() . $newfile;

			_tplDebug( 'BASEURL:'.$newfile );
		}
		else
		{
			// not using basepath, so add the parts path
			$newfile .= tplGet('parts');

			// if 'parts' value is blank, don't add extra / to path
			if ( substr( $newfile, strlen($newfile)-1, 1) != "/" ) $newfile .= "/";
		}

		// now add filename to path
		if ($use_baseurl)
		{
			// if using baseurl, assume a full filename has been specified
			$newfile .= $file;
		}
		else
		{
			// not using baseurl, so append .php to specified filename
			$newfile .= $file;
			if (substr($file, -4) != '.php')
			{
				$newfile .= '.php';
			}
		}

		_tplDebug( "LOOKING FOR:".$newfile );
		_tplDebug( "REAL PATH=".realpath($newfile) );


		if ($use_baseurl)
		{
			// using baseurl, so assume full path to file is specified
			$file = $newfile;
		}
		else
		{
			// need to search for file as baseurl not used...
			// use for-loop instead of while to prevent against infinite loop when searching
			// we will only traverse back 100 parent folders maximum
			for ( $lwp=100; $lwp>0; $lwp-- )
			{
				// see if the file exists at the new filepath
				if ( file_exists($newfile) )
				{
					// file found, quit looking
					$file = $newfile;
					break;
				}
				// get parent location
				$newfile = dirname(dirname($newfile))."/".basename($newfile);
			}
		}

		_tplDebug( "FOUND:".$newfile );

		return $file;
	}
}


/*
 * STYLESHEET HANDLING
 * ==========
 */

if (!function_exists('tplStylesheet'))
{
	/**
	 * Return a fully qualified HTML element for the specified stylesheet file (in the current theme)
	 * @version	14-Jun-2012
	 *
	 * @param	string	$file				path/name of stylesheet - without (or with .css) (eg. style or style.css)
	 * @param	bool	$in_theme			Is stylesheet located in theme folder? Yes=True, No=False
	 * 										If No, assume full path has been specified
	 * @param	bool	$in_parts_subdir	Are stylesheet files in subfolders named after Parts setting?
	 * 										True=Yes, e.g. /theme/css/techRS/mystyle.css
	 * 										False=No, e.g. /theme/css/mystyle.css
	 * @return	string						HTML stylesheet element
	 */
	function tplStylesheet($file, $in_theme=TRUE, $in_parts_subdir=FALSE)
	{
		if ( ! $file == '' )
		{
			// add .css to filename if not already present
			if ( substr( trim($file), -4, 4) !== '.css' )
			{
				$file = trim($file).".css";
			}

			if ($in_theme)
			{
				// get location of file from theme

				$retval = "<link rel='stylesheet' href='" ._tplGetThemeFile("css", $file, $in_parts_subdir). "' type='text/css'>\n";
			}
			else
			{
				// not using theme so assume literal file path specified
				$retval = "<link rel='stylesheet' href='" .$file. "' type='text/css'>\n";
			}

			return $retval;
		}
	}
}


if (!function_exists('tplStylesheets'))
{
	/**
	 * Output all stylesheets stacked up with tplAddStylesheet
	 * @version	14-Jun-2012
	 *
	 * @return	valid HTML stylesheet statement(s)
	 */
	function tplStylesheets()
	{
		_checkTplVar();

		$ci=& get_instance();

		$retval = "";

		if ( array_key_exists('css', $ci->hAtsData[TPLVAR]) ) {
			$parts = $ci->hAtsData[TPLVAR]['css'];

			if ( is_array($parts) ) {
				foreach( $parts as $css ) {

					_tplDebug( "STYLESHEETS: CSS FOUND: ".$css );

					$retval .= tplStylesheet( $css );
				}

			}else{

				// this should never happen as all parts are added as array entities, but just in case!...

				_tplDebug( "STYLESHEETS: ONLY ONE STYLESHEET FOUND: ".$ci->hAtsData[TPLVAR]['css'][0] );

				$retval = tplStylesheet( $ci->hAtsData[TPLVAR]['css'][0] );
			}
		}

		return $retval;
	}
}


if (!function_exists('tplAddStylesheet'))
{
	/**
	 * Add a stylesheet to the stack - for output with tplStylesheets()
	 * @version	11-Sept-2012
	 *
	 * @param	string	$css	name of stylesheet to load - excluding (or including .css file extension)
	 * 							the path is automatically located in /theme/[theme name]/css/[css name]/
	 * @return			$this
	 */
	function tplAddStylesheet($css)
	{
		_checkTplVar();

		$ci=& get_instance();

		$ci->hAtsData[TPLVAR]['css'][] = $css;

		//		return $this;
	}
}



/*
 * JAVASCRIPT HANDLING
 * ===================
 */

if (!function_exists('tplJavascript'))
{
	/**
	 * Return a fully qualified HTML element for a javascript file in the current theme
	 * @version	14-Jun-2012
	 *
	 * @param	string	file				path/name of javascript (eg. jQuery.js or just jQuery)
	 * @param	bool	$in_theme			Is javascript located in theme folder? Yes=True, No=False
	 * 										If No, assume full path has been specified
	 * @param	bool	$in_parts_subdir	Are javascript files in subfolders named after Parts setting?
	 * 										True=Yes, e.g. /theme/js/techRS/validate.js
	 * 										False=No, e.g. /theme/js/validate.js
	 * @return	string						HTML javascript element
	 */
	function tplJavascript($file, $in_theme=TRUE, $in_parts_subdir=FALSE)
	{
		if ( ! $file == '' )
		{
			// add .js to filename if not already present
			if ( substr( trim($file), -3, 3) !== '.js' )
			{
				$file = trim($file).".js";
			}

			if ($in_theme)
			{
				// get location of file from theme

				$retval = "<script type='text/javascript' src='" ._tplGetThemeFile("js", $file, $in_parts_subdir). "'></script>";
			}
			else
			{
				// not using theme so assume literal file path specified
				$retval = "<script type='text/javascript' src='" .$file. "'></script>";
			}

			return $retval;
		}
	}
}


if (!function_exists('tplJavascriptParsed'))
{
	/**
	 * Load a theme Javascript file into memory, parse it for PHP, then return it as string.
	 * Returned file content will automatically be wrapped in <script> tags.
	 * @author	hArpanet.com
	 * @version	16-Jan-2013
	 *
	 * @param	string	file				path/name of javascript (eg. jQuery.js or just jQuery)
	 * @param	bool	$in_theme			Is javascript located in theme folder? Yes=True, No=False
	 * 										If No, assume full path has been specified
	 * @param	bool	$in_parts_subdir	Are javascript files in subfolders named after Parts setting?
	 * 										True=Yes, e.g. /theme/js/techRS/validate.js
	 * 										False=No, e.g. /theme/js/validate.js
	 * @return	string						HTML javascript <script> block
	 */
	function tplJavascriptParsed($file, $in_theme=TRUE, $in_parts_subdir=FALSE)
	{
		// no point doing anything if filename not specified
		if ( ! empty($file) )
		{
			// add .js to filename if not already present
			if ( substr( trim($file), -3, 3) !== '.js' )
			{
				$file = trim($file).".js";
			}

			_tplDebug( "JAVASCRIPT PARSED: ".$file );

			if ($in_theme)
			{
				// get location of file from theme
				$file = _tplGetThemeFile("js", $file, $in_parts_subdir);	//tplGetPath($file);
//				$tfile = realpath('.') . '/..' .$tfile;
				$file = realpath($_SERVER['DOCUMENT_ROOT']) .$file;
			}
			else
			{
				// assume full path was specified
				$file = $file;
			}

			_tplDebug( "JAVASCRIPT PARSED REAL PATH: ".$file );

			// load the file contents
			$jsfile = file_get_contents($file);

			// wrap in script tags
			$retval = "<script type='text/javascript'>$jsfile</script>";

			// run eval to parse any PHP instructions
			$retval = eval('?>'.$retval);

			return $retval;
		}
	}
}


if (!function_exists('tplJavascripts'))
{
	/**
	 * Output all javascripts stacked up with tplAddJavascript
	 * @version	17-Jan-2013
	 *
	 * @return	valid HTML javascript statement(s)
	 */
	function tplJavascripts()
	{
		_checkTplVar();

		$ci=& get_instance();

		$retval = "";

		if ( array_key_exists('js', $ci->hAtsData[TPLVAR]) ) {
			$parts = $ci->hAtsData[TPLVAR]['js'];

			if ( is_array($parts) ) {
				foreach( $parts as $js ) {

					_tplDebug( "JAVASCRIPTS: JS FOUND: ".$js );

					// check if js name contains 'parse' flag '#'
					if ( substr($js,0,1)=='#' )
					{
						// this javascript needs to be parsed for PHP tags
						$retval = tplJavascriptParsed( substr($js,1) );
					}
					else
					{
						$retval .= tplJavascript( $js );
					}
				}

			}else{

				// this should never happen as all parts are added as array entities, but just in case!...

				_tplDebug( "JAVASCRIPTS: ONLY ONE JS FILE FOUND: ".$ci->hAtsData[TPLVAR]['js'][0] );

				$retval = tplJavascript( $ci->hAtsData[TPLVAR]['js'][0] );
			}
		}

		return $retval;
	}
}


if (!function_exists('tplAddJavascript'))
{
	/**
	 * Add a javascript file to the stack - for output with tplJavascripts()
	 * @version	11-Sept-2012
	 *
	 * NOTE: all javascripts are assumed to be in the current theme/js/partname folder,
	 * 		 It is NOT POSSIBLE to add javascript from other locations (e.g. /assets/js)
	 * 		 using this method. (Use tplJavascript() to load files from other locations.)
	 *
	 * NOTE: precede the javascript filename with hash symbol to indicate if it should be 'parsed'
	 * 		 during later processing with tplJavascripts()
	 * 		 eg. '#add_user' would cause the 'add_user.js' file to be passed to tplJavascriptParsed() before output
	 * 			 'add_user'  would cause the 'add_user.js' file to be output as-is
	 *
	 * @param	string	$js		name of javascript to load - excluding (or including) .js file extension
	 * 							the path is automatically located in /theme/[theme name]/js/[js name]/
	 * @return			$this
	 */
	function tplAddJavascript($js)
	{
		_checkTplVar();

		$ci=& get_instance();

		$ci->hAtsData[TPLVAR]['js'][] = $js;

		//		return $this;
	}
}




/*
 * IMAGE HANDLING
 * =====
 */

if (!function_exists('tplImage'))
{
	/**
	 * Return a fully qualified path to a current theme image file
	 * @version	14-Jun-2012
	 *
	 * @param	string	file	path/name of image (eg. companyLogo.png) - NOTE: file extension required
	 * @param	bool	$in_theme			Is image located in theme folder? Yes=True, No=False
	 * 										If No, assume full path has been specified
	 * @param	bool	$in_parts_subdir	Are image files in subfolders named after Parts setting?
	 * 										True=Yes, e.g. /theme/img/techRS/machine.png
	 * 										False=No, e.g. /theme/img/machine.png
	 * @return	string			filepath
	 */
	function tplImage($file, $in_theme=TRUE, $in_parts_subdir=FALSE)
	{
		// TODO:	Possibly need a 'tplImages()' function to return multiple HTML include lines for multiple image files
		//			as per tplGetParts()

		if ($in_theme)
		{
			// get location of file from theme

			$retval = _tplGetThemeFile("img", $file, $in_parts_subdir);
		}
		else
		{
			// not using theme so assume literal file path specified
			$retval = $file;
		}

		return $retval;
	}
}



/*
 * VARIABLE HANDLING
 * ========
 */

if (!function_exists('tplSet'))
{
	/**
	 * SET VALUE OF TEMPLATE VARIABLE - overwrite existing value
	 * @version	14-Sept-2012
	 *
	 * @param	string	$var	template variable name
	 * 							NOTE: As of @version 1.5.7...
	 * 								  $var can now be passed as an assoc array, but if this
	 * 								  is used then it can only set plain variable values
	 * 								  and not an element of a variable containing an array
	 *	 							  (eg. array('var'=>'val', 'var'=>'val', etc.)
	 * @param	mixed	$val	value to set
	 * @param	string	$elem	if $var is an array, $elem allows us to set a value
	 * 							within that array by specifying its key.
	 * 							$var MUST already exist and be an array.
	 * @return			$this
	 */
	function tplSet($var='clipboard', $val='', $elem='' )
	{
		_checkTplVar();

		$ci=& get_instance();

		// hAtsData should always be an array, but check anyway to prevent error
		if ( is_array($ci->hAtsData) )
		{
			// is $var an array?
			if (is_array($var))
			{
				// array sent, process elements
				$var_array = $var;
				foreach($var_array as $var => $val)
				{
					// NOTE: Not able to set array elements when values passed as an array!
					// So here, we are just setting the $var with the $val...
					$ci->hAtsData[TPLVAR][$var] = $val;
				}
			}
			else
			{
				// plain parameters sent (not an array)

				// are we setting an array element?
				if ( empty($elem) )
				{
					// no, just set the $var
					$ci->hAtsData[TPLVAR][$var] = $val;
				}
				else
				{
					// yes, setting an array $elem, check that $var exists and that it is an array - if not create it
					if ( array_key_exists($var,$ci->hAtsData[TPLVAR]) === FALSE )
					{
						$ci->hAtsData[TPLVAR][$var] = array();
					}

					// now set the value
					$ci->hAtsData[TPLVAR][$var][$elem] = $val;
				}
			}
		}

		//		return $this;
	}
}

if (!function_exists('tplAdd'))
{
	/**
	 * Add another value to named template variable (value will be string appended to existing value)
	 * @version	14-Jun-2012
	 *
	 * @param	string	$var	template variable name
	 * @param	mixed	$val	value to set
	 * @return			$this
	 */
	function tplAdd($var = "clipboard", $val = "")
	{
		_checkTplVar();

		$ci=& get_instance();

		if ( array_key_exists($var, $ci->hAtsData[TPLVAR]) ) {
			$ci->hAtsData[TPLVAR][$var] .= $val;
		}else{
			tplSet($var, $val);
		}

		//		return $this;
	}
}

if (!function_exists('tplGet'))
{
	/**
	 * GET TEMPLATE VALUE
	 * @version	14-Jun-2012
	 *
	 * @param	string		$var	name of variable to retrieve
	 * @param	string		$elem	if $var contains an array, this optional parameter can
	 * 								specify an element within the $var array to return
	 * 								This saves having to return the array $var, then pulling
	 * 								out the element later.
	 * @return	string				Value of $var or blank string if not found
	 */
	function tplGet($var = "clipboard", $elem='')
	{
		_checkTplVar();

		$CI =& get_instance();
//
		// hAtsData should always be an array, but check anyway to prevent error
		if ( is_array($CI->hAtsData) )
		{
			// check if requested var exists
			if ( array_key_exists($var, $CI->hAtsData[TPLVAR]) ) {

				// if we are requesting an array element, check that the var is an array
				if ( $elem != '' AND is_array($CI->hAtsData[TPLVAR][$var]) )
				{
					// check if array element $elem exists
					if ( array_key_exists($elem, $CI->hAtsData[TPLVAR][$var]) )
					{
						return $CI->hAtsData[TPLVAR][$var][$elem];
					}

				}else{
					// not requesting array element $elem, so just return $var
					return $CI->hAtsData[TPLVAR][$var];
				}
			}
			elseif ($var == "__ALL__")
			{
				// return entire array - only of use for debugging
				return $CI->hAtsData;
			}
		}

		// var not found, return nothing
		return "";
	}
}

if (!function_exists('tplGetOr'))
{
	/**
	 * GET TEMPLATE VALUE OR RETURN SPECIFIED VALUE IF BLANK
	 * NOTE: YOU CANNOT GET AN ARRAY VALUE USING tplGetOr
	 * @version	18-Dec-2012
	 *
	 * @param	string		$var	name of variable to retrieve
	 * @param	string		$elem	if $var contains an array, this optional parameter can
	 * 								specify an element within the $var array to return
	 * 								This saves having to return the array $var, then pulling
	 * 								out the element later.
	 * @return	string				Value of $var or blank string if not found
	 */
	function tplGetOr($var = "clipboard", $or='')
	{
		$var = tplGet($var);

		if ($var == '')
		{
			$var = $or;
		}

		return $var;
	}
}

/*
 * PARTS HANDLING
 * =====
 */

if (!function_exists('tplAddPart'))
{
	/**
	 * Add a template Part to the stack - for output with getParts()
	 * A template 'part' is a small block of pHTML that can be included within
	 * another pHTML part file.
	 * @version	14-Jun-2012
	 *
	 * @param	string	$part	name of View part to load
	 * 							This should be a filename without .php extension.
	 * 							The path is by default located in /theme/parts/[parts name]/
	 * @return			$this
	 */
	function tplAddPart($part)
	{
		//@todo	Add a hierarchy 'position' to be specified when adding a part to dicate the order that parts will be shown in tplGetParts()

		_checkTplVar();

		$ci=& get_instance();

		$ci->hAtsData[TPLVAR]['viewPart'][] = $part;

		//		return $this;
	}
}

if (!function_exists('tplGetPart'))
{
	/**
	 * Get existing template Part contents (uses PHP 'require')
	 * A template 'part' is a small block of pHTML that can be included within
	 * another pHTML part file.
	 * @version	14-Jun-2012
	 *
	 * @param	string	$part	name of part to retrieve (i.e. filename without '.php' extension)
	 * @return			$this
	 *
	 * @example	Usage in a View file: <?php echo tplGetPart('nameOfPart'); ?>
	 */
	function tplGetPart($part)
	{
		// add .php extension if not specified
		if ( ! substr($part, -4) == '.php' )
		{
			$part .= '.php';
		}

		_tplDebug( "GETTING PART:".$part );

		$tfile = tplGetPath($part);

		require( $tfile );

		//		return $this;
	}
}

if (!function_exists('tplGetPartAsHtml'))
{
	/**
	 * Get contents of a template Part as a string value
	 * A template 'part' is a small block of pHTML that can be included within
	 * another pHTML part file.
	 * @version	14-Jun-2012
	 *
	 * @param	string	$part	name of part to retrieve (i.e. filename without '.php' extension)
	 * @return			$this
	 */
	function tplGetPartAsHtml($part)
	{
		// add .php extension if not specified
		if ( ! substr($part, -4) == '.php' )
		{
			$part .= '.php';
		}

		_tplDebug( "GETTING PART AS HTML:".$part );

		$tfile = tplGetPath($part);

		// load the file contents
		$file = file_get_contents($tfile);

		return $file;
	}
}


if (!function_exists('tplGetParts'))
{
	/**
	 * Get all view parts stacked for output
	 *
	 * @return	$this - No output from this function as parts are 'included' in the tplGetPart function
	 */
	function tplGetParts()
	{
		_checkTplVar();

		$ci=& get_instance();

		if ( array_key_exists('viewPart', $ci->hAtsData[TPLVAR]) ) {
			$parts = $ci->hAtsData[TPLVAR]['viewPart'];

			if ( is_array($parts) ) {
				foreach( $parts as $part ) {

					_tplDebug( "GETPARTS: PART FOUND: ".$part );

					tplGetPart( $part );
				}

			}else{

				// this should never happen as all parts are added as array entities, but just in case!...

				_tplDebug( "GETPARTS: ONLY ONE PART FOUND: ".$ci->hAtsData[TPLVAR]['viewPart'][0] );

				tplGetPart( $ci->hAtsData[TPLVAR]['viewPart'][0] );
			}
		}

		//		return $this;
	}
}


if (!function_exists('tplResponse_message'))
{
	/**
	 * Display a response message wrapped in a div
	 * @author 	hArpanet.com
	 * @version 02-May-2013 - added jQuery fadeout
	 * 			20-Jun-2012 - created
	 *
	 * @param	string	$name	array element name set using tplSet()
	 * @param	string	$css	if $name specified, $css can contains an optional name for the css div class; defaults to $name if not specified
	 * 							css class name always begins 'response_' with $css appended to it
	 */
	function tplResponse_message( $name='', $css='' )
	{
		_checkTplVar();

		$ci=& get_instance();

		if ( $name != '' && array_key_exists($name, $ci->hAtsData[TPLVAR]) )
		{
			$css 		= ( $css == '' ) ? $name : $css;
			$response 	= $ci->hAtsData[TPLVAR][$name];

			echo "<div class='response_$css'>$response</div>";
		}


		if ( array_key_exists('success', $ci->hAtsData[TPLVAR]) )
		{
			$success = $ci->hAtsData[TPLVAR]['success'];

			echo "<div style='cursor:pointer;' class='response_success' onclick=\"javascript:this.style.display='none';\">$success</div>";
		}

		if ( array_key_exists('fail', $ci->hAtsData[TPLVAR]) )
		{
			$fail = $ci->hAtsData[TPLVAR]['fail'];

			echo "<div style='cursor:pointer;' class='response_fail' onclick=\"javascript:this.style.display='none';\">$fail</div>";
		}

		// add some jQuery to auto close message if jQuery available
		$jq = "<script type='text/javascript'>
				if(window.jQuery) {
					setTimeout(function() {
						jQuery('[class^=\"response_\"]').animate({ height: 0, opacity: 0, margin: 0 }, 'slow', function() {jQuery(this).remove();});
					}, 8000);
				}
			   </script>";

		echo $jq;

		//		return $this;
	}
}


/*
 * Some 'private' helpers used by functions above
 */


if (!function_exists('_tplGetThemeFile'))
{
	/**
	 * main handler to return a fully qualified path to a current theme file
	 * @version	14-Jun-2012
	 *
	 * @param	string	$type				'css', 'js', or 'img' (or anything you want to specify, e.g. 'media', etc.)
	 * @param	string	$file				filename (eg. 'style.css')
	 * @param	bool	$in_parts_subdir	Are theme files in subfolders named after Parts setting?
	 * 										True=Yes, e.g. /theme/js/techRS/validation.js
	 * 										False=No, e.g. /theme/js/validation.js
	 * @return	string						path to specified theme file
	 */
	function _tplGetThemeFile($type="", $file="", $in_parts_subdir=FALSE)
	{
		$ci=& get_instance();

		// get current webroot
		$webRoot = $ci->config->item('webRoot');
		if ( !$webRoot ) $webroot = './';

		// get name of current theme and Parts name
		$theme = tplGet('theme');

		// TODO: default setting for $in_parts_subdir needs to change to TRUE and all methods
		//			need updating to strip out the Parts folder names.

		// are we adding the Parts subfolder to the path?
		$parts = _get_parts_dir( $in_parts_subdir );

		return $webRoot.$theme.$parts."/".$type."/".$file;
	}
}

/**
 * Return the Parts name if required with leading path slash '/'
 * @version	10-Sept-2012
 *
 * @param 	bool 	$in_parts_subdir	Flag indicating whether to return the Part name or not
 * @return	string						name of parts dir with leading slash, or blank
 */
function _get_parts_dir( $in_parts_subdir=TRUE )
{
	$parts = '';
	if ($in_parts_subdir)
	{
		$parts = "/".tplGet('parts');
	}

	return $parts;
}

/* End of file template_helper.php */
/* Location: sparks/ha-template/helpers/tvar_helper.php */
