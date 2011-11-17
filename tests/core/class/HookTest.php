<?php
require_once 'tests/config/auto_include.php';

class HookTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
        $this->obj = new Hook;
    }

    function testLoadHookEngineDirectories(){
		$this->assertArrayHasKey( 'email_sender', $this->obj->loadHookEngines() );
	}
	
	function testGetStructureName(){
		Connection::getInstance()->exec("INSERT INTO categorias (nome) VALUES ('Test777')");
		$stId = Connection::getInstance()->lastInsertId();
		$this->assertEquals( 'Test777', $this->obj->getStructureName($stId) );
	}
	
	function testWhenAndSelf(){
		$this->assertFalse( $this->obj->perform() );
		$this->assertFalse( $this->obj->perform(array()) );
		$this->assertFalse( $this->obj->perform(array('when' => 'approve_record', 'self' => 'hey')) );
	}
}
?>