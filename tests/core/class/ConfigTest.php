<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ConfigTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
		User::getInstance()->type('root');
        $this->obj = Config::getInstance();
    }

    function testGetUserType(){
		$this->assertEquals( "root", $this->obj->_userType );
    }

	function testRootType(){
		$this->assertEquals( "Webmaster", $this->obj->_rootType );
	}


}
?>