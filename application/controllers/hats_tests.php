<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * hAts FUNCTION TESTS
 *
 * Run tests against hAts_helper functions for comparison against
 * expected results.
 *
 * For hAts v2.1.0+
 *
 * @version 140428
 */

class Hats_tests extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // load hATS helper (best add it to autoload.php)
        $this->load->helper('hats');
    }


    public function index()
    {
        echo "<h1>hAts Testing Controller</h1>";

        echo "<h2>Call hats_tests/&lt;test_type&gt; where &lt;test_type&gt; is one of:</h2>";

        echo "<h3>vars  - <A HREF='hats_tests/vars'>show variable tests</a></h3>";
        echo "<h3>js    - <A HREF='hats_tests/js'>show javascript tests</a></h3>";
        echo "<h3>css   - <A HREF='hats_tests/css'>show stylesheet tests</a></h3>";
        echo "<h3>img   - <A HREF='hats_tests/img'>show image tests</a></h3>";
        echo "<h3>parts - <A HREF='hats_tests/parts'>show parts tests</a></h3>";
    }


    /**
     * RUN TESTS AGAINST hATS HELPER
     */

    /**
     * VARIABLE TESTS
     */

    public function vars() {

        $c = $this->config;

        echo "<h1>hAts_helper VARIABLE TESTS</h1>";
        echo "<hr/>";


        tplSet('metaTitle', "Page Title Variable");

            test(   "tplSet('metaTitle', 'Page Title Variable');",
                    $c->item('hAtsData'),
                    array('metaTitle'=>'Page Title Variable'),
                    "NOTE: Set a variable named 'metaTitle'"
                    );


            test(   "tplGet('__ALL__') = hAtsData Array",
                    tplGet('__ALL__'),
                    array('metaTitle'=>'Page Title Variable'),
                    'NOTE: retrieve entire hAtsData array.'
                    );


            test(   "tplGet('metaTitle')",
                    tplGet('metaTitle'),
                    'Page Title Variable',
                    'NOTE: retrieve content of hAtsData variable.'
                    );


        tplSet('arrayTest', 'the value', 'the_element');

            test(   "tplSet('arrayTest', 'the value', 'the_element');",
                    $c->item('arrayTest', 'hAtsData'),
                    Array('the_element' => 'the value')
                    );

            test(   "tplGet('arrayTest', 'the_element')",
                    tplGet('arrayTest', 'the_element'),
                    'the value'
                    );


        tplSet(array('one'=>'111', 'two'=>'222', 'three'=>333, 'four'=>array('zebra','lion')));

            test(   "tplSet(array('one'=>'111', 'two'=>'222', 'three'=>333, 'four'=>array('zebra','lion')));",
                    array(
                        'one'=>$c->item('one', 'hAtsData'),
                        'two'=>$c->item('two', 'hAtsData'),
                        'three'=>$c->item('three', 'hAtsData'),
                        'four'=>$c->item('four', 'hAtsData')
                    ),
                    array('one'=>'111', 'two'=>'222', 'three'=>333, 'four'=>array('zebra','lion'))
                    );

            test(   "tplGet('four')",
                    tplGet('four'),
                    Array('zebra', 'lion')
                    );

            test(   'tplGet( var, elem )',
                    tplGet('four', 1),
                    'lion'
                    );


        tplAdd('nonExist', 'just a string');

            test(   "tplAdd('nonExist', 'just a string');",
                    tplGet('nonExist'),
                    'just a string'
                    );

        tplAdd('nonExist', '...an appended string');

            test(   "tplAdd('nonExist', '...an appended string');",
                    tplGet('nonExist'),
                    'just a string...an appended string',
                    'Appending a string to an existing variable.'
                    );

        tplAdd('arrayTest', 'an added array elem');

            test(   "tplAdd('arrayTest', 'an added array elem');",
                    tplGet('arrayTest'),
                    Array('the_element' => 'the value', 'an added array elem'),
                    'Adding to an existing array. Note that the array key cannot be specified.'
                    );


        /* getOr() tests */

            test(   "tplGetOr('foo', 'bar')",
                    tplGetOr('foo', 'bar'),
                    'bar',
                    "Variable 'foo' doesn't exist, so 'bar' is returned."
                    );

            test(   "tplGetOr('metaTitle', 'bar')",
                    tplGetOr('metaTitle', 'bar'),
                    'Page Title Variable',
                    "'metaTitle' exists, and its contents are returned."
                    );


        /* END OF TESTS */

        echo "<hr/>END OF VARIABLE TESTS<hr/>";
    }


    /**
     * JAVASCRIPT TESTS
     */

    public function js() {

        $c = $this->config;

        echo "<h1>hAts_helper JAVASCRIPT TESTS</h1>";
        echo "<hr/>";


        tplAddJavascript('/js/testfile');

            test(   "tplAddJavascript('/js/testfile');",
                    $c->item('js', 'hAtsData'),
                    array(array('js'=>'/js/testfile', 'intheme'=>true))
                    );

        tplAddJavascript('/js/anotherfile.js');

            test(   "tplAddJavascript('/js/anotherfile.js'); + previous",
                    $c->item('js', 'hAtsData'),
                    array(array('js'=>'/js/testfile', 'intheme'=>true), array('js'=>'/js/anotherfile.js', 'intheme'=>true))
                    );

        /* retireve stacked javascripts */

            test(   'tplJavascripts()',
                    tplJavascripts(),
                    "<script type='text/javascript' src='./testfile.js'></script>\n<script type='text/javascript' src='./anotherfile.js'></script>\n",
                    "NOTE: /js is stripped from src path when traversal failed to find the requested file!"
                    );

        /* unset js array element */

            $this->unsetvar('js');

        /* stack a non-existent file */

        tplAddJavascript('/js/file-not-in-theme', false);

            test(   "tplAddJavascript('/js/file-not-in-theme', false);",
                    $c->item('js', 'hAtsData'),
                    array(array('js'=>'/js/file-not-in-theme', 'intheme'=>false)),
                    "NOTE: intheme is blank (false)"
                    );

        /* retireve stacked javascripts */

            test(   'tplJavascripts()',
                    tplJavascripts(),
                    "<script type='text/javascript' src='/js/file-not-in-theme.js'></script>\n",
                    "NOTE: /js is NOT stripped from src path because 'intheme' is false and no traversal happens!"
                    );


        /* unset js array element */

            $this->unsetvar('js');


        /**
         * SET A TEMPLATE VARIABLE THEN INSTANTIATE A PARSED JAVASCRIPT FILE
         * THAT DISPLAYS THE CONTENTS OF THE VARIABLE
         */
            /* add the javascript file to be parsed */
            tplAddJavascript('#test_javascript');

            /* display hAtsData to confirm that javascript was added to the stack */
            test(   "tplAddJavascript('#test_javascript');",
                    $c->item('js', 'hAtsData'),
                    array(array('js'=>'#test_javascript', 'intheme'=>true)),
                    "NOTE: filename begins with # so it will be parsed for PHP content when it is output (in tests below)"
                        );
            /* create a dummy test with an 'id' to be filled by the parsed javascript file */
            test(   'Result shown here-&gt; [<span id=\'test_result\'></span>]',
                    '',
                    '',
                    "NOTE: This test is a placeholder for the test below. \nThe title 'parsed javascript content' text is generated from a PHP template variable within a javascript file which is then parsed by the next test."
                    );
            /* set the template variable to be used in the parsed javascript file */
            tplSet('test_message','parsed javascript content');
            /* retrieve stacked javascripts */
            test(   "tplJavascripts() calling a parsed javascript file",
                    tplJavascripts(),
                    "",
                    "NOTE: This test results in the placeholder test above having its title modified to contain the text 'parsed javascript content'"
                    );


        /* unset js array element */

            $this->unsetvar('js');

        /* get html code for a non-existent file */

            test(   "tplJavascript('/some/path/to/a/test-javascript', false);",
                    tplJavascript('/some/path/to/a/test-javascript', false),
                    "<script type='text/javascript' src='/some/path/to/a/test-javascript.js'></script>\n",
                    "NOTE: Now calling tplJavascript() directly which shouldn't add an entry onto the stack but return it as a string.\n'/some/path/to/a' has been NOT stripped as file is not in theme (false specified)."
                    );

        /* get html code for a non-existent file */

            test(   "tplJavascript('/some/path/to/a/test-javascript');",
                    tplJavascript('/some/path/to/a/test-javascript'),
                    "<script type='text/javascript' src='./test-javascript.js'></script>\n",
                    "NOTE: Now calling tplJavascript() directly which shouldn't add an entry onto the stack but return it as a string.\n'/some/path/to/a' has been stripped as hAts searches unsuccessfully for the file."
                    );

        /* retrieve stacked javascripts */

            test(   'tplJavascript()',
                    tplJavascripts(),
                    '',
                    "NOTE: Stack should be empty as the above tplJavascript calls don't add items to stack."
                    );


        /* END OF TESTS */

            echo "<hr/>END OF JAVASCRIPT TESTS<hr/>";
        }


    /**
     * CSS TESTS
     */

    public function css() {

        $c = $this->config;

        echo "<h1>hAts_helper CSS STYLESHEET TESTS</h1>";
        echo "<hr/>";


        tplAddStylesheet('/includes/css/some-pretty-styling');

            test(   "tplAddStylesheet('/includes/css/some-pretty-styling');",
                    $c->item('css', 'hAtsData'),
                    array(array('css'=>'/includes/css/some-pretty-styling', 'intheme'=>true))
                    );


        tplAddStylesheet('/includes/css/more-styles');

            test(   "tplAddStylesheet('/includes/css/more-styles'); + previous",
                    $c->item('css', 'hAtsData'),
                    array(array('css'=>'/includes/css/some-pretty-styling', 'intheme'=>true), array('css'=>'/includes/css/more-styles', 'intheme'=>true))
                    );

        /* retrieve stacked javascripts */

            test(   'tplStylesheets()',
                    tplStylesheets(),
                    "<link rel='stylesheet' href='./some-pretty-styling.css' type='text/css'>\n<link rel='stylesheet' href='./more-styles.css' type='text/css'>\n",
                    "NOTE: showing stacked stylesheets."
                    );

        /* unset css array element */

            $this->unsetvar('css');


        /* get html code for single stylesheet file in theme */

            test(   "tplStylesheet('/some/path/to/a/test-stylesheet');",
                    tplStylesheet('/some/path/to/a/test-stylesheet'),
                    "<link rel='stylesheet' href='./test-stylesheet.css' type='text/css'>\n",
                    "NOTE: Now calling tplStylesheet() directly which shouldn't add an entry onto the stack but return it as a string.\n'/some/path/to/a' has been stripped as hAts searches unsuccessfully for the file."
                    );


        /* get html code for single stylesheet file NOT in theme */

            test(   "tplStylesheet('/some/path/to/a/test-stylesheet', false);",
                    tplStylesheet('/some/path/to/a/test-stylesheet', false),
                    "<link rel='stylesheet' href='/some/path/to/a/test-stylesheet.css' type='text/css'>\n",
                    "NOTE: Now calling tplStylesheet() directly which shouldn't add an entry onto the stack but return it as a string.\n'/some/path/to/a' has NOT been stripped as file is not in theme (false specified)."
                    );

        /* retrieve stacked javascripts */

            test(   'tplStylesheets()',
                    tplStylesheets(),
                    '',
                    "NOTE: css element has been cleared prior to the above test.\n Stack should be empty as the above tplJavascript doesn't add item to stack."
                    );

        /* END OF TESTS */

            echo "<hr/>END OF STYLESHEET TESTS<hr/>";
    }


    /**
     * IMAGE TESTS
     */

    public function img() {

        $c = $this->config;

        echo "<h1>hAts_helper IMAGE TESTS</h1>";
        echo "<hr/>";


            test(   "tplImage('sub/path/to/a-test-image');",
                    tplImage('sub/path/to/a-test-image'),
                    './a-test-image',
                    'NOTE: sub/path/to has been removed as hAts searches for the specified file.'
                    );

            test(   "tplImage('sub/path/to/a-test-image', false);",
                    tplImage('sub/path/to/a-test-image', false),
                    'sub/path/to/a-test-image',
                    'NOTE: sub/path/to has NOT been removed as the file is not within theme (false specified).'
                    );


        /* END OF TESTS */

            echo "<hr/>END OF IMAGE TESTS<hr/>";
    }



    /**
     * PARTS TESTS
     */

    public function parts() {

        $c = $this->config;

        echo "<h1>hAts_helper PARTS TESTS</h1>";
        echo "<hr/>";


        tplAddPart('head');

            test(   "tplAddPart('head');",
                    tplGet('viewPart'),
                    array('head'),
                    "NOTE: part has been stacked ready for output without any 'data'."
                    );


        tplAddPart('body', array('name'=>'hArpanet', 'url'=>'harpanet.com'));

            test(   "tplAddPart('body', array('name'=>'hArpanet', 'url'=>'harpanet.com'));",
                    tplGet('viewPart'),
                    array('head', array('body', array('name'=>'hArpanet', 'url'=>'harpanet.com'))),
                    "NOTE: part has been added to stack ready for output with part-specific variables."
                    );




        /* END OF TESTS */

            echo "<hr/>END OF PARTS TESTS<hr/>";
    }



    /**
     * Unset a hAtsData element
     *
     * @param  string $elem1 Name of element to unset
     * @param  string $elem2 (optional) Name of sub element to unset
     * @return void
     */
    private function unsetvar($elem1, $elem2='') {
        $c = $this->config;

        $hAtsData = $c->item('hAtsData');
        if ($elem2==='') {
            unset($hAtsData[$elem1]);
        } else {
            unset($hAtsData[$elem1][$elem2]);
        }
        $c->set_item('hAtsData', $hAtsData);
    }

}



/**
 * VERY NAUGHTY putting this function outside the class
 * (but it works, and allows it to be treated like a helper)
 *
 * @param  string $msg     Title message for test block
 * @param  mixed $test     Test to be performed
 * @param  mixed $result   Expected result
 * @param  string $comment Comment to display below test block
 * @return void
 */
function test($msg, $test, $result, $comment='') {
    $act = '';
    $res = '';

    // if (is_array($test)) {
    //     foreach($test as $t) {

    //         if (is_array($t)) $t = print_r($t, true);

    //         $act .= $t;
    //     }
    // } else {
    //         $act .= $test;
    // }

    // if (is_array($result)) {
    //     foreach($result as $r) {

    //         if (is_array($r)) $r = print_r($r, true);

    //         $res .= $r;
    //     }
    // } else {
    //         $res .= $result;
    // }
    $act = htmlentities(print_r($test, true), ENT_QUOTES);
    $res = htmlentities(print_r($result, true), ENT_QUOTES);
    // $act='test';
    // $res='test';

    $colr = ($act === $res) ? 'green' : 'red';
    echo "<table border=1 cellpadding=5 width=95%>";
    echo "<tr><td colspan=2 style='background-color:{$colr};'><h2>{$msg}</h2></td></tr>";
    echo "<tr><td width=180px><b>TEST RESULT</b></td><td><pre style='font-size:10px;'>$act</pre></td></tr>";
    echo "<tr><td width=180px><b>EXPECTED RESULT</b></td><td><pre style='font-size:10px;'>$res</pre></td></tr>";
    if ($comment) {
        echo "<tr><td colspan=2 style='background-color:#eeeeee'><pre>{$comment}</pre></td></tr>";
    }
    echo "</table><br/></br>";
}


/* End of file hats_test.php */
/* Location: ./application/controllers/hats_tests.php */
