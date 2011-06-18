<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ModActionControllerTest extends PHPUnit_Framework_TestCase
{

	public $params;
	public $structureId;
	
	function setUp(){
		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='conteudo' AND classe='estrutura' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$this->structureId = $query[0]["id"];
		}
		
        $this->params = array(
            'austNode' => $this->structureId
		);
	}
	
	function testViewFile(){
		$_GET["action"] = "test_action";
        $controller = new ModActionController($this->params);

		$this->assertEquals(MODULES_DIR."conteudo/view/mod/test_action.php", $controller->_viewFile());
	}
	
	function testGetAction(){
		$_GET['action'] = 'listing';
		$this->obj = new ModActionController($this->params);
		$this->assertEquals("listing", $this->obj->_action());
	}

    function testInitialization(){
		$_GET['action'] = 'listing';
        $this->obj = new ModActionController($this->params);
    }

	function testRenderization(){
		$_GET['action'] = 'listing';
		$this->obj = new ModActionController($this->params);
		$this->assertEquals("listing", $this->obj->_action());
	}


}
?>