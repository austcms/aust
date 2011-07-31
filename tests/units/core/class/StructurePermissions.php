<?php
// require_once 'PHPUnit/Framework.php';

#####################################

require_once 'tests/config/auto_include.php';


#####################################

class StructurePermissionsTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $conexao;

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        
        
        $this->conexao = Connection::getInstance();
    }

    function testWhatever(){
        $this->assertTrue(true);
    }


}
?>