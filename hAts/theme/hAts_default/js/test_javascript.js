// this javascript is part of hArpanet Template System
// and is used in the hats_tests() controller to test the parsing
// of php within a javascript file.

document.getElementById('test_result').innerHTML = '<?php echo tplGet("test_message"); ?>';
