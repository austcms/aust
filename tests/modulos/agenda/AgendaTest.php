<?php
require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
require_once 'core/config/variables.php';
require_once 'core/libs/functions/func.php';
#####################################

class AgendaTest extends PHPUnit_Framework_TestCase
{
    public $lastSaveId;
    
    public function setUp(){
        /*
         * MÓDULOS ATUAL
         *
         * Diretório do módulo
         */
        $mod = 'agenda';

        /*
         * Informações de conexão com banco de dados
         */

        include 'modulos/'.$mod.'/'.MOD_CONFIG;
        include_once 'modulos/'.$mod.'/'.$modInfo['className'].'.php';

        $this->obj = new $modInfo['className'];//new $modInfo['className']();
    }


}
?>