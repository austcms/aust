<?php
// require_once 'PHPUnit/Framework.php';

require_once 'tests/config/auto_include.php';

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
		Connection::getInstance()->exec('create table '.$this->standardTableName.'(id int)');
	}

	function tearDown(){
		Connection::getInstance()->exec('drop table '.$this->standardTableName.'');
	}
	
	function testDbExists(){
		$this->assertTrue( $this->conexao->dbExists() );
	}

	function testConexaoWithPdoInit(){
		$this->assertObjectHasAttribute('conn', Connection::getInstance() );
	}

	/**
	 * @depends testConexaoWithPdoInit
	 */
	function testQuery(){
		$this->assertArrayHasKey('0', Connection::getInstance()->query('SHOW TABLES'));
	}


	function testWrongQuery(){
		$this->assertInternalType('array', Connection::getInstance()->query('blabla'));
	}

	function test_acquireTablesList(){
		$this->assertInternalType('array', Connection::getInstance()->_acquireTablesList() );
		
		if( !in_array($this->standardTableName, Connection::getInstance()->_acquireTablesList() ) )
			$this->fail('Not acquiring table on SHOW TABLE');
	}

	function testHasTable(){
		$this->assertTrue( Connection::getInstance()->hasTable($this->standardTableName) );
	}
	
	function testTableHasField(){
		$query = Connection::getInstance()->query('SHOW TABLES');
		$query = reset( $query );
		$table = reset( $query );
		$query = Connection::getInstance()->query('DESCRIBE '.$table);
		$fields = reset( $query );
		
		$this->assertTrue( Connection::getInstance()->tableHasField($table, $fields['Field']) );
		$this->assertFalse( Connection::getInstance()->tableHasField($table, 'tabela_com_campo_inexistente_test') );
	}

}
?>