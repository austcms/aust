<?php
require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';
require_once 'core/config/variables.php';
#####################################

class CadastroSetupTest extends PHPUnit_Framework_TestCase
{

    function setUp(){
        $modelName = 'CadastroSetup';
        require_once 'modulos/'.$mod.'/'.MOD_MODELS_DIR.$modelName.'.php';
        
        $this->obj = new $modelName;//new $modInfo['className']();

    }

    function testCreateStructure(){

    }

    function test

}
?>