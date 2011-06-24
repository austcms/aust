<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';


class ModuleTest extends PHPUnit_Framework_TestCase
{

    public function testInitialization(){
		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='conteudo' AND classe='estrutura' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$this->structureId = $query[0]["id"];
		}
		
        $this->obj = new Module($this->structureId);
    }

}
?>