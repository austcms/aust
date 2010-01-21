<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'core/class/SQLObject.class.php';
require_once 'core/class/Conexao.class.php';

#####################################

class AustTest extends PHPUnit_Framework_TestCase
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