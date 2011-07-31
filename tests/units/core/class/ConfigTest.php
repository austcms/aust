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
				config
					(propriedade,tipo,valor,nome)
				VALUES 
					('forbidden_configuration','private','value!','Forbidden configuration'),
					('second_test_configuration','general','value??','Second test configuration'),
					('test_configuration','general','value?','Test configuration')";

		Connection::getInstance()->exec($sql);
	}
	
	function neededConfig(){
		return array(
            array(
                'tipo' => 'general',
                'local' => '',
                'nome' => 'Site name',
                'propriedade' => 'site_name',
                'valor' => 'Modify the site name',
                'explanation' => 'some explanation',
            ),
		    array(
		        'tipo' => 'private',
		        'local' => '',
		        'nome' => 'user can haz cookies?',
		        'propriedade' => 'user_haz_cookies',
		        'valor' => '0',
		        'explanation' => 'ha, nope!',
		    )
        );
	}
	
	/* tests */

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
		$this->assertArrayHasKey("valor", $aValue );
		$this->assertContains("value??", $aValue );
	}
	
	function testGetConfigsUsingParams(){
		$this->populate();
        $params = array(
        	'where' => "propriedade='test_configuration'",
            'mode' => 'single',
        );

		$configurations = $this->obj->getConfigs($params);
		
		$this->assertArrayHasKey("general", $configurations);
		$this->assertArrayHasKey("valor", $configurations['general'][0] );
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
				config
					(tipo,nome,propriedade,valor,explanation)
				VALUES 
					('general', 'Site name', 'site_name', 'Modify the site name', 'some explanation'),
					('private', 'user can haz cookies?', 'user_haz_cookies', '0', 'ha, nope!')";

		Connection::getInstance()->exec($sql);
		
		$this->assertTrue($this->obj->checkIntegrity());		
	}
	
	function test_InitConfig(){
		$this->obj->_missingConfig = $this->neededConfig();
		$this->obj->_initConfig();
		$valueOne = Connection::getInstance()->query("SELECT * FROM ".$this->obj->table." WHERE tipo='general' AND propriedade='site_name'");
		$valueTwo = Connection::getInstance()->query("SELECT * FROM ".$this->obj->table." WHERE tipo='private' AND propriedade='user_haz_cookies'");
		$this->assertFalse( empty($valueTwo) );
		$this->assertFalse( empty($valueOne) );
		$this->assertArrayHasKey("tipo", reset($valueOne) );
		$this->assertArrayHasKey("tipo", reset($valueTwo) );
	}
	
	function testUpdateOptions(){
		
	}
	
	function testAdjustOptions(){
		
	}
	
	function testSave(){
		
	}

}
?>