<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ContentControllerTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
		require_once(CONTROLLERS_DIR."content_controller.php");
        $this->obj = new ContentController;
    }

	public function testIndex(){
		$this->assertRegExp('/<h2>Gerenciar conteúdo<\/h2>/', $this->obj->render());
	}

	public function testConfigurationsWithModule(){
		
		$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='conteudo' AND classe='estrutura' LIMIT 1");
		$this->assertArrayHasKey(0, $query);
		$structureId = $query[0]["id"];
		
		$_GET['aust_node'] = $structureId;
		$_GET['action'] = "listing";
#        $this->obj = new ContentController;
#		$this->assertEquals('load_structure', $this->obj->_action());
		
#		$this->assertRegExp('/List/', $this->obj->render());
		
	}

}
?>