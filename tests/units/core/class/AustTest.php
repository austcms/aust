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
        
        $this->conexao = Connection::getInstance();
        $this->obj = new Aust($this->conexao);
    }

	function tearDown(){
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='Test777'");
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='TestFather777'");
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='Test777Hidden'");
		$this->obj->connection->query("DELETE FROM config WHERE explanation='test'");
	}

    function testGetStructures(){
        $this->assertInternalType('array', $this->obj->getStructures() );
    }
	
	function testGetStructure(){
		Fixture::getInstance()->create();
		$query = Connection::getInstance()->query("SELECT id FROM categorias WHERE tipo='textual' AND classe='estrutura' LIMIT 1");
		$structureId = $query[0]["id"];
		
		$structure = $this->obj->getStructureById($structureId);
		$this->assertInternalType("array", $structure);
		$this->assertEquals($structureId, $structure["id"]);
	}

    function testGetStructuresByFather(){
        $this->assertInternalType('array', $this->obj->getStructuresByFather('1') );
        $this->assertInternalType('array', $this->obj->getStructuresByFather('999') );
        $this->assertFalse($this->obj->getStructuresByFather() );
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
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='Test777'");
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='TestFather777'");

		$this->obj->connection->query("INSERT INTO categorias (nome,subordinadoid,classe) VALUES ('TestFather777','0','categoria-chefe')");
		$siteId = $this->obj->connection->lastInsertId();

		$this->obj->connection->query("INSERT INTO categorias (nome,subordinadoid,classe) VALUES ('Test777', '".$siteId."', 'estrutura')");
		$stId = $this->obj->connection->lastInsertId();

		$this->obj->connection->query("INSERT INTO categorias (nome,subordinadoid,classe) VALUES ('Test777Hidden', '".$siteId."', 'estrutura')");
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
			if( $sites['Site']['nome'] == "TestFather777" )
				$hasSite = true;
			
			foreach( $sites['Structures'] as $key => $data ){
				if( $data["nome"] == "Test777" )
					$hasStructure = true;
				elseif( $data["nome"] == "Test777Hidden" )
					$hasHiddenStructure = true;
			}
		}

		$this->assertTrue($hasSite);
		$this->assertTrue($hasStructure);
		$this->assertFalse($hasHiddenStructure);

	}

	function testCreateNewCategory(){

		// TEST #1
		$this->obj->connection->query("INSERT INTO categorias (nome,classe) VALUES ('TestFather777','categoria')");
		$lastInsert = $this->obj->connection->lastInsertId();
		
	    $params = array(
	        'father' => $lastInsert,
	        'name' => 'Test777',
	        'description' => 'Test777',
	        'class' => 'categoria',
	        'type' => 'modulo777',
	        'author' => '1',
	    );
		
		$result = $this->obj->create($params);
		$query = $this->obj->connection->query("SELECT * FROM categorias WHERE nome='Test777' AND subordinadoid='".$lastInsert."'");
		$saved = reset( $query );
		
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='Test777'");
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='TestFather777'");
		
		$this->assertInternalType('int', $result);
		$this->assertArrayHasKey('nome', $saved);
		$this->assertEquals('Test777', $saved['nome']);
		$this->assertEquals('categoria', $saved['classe']);
		$this->assertEquals('modulo777', $saved['tipo']);
		$empty = empty($saved['descricao']);
		$this->assertFalse( $empty );
		
		// TEST #2
		$this->assertFalse( $this->obj->create( array() ) );
	}
	
	function testGetStructureIdByName(){
		$this->obj->connection->query("INSERT INTO categorias (nome,classe) VALUES ('TestFather777','categoria')");
		$lastInsert = $this->obj->connection->lastInsertId();
		
	    $params = array(
	        'father' => $lastInsert,
	        'name' => 'Football',
	        'description' => 'A Football section',
	        'class' => 'estrutura',
	        'type' => 'flex_fields',
	        'author' => '1',
	    );
		
		$id = $this->obj->create($params);
		$result = $this->obj->getStructureIdByName("Football");
		$this->assertArrayHasKey("0", $result);
		$this->assertEquals($id, $result[0]);

		$secondId = $this->obj->create($params);
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