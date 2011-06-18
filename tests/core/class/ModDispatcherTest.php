<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ModDispatcherTest extends PHPUnit_Framework_TestCase
{

	public $params;
	public $structureId;
	
	function setUp(){
		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='conteudo' AND classe='estrutura' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$this->structureId = $query[0]["id"];
		}
		
        $this->params = $this->structureId;
	}
	
	function testController(){
        $dispatcher = new ModDispatcher($this->params);
		$this->assertEquals("mod", $dispatcher->controller());
	}
	
	function testDirectory(){
        $obj = new ModDispatcher($this->params);
		$this->assertEquals("conteudo/", $obj->directory());
	}
	
	function testGetAction(){
		$_GET['action'] = 'listing';
		$this->obj = new ModDispatcher($this->params);
		$this->assertEquals("listing", $this->obj->action());
	}
	
	function testControllerFile(){
        $obj = new ModDispatcher($this->params);
		$this->assertEquals(MODULES_DIR."conteudo/".MOD_CONTROLLER_DIR."mod_controller.php", $obj->controllerFile());
	}
	
	function testGetModuleModelClass(){
        $obj = new ModDispatcher($this->params);
		$this->assertEquals("Conteudo", $obj->modelClassName());
	}

}
?>