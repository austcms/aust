<?php
// require_once 'PHPUnit/Framework.php';
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
	
	function testViewFile(){
		$_GET["section"] = "content";
		$_GET["action"] = "test_action";
        $controller = new ActionController();

		$this->assertEquals("core/app/views/content/", $controller->_viewFile());
	}

	function testCallingActionWithoutRendering(){
		$_GET["section"] = "content";
		$_GET["action"] = "test_action";
        $controller = new ActionController();

		$this->assertEquals("test_action", $controller->_action());
		
		// setting params
		$this->assertEquals("Action test_action from controller content working.", $controller->testVar);
		$this->assertFalse($controller->isRendered);

		$this->assertTrue($controller->beforeFiltered);
		$this->assertTrue($controller->afterFiltered);

	}
	
	function testRenderizationAndSettingParamsVariable(){
		$_GET["section"] = "content";
		$_GET["action"] = "test";
        $controller = new ActionController();

		$this->assertRegExp('/View test from content controller./', $controller->render());
		$this->assertTrue($controller->isRendered);
	}
	
	function test_controllerPathName(){
		$_GET["section"] = "content";
		$_GET["action"] = "test";
        $controller = new ActionController();
		
		$this->assertEquals("action", $controller->_controllerPathName());
	}
	
}
?>