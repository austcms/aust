<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class DispatcherTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
        $this->obj = new Dispatcher();
    }

    function testController(){
		$_GET["section"] = "main";
		$this->assertEquals( "main", $this->obj->controller());

		$_GET["section"] = "";
		$this->assertEquals( "content", $this->obj->controller());
    }

    function testAction(){
		$_GET["action"] = "my_action";
		$this->assertEquals( "my_action", $this->obj->action());

		$_GET["action"] = "";
		$this->assertEquals( "index", $this->obj->action());
    }

	function testControllerFile(){
		$_GET["action"] = "content";
		$this->assertEquals( "core/app/controllers/content_controller.php", $this->obj->controllerFile());
	}

	function testSectionFile(){
		$_GET["action"] = "content";
		$this->assertEquals( "core/inc/content.inc.php", $this->obj->sectionFile());
	}

}
?>