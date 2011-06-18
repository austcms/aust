<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ConteudoControllerTest extends PHPUnit_Framework_TestCase
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

		include_once(MODULES_DIR."conteudo/".MOD_CONTROLLER_DIR."mod_controller.php");
    }

    function testInstallationDiagnostics(){
		$_GET["action"] = "listing";
        $this->obj = new ModController($this->params);
		var_dump($this->obj->render());
    }

}
?>