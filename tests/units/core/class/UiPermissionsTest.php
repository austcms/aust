<?php
// require_once 'PHPUnit/Framework.php';

require_once 'tests/config/auto_include.php';

require_once 'config/nav_permissions.php';

class UiPermissionsTest extends PHPUnit_Framework_TestCase
{

	public $dbConfig = array();

	public $conexao;

	public function setUp(){
	
		/*
		 * Informações de conexão com banco de dados
		 */
		
		
		$this->user = User::getInstance();
		$this->obj = UiPermissions::getInstance();
	}

	function testCanAccessWidgets(){
		$this->user->userInfo['group'] = "Administrador";
		$this->assertTrue( $this->obj->canAccessWidgets() );

		$this->user->userInfo['group'] = "Colaborador";
		$this->assertTrue( $this->obj->canAccessWidgets() );

		$this->user->userInfo['group'] = "Outro Usuário";
		$this->assertFalse( $this->obj->canAccessWidgets() );
	}


}
?>