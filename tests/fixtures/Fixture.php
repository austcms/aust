<?php
class Fixture {

	public $tables = array(
		"aust" 		=> "categorias",
		"users"		=> "admins",
		"modules"	=> "modulos",
	);
	
	public $siteId;
	
	function __construct(){
		$this->destroy();
		$this->create();
	}
	
	public function create(){
		$this->destroy();
		
		$authors[] = 
			"INSERT INTO admins
				(tipo, nome, login, senha, email)
			VALUES
				('1', 'Test User', 'test_user', '123', 'testuser@austtest.com')
				";

		Connection::getInstance()->exec($authors[0]);
		
		# categories
		$this->createStructures();
			
		# modules
		$this->createInstalledModules();
		
		return true;
	}
	
	public function destroy(){
		foreach( $this->tables as $table ){
			$sql = "DELETE FROM ". $table;
			Connection::getInstance()->exec($sql);
		}
	}
	
	public function createStructures(){
		Connection::getInstance()->exec("DELETE FROM ".$this->tables["aust"]);
		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, classe, autor)
			VALUES
				('Site', 'site', 'categoria-chefe', '1')
				";
		Connection::getInstance()->exec($aust);
		$this->siteId = Connection::getInstance()->lastInsertId();

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, tipo, subordinadoid, autor)
			VALUES
				('News', 'news', 'textual', ".$this->siteId.", '1')
				";
		Connection::getInstance()->exec($aust);

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, classe, tipo, subordinadoid, autor)
			VALUES
				('News', 'news', 'estrutura', 'textual', ".$this->siteId.", '1')
				";
		Connection::getInstance()->exec($aust);
		$textsId = Connection::getInstance()->lastInsertId();

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, classe, tipo, subordinadoid, autor)
			VALUES
				('Calendar', 'calendar', 'estrutura', 'agenda', ".$this->siteId.", '1')
				";
		Connection::getInstance()->exec($aust);
	}
	
	public function createInstalledModules(){
		return true;
		$sql = 
			"INSERT INTO ".$this->tables["modules"]."
				(tipo, 		chave, valor, 		pasta, 		nome, 		embed,	autor)
			VALUES
				('módulo', 'dir', 'textual', 	'textual',  'Textual', '0',	'1'),
				('módulo', 'dir', 'agenda', 	'agenda', 	'Agenda', 	'0',	'1')
				";
		Connection::getInstance()->exec($sql);
	}
	
	public function createApiData(){
		$this->destroy();
		installModule('flex_fields');
		
        $modelName = 'FlexFieldsSetup';
        $modDir = 'flex_fields';
        $mod = 'FlexFields';
        include_once MODULES_DIR.$modDir.'/'.$mod.'.php';
        include_once MODULES_DIR.$modDir.'/'.MOD_MODELS_DIR.$modelName.'.php';
        
        $flexFieldsSetup = new $modelName;
        $flexFields = new $mod;

		Connection::getInstance()->query("INSERT INTO categorias (nome,classe) VALUES ('Website777','categoria-chefe')");
		$lastInsert = Connection::getInstance()->lastInsertId();
		
		$params = array(
            'name' => 'News',
            'father' => $lastInsert,
            'class' => 'estrutura',
            'type' => 'flex_fields',
            'author' => 1,
			'fields' => array(
				array(
					'name' => 'Title',
					'type' => 'string',
					'description' => 'Description',
				),
				array(
					'name' => 'Text',
					'type' => 'text',
					'description' => 'Description',
				),
				array(
					'name' => 'Relational 1-n-1',
					'type' => 'relational_onetoone',
					'description' => 'haha777',
					'refTable' => 'ref_table',
					'refField' => 'ref_field',
				),
				array(
					'name' => 'Relational 1-to-n',
					'type' => 'relational_onetomany',
					'description' => 'haha777',
					'refTable' => 'ref_table',
					'refField' => 'ref_field',
				),
			),
			'options' => array(
				'approval' => '',
				'pre_password' => '',
				'description' => '',
			),
			
		);
		
		$result = $flexFieldsSetup->createStructure($params);
		return $result;
	}
	
	public function createApiTextualData(){
		$this->createStructures();
		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, classe, tipo, subordinadoid, autor)
			VALUES
				('Articles', 'articles', 'estrutura', 'textual', ".$this->siteId.", '1')
				";
		Connection::getInstance()->exec($aust);
		return Connection::getInstance()->lastInsertId();
	}
	
	static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new Fixture;
        }

        return $instance[0];
    }
    
}
?>