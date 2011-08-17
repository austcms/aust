<?php
/**
 * Dispatches requests to the appropriated place.
 *
 * @since v0.2.0, 17/06/2011
 */
class Dispatcher {
	
	public $customController = "";
	public $controller;
	
	public function __construct(){
	}
	
	/**
	 * getInstance()
	 *
	 * Para Singleton
	 *
	 * @staticvar <object> $instance
	 * @return <Conexao object>
	 */
	static function getInstance(){
		static $instance;

		if( !$instance ){
			$instance[0] = new Dispatcher;
		}

		return $instance[0];
	}
	
	public function controller(){
		if( empty($_GET["section"]) )
			return "content";

		return $_GET["section"];
	}

	public function action(){
		if( empty($_GET["action"]) )
			return "index";

		return $_GET["action"];
	}
	
	public function dispatch(){

		$_GET['action'] = $this->action();
		$_GET['section'] = $this->controller();

		$hasCalledController = $this->callController();

		if( $hasCalledController )
			return true;

		ob_start();
		include($this->sectionFile());
		$content_for_layout = ob_get_contents();
		ob_end_clean();
		
		// show only view?
		$viewOnly = false;
		if( (
				!empty($_GET['viewonly'])
				AND $_GET['viewonly'] == 'yes'
			)
			OR 
			(
				!empty($_POST['viewonly'])
				AND $_POST['viewonly'] == 'yes'
			)
		)
		{
			$viewOnly = true;
		}

		if( $viewOnly == false
			AND (
				empty($_GET['theme'])
				OR $_GET['theme'] != 'blank'
			)
		)
		{
			include(UI_STANDARD_FILE);
		} else {
			echo $content_for_layout;
		}

	}
	
	function callController(){
		if( file_exists($this->controllerFile()) ){
			include_once($this->controllerFile());
			$controllerName = ucfirst($this->controller())."Controller";
			$this->controller = new $controllerName();
			return true;
		}
		return false;
	}

	function controllerFile(){
		if( UiPermissions::getInstance()->isPermittedSection() ){
			return CONTROLLERS_DIR.$this->controller()."_controller.php";
		}
		
		$_GET["section"] = MSG_CONTROLLER;
		$_GET["action"] = MSG_DENIED_ACCESS_ACTION;
		return CONTROLLERS_DIR.$this->controller()."_controller.php";
	}
	
	function sectionFile(){
		if( UiPermissions::getInstance()->isPermittedSection() )
			return INC_DIR . $this->controller() . '.inc.php';

		return MSG_DENIED_ACCESS;
	}
	
}
?>