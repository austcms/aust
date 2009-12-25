<?php
require_once 'PHPUnit/Framework.php';

class ConexaoTest extends PHPUnit_Framework_TestCase
{
    public $dbConfig = array(
        'server' => 'localhost',
        'database' => 'aust',
        'username' => 'root',
        'password' => '',
        'encoding' => 'utf8',
        //'driver' => 'mysql'
    );

    public $conexao;

    public function setUp(){
        require_once '../core/class/SQLObject.class.php';
        require_once '../core/class/Conexao.class.php';
    
        $this->conexao = new Conexao($this->dbConfig);
    }

    public function testPdoInit(){

        $this->assertTrue( new Conexao($this->dbConfig) );
        //$this->assertEquals(0, count($stack));
    }
}
?>