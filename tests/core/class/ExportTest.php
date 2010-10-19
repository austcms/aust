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
        $this->mod1 = new Conteudo;

        // Conteúdos
        include 'modulos/cadastro/'.MOD_CONFIG;
        include_once 'modulos/cadastro/'.$modInfo['className'].'.php';
        $this->mod2 = new Cadastro;

        $this->obj = Export::getInstance();
        $this->aust = Aust::getInstance();

		$this->aust->connection->query("INSERT INTO categorias (nome,classe,subordinadoid) VALUES ('TestePai777','categoria-chefe','0')");
		$lastInsert = $this->aust->connection->lastInsertId();
		
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
	        'father' => $lastInsert,
	        'name' => 'Teste777Cadastro',
	        'description' => 'Teste777',
	        'class' => 'estrutura',
	        'type' => 'cadastro',
	        'author' => '1',
	    );
		
		$result = $this->aust->create($params);
		
    }

	function tearDown(){
		$this->obj->connection->exec("DELETE FROM categorias WHERE nome='Teste777Cadastro'");
		$this->obj->connection->exec("DELETE FROM categorias WHERE nome='Teste777Conteudo'");
		$this->obj->connection->exec("DELETE FROM categorias WHERE nome='TestePai777'");
		$this->obj->connection->exec("DELETE FROM categorias WHERE nome='Teste777'");
		
	}

	function testGetStructures(){

		$structures = $this->obj->getStructures();

		$hasSite = false;
		$hasConteudo = false;
		$hasCadastro = false;
		
		foreach( $structures as $value ){
			
			// each site
			if( $value['Site']['name'] == 'TestePai777' ){
				$hasSite = true;
			}
			
			// each structure
			foreach( $value['Structures'] as $key=>$structure ){
				
				if( in_array($structure['name'], array('Teste777Cadastro') ) ){
					$hasCadastro = true;
				} else if( in_array($structure['name'], array('Teste777Conteudo') ) ){
					$hasConteudo = true;
				}
				
			}
		}
		
		$this->assertTrue($hasSite, 'not saving the site');
		$this->assertTrue($hasConteudo, 'not creating Conteudo');
		$this->assertTrue($hasCadastro, 'not creating Cadastro');

	}
	
	function testGetConteudo(){
		
	}

}