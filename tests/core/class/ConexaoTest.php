<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'core/class/SQLObject.class.php';
require_once 'core/class/Conexao.class.php';

#####################################

class ConexaoTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $conexao;

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        require('config/database.php');
        $this->dbConfig = $dbConn;
        
        $this->conexao = new Conexao($this->dbConfig);
    }

    public function testConexaoWithPdoInit(){

        $this->assertObjectHasAttribute('conn', new Conexao($this->dbConfig) );
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

    function testListaTabelasParaArray(){
        $this->assertType('array', $this->conexao->listaTabelasDoDBParaArray());
    }


}
?>