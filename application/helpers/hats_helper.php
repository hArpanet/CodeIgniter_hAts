<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * hArpanet Template System (hATS) helper
 *
 * @package     hATS
 * @author      DH
 * @copyright   Copyright (c) 2012+, hArpanet
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://harpanet.com
 * @version     Version 2.0.0 / 140228
 *
 * @requires    GLOBAL PROPERTY ARRAY $hAtsData[] to be present and set in your controller (see _checkTplVar below)
 *
 * @method      tplPartsPath    ( $file, $in_theme, $use_baseurl )      - v1.7.0 modified v2.0.0 fixed path bugs
 * @method      tplGetPath      ( $file, $in_theme, $use_baseurl )      - v1.5.0 removed in v1.7.0
 * @method      tplStylesheet   ( $file, $in_theme )                    - v1.5.0 modified v2.0.0 removed $in_parts_subdir parameter
 * @method      tplStylesheets  ( )                                     - v1.5.2 modified v1.7.0 now also processes css outside theme
 * @method      tplAddStylesheet( $css )                                - v1.5.2
 * @method      tplJavascript   ( $file, $in_theme )                    - v1.5.0 modified v2.0.0 removed $in_parts_subdir parameter
 * @method      tplJavascriptParsed ( $file, $in_theme )                - v1.6.0 modified v2.0.0 removed $in_parts_subdir parameter
 * @method      tplJavascripts  ( )                                     - v1.5.3 modified v1.7.0 now also processes js outside theme
 * @method      tplAddJavascript( $js, $in_theme )                      - v1.5.3 modified v2.0.0 removed $in_parts_subdir parameter
 * @method      tplImage        ( $file, $in_theme )                    - v1.5.0 modified v2.0.0 removed $in_parts_subdir parameter
 * @method      tplSet          ( $var, $val, $elem )                   - v1.1.0 modified v1.5.7
 * @method      tplAdd          ( $var, $val, $nl )                     - v1.1.0
 * @method      tplGet          ( $var [,$elem] )                       - v1.1.0
 * @method      tplGetOr        ( $var, $or )                           - v1.5.11 added
 * @method      tplAddPart      ( $part, $data )                        - v1.5.1 modified v1.7.0 - added $data param
 * @method      tplGetParts     ( )                                     - v1.5.1 modified v1.7.0 - runs tplSet() against any $data
 * @method      tplGetPart      ( $part )                               - v1.5.0 modified v1.5.4
 * @method      tplGetPartAsHtml( $part )                               - v1.5.4
 * @method      tplResponse_message( $name, $css )                      - v1.5.x modified v1.6.1 added jQuery fadout to messages
 *
 * @method      _tplGetThemeFile( $type, $file )                        - v1.2.0 modified v2.0.0 in_parts_subdir changes
 * @method      _addSlash       ( $string, $trim )                      - v1.7.0 added
 * @method      _makePath       ( <multiple params> )                   - v2.0.0 added
 * @method      _tplFindFile    ( $file )                               - v1.7.0 added
 * @method      _tplSetDefaults ( )                                     - v1.5.0 added v2.0.0 renamed from tplSetTemplate()
 *
 * @note        RESERVED hATS VARIABLES for tplSet / tplGet
 * @note            ['theme']       - name of theme in use
 * @note            ['themePath']   - location (folder name) of theme files
 * @note            ['parts']       - name of part in use
 * @note            ['partsPath']   - location (folder name) of parts files
 * @note            ['viewPart']    - array containing names of view parts to display - use tplAddPart() & tplGetPart()
 * @note            ['css']         - array containing names of css files to display - use tplAddStylesheet()
 * @note            ['js']          - array containing names of js  files to output  - use tplAddJavascript()
 * @note            ['STOP_ON_ERROR'] - if this variable contains any value, a PHP error will be generated if a parts file is not found
 * @note            ['success']     - if you use tplResponse_message(), it assumes anything in the 'success' var is the message to show
 * @note            ['fail']        - if you use tplResponse_message(), it assumes anything in the 'fail' var is the message to show
 */

/*
 * GENERAL FUNCTIONS
 * =================
 */

define('TPLVAR', 'hATS');    // the array key (in $hAtsData) used to store all hATS values

/*
 * INTERNAL FUNCTIONS - used by other hATS functions
 * ==================
 */

if (!function_exists('_tplDebug'))
{
    function _tplDebug( $msg='' )
    /**
     * If debugging enabled, output hATS debug messages
     * @version 28-Feb-2014
     *
     * @return  bool    current setting     (True = enabled, False = disabled)
     */
    {
        // get debugging state
        $ENABLED = (tplGet('debug') === TRUE) ? TRUE : FALSE;

        if ( $ENABLED && $msg ) {
            echo "<div style='color:grey; clear:both; font-size:10px;'>{$msg}</div>";
        }

        return $ENABLED;
    }
}

if (!function_exists('_checkTplVar'))
{
    /**
     * Check if the tplvar array element exists and create it if it doesn't
     * @version 11-Sept-2012
     *
     * @return          $this
     */
    function _checkTplVar()
    {
        $ci=& get_instance();

        if ( !isset($ci->hAtsData) ) exit('ERROR: GLOBAL PROPERTY ARRAY $hAtsData[] DOES NOT EXIST IN YOUR CONTROLLER. hATS CANNOT CONTINUE. Add: var $hAtsData = array(); to fix.');

        if ( !isset($ci->hAtsData[TPLVAR]) ) {
            $ci->hAtsData[TPLVAR] = array();
        }

        //      return $this;
    }
}

/*
 * PUBLIC FUNCTIONS - available to Controllers, Views, etc.
 * ================
 */

if (!function_exists('tplPartsPath'))
{
    /**
     * Get the path to a specified PARTS file using traversal
     * @version 25-Feb-2014
     *
     * @param   string  $file           filename requested, including path
     * @param   bool    $use_baseurl    (default TRUE) base_url() is prepended to $file path
     *                                  if TRUE, partsPath contains just the folder name of the parts
     *                                  if FALSE, partsPath must contain a full path to parts folder
     *
     * @return  string                  path to found file
     */
    function tplPartsPath( $file='', $use_baseurl=true )
    {
        $newfile = '';

        // if paths not already set, set default ones
        _tplSetDefaults();

        // get baseurl if requested
        $baseurl = ($use_baseurl) ? FCPATH : '';

        _tplDebug( 'BASE FCPATH:'.$baseurl );
        _tplDebug( 'PARTS PATH:'.tplGet('partsPath') );
        _tplDebug( 'PARTS FOLDER:'.tplGet('parts') );

        // build the path to requested parts file
        $newfile = _makePath($baseurl, tplGet('partsPath'), tplGet('parts'));

        // add filename & extension to path
        $newfile .= _addFileExt($file);

        _tplDebug( 'LOOKING FOR:'.$newfile );
        _tplDebug( 'REAL PATH='.realpath($newfile) );

        return _tplFindFile($newfile);
    }
}


/*
 * STYLESHEET HANDLING
 * ===================
 */

if (!function_exists('tplStylesheet'))
{
    /**
     * Return a fully qualified HTML element for the specified stylesheet file (in the current theme)
     * @version 29-Oct-2013
     *
     * @param   string  $file               path/name of stylesheet - without (or with .css) (eg. style or style.css)
     * @param   bool    $in_theme           Is stylesheet located in theme folder? Yes=True, No=False
     *                                      If No, assume full path has been specified
     *
     * @return  string                      HTML stylesheet element
     */
    function tplStylesheet( $file, $in_theme=true )
    {
        if (!empty($file))
        {
            // add .css to filename if not already present
            $file = _addFileExt($file, 'css');

            if ($in_theme)
            {
                // get location of file from theme
                $retval = "<link rel='stylesheet' href='" ._tplGetThemeFile('css', $file). "' type='text/css'>\n";
            }
            else
            {
                // not using theme so assume literal file path specified
                $retval = "<link rel='stylesheet' href='{$file}' type='text/css'>\n";
            }

            return $retval;
        }
    }
}


if (!function_exists('tplStylesheets'))
{
    /**
     * Output all stylesheets stacked up with tplAddStylesheet
     * @version 09-Oct-2013
     *
     * @return  valid HTML stylesheet statement(s)
     */
    function tplStylesheets()
    {
        _checkTplVar();

        $ci=& get_instance();

        $retval = '';

        // process css files if specified
        if ( array_key_exists('css', $ci->hAtsData[TPLVAR]) ) {

            $parts = $ci->hAtsData[TPLVAR]['css'];

            if ( is_array($parts) ) {
                foreach( $parts as $css_data ) {

                    $css      = $css_data['css'];
                    $in_theme = $css_data['intheme'];

                    _tplDebug( 'STYLESHEETS: CSS REQUESTED: '.$css );

                    $retval .= tplStylesheet( $css, $in_theme );
                }

            } else {

                // this should never happen as all parts are added as array entities, but just in case!...

                _tplDebug( 'STYLESHEETS: ONLY ONE STYLESHEET FOUND: '.$ci->hAtsData[TPLVAR]['css'][0] );

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
     *
     * @version 25-Feb-2014
     *
     * @param   string  $css    name of stylesheet to load - excluding (or including .css file extension)
     *                          the path is automatically located in /theme/[theme name]/css/[css name]/
     * @param   string  $in_theme        Flag indicating if this css file exists within the current theme
     *
     * @return  void
     */
    function tplAddStylesheet( $css, $in_theme=true )
    {
        _checkTplVar();

        $ci=& get_instance();

        $ci->hAtsData[TPLVAR]['css'][] = array('css'=>$css, 'intheme'=>$in_theme);
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
     * @version 25-Feb-2014
     *
     * @param   string  file                path/name of javascript (eg. jQuery.js or just jQuery)
     * @param   bool    $in_theme           Is javascript located in theme folder? Yes=True, No=False
     *                                      If No, assume full path has been specified
     * @return  string                      HTML javascript element
     */
    function tplJavascript( $file, $in_theme=true )
    {
        if (!empty($file))
        {
            // add .js to filename if not already present
            $file = _addFileExt($file, 'js');

            _tplDebug('JS FILENAME:', $file);

            if ($in_theme)
            {
                _tplDebug('JS FROM THEME!');

                // get location of file from theme
                $retval = "<script type='text/javascript' src='" ._tplGetThemeFile('js', $file, FALSE). "'></script>";
            }
            else
            {
                _tplDebug('JS LITERAL PATH SUPPLIED!');

                // not using theme so assume literal file path specified
                $retval = "<script type='text/javascript' src='{$file}'></script>";
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
     * @author  hArpanet.com
     * @version 16-Jan-2013
     *
     * @param   string  file                path/name of javascript (eg. jQuery.js or just jQuery)
     * @param   bool    $in_theme           Is javascript located in theme folder?
     *                                      If No, assume full path has been specified
     * @return  string                      HTML javascript <script> block
     */
    function tplJavascriptParsed( $file, $in_theme=true )
    {
        // no point doing anything if filename not specified
        if ( ! empty($file))
        {
            // add .js to filename if not already present
            $file = _addFileExt($file, 'js');

            _tplDebug( 'JAVASCRIPT PARSED: '.$file );

            if ($in_theme)
            {
                // get location of file from theme
                $file = _tplGetThemeFile('js', $file);
                $file = realpath($_SERVER['DOCUMENT_ROOT']) .$file;
            }

            _tplDebug( 'JAVASCRIPT PARSED REAL PATH: '.$file );

            // load the file contents
            $jsfile = file_get_contents($file);

            // wrap in script tags
            $retval = "<script type='text/javascript'>{$jsfile}</script>";

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
     * @version 09-Oct-2013
     *
     * @return  valid HTML javascript statement(s)
     */
    function tplJavascripts()
    {
        _checkTplVar();

        $ci=& get_instance();

        $retval = '';

        if ( array_key_exists('js', $ci->hAtsData[TPLVAR]) ) {
            $parts = $ci->hAtsData[TPLVAR]['js'];

            if ( is_array($parts) ) {
                foreach( $parts as $js_data ) {

                    $js       = $js_data['js'];
                    $in_theme = $js_data['intheme'];

                    // check if js name contains 'parse' flag '#'
                    if ( $js[0] == '#' )
                    {
                        _tplDebug( "JAVASCRIPTS: PARSED JS REQUIRED: {$js}" );

                        // this javascript needs to be parsed for PHP tags
                        $retval .= tplJavascriptParsed( substr($js, 1), $in_theme );
                    }
                    else
                    {
                        _tplDebug( "JAVASCRIPTS: NORMAL JS REQUIRED: {$js}" );

                        $retval .= tplJavascript( $js, $in_theme );
                    }

                    _tplDebug("JAVASCRIPTS: JS RETURNED: {$retval}");
                }

            }else{

                // this should never happen as all parts are added as array entities, but just in case!...

                _tplDebug( 'JAVASCRIPTS: ONLY ONE JS FILE FOUND: '.$ci->hAtsData[TPLVAR]['js'][0] );

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
     * @version 11-Sept-2012
     *
     * NOTE: To add javascript from non theme locations (e.g. /assets/js)
     *       ensure $in_theme is passed as FALSE and put full filepath into $file.
     *
     * NOTE: precede the javascript filename with hash symbol to indicate if it should be 'parsed'
     *       for PHP code during later processing with tplJavascripts()
     *       eg. '#add_user' would cause the 'add_user.js' file to be passed to tplJavascriptParsed() before output
     *           'add_user'  would cause the 'add_user.js' file to be output as-is
     *
     * @param   string  $file   name of javascript to load - excluding (or including) .js file extension
     *                          the path is automatically located in /theme/[theme name]/js/[js name]/
     *                          if the following two parameters are left TRUE
     * @param   string  $in_theme        Flag indicating if current Parts name is within the current theme folder
     *
     * @return  void
     */
    function tplAddJavascript( $file, $in_theme=true )
    {
        _checkTplVar();

        $ci=& get_instance();

        $ci->hAtsData[TPLVAR]['js'][] = array('js'=>$jfile, 'intheme'=>$in_theme);
    }
}




/*
 * IMAGE HANDLING
 * ==============
 */

if (!function_exists('tplImage'))
{
    /**
     * Return a fully qualified path to a current theme image file
     * @version 14-Jun-2012
     *
     * @param   string  file    path/name of image (eg. companyLogo.png) - NOTE: file extension required
     * @param   bool    $in_theme           Is image located in theme folder? Yes=True, No=False
     *                                      If No, assume full path has been specified
     * @return  string          filepath
     */
    function tplImage( $file, $in_theme=true )
    {
        // TODO:    Possibly need a 'tplImages()' function to return multiple HTML include lines for multiple image files
        //          as per tplGetParts()

        if ($in_theme)
        {
            // get location of file from theme

            $retval = _tplGetThemeFile('img', $file);
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
 * =================
 */

if (!function_exists('tplSet'))
{
    /**
     * SET VALUE OF hATS VARIABLE - overwrite existing value
     * @version 14-Sept-2012
     *
     * @param   string  $var    hATS variable name
     *                          NOTE: As of @version 1.5.7...
     *                                $var can now be passed as an assoc array, but if this
     *                                is used then it can only set plain variable values
     *                                and not an element of a variable containing an array
     *                                (eg. array('var'=>'val', 'var'=>'val', etc.)
     * @param   mixed   $val    value to set
     * @param   string  $elem   if $var is an array, $elem allows us to set a value
     *                          within that array by specifying its key.
     *                          $var MUST already exist and be an array.
     * @return          $this
     */
    function tplSet( $var='clipboard', $val='', $elem='' )
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

        //      return $this;
    }
}

if (!function_exists('tplAdd'))
{
    /**
     * Add another value to named hATS variable (value will be string appended to existing value)
     * @version 14-Jun-2012
     *
     * @param   string  $var    hATS variable name
     * @param   mixed   $val    value to set
     * @return          $this
     */
    function tplAdd( $var = 'clipboard', $val = '' )
    {
        _checkTplVar();

        $ci=& get_instance();

        if ( array_key_exists($var, $ci->hAtsData[TPLVAR]) ) {
            $ci->hAtsData[TPLVAR][$var] .= $val;
        }else{
            tplSet($var, $val);
        }

        //      return $this;
    }
}

if (!function_exists('tplGet'))
{
    /**
     * GET hATS VARIABLE VALUE
     * @version 14-Jun-2012
     *
     * @param   string      $var    name of variable to retrieve
     * @param   string      $elem   if $var contains an array, this optional parameter can
     *                              specify an element within the $var array to return
     *                              This saves having to return the array $var, then pulling
     *                              out the element later.
     * @return  string              Value of $var or blank string if not found
     */
    function tplGet( $var = 'clipboard', $elem='' )
    {
        _checkTplVar();

        $CI =& get_instance();

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
            elseif ($var == '__ALL__')
            {
                // return entire array - only of use for debugging
                return $CI->hAtsData;
            }
        }

        // var not found, return nothing
        return '';
    }
}

if (!function_exists('tplGetOr'))
{
    /**
     * GET hATS VARIABLE VALUE OR RETURN SPECIFIED VALUE IF BLANK
     * NOTE: YOU CANNOT GET AN ARRAY VALUE USING tplGetOr
     * @version 18-Dec-2012
     *
     * @param   string      $var    name of variable to retrieve
     * @param   string      $elem   if $var contains an array, this optional parameter can
     *                              specify an element within the $var array to return
     *                              This saves having to return the array $var, then pulling
     *                              out the element later.
     * @return  string              Value of $var or blank string if not found
     */
    function tplGetOr( $var='clipboard', $or='' )
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
 * ==============
 */

if (!function_exists('tplAddPart'))
{
    /**
     * Add a hATS Part to the stack - for output with getParts()
     * A hATS 'part' is a small block of pHTML that can be included within
     * another pHTML part file.
     * @version 07-Oct-2013
     *
     * @param string $part  Name of View part to load
     *                          This should be a filename without .phtml extension.
     *                          The path is by default located in /theme/parts/[parts name]/
     * @param array  $data  Use this to assign specific data to a part that is being loaded multiple times
     *                      $data is an Array of variable name/value pairs to make available to this Part.
     *                          eg. $data=array('name'=>'hArpanet', 'url'=>'harpanet.com');
     *                          When parts are generated (in tplGetParts()) any $data vars are created using tplSet()
     *                          eg. tplSet('name', 'hArpanet');
     */
    function tplAddPart( $part, $data=null )
    {
        //@todo Add a hierarchy 'position' to be specified when adding a part to dicate the order that parts will be shown in tplGetParts()

        _checkTplVar();

        $ci=& get_instance();

        if (is_null($data)) {
            // just a part name
            $ci->hAtsData[TPLVAR]['viewPart'][] = $part;

        } else {
            // if data specified...
            $ci->hAtsData[TPLVAR]['viewPart'][] = array($part, $data);
        }
    }
}

if (!function_exists('tplGetPart'))
{
    /**
     * Get existing hATS Part contents (uses PHP 'require')
     * A hATS 'part' is a small block of pHTML that can be included within
     * another pHTML part file.
     * @version 07-Oct-2013
     *
     * @param   string  $part   name of part to retrieve (i.e. filename without '.phtml' extension)
     *
     * @example Usage in a View file: <?php echo tplGetPart('nameOfPart'); ?>
     */
    function tplGetPart( $part )
    {
        // add phtml extension if not specified
        $part = _addFileExt($part);

        _tplDebug( 'GETTING PART:'.$part );

        $part = tplPartsPath($part);

        if (file_exists($part) OR !empty(tplGet('STOP_ON_ERROR'))) {
            require $part;
        } else {
            echo "<div class='response_fail'>UNABLE TO FIND PART: {$part}</div>";
        }
    }
}

if (!function_exists('tplGetPartAsHtml'))
{
    /**
     * Get contents of a hATS Part as a string value
     * A hATS 'part' is a small block of pHTML that can be included within
     * another pHTML part file.
     * @version 07-Oct-2013
     *
     * @param   string  $part   name of part to retrieve (i.e. filename without 'phtml' extension)
     * @return          $this
     */
    function tplGetPartAsHtml( $part )
    {
        // add .phtml extension if not specified
        $part = _addFileExt($part);

        _tplDebug( 'GETTING PART AS HTML:'.$part );

        return file_get_contents( tplPartsPath($part) );
    }
}


if (!function_exists('tplGetParts'))
{
    /**
     * Get all view parts stacked for output
     * @version 07-Oct-2013
     *
     * @return  $this - No output from this function as parts are 'included' in the tplGetPart function
     */
    function tplGetParts()
    {
        _checkTplVar();

        $ci   =& get_instance();
        $data = '';

        if ( array_key_exists('viewPart', $ci->hAtsData[TPLVAR]) ) {
            $parts = $ci->hAtsData[TPLVAR]['viewPart'];

            if ( is_array($parts) ) {
                foreach( $parts as $part ) {

                    if (is_array($part)) {
                        // variable data has been specified
                        $data = $part[1];
                        $part = $part[0];

                        // create part-specific variables
                        foreach($data as $var=>$value) {
                            tplSet($var, $value);

                            _tplDebug('SET VARIABLE FROM PART: '.$var.'='.htmlspecialchars($value));
                        }
                    }

                    _tplDebug( 'GETPARTS: PART FOUND: '.$part );

                    tplGetPart( $part );
                }

            } else {

                // this should never happen as all parts are added as array entities, but just in case!...

                _tplDebug( 'GETPARTS: ONLY ONE PART FOUND: '.$ci->hAtsData[TPLVAR]['viewPart'][0] );

                tplGetPart( $ci->hAtsData[TPLVAR]['viewPart'][0] );
            }
        }
    }
}


if (!function_exists('tplResponse_message'))
{
    /**
     * Display a response message wrapped in a div
     * @author  hArpanet.com
     * @version 02-May-2013 - added jQuery fadeout
     *          20-Jun-2012 - created
     *
     * @param   string  $name   array element name set using tplSet()
     * @param   string  $css    if $name specified, $css can contains an optional name for the css div class; defaults to $name if not specified
     *                          css class name always begins 'response_' with $css appended to it
     */
    function tplResponse_message( $name='', $css='' )
    {
        _checkTplVar();

        $ci=& get_instance();

        if ( $name != '' && array_key_exists($name, $ci->hAtsData[TPLVAR]) )
        {
            $css        = ( $css == '' ) ? $name : $css;
            $response   = $ci->hAtsData[TPLVAR][$name];

            echo "<div class='response_{$css}'>{$response}</div>";
        }


        if ( array_key_exists('success', $ci->hAtsData[TPLVAR]) )
        {
            $success = $ci->hAtsData[TPLVAR]['success'];

            echo "<div style='cursor:pointer;' class='response_success' onclick=\"javascript:this.style.display='none';\">{$success}</div>";
        }

        if ( array_key_exists('fail', $ci->hAtsData[TPLVAR]) )
        {
            $fail = $ci->hAtsData[TPLVAR]['fail'];

            echo "<div style='cursor:pointer;' class='response_fail' onclick=\"javascript:this.style.display='none';\">{$fail}</div>";
        }

        // add some jQuery to auto close message if jQuery available
        echo "<script type='text/javascript'>
                if(window.jQuery) {
                    setTimeout(function() {
                        jQuery('[class^=\"response_\"]').animate({ height: 0, opacity: 0, margin: 0 }, 'slow', function() {jQuery(this).remove();});
                    }, 8000);
                }
               </script>";
    }
}


/*
 * Some 'private' helpers used by functions above
 */


if (!function_exists('_tplGetThemeFile'))
{
    /**
     * main handler to return a fully qualified path to file within the current theme
     * @version 25-Feb-2014
     *
     * @param   string  $type               'css', 'js', or 'img' (or anything you want to specify, e.g. 'media', etc.)
     * @param   string  $file               filename (eg. 'style.css')
     *
     * @return  string                      path to specified theme file
     */
    function _tplGetThemeFile( $type='', $file='' )
    {
        // Check if searching within Parts subfolder has been disabled
        // no real need to disable this due to directory traversal in _tplFindFile(), but
        // excluding the Parts folder will remove one traversal and hence give a very slight
        // performance increase.
        if (tplGet('IN_PARTS_SUBDIR') === FALSE) {
            // leave out the Parts subfolder from path
            $findfile = _makePath(tplGet('themePath'), tplGet('theme'), $type) . $file;
        } else {
            // include Parts subfolder in path
            $findfile = _makePath(tplGet('themePath'), tplGet('theme'), $type, tplGet('parts')) . $file;
        }

        _tplDebug('GET THEME FILE: '.$findfile);

        return _tplFindFile($findfile);
    }
}


if (!function_exists('_addSlash'))
{
    /**
     * Add slash to end of string
     * @version 30-Sep-2013
     *
     * @param string $string  String to append slash to
     * @param bool   $trim    Should the string be trimmed first
     *
     * @return string         String with ending slash
     */
    function _addSlash( $string, $trim=true ) {
        if ($trim)
            $string = trim($string);

        if ('/' != substr($string, -1))
            $string .= '/';

        return $string;
    }
}

if (!function_exists('_makePath'))
{
    /**
     * Create path from passed parameters
     * @version 25-Feb-2014
     *
     * @param multi $string  Strings of elements to build path from
     *
     * @return string        Concatenated Path with trailing slash
     */
    function _makePath() {
        $params = func_get_args();
        $path = '';

        foreach ($params as $value) {
            $path .= $value;

            if (!empty($value)) $path = _addSlash($path);
        }

        return $path;
    }
}

if (!function_exists('_addFileExt'))
{
    /**
     * Append file extension to filename
     * @version 07-Oct-2013
     *
     * @param string $file Original file path/name
     * @param string $ext  File extension to be added. Defaults to .phtml
     *
     * @return  string Filename with file extension
     */
    function _addFileExt( $file, $ext='phtml', $force = false ) {
        $file = trim($file);

        // add extension if one not present
        if ('' == pathinfo($file, PATHINFO_EXTENSION) OR $force)
        {
            return $file.'.'.$ext;
        }

        // already has a file extension, so just return original name
        return $file;
    }
}


if (!function_exists('_tplFindFile'))
{
    /**
     * Look for the specified $file. If not found, traverse parent folder until found.
     * @version 27-Feb-2014
     *
     * @param   string  $file           filename requested, including path
     *
     * @return  string                  path of found file
     */
    function _tplFindFile($file) {

        // SEARCH for specified file
        // we will only traverse back 50 parent folders maximum

        for ( $lwp=50; $lwp>0; $lwp-- )
        {
            _tplDebug( 'LOOKING IN: '.$file );

            // quit if file found
            if ( file_exists($file) )
                break;

            // store current location
            $current = $file;

            // get parent location
            $file = _makePath(dirname(dirname($file))) . basename($file);

            // quit if we've reached the root
            if ($current == $file)
                break;
        }

        _tplDebug( 'FINDFILE FOUND: '.$file );

        return $file;
    }
}


if (!function_exists('_tplSetDefaults'))
{
    /**
     * Set default names/paths of theme and parts
     * @version 28-Feb-2014
     * @return  void
     *
     * NOTE:
     * The default folders can be overridden in the application
     * controller by using tplSet lines similar to those used below.
     */
    function _tplSetDefaults()
    {
        // default paths if not already set
        if ('' == tplGet('themePath'))
            tplSet('themePath','hAts/theme');

        if ('' == tplGet('partsPath'))
                tplSet('partsPath','hAts/parts');

        if ('' == tplGet('theme'))
                tplSet('theme', 'hAts_default');

        if ('' == tplGet('parts'))
                tplSet('parts', 'hAts_default');

        _tplDebug('hATS DEFAULTS SET!');
    }
}

/* End of file: hats_helper.php */
