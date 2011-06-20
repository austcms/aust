<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';


class ModulesManagerTest extends PHPUnit_Framework_TestCase
{

    public function testInitialization(){
        $this->obj = new ModulesManager();
    }


    public function testGetModuleInformation(){
        $this->obj = new ModulesManager();
		
		$modulesInstalled = array(
			"conteudo",
			"cadastro"
		);
		$result = $this->obj->getModuleInformation($modulesInstalled);
		
		$this->assertTrue($result["conteudo"]["version"]);
		$this->assertEquals("Conteudo", $result["conteudo"]["config"]["className"]);
		$this->assertEquals("conteudo", $result["conteudo"]["path"]);
		
    }

}
?>