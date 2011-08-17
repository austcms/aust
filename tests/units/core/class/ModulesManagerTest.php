<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';


class ModulesManagerTest extends PHPUnit_Framework_TestCase
{

	function setUp(){
		Fixture::getInstance()->create();
		Aust::getInstance()->_structureCache = array();
		Aust::getInstance()->_structureModuleCache = array();
	}
	
	function tearDown(){
	}
	
	public function testInitialization(){
		$this->obj = new ModulesManager();
	}


	public function testGetModuleInformation(){
		$this->obj = new ModulesManager();
		
		$modulesInstalled = array(
			"textual",
			"agenda"
		);
		$result = $this->obj->getModuleInformation($modulesInstalled);

		$this->assertEquals("Textual", $result["textual"]["config"]["className"]);
		$this->assertEquals("textual", $result["textual"]["path"]);
		
	}

	function testDirectory(){
		$obj = new ModulesManager();

		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM taxonomy WHERE type='agenda' AND class='structure' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$structureId = $query[0]["id"];
		}

		$this->assertEquals('agenda/', $obj->directory($structureId));
		$this->assertEquals('agenda/', $obj->directory("agenda"));
		$this->assertEquals('conteudo/', $obj->directory("conteudo"));
	}

	function testConfigureModule(){
		
		Connection::getInstance()->exec("DELETE FROM modules_installed");
		
		$modInfo['name'] = 'textuall';
		$modInfo['description'] = 'desc';
		
		$param = array(
			'property' => 'dir',
			'value' => 'textual',
			'directory' => 'textual',
			'modInfo' => $modInfo,
			'admin_id' => '7',
		);
		ModulesManager::getInstance()->configureModule($param);
		
		$result = Connection::getInstance()->query("SELECT * FROM modules_installed");
		$this->assertArrayHasKey("0", $result);
		
		$result = reset($result);
		$this->assertEquals('dir', $result['property']);
		$this->assertEquals('textual', $result['value']);
		$this->assertEquals('modules/textual', $result['directory']);
		$this->assertEquals('textuall', $result['name']);
		$this->assertEquals('desc', $result['description']);
		$this->assertEquals('7', $result['admin_id']);
	}

	function testExists(){
		$obj = new ModulesManager();
		$this->assertTrue($obj->exists('agenda'));
		$this->assertTrue($obj->exists('orders'));
		$this->assertFalse($obj->exists('foo'));
		$this->assertFalse($obj->exists('bar'));
	}

	function testGetModelClassNameByAustNode(){
		$obj = new ModulesManager();

		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM taxonomy WHERE type='agenda' AND class='structure' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$structureId = $query[0]["id"];
		}
		Aust::getInstance()->_structureCache = array();
		$this->assertEquals("Agenda", $obj->modelClassName($structureId));
	}

	function testGetModelClassNameByModuleName(){
		$obj = new ModulesManager();
		$this->assertEquals("Agenda", $obj->modelClassName('agenda'));
		$this->assertEquals("Agenda", $obj->modelClassName('agenda//'));
	}

	function testModelInstanceByAustNode(){
		$obj = new ModulesManager();
		
		$this->assertFalse($obj->modelInstance());

		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM taxonomy WHERE type='textual' AND class='structure' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$structureId = $query[0]["id"];
		}

		$obj = new ModulesManager();
		$this->assertNotNull($obj->modelInstance($structureId));
		$this->assertObjectHasAttribute("mainTable", $obj->modelInstance($structureId));
	}

	function testModelInstanceByModuleName(){
		$obj = new ModulesManager();
		$this->assertNotNull($obj->modelInstance('agenda'));
		$this->assertObjectHasAttribute("mainTable", $obj->modelInstance('agenda'));
	}

}
?>