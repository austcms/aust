<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ConfigTest extends PHPUnit_Framework_TestCase
{

    function setUp(){
		User::getInstance()->type('root');
        $this->obj = new Config;
    }

	function tearDown(){
		Connection::getInstance()->exec("DELETE FROM ".$this->obj->table."");
	}
	
	function populate(){
		$sql = "INSERT INTO
				configurations
					(property, type, value, name)
				VALUES 
					('forbidden_configuration','private','value!','Forbidden configuration'),
					('second_test_configuration','general','value??','Second test configuration'),
					('test_configuration','general','value?','Test configuration')";

		Connection::getInstance()->exec($sql);
	}
	
	function neededConfig(){
		return array(
            array(
                'type' => 'general',
                'local' => '',
                'name' => 'Site name',
                'property' => 'site_name',
                'value' => 'Modify the site name',
                'explanation' => 'some explanation',
            ),
		    array(
		        'type' => 'private',
		        'local' => '',
		        'name' => 'user can haz cookies?',
		        'property' => 'user_haz_cookies',
		        'value' => '0',
		        'explanation' => 'ha, nope!',
		    )
        );
	}
	
	/* tests */

	function testTableExists(){
		$this->assertTrue( Connection::getInstance()->hasTable("configurations") );
	}
	
    function testGetUserType(){
		User::getInstance()->type('root');
        $this->obj = new Config;
		$this->assertEquals( "root", $this->obj->_userType );
    }

	function testRootType(){
		$this->assertEquals( "Webmaster", $this->obj->_rootType );
	}
	
	function testInexistentConfiguration(){
		Connection::getInstance()->exec("DELETE FROM ".$this->obj->table."");
		$this->assertFalse($this->obj->getConfig('site_name'));
	}
	
	function testGetConfigWithSingleProperty(){
		$this->populate();

		$configuration = $this->obj->getConfig("test_configuration");
		$this->assertEquals("value?", $configuration );
	}
	
	function testGetConfigs(){
		$this->populate();

		$configurations = $this->obj->getConfigs();
		$this->assertArrayHasKey("general", $configurations);
		$aValue = reset($configurations['general']);
		$this->assertArrayHasKey("value", $aValue );
		$this->assertContains("value??", $aValue );
	}
	
	function testGetConfigsUsingParams(){
		$this->populate();
        $params = array(
        	'where' => "property='test_configuration'",
            'mode' => 'single',
        );

		$configurations = $this->obj->getConfigs($params);
		
		$this->assertArrayHasKey("general", $configurations);
		$this->assertArrayHasKey("value", $configurations['general'][0] );
		$this->assertContains("value?", $configurations['general'][0] );
		$this->assertArrayNotHasKey("1", $configurations['general'] );
	}

	function testGetConfigsWithSpecificProperties(){
		$this->populate();
        $params = array(
        	'test_configuration', 'second_test_configuration', 'forbidden_configuration', 
        );

		$configurations = $this->obj->getConfigs($params, true);
		
		$this->assertArrayHasKey("test_configuration", $configurations);
		$this->assertArrayHasKey("second_test_configuration", $configurations );
		$this->assertArrayHasKey("forbidden_configuration", $configurations );
		$this->assertEquals("value?", $configurations["test_configuration"] );
		$this->assertEquals("value??", $configurations["second_test_configuration"] );
		$this->assertEquals("value!", $configurations["forbidden_configuration"] );
	}
	
	function testHasPermission(){
		$this->assertTrue($this->obj->hasPermission("general"));
		$this->assertTrue($this->obj->hasPermission("Geral"));
		$this->assertFalse($this->obj->hasPermission("private"));
		$this->assertTrue($this->obj->hasPermission("private", $this->obj->_rootType));
	}
	
	function testCheckIntegrity(){
        $this->obj->neededConfig = $this->neededConfig();
		
		$this->assertFalse($this->obj->checkIntegrity());
		$this->assertFalse( empty($this->obj->_missingConfig) );

		$sql = "INSERT INTO
				".$this->obj->table."
					(type, name, property, value, explanation)
				VALUES 
					('general', 'Site name', 'site_name', 'Modify the site name', 'some explanation'),
					('private', 'user can haz cookies?', 'user_haz_cookies', '0', 'ha, nope!')";

		Connection::getInstance()->exec($sql);
		
		$this->assertTrue($this->obj->checkIntegrity());		
	}
	
	function test_InitConfig(){
		$this->obj->_missingConfig = $this->neededConfig();
		$this->obj->_initConfig();
		$valueOne = Connection::getInstance()->query("SELECT * FROM ".$this->obj->table." WHERE type='general' AND property='site_name'");
		$valueTwo = Connection::getInstance()->query("SELECT * FROM ".$this->obj->table." WHERE type='private' AND property='user_haz_cookies'");
		$this->assertFalse( empty($valueTwo) );
		$this->assertFalse( empty($valueOne) );
		$this->assertArrayHasKey("type", reset($valueOne) );
		$this->assertArrayHasKey("type", reset($valueTwo) );
	}
	
	function testUpdateOptions(){
		
	}
	
	function testAdjustOptions(){
		
	}
	
	function testSave(){
		
	}

}
?>