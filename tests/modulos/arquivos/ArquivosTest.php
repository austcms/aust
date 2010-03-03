<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';
require_once 'core/config/variables.php';

#####################################

class ArquivosTest extends PHPUnit_Framework_TestCase
{
    public $dbConfig = array();

    public $conexao;

    public function setUp(){

        /*
         * Informações de conexão com banco de dados
         */

        require 'tests/config/database.php';
        $this->dbConfig = $dbConn;
        
        $this->conexao = Connection::getInstance();

        require_once 'core/class/Modulos.class.php';
        require 'modulos/arquivos/core/config/config.php';
        require 'modulos/arquivos/Arquivos.php';
        
        $this->obj = new $modInfo['className']($dbSchema, $this->conexao);
    }

    function testParseURL(){

        $this->assertEquals('http://localhost/testes.php',
                $this->obj->parseUrl('http://localhost/testes/../testes.php') );
        $this->assertEquals('http://localhost/testes/testes.php',
                $this->obj->parseUrl('http://localhost/testes/./testes.php') );
        $this->assertEquals('localhost//testes/testes.php',
                $this->obj->parseUrl('localhost//testes/./testes.php') );
    }



}
?>