<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'core/class/SQLObject.class.php';
require_once 'core/class/Conexao.class.php';
require_once 'core/config/variables.php';

#####################################

class PrivilegiosTest extends PHPUnit_Framework_TestCase
{
    public $dbConfig = array();

    public $conexao;

    public function setUp(){

        /*
         * Informações de conexão com banco de dados
         */

        require 'tests/config/database.php';
        $this->dbConfig = $dbConn;
        
        $this->conexao = new Conexao($this->dbConfig);

        require_once 'core/class/Modulos.class.php';
        require 'modulos/conteudo/core/config/config.php';
        require 'modulos/conteudo/Privilegios.php';
        
        $this->modulo = new $modInfo['className']($dbSchema, $this->conexao);
    }

}
?>