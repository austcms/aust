<?php
require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';


class ModulesManagerTest extends PHPUnit_Framework_TestCase
{

    public function testInitialization(){
        $this->obj = new ModulesManager();
    }

    public function testGetContentTable(){
        $this->obj = new ModulesManager();

    }

}
?>