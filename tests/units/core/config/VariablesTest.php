<?php
require_once 'PHPUnit/Framework.php';
include_once('core/config/variables.php');

class VariablesTest extends PHPUnit_Framework_TestCase
{

    function testVariables(){
        $this->assertEquals( "content", MODULES );
    }

}
?>