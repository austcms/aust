<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ActiveModuleTest extends PHPUnit_Framework_TestCase
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
	
	function testSaveAustNode(){
        $module = new ActiveModule($this->params);
		$this->assertEquals( $this->params, $module->austNode);
	}
	
	function testViewFile(){
        $module = new ActiveModule($this->params);
		$this->assertEquals("conteudo/", $module->directory());
	}

}
?>