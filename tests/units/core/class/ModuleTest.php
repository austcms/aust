<?php
// Uses Textual as Mockup

// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';


class ModuleTest extends PHPUnit_Framework_TestCase
{
	
	function setUp(){
		installModule('textual');
		Fixture::getInstance()->create();
		require_once MODULES_DIR.'textual/Textual.php';

		if( empty($this->structureId) ){
			$query = Connection::getInstance()->query("SELECT id FROM taxonomy WHERE type='textual' AND class='structure' LIMIT 1");
			$this->assertArrayHasKey(0, $query);
			$this->structureId = $query[0]["id"];
		}
	}
	
    function testInitialization(){
        $this->obj = new Textual($this->structureId);
    }

	function testLoadAustDataOnModule(){
        $this->obj = new Textual($this->structureId);

		$this->assertEquals("News", $this->obj->name);
		$this->assertInternalType("array", $this->obj->information);
		$this->assertEquals("News", $this->obj->information["name"]);
		$this->assertEquals($this->structureId, $this->obj->information["id"]);
	}

}
?>