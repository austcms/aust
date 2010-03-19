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

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        require('tests/config/database.php');
        $this->dbConfig = $dbConn;
        
        $this->conexao = Connection::getInstance();
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

}
?>