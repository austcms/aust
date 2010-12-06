<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'core/class/SQLObject.class.php';
require_once 'core/class/Conexao.class.php';

require_once 'core/class/Modulos.class.php';

#####################################

class ModulosTest extends PHPUnit_Framework_TestCase
{
    public $dbConfig = array();

    public $conexao;

    public function setUp(){

        /*
         * Informações de conexão com banco de dados
         */

        
        $this->dbConfig = $dbConn;
        
        $this->conexao = new Conexao($this->dbConfig);
        $this->modulos = new Modulos();
    }

    function tearDown(){
        $this->dbSchema = false;
    }

    public function testGetContentTable(){
        $this->assertType('string', $this->modulos->getContentTable() );
    }

}
?>