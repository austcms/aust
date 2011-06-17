<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ApplicationTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
        $this->obj = new Application(false);
    }

    function testInstallationDiagnostics(){
		/*
     	 *     -2: Some tables are missing
     	 *     -1: All tables exist, but some fields are missing
     	 *      0: No table exist
     	 *      1: Everything's ok
		 */

		$return = $this->obj->installationDiagnostics();
        $this->assertType('integer', $return);
        $this->assertEquals(1, $return);
    }

    function testLoadDatabaseConnection(){
#		$return = $this->installationDiagnostics();
#        $this->assertType('integer', $return);
#        $this->assertEqual(1, $return);
    }


}
?>