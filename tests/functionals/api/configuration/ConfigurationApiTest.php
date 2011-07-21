<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ConfigurationApiTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
		$this->createContent();
        $this->obj = new ApiTransaction();
    }

	function createContent(){
		Connection::getInstance()->exec('DELETE FROM config');
		$sql = "INSERT INTO config
					(propriedade, valor)
					VALUES
						('site_title', 'Aust makes history'),
						('slogan', 'Aust: water proof')
				";
		Connection::getInstance()->exec($sql);
	}
	
	// returns Array
	function testGetSingleConfiguration(){
		
		$query = array(
			'configuration' => 'site_title',
		);

		$return = $this->obj->getData($query);
		$this->assertEquals(1, count($return));
		$this->assertArrayHasKey('site_title', $return);
		$this->assertEquals('Aust makes history', $return['site_title']);

	}

	// returns Array
	function testGetMultipleConfigurations(){
		
		$query = array(
			'configuration' => 'site_title;slogan',
		);

		$return = $this->obj->getData($query);
		$this->assertEquals(2, count($return));
		$this->assertArrayHasKey('site_title', $return);
		$this->assertArrayHasKey('slogan', $return);
		$this->assertEquals('Aust makes history', $return['site_title']);
		$this->assertEquals('Aust: water proof', $return['slogan']);

	}
	
}
?>