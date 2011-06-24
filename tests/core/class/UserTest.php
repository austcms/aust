<?php
require_once 'PHPUnit/Framework.php';

require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';

class UserTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $conexao;

    public function setUp(){
    	Fixture::getInstance()->create();

        $this->conexao = Connection::getInstance();
        $this->obj = new User;
    }

    public function testRedirectForbiddenSession(){
        $this->assertEquals($this->obj->forbiddenCode, '' );
        $this->assertFalse($this->obj->redirectForbiddenSession() );
    }

    public function testVerifySession(){
        $this->assertFalse($this->obj->verifySession() );
        // user is not logged
        $this->assertEquals($this->obj->forbiddenCode, '100' );
    }

    public function testBlockedLoggedUserOnRealTime(){

        $_SESSION['login']['id'] = 1;
        $_SESSION['login']['username'] = 'kurko';
        $this->assertTrue($this->obj->isLogged() );

        // bloqueia usuário
        $_SESSION['login']['is_blocked'] = 1;
        $this->assertFalse( $this->obj->verifySession() );
        $this->assertEquals($this->obj->forbiddenCode, '103' );
    }

    public function testIsLogged(){
        $this->assertFalse($this->obj->isLogged() );

        // vai logar
        $_SESSION['login']['id'] = 1;
        $_SESSION['login']['username'] = 'kurko';
        $this->assertTrue($this->obj->isLogged() );

        // algum erro no login
        $_SESSION['login']['id'] = 0;
        $this->assertFalse($this->obj->isLogged() );
    }

    public function testLeRegistro(){
		$query = Connection::getInstance()->query("SELECT id FROM admins LIMIT 1");
		$query = reset($query);
		$id = $query["id"];

        // connect
        $_SESSION['login']['id'] = $id;
        $_SESSION['login']['login'] = 'test_user';
        $this->assertTrue($this->obj->isLogged() );

        $this->assertGreaterThan( 0, $this->obj->LeRegistro('id') );

    }

 	function testReset(){
		$query = Connection::getInstance()->query("SELECT id FROM admins LIMIT 1");
		$query = reset($query);
		$id = $query["id"];

        // connect
        $_SESSION['login']['id'] = $id;
        $_SESSION['login']['username'] = 'test_user';
        $this->assertTrue($this->obj->isLogged() );

        $this->assertEquals($id, 			$this->obj->getId() 				);
        $this->assertEquals("test_user",	$this->obj->LeRegistro("login") 	);

		$this->obj->reset();
		
        $this->assertFalse( $this->obj->getId() 				);
        $this->assertFalse( $this->obj->type() 					);
        $this->assertFalse( $this->obj->LeRegistro("username") 	);
	}
	
	function testType(){
		$this->assertEquals("root", $this->obj->type("root"));
		$this->assertEquals("root", $this->obj->type());
		$this->assertEquals("root", $this->obj->tipo());
	}
	
	function testRootType(){
		$this->assertEquals("Webmaster", User::getInstance()->rootType());
	}
	
	function testHasUser(){
		$this->assertTrue($this->obj->hasUser());
    	Fixture::getInstance()->destroy();
		$this->assertFalse($this->obj->hasUser());
	}
    
}
?>