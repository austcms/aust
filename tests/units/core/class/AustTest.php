<?php
require_once 'tests/config/auto_include.php';

class AustTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $conexao;

    function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        
        $this->connection = Connection::getInstance();
        $this->obj = new Aust($this->connection);
    }

	function tearDown(){
		$this->obj->connection->query("DELETE FROM config");
		$this->obj->connection->query("DELETE FROM taxonomy");
	}

	function testCreateStructure(){
		$this->obj->connection->query("INSERT INTO taxonomy (name,father_id,class) VALUES ('My Site','0','categoria-chefe')");
		$siteId = $this->obj->connection->lastInsertId();
		
		$params = array(
            'name' => "News",
            'site' => $siteId,
            'module' => 'textual',
            'author' => '1'
        );
        
		$result = $this->obj->createStructure($params);
		$query = Connection::getInstance()->query("SELECT * FROM taxonomy WHERE id='".$result."'");
		$this->assertInternalType('array', $query);
		$this->assertArrayHasKey('0', $query);
		$query = $query[0];
		$this->assertEquals('News', $query['name']);
		$this->assertEquals('news', $query['name_encoded']);
		$this->assertEquals('textual', $query['type']);
		$this->assertEquals($siteId, $query['father_id']);
		$this->assertEquals('1', $query['admin_id']);

		$this->assertEquals($siteId, $query['site_id']);
		$this->assertEquals('My Site', $query['site_name']);
		$this->assertEquals('my_site', $query['site_name_encoded']);
		
	}
	
    function testGetStructures(){
        $this->assertInternalType('array', $this->obj->getStructures() );
    }

	function testHasSite(){
		$this->obj->connection->query("DELETE FROM taxonomy");
        $this->assertFalse($this->obj->anySiteExists() );
		$this->obj->connection->query("INSERT INTO taxonomy (name,father_id,class) VALUES ('TestFather777','0','categoria-chefe')");
        $this->assertTrue($this->obj->anySiteExists() );
	}

	function testCreateFirstSiteAutomatically(){
		$this->obj->connection->query("DELETE FROM taxonomy");
        $this->assertTrue($this->obj->createFirstSiteAutomatically() );
        $this->assertFalse($this->obj->createFirstSiteAutomatically() );
	}
	
	function testGetStructure(){
		Fixture::getInstance()->create();
		$query = Connection::getInstance()->query("SELECT id FROM taxonomy WHERE type='textual' AND class='estrutura' LIMIT 1");
		$structureId = $query[0]["id"];
		
		$structure = $this->obj->getStructureById($structureId);
		$this->assertInternalType("array", $structure);
		$this->assertEquals($structureId, $structure["id"]);
	}

    function testGetStructuresBySite(){
        $this->assertInternalType('array', $this->obj->getStructuresBySite('1') );
        $this->assertInternalType('array', $this->obj->getStructuresBySite('999') );
        $this->assertFalse($this->obj->getStructuresBySite() );
    }

    function testGetStructureByCategoryId(){

		$this->obj->connection->exec("INSERT INTO taxonomy (name,class) VALUES ('My site','categoria-chefe')");
		$siteLastInsert = $this->obj->connection->lastInsertId();

		$sql = "INSERT INTO taxonomy (name, type, class, site_id, site_name, site_name_encoded) VALUES ('First node', 'textual', 'estrutura', '".$siteLastInsert."', 'My site', 'my_site')";
		$this->obj->connection->exec($sql);
		$structurelastInsert = $this->obj->connection->lastInsertId();

		$result = $this->obj->getStructureByCategoryId($structurelastInsert);
		$this->assertEquals($structurelastInsert, $result['id']);
		$this->assertEquals('First node', $result['name']);

		$this->obj->connection->exec("INSERT INTO taxonomy (name, type, class, father_id) VALUES ('Second node', 'textual', 'categoria', '$structurelastInsert')");
		$lastInsert = $this->obj->connection->lastInsertId();

		$this->obj->connection->exec("INSERT INTO taxonomy (name, type, class, father_id) VALUES ('Third node', 'textual', 'categoria', '$lastInsert')");
		$lastInsert = $this->obj->connection->lastInsertId();

		$result = $this->obj->getStructureByCategoryId($lastInsert);
		$this->assertEquals($structurelastInsert, $result['id']);
		$this->assertEquals('First node', $result['name']);

		$this->assertFalse($this->obj->getStructureByCategoryId(''));

    }

	function testGetInvisible(){
		$this->obj->connection->query(
			"INSERT INTO config
				(tipo,local,propriedade,valor, explanation)
			VALUES 
				('mod_conf', '1', 'related_and_visible', '0', 'test'),
				('mod_conf', '2', 'related_and_visible', '0', 'test'),
				('mod_conf', '3', 'related_and_visible', '0', 'test')"
		);
		
		$invisibleStructures = $this->obj->getInvisibleStructures();
		$this->assertTrue( in_array("1", $invisibleStructures) );
		$this->assertTrue( in_array("2", $invisibleStructures) );
		$this->assertTrue( in_array("3", $invisibleStructures) );
	}

	function testGetStructuresAndHideInvisible(){
		$this->obj->connection->query("DELETE FROM taxonomy WHERE name='Test777'");
		$this->obj->connection->query("DELETE FROM taxonomy WHERE name='TestFather777'");

		$this->obj->connection->query("INSERT INTO taxonomy (name,father_id,class) VALUES ('TestFather777','0','categoria-chefe')");
		$siteId = $this->obj->connection->lastInsertId();

		$this->obj->connection->query("INSERT INTO taxonomy (name,father_id,class) VALUES ('Test777', '".$siteId."', 'estrutura')");
		$stId = $this->obj->connection->lastInsertId();

		$this->obj->connection->query("INSERT INTO taxonomy (name,father_id,class) VALUES ('Test777Hidden', '".$siteId."', 'estrutura')");
		$hiddenStId = $this->obj->connection->lastInsertId();

		$this->obj->connection->query(
			"INSERT INTO config
				(tipo,local,propriedade,valor, explanation)
			VALUES 
				('mod_conf', '".$hiddenStId."', 'related_and_visible', '0', 'test')"
		);
		
		$aust = $this->obj->getStructures();
		
		$hasSite = false;
		$hasStructure = false;
		$hasHiddenStructure = false;
		
		foreach( $aust as $siteKeys => $sites ){
			if( $sites['Site']['name'] == "TestFather777" )
				$hasSite = true;
			
			foreach( $sites['Structures'] as $key => $data ){
				if( $data["name"] == "Test777" )
					$hasStructure = true;
				elseif( $data["name"] == "Test777Hidden" )
					$hasHiddenStructure = true;
			}
		}

		$this->assertTrue($hasSite);
		$this->assertTrue($hasStructure);
		$this->assertFalse($hasHiddenStructure);

	}
	
	function testGetSiteByNodeId(){

		$this->assertFalse($this->obj->getSiteByCategoryId(''));
		
		$this->obj->connection->query("INSERT INTO taxonomy (name,class) VALUES ('My site','categoria-chefe')");
		$siteLastInsert = $this->obj->connection->lastInsertId();

		$result = $this->obj->getSiteByCategoryId($siteLastInsert);
		$this->assertEquals($siteLastInsert, $result['id']);
		$this->assertEquals('My site', $result['name']);

		$this->obj->connection->query("INSERT INTO taxonomy (name, type, class, site_id, site_name, site_name_encoded) VALUES ('First node', 'textual', 'categoria', '".$siteLastInsert."', 'My site', 'my_site')");
		$lastInsertOne = $this->obj->connection->lastInsertId();
		$different = ($lastInsertOne != $siteLastInsert);
		$this->assertTrue($different);

		$this->obj->connection->query("INSERT INTO taxonomy (name, type, class, father_id) VALUES ('Second node', 'textual', 'categoria', '$lastInsertOne')");
		$lastInsertTwo = $this->obj->connection->lastInsertId();
		$different = ($lastInsertTwo != $lastInsertOne);
		$this->assertTrue($different);

		$this->obj->connection->query("INSERT INTO taxonomy (name, type, class, father_id) VALUES ('Third node', 'textual', 'categoria', '$lastInsertTwo')");
		$lastInsertThree = $this->obj->connection->lastInsertId();
		$different = ($lastInsertThree != $lastInsertTwo);
		$this->assertTrue($different);

		$result = $this->obj->getSiteByCategoryId($lastInsertThree);
		$this->assertEquals($siteLastInsert, $result['id']);
		$this->assertEquals('My site', $result['name']);
	}

	function testCreateNewCategoryWithItsStructureHavingItsSitesData(){

		$this->obj->connection->exec("INSERT INTO taxonomy (name,class) VALUES ('My site','categoria-chefe')");
		$siteLastInsert = $this->obj->connection->lastInsertId();

		$this->obj->connection->exec("INSERT INTO taxonomy (name, type, class, father_id, site_id, site_name, site_name_encoded) VALUES ('TestFather777', 'textual', 'estrutura', '".$siteLastInsert."', '".$siteLastInsert."', 'My site', 'my_site')");
		$lastInsert = $this->obj->connection->lastInsertId();
		
	    $params = array(
	        'father' => $lastInsert,
	        'name' => 'Test777',
	        'description' => 'Test777',
	        'author' => '1',
	    );
		
		$result = $this->obj->createCategory($params);
		$query = $this->obj->connection->query("SELECT * FROM taxonomy WHERE name='Test777' AND father_id='".$lastInsert."'");
		$saved = reset( $query );

		$this->obj->connection->query("DELETE FROM taxonomy");
		
		$this->assertInternalType('int', $result);
		$this->assertArrayHasKey('name', $saved);
		$this->assertEquals('Test777', $saved['name']);
		$this->assertEquals('categoria', $saved['class']);
		$this->assertEquals('textual', $saved['type']);
		$empty = empty($saved['description']);
		$this->assertFalse( $empty );
		
		$this->assertEquals($siteLastInsert, $saved['site_id']);
		$this->assertEquals('My site', $saved['site_name']);
		$this->assertEquals('my_site', $saved['site_name_encoded']);
		
	}
	
	function testTryCreatingCategoryWithoutArguments(){
		$this->assertFalse( $this->obj->createCategory( array() ) );
	}
	
	function testGetStructureIdByName(){
		$this->obj->connection->query("INSERT INTO taxonomy (name,class) VALUES ('TestFather777','categoria-chefe')");
		$lastInsert = $this->obj->connection->lastInsertId();
		
	    $params = array(
	        'father' => $lastInsert,
	        'name' => 'Football',
	        'description' => 'A Football section',
	        'site' => $lastInsert,
	        'module' => 'flex_fields',
	        'author' => '1',
	    );
		
		$id = $this->obj->createStructure($params);
		$result = $this->obj->getStructureIdByName("Football");
		$this->assertArrayHasKey("0", $result);
		$this->assertEquals($id, $result[0]);

		$secondId = $this->obj->createStructure($params);
		$secondResult = $this->obj->getStructureIdByName("Football");
		$this->assertArrayHasKey("0", $secondResult);
		$this->assertArrayHasKey("1", $secondResult);
		$this->assertTrue(in_array($id, $secondResult));
		$this->assertTrue(in_array($secondId, $secondResult));

		$secondResult = $this->obj->getStructureIdByName("football");
		$this->assertArrayHasKey("0", $secondResult);
		$this->assertArrayHasKey("1", $secondResult);
		$this->assertTrue(in_array($id, $secondResult));
		$this->assertTrue(in_array($secondId, $secondResult));

	}
}
?>