<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * hAts_MY_Controller
 * 29-Oct-2013
 *
 * This is an example CI_Controller extender used to show the instantiation of the
 * $hAtsData variable and some default metadata settings for your project.
 *
 * To use this file, rename it to MY_Controller.php (ie. remove hAts_ from the filename).
 */

class MY_Controller extends CI_Controller {

    /*
     * REQUIRED: $hAtsData global array is used by hArpanet Template System (hATS)
     *           to pass all necessary variables/values into the Views and Parts
     */
    public $hAtsData = array();

    public function __construct()
    {
        parent::__construct();

        /*
         * SET DEFAULT METADATA
         */
        $this->_setMetadata();

        /*
         * set a default template for our application
         * (doing this here saves having to set it within each Controller later)
         *
         * By default hAts will use the 'default' template within the '/theme' folder
         * but in nearly all cases we will have copied the 'default' folder to a new
         * theme (eg. 'hArpanet') so that we keep 'default' as a base for future themes.
         *
         * Obviously you will want to change 'hArpanet' to whatever you've named your theme.
         */

        // tplSetTemplate('hArpanet');

    }

// ========================================================================

    private function _setMetadata()
    {
        /*
         * set hATS template content vars
         * the variable names (first parameter) are arbitrary and can be named as required
         * the ones shown here have been used within the hATS template system to place
         * content at various places in the view parts
         */
        tplSet('metaTitle',         'My hAts Homepage');
        tplSet('mainHeading',       'My hAts Homepage');
        tplSet('logoSrc',           'my_logo.png');
        tplSet('logoAlt',           'My brand logo');
        tplSet('bodyCloseContent',  '');
        tplSet('footerContent',     '');
    }

// ------------------------------------------------------------------------

    /**
     * flag a response to user
     * hArpanet, 21-Jun-2012
     *
     * use <?php tplResponse_message(); ?> in your View part to display flagged reponses
     *           tplResponse_message is defined within template helper
     * set styling for response messages in theme css file, named: response_success or response_fail
     *
     * @param   bool    $result     TRUE = log 'success' response; FALSE = 'fail' response;
     * @param   string  $msg        Message text to display to user. If not specified, default text
     *                              from config file will be used.
     *
     * @uses    template_helper.php     - must already be loaded by controller
     */
    public function flagResponse($result, $msg='')
    {
        if ( $result )
        {
            $msg = ($msg !='') ? $msg : $this->config->item('SUCCESS_MSG');
            tplAdd('success', $msg );
        }else{
            $msg = ($msg !='') ? $msg : $this->config->item('FAILURE_MSG');
            tplAdd('fail', $msg );
        }
    }

// ========================================================================

}

/* end of file: ./application/core/MY_Controller.php */
