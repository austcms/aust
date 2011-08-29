<?php
// require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';

require_once 'core/config/variables.php';
#####################################

class FlexFieldsTest extends PHPUnit_Framework_TestCase
{
	
	public $structureId;

	public function setUp(){

		Fixture::getInstance()->destroy();
		installModule('flex_fields');
	
		/*
		 * MÓDULOS ATUAL
		 *
		 * Diretório do módulo
		 */
		$this->mod = 'flex_fields';

		/*
		 * Informações de conexão com banco de dados
		 */
		include MODULES_DIR.$this->mod.'/'.MOD_CONFIG;
		include_once MODULES_DIR.$this->mod.'/'.$modInfo['className'].'.php';
		
		$lastSiteId = Aust::getInstance()->createSite('site', '');
		$this->structureId = Aust::getInstance()->createStructure(
						array(
							'name'		=> 'flex_structure',
							'site'		=> $lastSiteId,
							'public'	=> '1',
							'module'	=> 'flex_fields',
							'author'	=> '1'
						)
					);

		$_GET['aust_node'] = $this->structureId;
		$this->obj = new $modInfo['className']($this->structureId);
		$this->createEnvironment();
	}

	function createEnvironment(){
		$this->destroyEnvironment();
		
		$this->obj->connection->exec(
			'CREATE TABLE table_for_unittests
			(
				id int auto_increment,
				title varchar(200),
				images varchar(200),
				approved int,
				public int,
				admin_id int,
				PRIMARY KEY (id)
			)',
			'CREATE_TABLE'
		);

		$this->obj->connection->exec(
			"CREATE TABLE table_for_unittests_files
			(
				id int auto_increment,
				maintable_id int,
				type varchar(80),
				title varchar(250),
				description text,
				reference varchar(120),
				reference_table varchar(120),
				reference_field varchar(120),
				node_id int,
				admin_id int,
				PRIMARY KEY (id)
			)",
			'CREATE_TABLE'
		);
		
		$this->obj->connection->exec(
			"INSERT INTO flex_fields_config
				(type,property,value,node_id)
				VALUES
				('structure','table','table_for_unittests', '".$this->structureId."')
			"
		);
		
		$this->obj->connection->exec(
			"INSERT INTO flex_fields_config
				(type,property,value,node_id)
				VALUES
				('structure','table_images','table_for_unittests_images', '".$this->structureId."')
			"
		);

		$this->obj->connection->exec(
			"INSERT INTO flex_fields_config
				(type,property,value,node_id)
				VALUES
				('structure','table_files','table_for_unittests_files', '".$this->structureId."')
			"
		);
		$this->obj->connection->exec(
			"INSERT INTO flex_fields_config
				(type,property,value, specie, node_id)
				VALUES
				('campo','images','Images', 'images','".$this->structureId."')
			"
		);

		$this->obj->connection->exec(
			"INSERT INTO flex_fields_config
				(type,property,value, specie, node_id)
				VALUES
				('campo','title','Title', 'string','".$this->structureId."')
			"
		);
	
		$this->obj->connection->exec("INSERT INTO table_for_unittests (title) VALUES ('My first text')");
		$newsId = $this->obj->connection->lastInsertId();

		/* images table */
		$this->obj->connection->exec(
			"CREATE TABLE table_for_unittests_images
			(
				id int auto_increment,
				maintable_id int,
				type varchar(80),
				order_nr int,
				title varchar(250),
				description text,
				local varchar(180),
				link text,
				file_systempath text,
				file_path text,
				file_name varchar(250),
				original_file_name varchar(250),
				file_type varchar(250),
				file_size varchar(250),
				file_ext varchar(10),
				reference varchar(120),
				reference_table varchar(120),
				reference_field varchar(120),
				node_id int,
				created_on datetime,
				updated_on datetime,
				admin_id int,
				PRIMARY KEY (id)
			)",
			'CREATE_TABLE'
		);
	
		/* insert news' images */
		$sql = "INSERT INTO table_for_unittests_images
					(maintable_id, type, title,
					file_systempath,
					file_path,
					file_name, file_type, reference_field, node_id)
					VALUES
						('".$newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/123.jpg',
						'uploads/2011/08/123.jpg',
						'123.jpg', 'image/jpeg', 'images', '".$this->structureId."'
						),
						('".$newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/456.jpg',
						'uploads/2011/08/456.jpg',
						'456.jpg', 'image/jpeg', 'images', '".$this->structureId."'
						),
						('".$newsId."', 'main', NULL,
						'~/code/aust/uploads/2011/08/789.jpg',
						'uploads/2011/08/789.jpg',
						'789.jpg', 'image/jpeg', 'images', '".$this->structureId."'
						),
						('".($newsId+1)."', 'main', NULL,
						'~/code/aust/uploads/2011/08/???.jpg',
						'uploads/2011/08/???.jpg',
						'???.jpg', 'image/jpeg', 'images', '".$this->structureId."'
						)
				";
		Connection::getInstance()->exec($sql);

	}
	
	function destroyEnvironment(){
		if( Connection::getInstance()->hasTable('table_for_unittests') )
			$this->obj->connection->exec('DROP TABLE table_for_unittests', 'CREATE_TABLE');

		if( Connection::getInstance()->hasTable('table_for_unittests_images') )
			$this->obj->connection->exec('DROP TABLE table_for_unittests_images', 'CREATE_TABLE');

		if( Connection::getInstance()->hasTable('table_for_unittests_files') )
			$this->obj->connection->exec('DROP TABLE table_for_unittests_files', 'CREATE_TABLE');
		
		$this->obj->connection->exec("DELETE FROM flex_fields_config");
		$this->obj->connection->exec("DELETE FROM ".Config::getInstance()->table."");
	}
	
	function testConfigurations(){
		$configurations = $this->obj->configurations();
		$this->assertArrayHasKey("campo", $configurations);
		$this->assertEquals("title", $configurations['campo']['title']['property']);
	}
	
	function testLoad(){
		$this->assertTrue(Connection::getInstance()->hasTable('table_for_unittests'));
		
		$result = $this->obj->load(array('fields' => '*'));
		$empty = empty($result);
		$this->assertFalse($empty);
		$this->assertArrayHasKey("title", $result[0]);
		$this->assertArrayHasKey("images", $result[0]);
		$this->assertEquals("My first text", $result[0]['title']);
		$emptyImages = empty($result[0]['images']);
		$this->assertTrue($emptyImages);
	}
	
	function testLoadParamsIncludeFieldsImage(){
		$this->assertTrue(Connection::getInstance()->hasTable('table_for_unittests'));
		
		/* with include_fields */
		$result = $this->obj->load(
			array(
				'aust_node' => '777',
				'fields' => '*',
				'include_fields' => array('images')
			)
		);
		$empty = empty($result);
		$this->assertFalse($empty);
		$this->assertArrayHasKey("title", $result[0]);
		$this->assertEquals("My first text", $result[0]['title']);

		$emptyImages = empty($result[0]['images']);
		$this->assertFalse($emptyImages);
		$this->assertArrayHasKey(0, $result[0]['images']);
		$this->assertArrayHasKey(1, $result[0]['images']);
		$this->assertArrayHasKey(2, $result[0]['images']);
		$this->assertArrayNotHasKey(3, $result[0]['images']);
		
		$this->assertEquals("image/jpeg", $result[0]['images'][0]['file_type']);

	}

	function testGetFiles(){
		$this->obj->connection->exec(
			"INSERT INTO table_for_unittests_files
				(maintable_id,title,node_id,reference_field, type)
				VALUES
				('777','title', '777', 'file', 'main')
			"
		);

		$params = array(
			'w' => '777',
			'field' => 'file',
			'austNode' => '777',
			'tableFiles' => 'table_for_unittests_files'
		);

		$files = $this->obj->getFiles($params);
		$this->assertInternalType('array', $files);
		$notEmpty = !empty($files);
		$this->assertTrue($notEmpty);
		$this->assertEquals('777', $files[0]['maintable_id']);
		$this->assertEquals('file', $files[0]['reference_field']);
		
		$this->destroyEnvironment();
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
					type='divisor' AND
					value='777titulo' AND
					commentary='777comentario'
				";

		/*
		 * Realiza operações. Teste vem no final.
		 */
		$resultFind = $this->obj->connection->query($sqlFind);

		$sqlDelete = "DELETE FROM ".$this->obj->useThisTable()."
						WHERE
							type='divisor' AND
							value='777titulo' AND
							commentary='777comentario'
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
		$this->assertArrayHasKey('value', $divisors['777before777'], $divisors['777before777']);
		$this->assertEquals('777titulo', $divisors['777before777']['value'], $divisors['777before777']['value']);


		/*
		 * Exclui do DB dados testados
		 */

			$sqlDelete = "DELETE FROM ".$this->obj->useThisTable()."
							WHERE
								type='divisor' AND
								value='777titulo' AND
								commentary='777comentario'
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
				'type' => 'campo',
				'property' => 'name',
				'value' => 'Name',
				'specie' => 'string',
			),
			'image' => array(
				'type' => 'campo',
				'property' => 'image',
				'value' => 'Images',
				'specie' => 'images',
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
			$sql = "INSERT INTO flex_fields_config
						 (type,key,value,node_id)
					 VALUES
						 ('structure','table','tabela_1','777')
					 ";
			$this->obj->connection->exec($sql);
	
			// cria tabela física
			$sql = "CREATE TABLE tabela_1 (campo_1 varchar(250))";
			$this->obj->connection->exec($sql, 'CREATE_TABLE');
		
		}
	
		function deleteTemporaryTable(){

			$this->obj->connection->query("DELETE FROM flex_fields_config");
			$this->obj->connection->query("DROP TABLE tabela_1");
		}

	function testLoadModConf(){
		$sql = "INSERT INTO ".Config::getInstance()->table."
					(type, local, property, value,  class, ref_field)
				VALUES
					('structure','".$this->structureId."', 'teste','1', 'field', 'images')
				";
		$this->obj->connection->query($sql);

		$result = $this->obj->loadModConf($this->structureId, 'field');
		$this->assertArrayHasKey(
				'images',
				$result,
				'No information about the field');

		$this->assertEquals(
				'1',
				$result['images']['teste']['value'],
				'Value is not being loaded');
	}
	
	function testAustNode(){
		$_GET["aust_node"] = 777;
		$obj = new FlexFields();
		$obj->austNode(777);
		$this->assertEquals(777, $obj->austNode);
	}
	
	function testLoadModConfWithoutSavedData(){
		/* start test #1 */
		$result = $this->obj->loadModConf($this->structureId, 'field');
		$this->assertArrayHasKey('images', $result);
		$this->assertArrayHasKey(
				'image_field_limit_quantity',
				$result['images'],
				'Field configuration not being loaded.');
	}
	
	function testGetFieldConfig(){
		$this->obj->connection->query("DELETE FROM ".Config::getInstance()->table." WHERE local='777' AND name='teste777'");
		$this->obj->connection->query("DELETE FROM flex_fields_config WHERE node_id='777' AND name='teste777'");

		$this->createTemporaryTable();
		/*
		 * Criar os campos
		 */
		$sql = "INSERT INTO flex_fields_config
					 (type,property,value,node_id,name,specie)
				 VALUES
					 ('campo','campo_1','Campo 1','777','teste777', 'images')
				 ";
		$this->obj->connection->query($sql);
		

		$sql = "INSERT INTO ".Config::getInstance()->table."
					(type, local, name, property, value,  class, ref_field)
				VALUES
					('structure','777','teste777','has_conf','1', 'field', 'campo_1')
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
		
		$this->obj->connection->query("DELETE FROM ".Config::getInstance()->table." WHERE local='777' AND name='teste777'");
		$this->obj->connection->query("DELETE FROM flex_fields_config WHERE node_id='777' AND name='teste777'");
	
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
		$this->obj->connection->exec('DELETE FROM table_for_unittests_images');

		$this->obj->austNode = $this->structureId;

		$sqlImages =
			"INSERT INTO table_for_unittests_images
				(type,reference_table,reference_field,node_id,maintable_id)
				VALUES
				('main','table_for_unittests','test_field','".$this->structureId."','".$this->structureId."')
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

			$sql = "INSERT INTO flex_fields_config
						 (type,property,value,node_id,specie)
					 VALUES
						 ('campo','test_field','Campo 1','".$this->structureId."', 'images')
					 ";
			$this->obj->connection->query($sql);

			$sql = "INSERT INTO ".Config::getInstance()->table."
						(type, local, property, value,  class, ref_field)
					VALUES
						('structure','".$this->structureId."', 'image_field_limit_quantity','1', 'field', 'test_field')
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
			$this->obj->deleteExtraImages($this->structureId, $params );
		
			// verifica se ids foram relamente excluidos como deveriam
			$images = $this->obj->connection->query('SELECT id FROM table_for_unittests_images');
		
			foreach( $images as $image ){
				$this->assertArrayNotHasKey( $image['id'], $idsToDelete, 'Imagem extra não excluída.' );
			}
		
		/*
		 * Configura para ilimitadas imagens
		 */
			$this->obj->connection->exec("DELETE FROM ".Config::getInstance()->table." WHERE local='777'");
			$this->obj->structureFieldsConfig = array();
			$this->obj->config = array();
			
			$sql = "INSERT INTO ".Config::getInstance()->table."
						(type, local, name, property, value,  class, ref_field)
					VALUES
						('structure','777','teste777','image_field_limit_quantity','0', 'field', 'test_field')
					";
			$this->obj->connection->query($sql);

			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);
			$this->obj->connection->exec($sqlImages);
		
			$images = $this->obj->connection->query('SELECT id FROM table_for_unittests_images');
			$oldCount = count($images);

			$params = array('test_field');
			$this->obj->deleteExtraImages( '777', $params );
			$images = $this->obj->connection->query('SELECT id FROM table_for_unittests_images');
			$newCount = count($images);

			$this->assertEquals( $oldCount, $newCount);
			
		$this->destroyEnvironment();
	}

}
?>