<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ActionControllerTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){

    }

	function testActionControllerInitialization(){
        $controller = new ActionController(false);
		$this->assertTrue($controller->completedRequest);
		$this->assertFalse($controller->isRendered);
	}

	function testCallingActionWithoutRendering(){
		$_GET["action"] = "test_action";
        $controller = new ActionController();

		$this->assertEquals("test_action", $controller->_action());
		$this->assertEquals("Action working.", $controller->testVar);
		$this->assertFalse($controller->isRendered);

		$this->assertTrue($controller->beforeFiltered);
		$this->assertTrue($controller->afterFiltered);

	}
}
?>