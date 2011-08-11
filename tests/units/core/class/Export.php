<?php
// require_once 'PHPUnit/Framework.php';

#####################################

require_once 'tests/config/auto_include.php';
require_once 'config/nav_permissions.php';

#####################################

class ExportTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $connection;

	public $populate = false;

	public $images = array(
		
	);
	
	public $lastSite;

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */

        // Conteúdos
        include MODULES_DIR.'conteudo/'.MOD_CONFIG;
        include_once MODULES_DIR.'conteudo/'.$modInfo['className'].'.php';
        $this->Conteudo = new Conteudo;

		// Cadastro Setup
        $modelName = 'CadastroSetup';
        include_once MODULES_DIR.'Cadastro/'.MOD_MODELS_DIR.$modelName.'.php';
        $this->CadastroSetup = new $modelName;

        // Cadastro
        include MODULES_DIR.'cadastro/'.MOD_CONFIG;
        include_once MODULES_DIR.'cadastro/'.$modInfo['className'].'.php';
        $this->Cadastro = new Cadastro;

        $this->obj = Export::getInstance();
        $this->aust = Aust::getInstance();

	}

	function populate(){
		Aust::getInstance()->connection->exec("INSERT INTO categorias (nome,classe,father_id) VALUES ('TestePai777','categoria-chefe','0')");
		$lastInsert = Aust::getInstance()->connection->lastInsertId();
		$this->lastSite = $lastInsert;
		
	    $params = array(
	        'father' => $lastInsert,
	        'name' => 'Teste777Conteudo',
	        'description' => 'Teste777',
	        'class' => 'estrutura',
	        'type' => 'conteudo',
	        'author' => '1',
	    );
		
		$result = Aust::getInstance()->createCategory($params);
		
		$params = array(
            'name' => 'Teste777Cadastro',
            'father' => $lastInsert,
            'class' => 'estrutura',
            'type' => 'cadastro',
            'author' => 1,
			'fields' => array(
				array(
					'name' => 'Campo 1777',
					'type' => 'string',
					'description' => 'haha777',
				),
				array(
					'name' => 'Campo 2777',
					'type' => 'relational_onetoone',
					'description' => 'haha777',
					'refTable' => 'ref_table',
					'refField' => 'ref_field',
				),
				array(
					'name' => 'Campo 3777',
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
		
		// Pega ID da estrutura salva
		$st = reset(Aust::getInstance()->connection->query("SELECT id FROM taxonomy WHERE nome='Teste777Conteudo'"));
		
		$stId = $st['id'];
		Aust::getInstance()->connection->exec("INSERT INTO ".Config::getInstance()->table." (type,local,property,value) VALUES('structure','$stId','teste777777','teste777777')");
		
		$result = $this->CadastroSetup->createStructure($params);		
    }

	function resetTables(){
		$this->obj->connection->exec("DELETE FROM taxonomy WHERE nome='Teste777Cadastro'");
		$this->obj->connection->exec("DELETE FROM taxonomy WHERE nome='Teste777Conteudo'");
		$this->obj->connection->exec("DELETE FROM taxonomy WHERE nome='TestePai777'");
		$this->obj->connection->exec("DELETE FROM taxonomy WHERE nome='Teste777'");
		$this->obj->connection->exec(
			"DELETE FROM
				flex_fields_config
			WHERE
				categorias_id='".$this->lastSite."' OR
				comentario='haha777' OR
				nome='haha777' OR
				valor IN ('haha777','teste777cadastro', 'Campo 1777', 'Campo 2777', 'Campo 3777')");
		$this->obj->connection->exec("DELETE FROM ".Config::getInstance()->table." WHERE property='teste777777'");
		$this->obj->connection->exec("DROP TABLE teste777cadastro");
		$this->obj->connection->exec("DROP TABLE teste777cadastro_ref_field_ref_table");
		
	}

	function testGetStructures(){
		$this->populate();
		$structures = $this->obj->getStructures();

		$hasSite = false;
		$hasConteudo = false;
		$hasCadastro = false;
		$hasConfig = false;
		
		foreach( $structures as $value ){
			
			// each site
			if( $value['Site']['nome'] == 'TestePai777' ){
				$hasSite = true;
			}
			$this->assertArrayNotHasKey('name', $value['Site'], 'name field shouldn\'t exist' );
			
			// each structure
			foreach( $value['Structures'] as $key=>$structure ){
				
				if( in_array($structure['nome'], array('Teste777Cadastro') ) ){
				
					if( $structure['classe'] != 'estrutura' )
						$this->fail('not a structure');
					else
						$hasCadastro = true;
					
				} else if( in_array($structure['nome'], array('Teste777Conteudo') ) ){
					$hasConteudo = true;
					
					$config = $structure['modConfig'][0];
					if( $config['valor'] == 'teste777777' )
						$hasConfig = true;
				}
				
			}
		}
		
		$this->assertTrue($hasSite, 'not saving the site');
		$this->assertTrue($hasConteudo, 'not creating Conteudo');
		$this->assertTrue($hasConfig, 'not creating Configuration');
		$this->assertTrue($hasCadastro, 'not creating Cadastro');

		$this->resetTables();
	}
	
	function testGetStructuresBySite(){
		$this->populate();
		
		$structures = $this->obj->getStructuresBySite($this->lastSite);
//		pr(json_encode($structures));
		foreach( $structures as $value ){
			
			if( $value['Site']['nome'] != 'TestePai777' )
				$this->fail('others sites are being saved');
			
			foreach( $value['Structures'] as $key=>$structure ){
				
				if( !in_array($structure['nome'], array('Teste777Cadastro', 'Teste777Conteudo') ) )
					$this->fail('failed getting structures other then the requested site');
				
			}
		}
		$this->resetTables();
	}

	function testGetStructuresBySiteAsJSON(){
		$this->populate();
		
		$structures = $this->obj->getStructuresBySite($this->lastSite);
		$generatedJson = $this->obj->json($structures);

		$this->assertRegExp("/(id\":\"[0..9]\")/i", $generatedJson);
		$this->assertRegExp("/(nome\":\"TestePai777\")/i", $generatedJson);
		$this->assertRegExp("/(nome\":\"Teste777Cadastro\")/i", $generatedJson);
		$this->assertRegExp("/(nome\":\"Teste777Conteudo\")/i", $generatedJson);
		$this->assertNotRegExp("/(name\":\"TestePai777)/i", $generatedJson, 'tem name na variável do site');
		$this->assertNotRegExp("/(name\":\"Teste777)/i", $generatedJson, 'tem name na variável das estruturas');
		$this->assertRegExp("/(referencia\":\"teste777cadastro_ref_field_ref_table\")/i", $generatedJson);
		
		// complete export
		$newJson = $this->obj->export(array('site'=>$this->lastSite));
		
		include( EXPORTED_FILE );
		$this->assertEquals( $json, $newJson, 'Not creating file with exported data.' );
		$this->resetTables();
	}
	
	function testImportation(){
		$this->populate();
		include(dirname(__FILE__).'/ExportPopulate.php');
		
		$importData = $this->obj->jsonToArray($json);

		// verifica integridade do JSON
		$testData = reset( $importData );
		$testData = $testData['Site'];
		$this->assertEquals( 'TestePai777', $testData['nome']);
		

		$this->obj->importSite(reset(json_decode($json, true)));
		
		// salvou o site?
			$conf = $this->obj->connection->query("SELECT * FROM taxonomy WHERE nome='TestePai777'");
			$this->assertEquals('0', $conf[0]['father_id'], 'Did not save the site.' );
			$siteId = $conf[0]['id'];

		// CONTEUDO
			$conf = $this->obj->connection->query("SELECT * FROM taxonomy WHERE nome='Teste777Conteudo'");
			$this->assertEquals($siteId, $conf[0]['father_id'], 'Did not save the conteudo.' );
			$this->assertEquals('estrutura', $conf[0]['classe'], 'Did not save the conteudo.' );
			$conteudoId = $conf[0]['id'];
		
			// CONTEUDO MODCONFIG
				$conf = $this->obj->connection->query("SELECT * FROM ".Config::getInstance()->table." WHERE local='$conteudoId' AND property='teste777777' LIMIT 1");
				$this->assertEquals('teste777777', $conf[0]['valor'], 'Not importing modConfig.' );
				
		// CADASTRO
			$conf = $this->obj->connection->query("SELECT * FROM taxonomy WHERE nome='Teste777Cadastro'");
			$this->assertEquals($siteId, $conf[0]['father_id'], 'Did not save the cadastro.' );
			$this->assertEquals('estrutura', $conf[0]['classe'], 'Did not save the cadastro.' );
			
			// criou tabelas tb?
			$this->assertTrue( $this->obj->connection->hasTable('teste777cadastro'), 'Não criou tabelas do cadastro.' );
			$this->assertTrue( $this->obj->connection->hasTable('teste777cadastro_ref_field_ref_table'), 'Não criou tabelas de referência do cadastro.' );
			$stId = $conf[0]['id'];
		
		$conf = $this->Cadastro->pegaInformacoesCadastro($stId);
		$this->assertEquals('teste777cadastro', $conf['estrutura']['tabela']['valor'], 'Did not save the cadastro\' conf.' );
		$this->assertEquals('campo_1777', $conf['campo']['campo_1777']['chave'], 'Did not save the cadastro\' field.' );
		$this->assertEquals('campo_2777', $conf['campo']['campo_2777']['chave'], 'Did not save the cadastro\' field.' );
		$this->assertEquals('campo_3777', $conf['campo']['campo_3777']['chave'], 'Did not save the cadastro\' field.' );
		$this->assertEquals( $stId, $conf['campo']['campo_3777']['categorias_id'], 'Did not save the austNode.' );
		
		$this->resetTables();
	}
	
	function testImportTwice(){
		$this->populate();
		include(dirname(__FILE__).'/ExportPopulate.php');
		
		$importData = $this->obj->jsonToArray($json);

		// verifica integridade do JSON
		$testData = reset( $importData );
		$testData = $testData['Site'];
		$this->assertEquals( 'TestePai777', $testData['nome']);
		

		$this->obj->importSite(reset(json_decode($json, true)));
		$this->obj->importSite(reset(json_decode($json, true)));

		$conf = $this->obj->connection->query("SELECT * FROM taxonomy WHERE classe='estrutura'");
		
		$existentSt = array();
		foreach( $conf as $value ){
			if( in_array($value['nome'], $existentSt) ){
				$this->fail('Salvando estrutura repetida.');
				break;
			}
			$existentSt[] = $value['nome'];
		}

		$conf = $this->obj->connection->query("SELECT * FROM flex_fields_config WHERE chave='campo_1777'");
		$this->assertEquals(1, count($conf), 'Salvando várias vezes a mesma configuração');

		$this->resetTables();
	}

}