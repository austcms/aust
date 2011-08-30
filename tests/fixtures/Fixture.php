<?php
class Fixture {

	public $tables = array(
		"aust" 		=> "taxonomy",
		"users"		=> "admins",
		"modules"	=> "modules_installed",
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
				(admin_group_id, name, login, password, email)
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
	
	public function dropAllTables(){
		$tables = Connection::getInstance()->_acquireTablesList();
		foreach( $tables as $table ){
			$sql = "DROP TABLE ". $table;
			Connection::getInstance()->exec($sql);
		}
	}
	
	public function installSchema(){
		dbSchema::getInstance()->instalarSchema();
	}
	
	public function createStructures(){
		Connection::getInstance()->exec("DELETE FROM ".$this->tables["aust"]);
		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(name, name_encoded, class, admin_id)
			VALUES
				('Site', 'site', 'site', '1')
				";
		Connection::getInstance()->exec($aust);
		$this->siteId = Connection::getInstance()->lastInsertId();

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(name, name_encoded, type, father_id, admin_id)
			VALUES
				('News', 'news', 'textual', ".$this->siteId.", '1')
				";
		Connection::getInstance()->exec($aust);

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(name, name_encoded, class, type, father_id, admin_id)
			VALUES
				('News', 'news', 'structure', 'textual', ".$this->siteId.", '1')
				";
		Connection::getInstance()->exec($aust);
		$textsId = Connection::getInstance()->lastInsertId();

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(name, name_encoded, class, type, father_id, admin_id)
			VALUES
				('Calendar', 'calendar', 'structure', 'agenda', ".$this->siteId.", '1')
				";
		Connection::getInstance()->exec($aust);
	}
	
	public function createInstalledModules(){
		return true;
		$sql = 
			"INSERT INTO ".$this->tables["modules"]."
				(property, value, 		directory, 		name, 		admin_id)
			VALUES
				('dir', 'textual', 	'textual',  'Textual', '1'),
				('dir', 'agenda', 	'agenda', 	'Agenda',	'1')
				";
		Connection::getInstance()->exec($sql);
	}
	
	public function createApiData($paramsToInstall = array()){
		$this->destroy();
		installModule('flex_fields');
		
		$modelName = 'FlexFieldsSetup';
		$modDir = 'flex_fields';
		$mod = 'FlexFields';
		include_once MODULES_DIR.$modDir.'/'.$mod.'.php';
		include_once MODULES_DIR.$modDir.'/'.MOD_MODELS_DIR.$modelName.'.php';
		
		$flexFieldsSetup = new $modelName;
		$flexFields = new $mod;

		Connection::getInstance()->query("INSERT INTO taxonomy (name,class) VALUES ('Website777','site')");
		$lastInsert = Connection::getInstance()->lastInsertId();
		
		if( !empty($paramsToInstall) )
			$params = $paramsToInstall;
		else
			$params = array(
				'name' => 'News',
				'site' => $lastInsert,
				'class' => 'structure',
				'module' => 'flex_fields',
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
						'name' => 'Images',
						'type' => 'images',
						'description' => 'A field for images :)',
					),
					array(
						'name' => 'A song',
						'type' => 'files',
						'description' => 'A field for songs',
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
				(name, name_encoded, class, type, father_id, admin_id)
			VALUES
				('Articles', 'articles', 'structure', 'textual', ".$this->siteId.", '1')
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