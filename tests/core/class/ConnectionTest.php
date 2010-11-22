<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';

#####################################

class ConnectionTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $conexao;

	public $standardTableName = 'mytest';

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        
        
        $this->conexao = Connection::getInstance();
		$this->conexao->exec('create table '.$this->standardTableName.'(id int)');
    }

	function tearDown(){
		$this->conexao->exec('drop table '.$this->standardTableName.'');
	}

    public function testConexaoWithPdoInit(){

        $this->assertObjectHasAttribute('conn', Connection::getInstance() );
        //$this->assertEquals(0, count($stack));
    }

    /**
     * @depends testConexaoWithPdoInit
     */
    function testQuery(){
        $this->assertArrayHasKey('0', $this->conexao->query('SHOW TABLES'));
    }


    function testWrongQuery(){
        $this->assertType('array', $this->conexao->query('blabla'));
    }

	function test_acquireTablesList(){
		$this->assertType('array', $this->conexao->_acquireTablesList() );
		
		if( !in_array($this->standardTableName, $this->conexao->_acquireTablesList() ) )
			$this->fail('Not acquiring table on SHOW TABLE');
	}

	function testHasTable(){
		$this->assertTrue( $this->conexao->hasTable($this->standardTableName) );
	}
	
	function testTableHasField(){
		$table = reset( reset( $this->conexao->query('SHOW TABLES') ) );
		$fields = reset($this->conexao->query('DESCRIBE '.$table));
		
		$this->assertTrue( $this->conexao->tableHasField($table, $fields['Field']) );
		$this->assertFalse( $this->conexao->tableHasField($table, 'tabela_com_campo_inexistente_test') );
	}

}
?>