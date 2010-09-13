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
	
	
	function testSetRelationalData(){
		$this->obj->fields = array(
			'name' => array(
				'tipo' => 'campo',
				'chave' => 'name',
				'valor' => 'Name',
				'especie' => 'string',
			),
			'image' => array(
				'tipo' => 'campo',
				'chave' => 'image',
				'valor' => 'Images',
				'especie' => 'images',
			),
		);
		
		$this->obj->data = array(
			'table_1' => array(
				'name' => 'alex',
				'image' => array(
					array(
						'name' => 'image_name.jpg',
						'type' => 'image/jpeg',
					),
					array(
						'name' => 'image_name2.jpg',
						'type' => 'image/jpeg',
					)
				)
			)
		);
		
		$this->obj->setRelationalData();
		$this->assertArrayHasKey('image', $this->obj->images['table_1']);
		$this->assertArrayHasKey('0', $this->obj->images['table_1']['image'] );
		$this->assertArrayHasKey('1', $this->obj->images['table_1']['image'] );
		$this->assertArrayNotHasKey('image', $this->obj->data['table_1']);
	}



	function testLoadModConf(){
		/* FIELDS */
        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
        $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' AND nome='teste7777'");

			/*
			 * Criar o campo de cadastro
			 */
		    $sql = "INSERT INTO cadastros_conf
		                 (tipo,chave,valor,categorias_id,nome, especie)
		             VALUES
		                 ('campo','campo_1','Campo 1','777','teste7777', 'images')
		             ";
		    $this->obj->connection->query($sql);
		
	        $sql = "INSERT INTO config
	                    (tipo,local,nome,propriedade,valor, class, ref_field)
	                VALUES
	                    ('mod_conf','777','teste7777','teste','1', 'field', 'campo_1')
	                ";
	        $this->obj->connection->query($sql);
	        $catLastInsertId = $this->obj->connection->lastInsertId();
		
        /* start test #4 */
	        $result = $this->obj->loadModConf(777, 'field');
	
	        $this->assertArrayHasKey(
	                'campo_1',
	                $result,
	                'Teste #4.1 falhou');

	        $this->assertEquals(
	                '1',
	                $result['campo_1']['teste']['value'],
	                'Teste #4.2 falhou');
	
        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
        $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' AND nome='teste7777'");
	}
	
	function testGetFieldConfig(){
        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
        $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' AND nome='teste7777'");

		/*
		 * Criar os campos
		 */
	    $sql = "INSERT INTO cadastros_conf
	                 (tipo,chave,valor,categorias_id,nome, especie)
	             VALUES
	                 ('campo','campo_1','Campo 1','777','teste7777', 'images')
	             ";
	    $this->obj->connection->query($sql);
		

        $sql = "INSERT INTO config
                    (tipo,local,nome,propriedade,valor, class, ref_field)
                VALUES
                    ('mod_conf','777','teste7777','has_conf','1', 'field', 'campo_1')
                ";

        $this->obj->connection->query($sql);
        $catLastInsertId = $this->obj->connection->lastInsertId();
		$this->obj->austNode = '777';

        $this->obj->config = array(
			'field_configurations' => array(
			    'has_conf' => array(
					'field_type' => 'images',
			        "value" => "",
			        "label" => "Working?",
			        "inputType" => "checkbox",
			    ),
			)
        );

		$result = $this->obj->getFieldConfig('campo_1', 'has_conf');
		$this->assertEquals('1', $result);
		
		$result = $this->obj->getFieldConfig('campo_1', 'has_conf2');
		$this->assertFalse($result);
		
        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
	    $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' AND nome='teste7777'");
    }


}
?>