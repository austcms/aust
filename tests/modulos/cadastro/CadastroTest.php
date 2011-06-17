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
        include MODULES_DIR.$this->mod.'/'.MOD_CONFIG;
        include_once MODULES_DIR.$this->mod.'/'.$modInfo['className'].'.php';
        
        $_GET['aust_node'] = '777';
        $this->obj = new $modInfo['className'];//new $modInfo['className']();

		

    }

	function createEnvironment(){
		$this->destroyEnvironment();
		$this->obj->connection->exec(
			'CREATE TABLE table_for_unittests
			(
				id int auto_increment,
				title varchar(200),
				PRIMARY KEY (id)
			)',
			'CREATE TABLE'
		);
		
		$this->obj->connection->exec(
			"CREATE TABLE table_for_unittests_images
			(
				id int auto_increment,
				systempath text,
				type varchar(80),
				maintable_id int,
				reference varchar(120),
				reference_table varchar(120),
				reference_field varchar(120),
				categoria_id int,
				PRIMARY KEY (id)
			)",
			'CREATE TABLE'
		);
		
		$this->obj->connection->exec(
			"INSERT INTO cadastros_conf
				(tipo,chave,valor,categorias_id)
				VALUES
				('estrutura','tabela','table_for_unittests', '7777')
			"
		);
		
		$this->obj->connection->exec(
			"INSERT INTO cadastros_conf
				(tipo,chave,valor,categorias_id)
				VALUES
				('estrutura','table_images','table_for_unittests_images', '7777')
			"
		);
		
	}
	
	function destroyEnvironment(){
		$this->obj->connection->exec('DROP TABLE table_for_unittests', 'CREATE TABLE');
		$this->obj->connection->exec('DROP TABLE table_for_unittests_images', 'CREATE TABLE');
		
		$this->obj->connection->exec("DELETE FROM cadastros_conf WHERE categorias_id='7777'");
		$this->obj->connection->exec("DELETE FROM config WHERE local='7777'");
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
		
        include MODULES_DIR.$this->mod.'/'.MOD_CONFIG;
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


	/*
	 * SUPPORT FUNCTION
	 *
	 * Alguns testes precisam de tabelas criadas.
	 */
		function createTemporaryTable(){
			$this->deleteTemporaryTable();
			// cria configuração da tabela
		    $sql = "INSERT INTO cadastros_conf
		                 (tipo,chave,valor,categorias_id)
		             VALUES
		                 ('estrutura','tabela','tabela_1','777')
		             ";
		    $this->obj->connection->exec($sql);
	
			// cria tabela física
		    $sql = "CREATE TABLE tabela_1 (campo_1 varchar(250))";
		    $this->obj->connection->exec($sql, 'CREATE_TABLE');
		
		}
	
		function deleteTemporaryTable(){
	        $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' OR categorias_id='7777'");
	        $this->obj->connection->query("DROP TABLE tabela_1");
		
		}

	function testLoadModConf(){
		/* FIELDS */
        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
        $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' AND nome='teste7777'");

		$this->createTemporaryTable();
			/*
			 * Criar o campo de cadastro
			 */
		    $sql = "INSERT INTO cadastros_conf
		                 (tipo,chave,valor,categorias_id,nome, especie)
		             VALUES
		                 ('campo','campo_1','Campo 1','777','teste7777', 'images')
		             ";
		    $this->obj->connection->exec($sql);

			
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

		$this->deleteTemporaryTable();
	}
	
	function testLoadModConfWithoutSavedData(){
		/* FIELDS */
        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
        $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' AND nome='teste7777'");

		$this->createTemporaryTable();
	
		/*
		 * Criar o campo de cadastro
		 */
	    $sql = "INSERT INTO cadastros_conf
	                 (tipo,chave,valor,categorias_id,nome, especie)
	             VALUES
	                 ('campo','campo_1','Campo 1','777','teste7777', 'images')
	             ";
	    $this->obj->connection->query($sql);

		
        /* start test #1 */
	        $result = $this->obj->loadModConf(777, 'field');
	        $this->assertArrayHasKey(
	                'image_field_limit_quantity',
	                $result['campo_1'],
	                'Teste #1.1 falhou');

        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
        $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' AND nome='teste7777'");

		$this->deleteTemporaryTable();
	}
	
	function testGetFieldConfig(){
        $this->obj->connection->query("DELETE FROM config WHERE local='777' AND nome='teste7777'");
        $this->obj->connection->query("DELETE FROM cadastro_conf WHERE categorias_id='777' AND nome='teste7777'");

		$this->createTemporaryTable();
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
	
		$this->deleteTemporaryTable();
    }

	/*
	 * testDeleteExtraImages()
	 *
	 * Imagens extras são aquelas que estão cadastradas no banco de dados,
	 * mas não deveriam.
	 *
	 * Suponha que o usuário possa inserir 1 imagem. Quando ele inserir a
	 * próxima, ele terá 2. Este método excluir a(s) imagem(ns) anterior(es).
	 */
	function testDeleteExtraImages(){
		$this->createEnvironment();
		$this->obj->connection->exec('DELETE FROM table_for_unittests_images');

		$this->obj->austNode = '7777';

		$sqlImages =
			"INSERT INTO table_for_unittests_images
				(type,reference_table,reference_field,categoria_id,maintable_id)
				VALUES
				('main','table_for_unittests','test_field','7777','7777')
			";
		
        $this->obj->config = array(
			'field_configurations' => array(
			    'image_field_limit_quantity' => array(
					'field_type' => 'images',
			        "value" => "",
			        "label" => "test",
			    ),
			)
        );

		/*
		 * Insere 4 imagens, mas deixa apenas 1 no db
		 */

		    $sql = "INSERT INTO cadastros_conf
		                 (tipo,chave,valor,categorias_id,nome, especie)
		             VALUES
		                 ('campo','test_field','Campo 1','7777','teste7777', 'images')
		             ";
		    $this->obj->connection->query($sql);

		    $sql = "INSERT INTO config
		                (tipo,local,nome,propriedade,valor, class, ref_field)
		            VALUES
		                ('mod_conf','7777','teste7777','image_field_limit_quantity','1', 'field', 'test_field')
		            ";
		    $this->obj->connection->query($sql);

			$limit = $this->obj->getFieldConfig('test_field', 'image_field_limit_quantity');

			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);

			// vai verificar quais os últimos IDs, e então vai definir qual o id que deve ser excluido
			$images = $this->obj->connection->query('SELECT id FROM table_for_unittests_images');
			$allIds = array();
			$idsToDelete = array();
			$firstId = '';
			$i = 0;
			$countIdsToBeDeleted = count($images) - $limit;
			foreach( $images as $image ){
				if( $i < $countIdsToBeDeleted )
					$idsToDelete[] = $image['id'];
			
				$allIds[] = $image['id'];
			
				$i++;
			}
			$this->assertEquals('4', count($allIds) );

			$params = array('test_field');
			$this->obj->deleteExtraImages('7777', $params );
		
			// verifica se ids foram relamente excluidos como deveriam
			$images = $this->obj->connection->query('SELECT id FROM table_for_unittests_images');
		
			foreach( $images as $image ){
				$this->assertArrayNotHasKey( $image['id'], $idsToDelete, 'Imagem extra não excluída.' );
			}
		
		/*
		 * Configura para ilimitadas imagens
		 */
			$this->obj->connection->exec("DELETE FROM config WHERE local='7777'");
			$this->obj->structureFieldsConfig = array();
			$this->obj->config = array();
			
		    $sql = "INSERT INTO config
		                (tipo,local,nome,propriedade,valor, class, ref_field)
		            VALUES
		                ('mod_conf','7777','teste7777','image_field_limit_quantity','0', 'field', 'test_field')
		            ";
		    $this->obj->connection->query($sql);

			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);
		
			$images = $this->obj->connection->query('SELECT id FROM table_for_unittests_images');
			$oldCount = count($images);

			$params = array('test_field');
			$this->obj->deleteExtraImages( '7777', $params );
			$images = $this->obj->connection->query('SELECT id FROM table_for_unittests_images');
			$newCount = count($images);

			$this->assertEquals( $oldCount, $newCount);
			
			
		$this->destroyEnvironment();
	}


}
?>