<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';

#####################################

class AustTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $conexao;

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        require('tests/config/database.php');
        
        $this->conexao = Connection::getInstance();
        require_once('core/class/Aust.php');
        $this->obj = new Aust($this->conexao);
    }

    public function testGetStructures(){
        $this->assertType('array', $this->obj->getStructures() );
    }

    public function testGetStructuresByFather(){
        $this->assertType('array', $this->obj->getStructuresByFather('1') );
        $this->assertType('array', $this->obj->getStructuresByFather('999') );
        $this->assertFalse($this->obj->getStructuresByFather() );
    }

	public function testCreateNewCategory(){

		// TEST #1
		$this->obj->connection->query("INSERT INTO categorias (nome,classe) VALUES ('TestePai777','categoria')");
		$lastInsert = $this->obj->connection->lastInsertId();
		
	    $params = array(
	        'father' => $lastInsert,
	        'name' => 'Teste777',
	        'description' => 'Teste777',
	        'class' => 'categoria',
	        'type' => 'modulo777',
	        'author' => '1',
	    );
		
		$result = $this->obj->create($params);
		$saved = reset($this->obj->connection->query("SELECT * FROM categorias WHERE nome='Teste777' AND subordinadoid='".$lastInsert."'") );
		
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='Teste777'");
		$this->obj->connection->query("DELETE FROM categorias WHERE nome='TestePai777'");
		
		$this->assertType('int', $result);
		$this->assertArrayHasKey('nome', $saved);
		$this->assertEquals('Teste777', $saved['nome']);
		$this->assertEquals('categoria', $saved['classe']);
		$this->assertEquals('modulo777', $saved['tipo']);
		$empty = empty($saved['descricao']);
		$this->assertFalse( $empty );
		
		// TEST #2
		$this->assertFalse( $this->obj->create( array() ) );
		
		
	}

}
?>