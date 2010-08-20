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
        $this->mod = 'cadastro';

        /*
         * Informações de conexão com banco de dados
         */

        include 'modulos/'.$this->mod.'/'.MOD_CONFIG;
        include_once 'modulos/'.$this->mod.'/'.$modInfo['className'].'.php';
        
        $_GET['aust_node'] = '777';
        $this->obj = new $modInfo['className'];//new $modInfo['className']();

    }

    /*
     * TÍTULOS DIVISORES
     */
    function testSaveDivisor(){

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


        $resultFindAfterDeleted = $this->obj->connection->exec($sqlDelete);

        /*
         * Teste.
         */
        $this->assertFalse( empty( $resultFind ),
            'NÃO ENCONTROU DADOS.'
        );
        $this->assertGreaterThan(0,
            $resultFindAfterDeleted,
            'NÃO EXCLUIU DADOS.'
        );


    }
    /**
     * @depends testSaveDivisor
     */
    function testLoadDivisors(){
        /*
         * Cria dados pra tests.
         */
        $params = array(
            'title' => '777titulo',
            'comment' => '777comentario',
            'before' => 'BEFORE 777before',
        );
        $this->obj->saveDivisor($params);
        $params = array(
            'title' => '777titulo',
            'comment' => '777comentario',
            'before' => 'BEFORE 777before777',
        );
        $this->obj->saveDivisor($params);

        /*
         * Testa.
         */
        $divisors = $this->obj->loadDivisors();

        $this->assertTrue( !empty($divisors) );
        $this->assertArrayHasKey('777before', $divisors, $divisors);
        $this->assertArrayHasKey('777before777', $divisors, $divisors);
        $this->assertArrayHasKey('valor', $divisors['777before777'], $divisors['777before777']);
        $this->assertEquals('777titulo', $divisors['777before777']['valor'], $divisors['777before777']['valor']);


        /*
         * Exclui do DB dados testados
         */

            $sqlDelete = "DELETE FROM ".$this->obj->useThisTable()."
                            WHERE
                                tipo='divisor' AND
                                valor='777titulo' AND
                                comentario='777comentario'
                            ";

            $this->obj->connection->query($sqlDelete);

    }

	/*
	 * verifica se todas as configurações do arquivo config.php existem no método
	 * loadModConf()
	 */
	function testConfigurationsExists(){
		
        include 'modulos/'.$this->mod.'/'.MOD_CONFIG;
		$configurations = $this->obj->loadModConf();
		foreach( $modInfo['configurations'] as $key=>$value ){
			$this->assertArrayHasKey($key, $configurations);
		}
	}


}
?>