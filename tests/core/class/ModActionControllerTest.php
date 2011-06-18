<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ModActionControllerTest extends PHPUnit_Framework_TestCase
{
    public function testInitialization(){

		$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='conteudo' AND classe='estrutura' LIMIT 1");
		$this->assertArrayHasKey(0, $query);
		$structureId = $query[0]["id"];

        $param = array(
            'austNode' => $structureId,
		);
		
        $this->obj = new ModActionController($param);
    }


}
?>