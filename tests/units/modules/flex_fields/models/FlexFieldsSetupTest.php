<?php
// require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
include_once MODULES_DIR.'flex_fields/FlexFields.php';

class FlexFieldsSetupTest extends PHPUnit_Framework_TestCase
{

    function setUp(){

		Fixture::getInstance()->destroy();
		installModule('flex_fields');
		
        $modelName = 'FlexFieldsSetup';
        $modDir = 'flex_fields';
        $mod = 'FlexFields';
        include_once MODULES_DIR.$modDir.'/'.MOD_MODELS_DIR.$modelName.'.php';
        
        $this->obj = new $modelName;
        $this->Cadastro = new $mod;

    }

	/**
	 * @dataProvider fieldTypesList
	 */
	function testIsAllowedFieldType($allowed = false, $fieldType){
		$this->assertSame( $allowed, $this->obj->isAllowedFieldType($fieldType) );
	}
	
		public function fieldTypesList(){
			return array(
				array(true, 'string', 					'varchar(250)'),
				array(true, 'text', 					'text'),
				array(true, 'date', 					'date'),
				array(true, 'pw', 						'varchar(250)'),
				array(false, 'file', 					'text'),
				array(true, 'files', 					'text'),
				array(true, 'relational_onetoone', 		'int'),
				array(true, 'relational_onetomany', 	'int'),
				array(false, 'uihiaehurg', 				''),
				array(false, 'uihiaehurg', 				''),
			);
		}

		/**
		 * @dataProvider decodedStrings
		 */
		function testEncodeStrings($final, $initial){
			$this->assertEquals( $final, $this->obj->encodeString($initial) );
		}

			function decodedStrings(){
				return array(
					array('minha_tabela', 'Minha Tabela'),
					array('tabela_com_c_cedilha', 'tábéla cOm ç ÇedilhA'),
					array('aaaaa_eeee_iiii_oooo_uuuu', 'áâäãà éêëè íîïì óôöò úûüù'),
					array('nnteste23um_dois', 'ñÑ#!@$teste23um dois'),
				);
			}

		/**
		 * @dataProvider possibleTableNames
		 */
		function testEncodeTableName($final, $initial){
			$this->assertEquals( $final, $this->obj->encodeTableName($initial) );
		}

			function possibleTableNames(){
				return array(
					array('ex_alunos', 'Ex-alunos'),
					array('ex_alunos', 'Ex/alunos'),
					array('barra_', 'Barra\\'),
					array('testeteste', 'Teste&Teste#@=*()<>'),
					array('minha_tabela', 'Minha Tabela'),
					array('tabela_com_c_cedilha', 'tábéla cOm ç ÇedilhA'),
					array('aaaaa_eeee_iiii_oooo_uuuu', 'áâäãà éêëè íîïì óôöò úûüù'),
					array('has', 'has?'),
				);
			}

		// Loop por Cada campo
			function testSanitizeString(){
				$this->assertEquals("há\\\"\\'\\\\teste", $this->obj->sanitizeString("há\"'\\teste"));
			}

			/**
			 * @dataProvider fieldTypesList
			 */
			function testGetFieldPhysicalType($allowed = false, $fieldType, $fieldPhysicalType){
				if( $allowed ){
					$this->assertEquals($fieldPhysicalType, $this->obj->getFieldPhysicalType($fieldType));
				}
			}

			/**
			 * @dataProvider possibleTableNames
			 */
			function testEncodeFieldName($final, $initial){
				$this->assertEquals( $final, $this->obj->encodeFieldName($initial) );
			}

			function testSetCommentForSql(){
				$this->assertEquals( "COMMENT 'Meu Comentário com \'(aspas)'", $this->obj->setCommentForSql("Meu Comentário com '(aspas)") );
			}


		/*
		 * FIELD SQLs
		 */
			function testGetFieldOrder(){
				$this->assertEquals('1', $this->obj->getFieldOrder());
				$this->assertEquals('2', $this->obj->getFieldOrder());
				$this->obj->getFieldOrder();
				$this->obj->getFieldOrder();
				$this->obj->getFieldOrder();
				$this->obj->getFieldOrder();
				$this->obj->getFieldOrder();
				$this->obj->getFieldOrder();
				$this->assertEquals('9', $this->obj->getFieldOrder());
				$this->assertEquals('10', $this->obj->getFieldOrder());
			}

			function testDecreaseFieldOrder(){
				$this->obj->getFieldOrder();
				$this->assertEquals('2', $this->obj->getFieldOrder());
				$this->obj->decreaseFieldOrder();
				$this->assertEquals('2', $this->obj->getFieldOrder());
				$this->obj->getFieldOrder();
				$this->assertEquals('4', $this->obj->getFieldOrder());
				$this->obj->decreaseFieldOrder();
				$this->assertEquals('4', $this->obj->getFieldOrder());

			}
		
		/*
		 * CONFIGURATIONS
		 *
		 * cada campo tem dados salvos em flex_fields_config
		 *
		 */

			function testCreateFieldConfigurationSql_Password(){
				// data for creating field SQL
				$field = array(
					'name' => 'field_one',
					'label' => 'Field One',
					'comment' => 'This is a comment',
					'austNode' => '777',
					'author' => '777',
					'class' => 'password',
				);

				$expectedSql = "INSERT INTO flex_fields_config ".
	                           "(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
	                           "VALUES ".
	                           "('campo','field_one','Field One','This is a comment',777,'777',0,0,1,0,1,'password',1)";

				$this->assertEquals(
					$expectedSql,
					$this->obj->createFieldConfigurationSql_Password($field)
				);
			}

			function testCreateFieldConfigurationSql_File(){
				// data for creating field SQL
				$field = array(
					'name' => 'field_one',
					'label' => 'Field One',
					'comment' => 'This is a comment',
					'austNode' => '777',
					'author' => '777',
					'class' => 'files',
				);

				$expectedSql = "INSERT INTO flex_fields_config ".
	                           "(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
	                           "VALUES ".
	                           "('campo','field_one','Field One','This is a comment',777,'777',0,0,1,0,1,'files',1)";

				$this->assertEquals(
					$expectedSql,
					$this->obj->createFieldConfigurationSql_File($field)
				);
			}
				// Criação da tabela relacional de arquivos: SQL
				function testCreateSqlForFileTable(){
					$this->assertEquals(
						'CREATE TABLE minhatabela_files('.
	                    'id int auto_increment,'.
	                    'maintable_id int,'.
						'type varchar(80),'.
						'order_nr int COMMENT "Contém o número da ordenação deste registro",'.
	                    'title varchar(250),'.
	                    'description text,'.
	                    'local varchar(180),'.
	                    'link text,'.
	                    'systempath text,'.
	                    'path text,'.
	                    'file_name varchar(250),'.
	                    'original_file_name varchar(250),'.
	                    'file_type varchar(250),'.
	                    'file_size varchar(250),'.
	                    'file_ext varchar(10),'.
	                    'reference varchar(120),'.
	                    'reference_table varchar(120),'.
	                    'reference_field varchar(120),'.
	                    'node_id int,'.
	                    'created_on datetime,'.
	                    'updated_on datetime,'.
	                    'admin_id int,'.
	                    'PRIMARY KEY (id),'.
	                    'UNIQUE id (id))',
						$this->obj->createSqlForFilesTable('minhatabela')
					);

					$this->assertEquals('minhatabela_files', $this->obj->filesTableName);
				}

				// Configurações sobre a tabela relacional de arquivos: SQL
				function testCreateSqlForConfigurationOfFiles(){
					$this->obj->filesTableName = 'minhatabela_files';
					$this->obj->austNode = '777';
					$sql = 
					    "INSERT INTO ".
	                    "flex_fields_config ".
	                    "(type,property,value,node_id,created_on,deactivated,disabled,public,restricted,approved) ".
	                    "VALUES ".
	                    "('estrutura','table_files','minhatabela_files',777, '".date('Y-m-d H:i:s')."',0,0,1,0,1)";

					$this->assertEquals(
						$sql,
						$this->obj->createSqlForFileConfiguration()
					);
				}

				// Execução da criação da tabela relacional de arquivos
				function testCreateTableForFiles(){
					$this->obj->connection->exec('DROP TABLE minhatabela_files');
					$this->obj->mainTable = 'minhatabela';
					$this->obj->austNode = '777';
					$this->assertTrue( $this->obj->createTableForFiles() );
					$this->assertTrue( $this->obj->filesTableCreated );

					$created = $this->obj->connection->hasTable('minhatabela_files');
					$this->obj->connection->exec('DROP TABLE minhatabela_files');
					$deleted = !$this->obj->connection->hasTable('minhatabela_files');

					// verifica se houve as criações
					$this->assertTrue($created, "Table not CREATED.");
					$this->assertTrue($deleted, "Table not DELETED.");

					//novas criações não são permitidas
					$this->assertFalse( $this->obj->createTableForFiles() );
				}

				// Execução da configuração da tabela relacional de arquivos
				function testCreateConfigurationForFiles(){
					$this->obj->filesTableName = 'minhatabela_files';
					$this->obj->austNode = '777';
					$this->obj->createConfigurationForFiles();
					
					$sql = "SELECT id FROM flex_fields_config WHERE node_id='".$this->obj->austNode."' AND value='minhatabela_files' AND property='table_files'";
					$created = $this->obj->connection->query($sql);
					if( !empty($created) ) $created = true;
					else $created = false;

					$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='".$this->obj->austNode."' AND value='minhatabela_files' AND property='table_files'");
					$deleted = $this->obj->connection->query("SELECT id FROM flex_fields_config WHERE node_id='".$this->obj->austNode."' AND value='minhatabela_files' AND property='table_files'");
					if( empty($deleted) ) $deleted = true;
					else $deleted = false;


					// verifica se houve as criações
					$this->assertTrue($created, "Files' Table, configuration not CREATED.");
					$this->assertTrue($deleted, "Files' Table, configuration not DELETED.");

				}


			function testCreateFieldConfigurationSql_RelationalOneToOne(){
				// data for creating field SQL
				$field = array(
					'name' => 'field_one',
					'label' => 'Field One',
					'comment' => 'This is a comment',
					'austNode' => '777',
					'author' => '777',
					'class' => 'relacional_umparaum',
					'refTable' => 'categorias',
					'refField' => 'nome',
				);

				$expectedSql = "INSERT INTO flex_fields_config ".
	                           "(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr,ref_table,ref_field) ".
	                           "VALUES ".
	                           "('campo','field_one','Field One','This is a comment',777,'777',0,0,1,0,1,'relacional_umparaum',1,'categorias','nome')";

				$this->assertEquals(
					$expectedSql,
					$this->obj->createFieldConfigurationSql_RelationalOneToOne($field)
				);
			}

			// Relational_OneToMany
			// 		configurações
			// 		cria tabela de referência
			// 		teste criação de sql para criação de tabela de referência
			function testCreateFieldConfigurationSql_RelationalOneToMany(){
				// data for creating field SQL
				$field = array(
					'name' => 'field_one',
					'label' => 'Field One',
					'comment' => 'This is a comment',
					'austNode' => '777',
					'author' => '777',
					'class' => 'relacional_umparamuitos',
					'refTable' => 'categorias',
					'refField' => 'nome',
					'refParentField' => 'parent',
					'refChildField' => 'child',
					'referenceTable' => 'tabelaum_tabelarelacional_categorias'
				);

				$expectedSql = "INSERT INTO flex_fields_config ".
	                           "(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr,ref_table,ref_field,reference,ref_parent_field,ref_child_field) ".
	                           "VALUES ".
	                           "('campo','field_one','Field One','This is a comment',777,'777',0,0,1,0,1,'relacional_umparamuitos',1,'categorias','nome','tabelaum_tabelarelacional_categorias','parent','child')";

				$this->assertEquals(
					$expectedSql,
					$this->obj->createFieldConfigurationSql_RelationalOneToMany($field)
				);
			}
				//testa criação do nome da tabela referencial
				function testCreateReferenceTableName_RelationalOneToMany(){
					$params = array(
						'mainTable' => 'tabelaum',
						'secondaryTable' => 'tabeladois',
						'referenceField' => 'title'
					);
					$this->assertEquals('tabelaum_title_tabeladois', $this->obj->createReferenceTableName_RelationalOneToMany($params));
					// criar mais testes

				}
				// testa Sql para criar tabela referencial
				function testCreateReferenceTableSql_RelationalOneToMany(){
					$params = array(
						'referenceTable' => 'tabelaum_campo_tabeladois',
						'mainTable' => 'tabelaum',
						'secondaryTable' => 'tabeladois',
						'refParentField' => 'tabelaum_id',
						'refChildField' => 'tabeladois_id',
					);

	            	$sql = 'CREATE TABLE tabelaum_campo_tabeladois('.
	                       'id int auto_increment,'.
	                       'tabelaum_id int,'.
	                       'tabeladois_id int,'.
						   'order_nr int,'.
	                       'blocked varchar(120),'.
	                       'approved int,'.
	                       'created_on datetime,'.
	                       'updated_on datetime,'.
	                       'PRIMARY KEY (id), UNIQUE id (id)'.
	                       ')';
					$this->assertEquals($sql, $this->obj->createReferenceTableSql_RelationalOneToMany($params) );
				}
			
			// Configuração: campo Images
			function testCreateFieldConfigurationSql_Images(){
				// data for creating field SQL
				$field = array(
					'name' => 'field_one',
					'label' => 'Field One',
					'comment' => 'This is a comment',
					'austNode' => '777',
					'author' => '777',
					'class' => 'images'
				);

				$expectedSql = "INSERT INTO flex_fields_config ".
	                           "(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
	                           "VALUES ".
	                           "('campo','field_one','Field One','This is a comment',777,'777',0,0,1,0,1,'images',1)";

				$this->assertEquals(
					$expectedSql,
					$this->obj->createFieldConfigurationSql_Images($field),
					'SQL para salvar configuração de campo tipo Images.'
				);
			}
			
					// Criação da tabela relacional de arquivos: SQL
					function testCreateSqlForImagesTable(){
						$this->assertEquals(
							'CREATE TABLE minhatabela_images('.
		                    'id int auto_increment,'.
		                    'maintable_id int,'.
							'type varchar(80) COMMENT "type=main são as imagens principais",'.
							'order_nr int COMMENT "Contém o número da ordenação deste registro",'.
		                    'title varchar(250),'.
		                    'description text,'.
		                    'local varchar(180),'.
		                    'link text,'.
		                    'systempath text,'.
		                    'path text,'.
		                    'file_name varchar(250),'.
		                    'original_file_name varchar(250),'.
		                    'file_type varchar(250),'.
		                    'file_size varchar(250),'.
		                    'file_ext varchar(10),'.
		                    'reference varchar(120),'.
		                    'reference_table varchar(120),'.
		                    'reference_field varchar(120),'.
		                    'node_id int,'.
		                    'created_on datetime,'.
		                    'updated_on datetime,'.
		                    'admin_id int,'.
		                    'PRIMARY KEY (id),'.
		                    'UNIQUE id (id))',
							$this->obj->createSqlForImagesTable('minhatabela')
						);

						$this->assertEquals('minhatabela_images', $this->obj->imagesTableName);
					}

					// Configurações sobre a tabela relacional de arquivos: SQL
					function testCreateSqlForImagesConfiguration(){
						$this->obj->imagesTableName = 'minhatabela_images';
						$this->obj->austNode = '777';
						$sql = 
						    "INSERT INTO ".
		                    "flex_fields_config ".
		                    "(type,property,value,node_id,created_on,deactivated,disabled,public,restricted,approved) ".
		                    "VALUES ".
		                    "('estrutura','table_images','minhatabela_images',777, '".date('Y-m-d H:i:s')."',0,0,1,0,1)";

						$this->assertEquals(
							$sql,
							$this->obj->createSqlForImagesConfiguration()
						);
					}

					// Execução da criação da tabela relacional de arquivos
					function testCreateTableForImages(){

						$this->obj->connection->exec('DROP TABLE minhatabela_images');
						$this->obj->mainTable = 'minhatabela';
						$this->obj->austNode = '777';
						$this->obj->createTableForImages();

						$created = $this->obj->connection->hasTable('minhatabela_images');
						$this->obj->connection->exec('DROP TABLE minhatabela_images');
						$deleted = !$this->obj->connection->hasTable('minhatabela_images');

						// verifica se houve as criações
						$this->assertTrue($created, "Table not CREATED.");
						$this->assertTrue($deleted, "Table not DELETED.");

						//novas criações não são permitidas
						$this->assertFalse( $this->obj->createTableForImages() );
					}

					// Execução da configuração da tabela relacional de arquivos
					function testCreateConfigurationForImages(){
						$this->obj->connection->exec('DROP TABLE minhatabela_images');
						$this->obj->imagesTableName = 'minhatabela_images';
						$this->obj->austNode = '777';
						$this->obj->createConfigurationForImages();

						$created = $this->obj->connection->query("SELECT id FROM flex_fields_config WHERE node_id='".$this->obj->austNode."' AND value='minhatabela_images' AND property='table_images'");
						if( !empty($created) ) $created = true;
						else $created = false;

						$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='".$this->obj->austNode."' AND value='minhatabela_images' AND property='table_images'");
						$deleted = $this->obj->connection->query("SELECT id FROM flex_fields_config WHERE node_id='".$this->obj->austNode."' AND value='minhatabela_images' AND property='table_images'");
						if( empty($deleted) ) $deleted = true;
						else $deleted = false;


						// verifica se houve as criações
						$this->assertTrue($created, "Images' Table, configuration not CREATED.");
						$this->assertTrue($deleted, "Images' Table, configuration not DELETED.");

					}
			


			function testCreateFieldConfigurationSql_String(){
				// data for creating field SQL
				$field = array(
					'name' => 'field_one',
					'label' => 'Field One',
					'comment' => 'This is a comment',
					'austNode' => '777',
					'author' => '777',
					'class' => 'string',
				);

				$expectedSql = "INSERT INTO flex_fields_config ".
	                           "(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
	                           "VALUES ".
	                           "('campo','field_one','Field One','This is a comment','777','777',0,0,1,0,1,'string',1)";

				$this->assertEquals(
					$expectedSql,
					$this->obj->createFieldConfigurationSql_String($field)
				);
			}

		// sql para criação da tabela da nova estrutura propriamente dita
		function testCreateMainTableSql(){
			$params = $this->fieldsForCreation();
			$this->obj->mainTable = 'testunit';

	        $sql = 'CREATE TABLE testunit('.
	                   'id int auto_increment,'.
	                   'node_id int,'.
	                   'blocked varchar(120),'.
	                   'approved int,'.
	                   'created_on datetime,'.
	                   'updated_on datetime,'.
	                   'PRIMARY KEY (id), UNIQUE id (id), INDEX (node_id)'.
	                ')';

			$this->assertEquals($sql, $this->obj->createMainTableSql($params));

		}

		public function fieldsForCreation(){

			return array(
				// field 1
				array(
					'name' => 'Campo 1',
					'type' => 'string',
					'description' => 'Campo 1 descrição',
				),
				// field 2
				array(
					'name' => 'Campo 2',
					'type' => 'text',
					'description' => 'Campo 2 descrição',
				),
				// field 3
				array(
					'name' => 'Campo 3',
					'type' => 'pw',
					'description' => 'Campo 3 descrição',
				),
				// field 4
				array(
					'name' => 'Campo 4',
					'type' => 'date',
					'description' => 'Campo 4 descrição',
				),
				// field 5
				array(
					'name' => 'Campo 5',
					'type' => 'file',
					'description' => 'Campo 5 descrição',
				),
				// field 6
				array(
					'name' => 'Campo 6',
					'type' => 'relacional_umparaum',
					'description' => 'Campo 6 descrição',
					'refTable' => 'reftable',
					'refField' => 'reffield',
				),
				// field 7
				array(
					'name' => 'Campo 7',
					'type' => 'relacional_umparamuitos',
					'description' => 'Campo 7 descrição',
					'refTable' => 'reftable',
					'refField' => 'reffield',
				),
				// field 8 - images
				array(
					'name' => 'Campo 8',
					'type' => 'images',
					'description' => 'Campo 8 descrição',
				),

			);
		}

		// salva dados da nova estrutura na tabela 'categorias'
		function testSaveStructureIntoDatabase(){

			$this->obj->connection->query("INSERT INTO categorias (nome,classe) VALUES ('TestePai777','categoria-chefe')");
			$lastInsert = $this->obj->connection->lastInsertId();

			// TEST #1
		    $params = array(
				'father' => $lastInsert,
		        'name' => 'Teste777',
		        'description' => 'Teste777',
		        'class' => 'flex_fields',
		        'type' => 'estrutura',
		        'author' => '1',
		    );

			$result = $this->obj->saveStructure($params);
			$query = Connection::getInstance()->query("SELECT * FROM categorias WHERE nome='Teste777' AND classe='flex_fields'");
			$saved = reset( $query );
			$this->assertInternalType('array', $saved);

			$this->obj->connection->query("DELETE FROM categorias WHERE nome='Teste777'");
			$this->obj->connection->query("DELETE FROM categorias WHERE id='".$lastInsert."'");

			$this->assertInternalType('int', $result);
			$this->assertArrayHasKey('nome', $saved);
			$this->assertEquals('Teste777', $saved['nome']);
			$this->assertEquals('flex_fields', $saved['classe']);
			$this->assertEquals('estrutura', $saved['tipo']);
			$empty = empty($saved['descricao']);
			$this->assertFalse( $empty );

			// TEST #2
			$this->assertFalse( $this->obj->saveStructure( array() ) );

			// TEST #3
			$this->assertInternalType('int', $this->obj->austNode() );
		}
		
		/*
		 * MÉTODOS CHEFES
		 *
		 * Servem para chamar todos os métodos menores
		 */

		// Função multifuncional, serve tanto para criar novas
		// estruturas como para editar antigas.
		function testCreateMainTable(){
			$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='7777'");
			$this->obj->connection->query("DROP TABLE testunit");
			$this->obj->mainTable = 'testunit';
			$this->obj->austNode = '7777';

			$result = $this->obj->createMainTable(array('austNode' => '7777'));
			
			$saved = $this->obj->connection->hasTable('testunit');
			
			$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='tabela' AND type='estrutura' AND node_id='7777'");
			$this->assertArrayHasKey('0', $conf );
			$this->assertArrayNotHasKey('1', $conf );
			$this->assertEquals('testunit', $conf[0]['value'] );

			$this->obj->connection->query("DROP TABLE testunit");
			
			$this->assertTrue($result);
			$this->assertTrue($saved);
			$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='7777'");
		}
		
		function testAddFieldWithoutName(){
			$this->assertFalse( $this->obj->addField(array()) );
		}
		
		// usado para reiniciar as tabelas
			function restartTable(){
				$this->obj->connection->query("DROP TABLE testunit");
				$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='7777'");
				$this->obj->mainTable = 'testunit';
				$this->obj->austNode = '7777';
				$this->obj->createMainTable();
			}
			function destroyTests(){
				$this->obj->connection->query("DROP TABLE testunit");
				$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='7777'");
				$this->obj->connection->exec("DELETE FROM categorias WHERE nome='TestUnit'");
			}
		
		/*
		 * 
		 * ADDING NEW FIELDS
		 * 
		 */
		
		/**
		 * @depends testCreateMainTable
		 */
			function testAddFieldString(){
				$this->restartTable();
			
				// test STRING
					$params = array(
						'name' => 'Campo 1',
						'type' => 'string',
						'description' => ''
					);
					$result = $this->obj->addField($params);
				
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_1') );
					$this->assertArrayHasKey('0', $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='campo_1' AND node_id='7777'") );
					$this->destroyTests();
			}
		
			function testAddFieldText(){
				$this->restartTable();
			
				// test TEXT
					$params = array(
						0 => array(
							'name' => 'Campo 1',
							'type' => 'text',
							'description' => ''
						),
					);
					$result = $this->obj->addField($params);
		
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_1'), 'Text: campo_1 not created.' );
					$query = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='campo_1' AND node_id='7777'");
					$this->assertArrayHasKey('0', $query );
					$query = reset($query);
					$this->assertEquals('text', $query['specie'] );
					$this->destroyTests();
			}
		
			function testAddFieldPassword(){
				$this->restartTable();
			
				// test PASSWORD
					$params = array(
						0 => array(
							'name' => 'Campo 1',
							'type' => 'pw',
							'description' => ''
						),
					);
					$result = $this->obj->addField($params);

					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_1') );
					$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='campo_1' AND node_id='7777'");
					$this->assertArrayHasKey('0', $conf );
					$this->assertEquals('password', $conf[0]['specie'] );
					$this->destroyTests();
			}
		
			function testAddFieldDate(){
				$this->restartTable();
			
				// test DATE
					$params = array(
						0 => array(
							'name' => 'Campo 1',
							'type' => 'date',
							'description' => ''
						),
					);
					$result = $this->obj->addField($params);

					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_1') );
					$this->assertArrayHasKey('0', $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='campo_1' AND node_id='7777'") );
				
					// campo do tipo date?
					$result = $this->obj->connection->query('DESCRIBE testunit');
					foreach( $result as $key=>$value ){
						if( $value['Field'] != 'campo_1' )
							continue;
					
						if( $value['Type'] != 'date' ){
							$this->fail('Date is NOT of type \'date\'');
						}
					}
					$this->destroyTests();
			}
		
			function testAddFieldFile(){
				$this->obj->connection->exec("DROP TABLE testunit_files");
				$this->restartTable();
			
				// test FILE
					$params = array(
						0 => array(
							'name' => 'file_field',
							'type' => 'files',
							'description' => ''
						),
					);
					$result = $this->obj->addField($params);

					$this->assertTrue( $this->obj->connection->hasTable('testunit_files') );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'file_field') );
					$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='file_field' AND node_id='7777'");
					$this->assertArrayHasKey('0', $conf );
					$this->assertEquals('files', $conf[0]['specie'] );
					$this->obj->connection->exec("DROP TABLE testunit_files");
					
					$this->destroyTests();
			}

		
			function testAddFieldRelationalOneToOne(){
				$this->restartTable();
			
				// test RELACIONAL_UMPARAUM
					$params = array(
						0 => array(
							'name' => 'Campo 1',
							'type' => 'relational_onetoone',
							'description' => '',
							'refTable' => 'ref_table',
							'refField' => 'ref_field',
						),
					);
					$result = $this->obj->addField($params);

					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_1') );
					$result = $this->obj->connection->query('DESCRIBE testunit');
					foreach( $result as $key=>$value ){
						if( $value['Field'] != 'campo_1' )
							continue;
					
						if( $value['Type'] != 'int(11)' ){
							$this->fail('Relational One to One is NOT of type \'int\', but of type '.$value['Type']);
						}
					}
					
					$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='campo_1' AND node_id='7777'");
					$this->assertArrayHasKey('0', $conf );
					$this->assertEquals('ref_table', $conf[0]['ref_table'] );
					$this->assertEquals('ref_field', $conf[0]['ref_field'] );
					$this->destroyTests();
			}

		
			function testAddFieldRelationalOneToMany(){
				$this->obj->connection->exec("DROP TABLE testunit_ref_field_ref_table");
				$this->obj->connection->exec("DROP TABLE testunit_id_testunit");
				$this->restartTable();
				$this->obj->mainTable = 'testunit';
			
				// test RELACIONAL_UMPARAMUITOS
					$params = array(
						0 => array(
							'name' => 'Campo 1',
							'type' => 'relational_onetomany',
							'description' => '',
							'refTable' => 'ref_table',
							'refField' => 'ref_field',
						),
					);
					$result = $this->obj->addField($params);

					$this->assertTrue( $this->obj->connection->hasTable('testunit_ref_field_ref_table') );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit_ref_field_ref_table', 'testunit_id') );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit_ref_field_ref_table', 'ref_table_id') );
					$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='campo_1' AND node_id='7777'");
					$this->assertArrayHasKey('0', $conf );
					$this->assertEquals('ref_table', $conf[0]['ref_table'] );
					$this->assertEquals('ref_field', $conf[0]['ref_field'] );
					$this->assertEquals('testunit_id', $conf[0]['ref_parent_field'] );
					$this->assertEquals('ref_table_id', $conf[0]['ref_child_field'] );
					
				// test relacional para si próprio
					$params = array(
						0 => array(
							'name' => 'Campo 2',
							'type' => 'relational_onetomany',
							'description' => '',
							'refTable' => 'testunit',
							'refField' => 'id',
						),
					);
					
					// testa SQL para criação da tabela intermediária
						$relationalTableParams = array(
							'referenceTable' => 'testunit_id_testunit',
							'mainTable' => 'testunit',
							'secondaryTable' => 'testunit',
							'refParentField' => 'parent_testunit_id',
							'refChildField' => 'testunit_id',
						);
		            	$sql = 'CREATE TABLE testunit_id_testunit('.
		                       'id int auto_increment,'.
		                       'parent_testunit_id int,'.
		                       'testunit_id int,'.
							   'order_nr int,'.
		                       'blocked varchar(120),'.
		                       'approved int,'.
		                       'created_on datetime,'.
		                       'updated_on datetime,'.
		                       'PRIMARY KEY (id), UNIQUE id (id)'.
		                       ')';
						$this->assertEquals($sql, $this->obj->createReferenceTableSql_RelationalOneToMany($relationalTableParams) );
					
					$result = $this->obj->addField($params);
					$this->assertTrue( $this->obj->connection->hasTable('testunit_id_testunit') );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit_id_testunit', 'parent_testunit_id') );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit_id_testunit', 'testunit_id') );
					$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='campo_2' AND node_id='7777'");
					$this->assertArrayHasKey('0', $conf );
					$this->assertEquals('testunit', $conf[0]['ref_table'] );
					$this->assertEquals('id', $conf[0]['ref_field'] );
					$this->assertEquals('parent_testunit_id', $conf[0]['ref_parent_field'] );
					$this->assertEquals('testunit_id', $conf[0]['ref_child_field'] );


					$this->obj->connection->exec("DROP TABLE testunit_id_testunit");
					$this->obj->connection->exec("DROP TABLE testunit_ref_field_ref_table");

				// fim, destrói
				$this->destroyTests();
			}
			
			function testAddFieldImages(){
				$this->restartTable();
			
				// test TEXT
					$params = array(
						0 => array(
							'name' => 'Campo 1 Images',
							'type' => 'images',
							'description' => ''
						),
					);
					$result = $this->obj->addField($params);
		
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_1_images'), 'Text: campo_1_images not created.' );
					$this->assertArrayHasKey('0', $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='campo_1_images' AND node_id='7777'") );
					$this->destroyTests();
			}

			
			function testHasStandardFields(){
				$this->restartTable();
			
				// test TEXT
					$params = array(
						0 => array(
							'name' => 'Campo 1 Images',
							'type' => 'images',
							'description' => ''
						),
					);
					$result = $this->obj->addField($params);
		
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'created_on'), 'Missing field: created_on' );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'updated_on'), 'Missing field: updated_on' );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'blocked'), 'Missing field: blocked' );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'approved'), 'Missing field: approved' );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'id'), 'Missing field: id' );
					$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'node_id'), 'Missing field: node_id' );
					$this->destroyTests();
			}

			function testAddFieldChangingOrders(){
				$this->restartTable();
			
				// test TEXT
					$params = array(
						array(
							'name' => 'Field 1',
							'type' => 'string',
							'order' => 'first_field'
						),
						array(
							'name' => 'Field 2',
							'type' => 'text',
							'order' => 'first_field'
						),
						array(
							'name' => 'Field 3',
							'type' => 'string',
							'order' => 'field_2'
						),
					);
					
					$result = $this->obj->addField($params);
					$this->Cadastro->austNode = '7777';
		
					$fields = $this->Cadastro->getFields();
					$this->assertArrayHasKey('field_1', $fields );
					$this->assertArrayHasKey('field_2', $fields );
					$this->assertArrayHasKey('field_3', $fields );

					$this->assertEquals('3', $fields['field_1']['order_nr'] );
					$this->assertEquals('1', $fields['field_2']['order_nr'] );
					$this->assertEquals('2', $fields['field_3']['order_nr'] );
					
					$new_field = array(
						'name' => 'Field 4',
						'type' => 'string',
					);
					$result = $this->obj->addField($new_field);
					$fields = $this->Cadastro->getFields();
					$this->assertEquals('4', $fields['field_4']['order_nr'] );
					
					$new_field = array(
						'name' => 'Field 5',
						'type' => 'string',
						'order' => 'field_1'
					);
					$result = $this->obj->addField($new_field);
					$fields = $this->Cadastro->getFields();
					$this->assertEquals('4', $fields['field_5']['order_nr'] );
					$this->assertEquals('5', $fields['field_4']['order_nr'] );
					
					
					//$this->assertTrue(  );
//					$this->assertArrayHasKey('0', $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE type='campo' AND property='field_1' AND node_id='7777'") );
					$this->destroyTests();
			}


		function testSaveStructureConfiguration(){
			// testa as configurações sobre o flex_fields que não
			// tem a ver com os campos.
			$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='777'");
			
			$this->obj->austNode = 777;
			$params = array(
				'approval' => 1,
				'pre_password' => '123',
				'description' => 'descrição 777',
			);
			
			$this->obj->saveStructureConfiguration($params);
			$this->obj->saveStructureConfiguration($params);
			$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='aprovacao' AND type='config' AND node_id='777'");
			$this->assertArrayHasKey('0', $conf );
			$this->assertArrayNotHasKey('1', $conf );
			$this->assertEquals('1', $conf[0]['value'] );

			$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='pre_senha' AND type='config' AND node_id='777'");
			$this->assertArrayHasKey('0', $conf );
			$this->assertArrayNotHasKey('1', $conf );
			$this->assertEquals('123', $conf[0]['value'] );

			$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='descricao' AND type='config' AND node_id='777'");
			$this->assertArrayHasKey('0', $conf );
			$this->assertArrayNotHasKey('1', $conf );
			$this->assertEquals('descrição 777', $conf[0]['value'] );
			
			$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='777'");
			// test PARÂMETROS
			$params = array(
				'options' => array(
					'approval' => 0,
				),
			);
			
			$this->obj->saveStructureConfiguration($params);
			$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='aprovacao' AND type='config' AND node_id='777'");
			$this->assertArrayHasKey('0', $conf );
			$this->assertArrayNotHasKey('1', $conf );
			$this->assertEquals('0', $conf[0]['value'] );
			
			$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='777'");
		}
		
		
		// executa a criação de uma nova estrutura inteira
	    function testCreateStructure(){
		
			$this->obj->connection->exec("DROP TABLE testunit");
			$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='7777' OR commentary='haha777' OR value='testunit' OR value='haha777' ");
			$this->obj->connection->exec("DELETE FROM categorias WHERE nome='TestUnit' AND subordinado_nome_encoded='testepai777'");
			$this->obj->connection->exec("DELETE FROM categorias WHERE nome='testunit'");
			$this->obj->connection->query("INSERT INTO categorias (nome,classe) VALUES ('TestePai777','categoria-chefe')");
			$lastInsert = $this->obj->connection->lastInsertId();
			
			$params = array(
	            'name' => 'TestUnit',
	            'father' => $lastInsert,
	            'class' => 'estrutura',
	            'type' => 'flex_fields',
	            'author' => 1,
				'fields' => array(
					array(
						'name' => 'Campo 1',
						'type' => 'string',
						'description' => 'haha777',
					),
					array(
						'name' => 'Campo 2',
						'type' => 'relational_onetoone',
						'description' => 'haha777',
						'refTable' => 'ref_table',
						'refField' => 'ref_field',
					),
					array(
						'name' => 'Campo 3',
						'type' => 'relational_onetomany',
						'description' => 'haha777',
						'refTable' => 'ref_table',
						'refField' => 'ref_field',
					),
				),
				'options' => array(
					'approval' => 'haha777',
					'pre_password' => 'haha777',
					'description' => 'haha777',
				),
				
			);
			
			$result = $this->obj->createStructure($params);
			$austNode = $this->obj->austNode;
			$austNodeIsNumeric = is_numeric($austNode);
			$this->assertTrue($austNodeIsNumeric);
			
			// verifica tabela criada
			$this->assertTrue( $this->obj->connection->hasTable('testunit') );
			$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_1') );
			$this->assertEquals('testunit', $this->obj->mainTable);
			
			// verifica se categoria foi criada
			$conf = $this->obj->connection->query("SELECT * FROM categorias WHERE id='$austNode'");
			$this->assertArrayHasKey('0', $conf, 'Not saving category.' );
			$this->assertArrayNotHasKey('1', $conf );
			$this->assertEquals('estrutura', $conf[0]['classe'], 'Did not save as structure.' );
			
			// verifica configurações da tabela
				// test 3.1
				$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='descricao' AND type='config' AND node_id='$austNode'");
				$this->assertArrayHasKey('0', $conf );
				$this->assertEquals('haha777', $conf[0]['value'], 'Did not save description. #3.1' );
				$this->assertEquals($austNode, $conf[0]['node_id'], 'Did not save austNode.' );
				// test 3.2
				$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='pre_senha' AND type='config' AND node_id='$austNode'");
				$this->assertArrayHasKey('0', $conf );
				$this->assertEquals('haha777', $conf[0]['value'], 'Did not save pre_password. #3.2' );
				$this->assertEquals($austNode, $conf[0]['node_id'], 'Did not save austNode.' );
				// test 3.3
				$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='aprovacao' AND type='config' AND node_id='$austNode'");
				$this->assertArrayHasKey('0', $conf );
				$this->assertEquals('haha777', $conf[0]['value'], 'Did not save approval. #3.3' );
				$this->assertEquals($austNode, $conf[0]['node_id'], 'Did not save austNode.' );
				// test 3.4
				$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='tabela' AND type='estrutura' AND node_id='$austNode'");
				$this->assertArrayHasKey('0', $conf );
				$this->assertEquals('testunit', $conf[0]['value'], 'Did not save table properties. #3.4' );
				$this->assertEquals($austNode, $conf[0]['node_id'], 'Did not save austNode.' );
			
			// verifica configurações dos campos
			$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_1') );
			$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_2') );
			$this->assertTrue( $this->obj->connection->tableHasField('testunit', 'campo_3') );
				// test 4.1
				$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='campo_1' AND type='campo' AND node_id='$austNode'");
				$this->assertArrayHasKey('0', $conf );
				$this->assertEquals('Campo 1', $conf[0]['value'], 'Did not save field campo_1. #4.1' );
				$this->assertEquals('haha777', $conf[0]['commentary'], 'Did not save field campo_1. #4.1' );
				$this->assertEquals($austNode, $conf[0]['node_id'], 'Did not save austNode.' );
				// test 4.2 - relational_onetoone
				$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='campo_2' AND type='campo' AND node_id='$austNode'");
				$this->assertArrayHasKey('0', $conf );
				$this->assertEquals('Campo 2', $conf[0]['value'], 'Did not save field campo_1. #4.2' );
				$this->assertEquals('haha777', $conf[0]['commentary'], 'Did not save field campo_1. #4.2' );
				$this->assertEquals('ref_table', $conf[0]['ref_table'] );
				$this->assertEquals('ref_field', $conf[0]['ref_field'] );
				$this->assertEquals($austNode, $conf[0]['node_id'], 'Did not save austNode.' );
				// test 4.3 - relational_onetomany
				$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='campo_3' AND type='campo' AND node_id='$austNode'");
				$this->assertArrayHasKey('0', $conf );
				$this->assertEquals('Campo 3', $conf[0]['value'], 'Did not save field campo_1. #4.3' );
				$this->assertEquals('haha777', $conf[0]['commentary'], 'Did not save field campo_1. #4.3' );
				$this->assertEquals('ref_table', $conf[0]['ref_table'] );
				$this->assertEquals('ref_field', $conf[0]['ref_field'] );
				$this->assertEquals($austNode, $conf[0]['node_id'], 'Did not save austNode.' );
				$this->assertTrue( $this->obj->connection->hasTable('testunit_ref_field_ref_table') );
			
			/*
			 * INSERE UM NOVO CAMPO
			 *
			 * Teste #5
			 */
				$newField = array(
					'name' => 'New Field',
					'type' => 'files',
					'description' => 'my description'
				);
				$this->obj->addField($newField);
			
				$this->assertTrue( $this->obj->connection->hasTable('testunit_files') );
				$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE property='new_field' AND type='campo' AND node_id='$austNode'");
				$this->assertArrayHasKey('0', $conf );
				$this->assertEquals('New Field', $conf[0]['value'], 'Did not save New Field configuration. #5' );
				$this->assertEquals('files', $conf[0]['specie'], 'Did not save New Field as "file". #5' );
				$this->assertEquals('4', $conf[0]['order_nr'], 'Did not save New Field as "file". #5' );
			
			
			
			$this->obj->connection->exec("DELETE FROM categorias WHERE nome='TestePai777'");
			$this->obj->connection->exec("DELETE FROM categorias WHERE nome='TestUnit' AND subordinado_nome_encoded='testepai777'");
			$this->obj->connection->exec("DELETE FROM flex_fields_config WHERE node_id='$austNode' OR commentary='haha777' OR value='testunit' OR value='haha777' ");
			$this->obj->connection->exec("DROP TABLE testunit");
			$this->obj->connection->exec("DROP TABLE testunit_files");
			$this->obj->connection->exec("DROP TABLE testunit_images");
			$this->obj->connection->exec("DROP TABLE testunit_ref_field_ref_table");
	    }
	
}
?>