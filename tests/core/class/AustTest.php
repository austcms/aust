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
        require_once('core/class/Aust.class.php');
        $this->aust = new Aust($this->conexao);
    }

    public function testGetStructures(){
        $this->assertType('array', $this->aust->getStructures() );
    }

    public function testGetStructuresByFather(){
        $this->assertType('array', $this->aust->getStructuresByFather('1') );
        $this->assertType('array', $this->aust->getStructuresByFather('999') );
        $this->assertFalse($this->aust->getStructuresByFather() );
    }

}
?>