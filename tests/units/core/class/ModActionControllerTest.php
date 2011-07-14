<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ModActionControllerTest extends PHPUnit_Framework_TestCase
{

	public $params;
	public $structureId;
	
	function setUp(){
		Fixture::getInstance()->create();

		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='conteudo' AND classe='estrutura' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$this->structureId = $query[0]["id"];
		}
		
		if( !defined('DO_ACT') )
			define('DO_ACT', false);
		
        $this->params = $this->structureId;
	}
	function tearDown(){
		Fixture::getInstance()->destroy();
	}
	
	function testAustNode(){
		$controller = new ModActionController($this->params);
		$this->assertEquals($this->params, $controller->austNode());
	}
	
	function testViewFile(){
		$_GET["action"] = "listing";
#        $controller = new ModActionController($this->params);

#		$this->assertEquals(MODULES_DIR."conteudo/view/mod/listing.php", $controller->_viewFile());
	}
	
	function testGetAction(){
		$_GET['action'] = 'listing';
		$this->obj = new ModActionController(false);
		$this->assertEquals("listing", $this->obj->_action());
	}

	function testGetController(){
		$_GET['section'] = 'content';
		$this->obj = new ModActionController(false);
		$this->assertEquals("content", $this->obj->_coreController());
	}

}
?>