<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

#####################################

class ModulesTest extends PHPUnit_Framework_TestCase
{
    public $dbConfig = array();

    public $conexao;

    public function setUp(){
    }

    function tearDown(){
    }

    public function testInitialization(){
        $this->obj = new Module;
    }

}
?>