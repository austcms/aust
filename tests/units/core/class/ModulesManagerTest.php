<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';


class ModulesManagerTest extends PHPUnit_Framework_TestCase
{

	function setUp(){
		Fixture::getInstance()->create();
	}
	
    public function testInitialization(){
        $this->obj = new ModulesManager();
    }


    public function testGetModuleInformation(){
        $this->obj = new ModulesManager();
		
		$modulesInstalled = array(
			"conteudo",
			"agenda"
		);
		$result = $this->obj->getModuleInformation($modulesInstalled);

		$this->assertEquals("Conteudo", $result["conteudo"]["config"]["className"]);
		$this->assertEquals("conteudo", $result["conteudo"]["path"]);
		
    }

	function testModelInstance(){
		$obj = new ModulesManager();
		
		$this->assertFalse($obj->modelInstance());

		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='conteudo' AND classe='estrutura' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$structureId = $query[0]["id"];
		}

		$obj = new ModulesManager();
		$this->assertNotNull($obj->modelInstance($structureId));
		$this->assertObjectHasAttribute("mainTable", $obj->modelInstance($structureId));
#		$this->assert
	}

}
?>