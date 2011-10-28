<?php
require_once 'tests/config/auto_include.php';
include_once 'modulos/cadastro/Cadastro.php';
include_once HOOKS_DIR.'sql_executer/SqlExecuterHook.php';

class SqlExecuterTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
        $modelName = 'CadastroSetup';
        $mod = 'Cadastro';
        include_once 'modulos/'.$mod.'/'.MOD_MODELS_DIR.$modelName.'.php';
        $this->structure = new $modelName;


		$this->obj = new SqlExecuterHook();
		
		$this->createStructure();
    }

	function createStructure(){
		Connection::getInstance()->exec("DELETE FROM admins");
		Connection::getInstance()->exec("DELETE FROM categorias");
		Connection::getInstance()->exec("DELETE FROM testunit");

		$sql = "INSERT INTO admins (nome, login, email, senha) VALUES ('Alexandre', 'kurko', 'my_email@gmail.com', '123')";
		Connection::getInstance()->exec($sql);
		$this->lastAdmin = Connection::getInstance()->lastInsertId();

		Connection::getInstance()->exec("INSERT INTO categorias (nome,classe) VALUES ('TestePai777','categoria-chefe')");
		$lastInsert = Connection::getInstance()->lastInsertId();
		
		$params = array(
            'name' => 'TestUnit',
            'father' => $lastInsert,
            'class' => 'estrutura',
            'type' => 'cadastro',
            'author' => 1,
			'fields' => array(
				array(
					'name' => 'Name',
					'type' => 'string',
					'description' => 'haha777',
				),
				array(
					'name' => 'Email',
					'type' => 'string',
					'description' => 'haha777',
				),
			),
			'options' => array(
				'approval' => 'haha777',
				'pre_password' => 'haha777',
				'description' => 'haha777',
			),
			
		);
		
		$result = $this->structure->createStructure($params);
		$austNode = $this->structure->austNode;
		$this->austNode = $austNode;
		$austNodeIsNumeric = is_numeric($austNode);
		$this->assertTrue($austNodeIsNumeric);
		
		Connection::getInstance()->exec("DELETE FROM testunit");
		Connection::getInstance()->exec("INSERT INTO testunit(name,email) VALUES ('Other user','email2@gmail.com')");
		Connection::getInstance()->exec("INSERT INTO testunit(name,email) VALUES ('Alexandre','email@gmail.com')");
		$this->selfId = Connection::getInstance()->lastInsertId();
	}

	function testSetUp(){
		$this->assertTrue($this->lastAdmin > 0);
	}

    function testPerform(){
		$perform = array(
		    'hook_engine' => 'sql_executer',
		    'when_action' => 'approve_record',
		    'node_id' => $this->austNode,
		    'perform' => "UPDATE testunit SET name='lxndr' WHERE id='{self.id}'"
		);
		$changed = Connection::getInstance()->query("SELECT name FROM testunit WHERE id='".$this->selfId."'");
		$this->assertEquals( "Alexandre", $changed[0]["name"] );

		$this->obj->perform($this->selfId, $perform);
		
		$changed = Connection::getInstance()->query("SELECT name FROM testunit WHERE id='".$this->selfId."'");
		$this->assertEquals( "lxndr", $changed[0]["name"] );
	}
	
    function testCleanUpText(){
		$perform = "Configuration: {to:sql(SELECT email FROM testunit)} more {hey} text.";
		$expected = "Configuration:  more  text.";
		$this->assertEquals( $expected, $this->obj->cleanUpText($perform) );
	}
	
    function testSqlFunction(){
		$perform = "Not to the script {to:sql(SELECT email FROM testunit)}";
		$expected = "Not to the script {to:email2@gmail.com;email@gmail.com}";
		$this->assertEquals( $expected, $this->obj->getSqlFunction($perform) );

		$perform = "Your new email is: sql(SELECT email FROM testunit LIMIT 1)";
		$expected = "Your new email is: email2@gmail.com";
		$this->assertEquals( $expected, $this->obj->getSqlFunction($perform) );
	}


    function testSelfData(){
		$perform = "{to:sql(SELECT a.email FROM admin a WHERE id={self.id} OR login='{self.email}')}";
		$expected = "{to:sql(SELECT a.email FROM admin a WHERE id=".$this->selfId." OR login='email@gmail.com')}";
		$this->assertEquals( $expected, $this->obj->getSelfData($this->selfId, $this->austNode, $perform) );

		$perform = "hey {to:self.email)} haha";
		$expected = "hey {to:self.email)} haha";
		$this->assertEquals( $expected, $this->obj->getSelfData($this->selfId, $this->austNode, $perform) );

		$perform = "hey {to:{self.email})} haha";
		$expected = "hey {to:email@gmail.com)} haha";
		$this->assertEquals( $expected, $this->obj->getSelfData($this->selfId, $this->austNode, $perform) );
	}
}
?>