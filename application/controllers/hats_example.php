<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * THIS IS JUST AN EXAMPLE CONTROLLER TO SHOW HOW
 * TO IMPLEMENT THE hArpanet Template System (hATS)
 */

class Hats_example extends CI_Controller {

    /*
     * REQUIRED: $hAtsData global array is used by hArpanet Template System (hATS)
     *           to pass all necessary variables/values into the Views and Parts
     *
     * NOTE: if you use MY_Controller in your project, move this declaration there.
     */
    public $hAtsData = array();


    public function __construct() {
        parent::__construct();

        // load hATS helper (best add it to autoload.php)
        $this->load->helper('hats');

        /* set template parts folder to name of current class
         * (if not set, 'hAts_default' is assumed
         *  which is good for this example but not for your site!)
         *  You SHOULD add this to your controllers!
         */
        // tplSet('parts', strtolower(get_class($this)));
    }


    public function index()
    {
        /* You can enable debug to see where hATS is looking for its files. */
        // tplSet('debug', true);

        /* hATS uses 'hAts_default' theme by default, so no need to set it here.
         * NOTE: not good practice, you should create your own theme ASAP
         *       and specify it here, even if it's just a copy/rename of 'hAts_default'.
         *       This will prevent your theme files being overwritten by a
         *       future hATS update.
         */
        // tplSet('theme', 'myTheme');

        /* For this example we will use the default parts (hAts_default) directly
         * (ie. not setting parts below or in constructor above)
         * NOTE: not good practice, you should copy/rename the hAts_default parts folder
         *       so that changes to your parts are not overwritten by future hATS update
         */
        // tplSet('parts', 'myParts');

        /* As Parts files are 'Required' they don't need to be in the public web space
         * so you could copy the entire parts folder into your CI Views folder.
         * You would then need to set the parts path to the new location.
         */
        // tplSet('partsPath', 'application/views/parts');

        // set up some page content
        $this->_hAtsExampleData();

        // load hATS default layout (View file)
        $this->load->view('hats_default');
    }



// ========================================================================

    private function _hAtsExampleData()
    {
        /*
         * This example sets ALL the hATS content vars used in the default part files.
         * The tplSet variable names (first parameter) are arbitrary and can be named as
         * required for your own parts.
         * The ones shown here have been used within the default hATS part files to place
         * content at relevant places within them.
         */
        tplSet('metaTitle',    'My hATS Example Page');
        tplSet('mainHeading',  'hATS v2.0.0 Example Page');
        tplSet('logoSrc',      'harpanet_logo.png');

        // note that we are 'add'ing body content not 'set'ting it. Setting a variable
        // overwrites anything previously stored within it.
        tplSet('bodyContent',  '<h2>This is the [bodyContent]</h2>');
        tplAdd('bodyContent',  '<p>Some content for the hATS template example contoller.</p>');
        tplAdd('bodyContent',  '<p>This is more content added after the above paragraph.</p>');

        // we can add other parts here (no file extension required - .phtml assumed)
        // NOTE: testpart.phtml includes a sub-part named subpart.phtml
        tplAddPart('test/testpart');
        // and we can feed it new variable values in the second parameter array
        // (the variable remains in effect for later parts but its value can change)
        tplAddPart('test/testpart', array('moreinfo'=>' with part specific variable value #1'));
        tplAddPart('test/testpart', array('moreinfo'=>' with part specific variable value #2'));

        // an error when a part can't be found
        tplAddPart('MissingPart');

        // we could also add a non hAts file from the root
        // (this works as it is a hierarchical parent of the hAts folder)
        // tplAddPart('license.txt');

        // finally, some closing comments
        tplSet('footerContent',     '<h4 style="text-align:center; font-size:1em;">(this is [footerContent], which appears after the footer)</h4>');
        tplSet('bodyCloseContent',  '<div style="background-color:yellow; padding:5px; margin-top:20px;"><h3 style="text-align:center;">And this [bodyCloseContent] appears just before the &lt/body&gt tag</h3>');
        tplAdd('bodyCloseContent',  '<h3 style="text-align:center;margin-top:10px;">NOTE: In the default hAts parts, Javascripts are output at end of HTML, not in &lthead&gt. <br/>There also exists a [headJS] variable to force JS into the head.</h3></div>');

        /* The recommended way to set and show response messages is to create a flagResponse() method like
         * the one below, but as an example we will just do it inline here...
         */
        // flag some responses (note: the hAts default template already contains a call to tplResponse_message() in body_open.phtml)
        tplAdd('success', ':-) THIS IS A SUCCESS MESSAGE - click me to go away, or I\'ll auto fade if jQuery exists!');
        tplAdd('fail',    ':-( THIS IS A FAIL MESSAGE    - click me to go away, or I\'ll auto fade if jQuery exists!');

    }

// ------------------------------------------------------------------------

    /**
     * flag a response to user
     * hArpanet, 21-Jun-2012
     *
     * use <?php tplResponse_message(); ?> in your View part to display flagged reponses
     * set styling for response messages in your theme css file,
     *     named: response_success or response_fail or response_[something else]
     *
     * @param   bool    $result     TRUE = log 'success' response; FALSE = 'fail' response;
     * @param   string  $msg        Message text to display to user. If not specified, default text
     *                              from config file will be used.
     *
     * @uses    template_helper.php     - must already be loaded by controller
     */
    public function flagResponse($result, $msg='')
    {
        if ( $result ) {
            // if no message supplied, get one from CI Config if present
            $msg = ($msg !='') ? $msg : $this->config->item('SUCCESS_MSG');
            tplAdd('success', $msg);
        } else {
            $msg = ($msg !='') ? $msg : $this->config->item('FAILURE_MSG');
            tplAdd('fail', $msg);
        }
    }

}

/* End of file hats_example.php */
/* Location: ./application/controllers/hats_example.php */
