<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class TextualModControllerTest extends PHPUnit_Framework_TestCase
{
	public $params;
	public $structureId;
	
	function setUp(){
		if( empty($this->structureId) ){
			installModule('textual');
			Fixture::getInstance()->create();
			$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='textual' AND classe='estrutura' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$this->structureId = $query[0]["id"];
		}
		
        $this->params = $this->structureId;

		include_once(MODULES_DIR."textual/".MOD_CONTROLLER_DIR."mod_controller.php");
		
		$this->addTexts();
    }

	function tearDown(){
		Connection::getInstance()->exec("DELETE FROM textual");
	}

	function addTexts(){
		Connection::getInstance()->exec("INSERT INTO textual (title,node_id) VALUES ('My first text', '".$this->params."')");
		Connection::getInstance()->exec("INSERT INTO textual (title,node_id) VALUES ('My second text', '".$this->params."')");
	}

    function testListing(){
		$_GET["action"] = "listing";
		$_GET["aust_node"] = $this->params;
		
		
        $this->obj = new ModController($this->params);
		$rendered = $this->obj->render();

		$this->assertRegExp('/My first text/', $rendered);
    }

    function testCreateAsNormalUser(){
		$_GET["action"] = "create";
		$_GET["aust_node"] = $this->params;

        $this->obj = new ModController($this->params);
		$rendered = $this->obj->renderized;
		
		$this->assertRegExp('/Criar:/', $rendered);
		$this->assertNotRegExp('/<div class="nova_categoria">/', $rendered);
    }

    function testCreateAsRootUser(){
		$_SESSION["login"]["id"] = getAdminId();
		$_GET["action"] = "create";
		$_GET["aust_node"] = $this->params;

        $this->obj = new ModController($this->params);
		$rendered = $this->obj->renderized;
		
		$this->assertRegExp('/Criar:/', $rendered);
    }

}
?>