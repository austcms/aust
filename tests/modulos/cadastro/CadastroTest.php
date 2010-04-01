<?php
require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';
require_once 'core/config/variables.php';
#####################################

class CadastroTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
        /*
         * MÓDULOS ATUAL
         *
         * Diretório do módulo
         */
        $mod = 'cadastro';

        /*
         * Informações de conexão com banco de dados
         */

        include 'modulos/'.$mod.'/'.MOD_CONFIG;
        include_once 'modulos/'.$mod.'/'.$modInfo['className'].'.php';
        
        $_GET['aust_node'] = '777';
        $this->obj = new $modInfo['className'];//new $modInfo['className']();

    }

    /*
     * TÍTULOS DIVISORES
     */
    function testSetDivisor(){

        /*
         * Teste de validação
         */
        $params = array(
            'title' => '',
            'comment' => '',
            'before' => '',
        );
        $this->assertFalse( $this->obj->saveDivisor($params) );

        /*
         * Teste de gravação de dados
         */
        $params = array(
            'title' => '777titulo',
            'comment' => '777comentario',
            'before' => 'BEFORE 777before',
        );
        $this->assertTrue( $this->obj->saveDivisor($params) );

        $sqlFind = "SELECT id FROM ".$this->obj->useThisTable()."
                WHERE
                    tipo='divisor' AND
                    valor='777titulo' AND
                    comentario='777comentario'
                ";

        /*
         * Realiza operações. Teste vem no final.
         */
        $resultFind = $this->obj->connection->query($sqlFind);

        $sqlDelete = "DELETE FROM ".$this->obj->useThisTable()."
                        WHERE
                            tipo='divisor' AND
                            valor='777titulo' AND
                            comentario='777comentario'
                        ";

        $this->obj->connection->query($sqlDelete);

        $resultFindAfterDeleted = $this->obj->connection->query($sqlDelete);

        /*
         * Teste.
         */
        $this->assertFalse( empty( $resultFind ),
            'NÃO ENCONTROU DADOS.'
        );
        $this->assertEquals(array(),
            $resultFindAfterDeleted,
            'NÃO EXCLUIU DADOS.'
        );


    }

}
?>