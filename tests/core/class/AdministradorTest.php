<?php
require_once 'PHPUnit/Framework.php';

#####################################

require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';

#####################################

class AdministradorTest extends PHPUnit_Framework_TestCase
{

    public $dbConfig = array();

    public $conexao;

    public function setUp(){
    
        /*
         * Informações de conexão com banco de dados
         */
        
        //$this->dbConfig = $dbConn;
        
        $this->conexao = Connection::getInstance();
        $this->obj = User::getInstance();
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

        // conecta
        $_SESSION['login']['id'] = 1;
        $_SESSION['login']['username'] = 'kurko';
        $this->assertTrue($this->obj->isLogged() );

        $this->assertGreaterThan( 0, $this->obj->LeRegistro('id') );

    }

    
}
?>