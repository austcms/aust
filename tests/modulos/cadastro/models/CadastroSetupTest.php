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
        $mod = 'Cadastro';
        require_once 'modulos/'.$mod.'/'.MOD_MODELS_DIR.$modelName.'.php';
        
        $this->obj = new $modelName;//new $modInfo['className']();

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
				array(true, 'file', 					'text'),
				array(true, 'relacional_umparaum', 		'int'),
				array(true, 'relacional_umparamuitos', 	'int'),
				array(false, 'uihiaehurg', 				''),
				array(false, 'uihiaehurg', 				''),
			);
		}

	/**
	 * @dataProvider decodedStrings
	 */
	function testEncodeStrings($final, $initial){
		$this->assertEquals( $final, $this->obj->encodeTableName($initial) );
	}
	
		function decodedStrings(){
			return array(
				array('minha_tabela', 'Minha Tabela'),
				array('tabela_com_c_cedilha', 'tábéla cOm ç ÇedilhA'),
				array('aaaaa_eeee_iiii_oooo_uuuu', 'áâäãà éêëè íîïì óôöò úûüù'),
				array('nntesteum_dois', 'ñÑ#!@$teste23um dois'),
			);
		}

	/**
	 * @dataProvider possibleTableNames
	 */
	function testEncodeTableName($final, $initial){
		$this->assertEquals( $final, $this->obj->encodeTableName($initial) );
	}
	
		function possibleTableName(){
			return array(
				array('minha_tabela', 'Minha Tabela'),
				array('tabela_com_c_cedilha', 'tábéla cOm ç ÇedilhA'),
				array('aaaaa_eeee_iiii_oooo_uuuu', 'áâäãà éêëè íîïì óôöò úûüù'),
			);
		}

	// Loop por Cada campo
		function testSanitizeFieldDescription(){
			$this->assertEquals("há\\\"\\'\\\\teste", $this->obj->satinizeFieldDescription("há\"'\\teste"));
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
			
			$expectedSql = "INSERT INTO cadastros_conf ".
                           "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
                           "VALUES ".
                           "('campo','field_one','Field One','This is a comment',777,777,0,0,1,0,1,'password',1)";

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
				'class' => 'arquivo',
			);
			
			$expectedSql = "INSERT INTO cadastros_conf ".
                           "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
                           "VALUES ".
                           "('campo','field_one','Field One','This is a comment',777,777,0,0,1,0,1,'arquivo',1)";

			$this->assertEquals(
				$expectedSql,
				$this->obj->createFieldConfigurationSql_File($field)
			);
		}
			// tabela relacional de arquivos
			function testGetCreateTableForFilesSql(){
				$this->fail();
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
			
			$expectedSql = "INSERT INTO cadastros_conf ".
                           "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo) ".
                           "VALUES ".
                           "('campo','field_one','Field One','This is a comment',777,777,0,0,1,0,1,'relational_umparaum',1,'categorias','nome')";

			$this->assertEquals(
				$expectedSql,
				$this->obj->createFieldConfigurationSql_RelationalOneToOne($field)
			);
		}

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
				'referenceTable' => 'tabelaum_tabelarelacional_categorias'
			);
			
			$expectedSql = "INSERT INTO cadastros_conf ".
                           "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo,referencia) ".
                           "VALUES ".
                           "('campo','field_one','Field One','This is a comment',777,777,0,0,1,0,1,'relational_umparamuitos',1,'categorias','nome','tabelaum_tabelarelacional_categorias')";

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
				);
				
            	$sql = 'CREATE TABLE tabelaum_campo_tabeladois('.
                       'id int auto_increment,'.
                       'tabelaum_id int,'.
                       'tabeladois_id int,'.
                       'blocked varchar(120),'.
                       'approved int,'.
                       'created_on datetime,'.
                       'updated_on datetime,'.
                       'PRIMARY KEY (id), UNIQUE id (id)'.
                       ')';
				$this->assertEquals($sql, $this->obj->createReferenceTableSql_RelationalOneToMany($params) );
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
			
			$expectedSql = "INSERT INTO cadastros_conf ".
                           "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
                           "VALUES ".
                           "('campo','field_one','Field One','This is a comment',777,777,0,0,1,0,1,'string',1)";

			$this->assertEquals(
				$expectedSql,
				$this->obj->createFieldConfigurationSql_String($field)
			);
		}

	// sql para criação da tabela da nova estrutura propriamente dita
	function testCreateTableSql(){
		$params = array(
			'field_one', 'field_two', 'field_three'
		);
		
        $sql = 'CREATE TABLE tabelaum('.
                   'id int auto_increment,'.
                   'oi,'.
                   'blocked varchar(120),'.
                   'approved int,'.
                   'created_on datetime,'.
                   'updated_on datetime,'.
                   'PRIMARY KEY (id), UNIQUE id (id)'.
                ')';
		$this->assertEquals($sql, $this->obj->createMainTableSql($params));
		$this->assertFalse($this->obj->createMainTableSql(array()));
	}

	// salva dados da nova estrutura na tabela 'categorias'
	function testSaveStructureIntoDatabase(){
		$this->fail();
	}
	
	function testExecuteQueriesForSavingStructures(){
		$this->fail();
	}

	function testGetStructureConfigurationSql(){
		$this->fail();
	}

	function testExecuteStructureConfigurationSql(){
		$this->fail();
	}

	/*
	 * MÉTODOS CHEFES
	 *
	 * Servem para chamar todos os métodos menores
	 */
	
	// Função multifuncional, serve tanto para criar novas
	// estruturas como para editar antigas.
	function testAddField(){

	}

	// Executa todas as funções necessárias para instalar
    function testCreateStructure(){

    }
	
}
?>