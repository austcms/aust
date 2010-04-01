<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';

#####################################

class StructurePermissionsTest extends PHPUnit_Framework_TestCase
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

    function testWhatever(){
        
    }


}
?>