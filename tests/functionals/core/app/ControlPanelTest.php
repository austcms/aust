<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ControlPanelTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
		require_once(CONTROLLERS_DIR."control_panel_controller.php");
    }

	public function testIndex(){
		
		/* only allowed users can access this page */
		Fixture::getInstance()->create();
		$_GET['section'] = "control_panel";
		$_GET['action'] = "index";

        $this->obj = new Dispatcher;
		$this->obj->dispatch();
		$rendered = $this->obj->controller->render();
		
		$this->obj = null;

		$this->assertRegExp('/permissão negada/', $rendered);

		$_GET['section'] = "control_panel";
		$_GET['action'] = "index";

		/* we login and we should access the page */
		login();

        $this->obj = new Dispatcher;
		$this->obj->dispatch();
		$rendered = $this->obj->controller->render();

		$this->assertRegExp('/Files/', $rendered);

		/* what about no modules installed? */
		$this->assertTrue(Connection::getInstance()->hasTable('modulos_conf'));
		Connection::getInstance()->exec("DELETE FROM modulos_conf");
        $this->obj = new Dispatcher;
		$this->obj->dispatch();
		$rendered = $this->obj->controller->render();

		$this->assertRegExp('/Files/', $rendered);


	}

}
?>