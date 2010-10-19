<?php
require_once 'PHPUnit/Framework.php';

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

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        require('tests/config/database.php');

        // Conteúdos
        include 'modulos/conteudo/'.MOD_CONFIG;
        include_once 'modulos/conteudo/'.$modInfo['className'].'.php';
        $this->Conteudo = new Conteudo;

		// Cadastro Setup
        $modelName = 'CadastroSetup';
        include_once 'modulos/Cadastro/'.MOD_MODELS_DIR.$modelName.'.php';
        $this->CadastroSetup = new $modelName;

        // Cadastro
        include 'modulos/cadastro/'.MOD_CONFIG;
        include_once 'modulos/cadastro/'.$modInfo['className'].'.php';
        $this->Cadastro = new Cadastro;

        $this->obj = Export::getInstance();
        $this->aust = Aust::getInstance();

	}

	function populate(){
		$this->aust->connection->query("INSERT INTO categorias (nome,classe,subordinadoid) VALUES ('TestePai777','categoria-chefe','0')");
		$lastInsert = $this->aust->connection->lastInsertId();
		$this->lastSite = $lastInsert;
		
	    $params = array(
	        'father' => $lastInsert,
	        'name' => 'Teste777Conteudo',
	        'description' => 'Teste777',
	        'class' => 'estrutura',
	        'type' => 'conteudo',
	        'author' => '1',
	    );
		
		$result = $this->aust->create($params);
		
		$params = array(
            'name' => 'Teste777Cadastro',
            'father' => $lastInsert,
            'class' => 'estrutura',
            'type' => 'cadastro',
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
		
		$result = $this->CadastroSetup->createStructure($params);		
    }

	function resetTables(){
		$this->obj->connection->exec("DELETE FROM categorias WHERE nome='Teste777Cadastro'");
		$this->obj->connection->exec("DELETE FROM categorias WHERE nome='Teste777Conteudo'");
		$this->obj->connection->exec("DELETE FROM categorias WHERE nome='TestePai777'");
		$this->obj->connection->exec("DELETE FROM categorias WHERE nome='Teste777'");
		$this->obj->connection->exec("DELETE FROM cadastros_conf WHERE categorias_id='".$this->lastSite."' OR comentario='haha777' OR valor IN ('haha777','teste777cadastro')");
		$this->obj->connection->exec("DROP TABLE teste777cadastro");
		$this->obj->connection->exec("DROP TABLE teste777cadastro_ref_field_ref_table");
		
	}

	function testGetStructures(){
		$this->populate();
		$structures = $this->obj->getStructures();

		$hasSite = false;
		$hasConteudo = false;
		$hasCadastro = false;
		
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
				}
				
			}
		}
		
		$this->assertTrue($hasSite, 'not saving the site');
		$this->assertTrue($hasConteudo, 'not creating Conteudo');
		$this->assertTrue($hasCadastro, 'not creating Cadastro');

		$this->resetTables();
	}
	
	function testGetStructuresBySite(){
		$this->populate();
		
		$structures = $this->obj->getStructuresBySite($this->lastSite);
//		pr($structures);
		foreach( $structures as $value ){
			
			if( $value['Site']['nome'] != 'TestePai777' )
				$this->fail('others sites are being saved');
			
			foreach( $value['Structures'] as $key=>$structure ){
				
				if( !in_array($structure['nome'], array('Teste777Cadastro', 'Teste777Conteudo') ) )
					$this->fail('getting structures other then the requested site');
				
			}
		}
		$this->resetTables();
	}

	function testGetStructuresBySiteAsJSON(){
		$this->populate();
		
		$structures = $this->obj->getStructuresBySite($this->lastSite);
		$json = $this->obj->json($structures);

		$this->assertRegExp("/(id\":\"[0..9]\")/i", $json);
		$this->assertRegExp("/(nome\":\"TestePai777\")/i", $json);
		$this->assertRegExp("/(nome\":\"Teste777Cadastro\")/i", $json);
		$this->assertRegExp("/(nome\":\"Teste777Conteudo\")/i", $json);
		$this->assertNotRegExp("/(name\":\"TestePai777)/i", $json, 'tem name na variável do site');
		$this->assertNotRegExp("/(name\":\"Teste777)/i", $json, 'tem name na variável das estruturas');
		$this->assertRegExp("/(referencia\":\"teste777cadastro_ref_field_ref_table\")/i", $json);
		
		// complete export
		$json = $this->obj->export(array('site'=>$this->lastSite));
		$this->resetTables();
	}
	
	function testImportation(){
		include(dirname(__FILE__).'/ExportPopulate.php');
		
		$importData = $this->obj->jsonToArray($json);

		// verifica integridade do JSON
		$testData = reset( $importData );
		$testData = $testData['Site'];
		$this->assertEquals( 'TestePai777', $testData['nome']);

		$this->obj->importSite(reset(json_decode($json, true)));
		
		// salvou o site?
		$conf = $this->obj->connection->query("SELECT * FROM categorias WHERE nome='TestePai777'");
		$this->assertEquals('0', $conf[0]['subordinadoid'], 'Did not save the site.' );
		$siteId = $conf[0]['id'];
		
		$conf = $this->obj->connection->query("SELECT * FROM categorias WHERE nome='Teste777Conteudo'");
		$this->assertEquals($siteId, $conf[0]['subordinadoid'], 'Did not save the conteudo.' );
		$this->assertEquals('estrutura', $conf[0]['classe'], 'Did not save the conteudo.' );
		
		$conf = $this->obj->connection->query("SELECT * FROM categorias WHERE nome='Teste777Cadastro'");
		$this->assertEquals($siteId, $conf[0]['subordinadoid'], 'Did not save the cadastro.' );
		$this->assertEquals('estrutura', $conf[0]['classe'], 'Did not save the cadastro.' );
		
//		pr($importData);
		
		$this->resetTables();
	}

}