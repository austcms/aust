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
}
?>