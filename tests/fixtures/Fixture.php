<?php
class Fixture {

	public $tables = array(
		"aust" 		=> "categorias",
		"users"		=> "admins",
		"modules"	=> "modulos",
		"texts"		=> "textos"
	);
	
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
		
		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, classe, autor)
			VALUES
				('Site', 'site', 'categoria-chefe', '1')
				";
		Connection::getInstance()->exec($aust);
		$siteId = Connection::getInstance()->lastInsertId();

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, tipo, subordinadoid, autor)
			VALUES
				('News', 'news', 'conteudo', ".$siteId.", '1')
				";
		Connection::getInstance()->exec($aust);

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, classe, tipo, subordinadoid, autor)
			VALUES
				('News', 'news', 'estrutura', 'conteudo', ".$siteId.", '1')
				";
		Connection::getInstance()->exec($aust);
		$textsId = Connection::getInstance()->lastInsertId();

		$aust = 
			"INSERT INTO ".$this->tables["aust"]."
				(nome, nome_encoded, classe, tipo, subordinadoid, autor)
			VALUES
				('Calendar', 'calendar', 'estrutura', 'agenda', ".$siteId.", '1')
				";
		Connection::getInstance()->exec($aust);
		
		$texts = 
			"INSERT INTO ".$this->tables["texts"]."
				(categoria, titulo, titulo_encoded, texto, adddate, autor)
			VALUES
				(".$textsId.", 'New text', 'new_text', 'This is a new text for news.', '2011-06-21 11:58:00', '1')
				";
		Connection::getInstance()->exec($texts);		
	}
	
	public function createInstalledModules(){
		return true;
		$sql = 
			"INSERT INTO ".$this->tables["modules"]."
				(tipo, 		chave, valor, 		pasta, 		nome, 		embed,	autor)
			VALUES
				('módulo', 'dir', 'conteudo', 	'conteudo', 'Conteúdo', '0',	'1'),
				('módulo', 'dir', 'agenda', 	'agenda', 	'Agenda', 	'0',	'1')
				";
		Connection::getInstance()->exec($sql);
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