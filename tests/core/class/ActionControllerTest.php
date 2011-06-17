<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';
require_once CORE_DIR."load_core.php";

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
		$_GET["section"] = "content";
		$_GET["action"] = "test_action";
        $controller = new ActionController();

		$this->assertEquals("test_action", $controller->_action());
		$this->assertEquals("Action working.", $controller->testVar);
		$this->assertFalse($controller->isRendered);

		$this->assertTrue($controller->beforeFiltered);
		$this->assertTrue($controller->afterFiltered);

	}
	
	function testRenderContentIndex(){

		$_GET["section"] = "content";
		$_GET["action"] = "index";
		$_SESSION["login"] = "fake_user";
        $controller = new ActionController();
		
		$this->assertEquals("index", $controller->_action());
		
		// render HTML
		$defaultErrorReporting = ini_get("error_reporting");
		$this->assertRegExp('/<h2>Gerenciar conteúdo<\/h2>/', $controller->render());
		ini_set('error_reporting', $defaultErrorReporting);
		
		
		$this->assertTrue($controller->isRendered);
		$this->assertTrue($controller->beforeFiltered);
		$this->assertTrue($controller->afterFiltered);

	}
}
?>