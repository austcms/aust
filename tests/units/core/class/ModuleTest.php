<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';


class ModuleTest extends PHPUnit_Framework_TestCase
{
	
	function setUp(){
		installModule('textual');
		Fixture::getInstance()->create();
	}
	
    function testInitialization(){
		require_once MODULES_DIR.'textual/Textual.php';
		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='textual' AND classe='estrutura' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$this->structureId = $query[0]["id"];
		}
		
        $this->obj = new Textual($this->structureId);
    }

}
?>