<?php
/**
 * Dispatches requests to the appropriated place.
 *
 * @since v0.2.0, 17/06/2011
 */
class ModDispatcher
{
	
	public $module;
	public $austNode;
	public $controller = "mod";
	
	public function __construct($austNode, $controller = "mod"){
		$this->controller = $controller;
		
		$this->austNode = $austNode;

		$this->module = ModulesManager::getInstance()->modelInstance($austNode);
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
			$instance[0] = new ModDispatcher;
		}

		return $instance[0];
	}

	public function directory(){
		return ModulesManager::getInstance()->directory($this->austNode);
	}

	public function action(){
		if( empty($_GET["action"]) )
			return "index";

		return $_GET["action"];
	}

	public function controller(){
		return $this->controller;
	}
	
	public function dispatch(){
		$_GET['action'] = $this->action();

		return $this->callController();
	}
	
	function callController(){
		if( file_exists($this->controllerFile()) ){
			include_once($this->controllerFile());
			$controllerName = ucfirst($this->controller())."Controller";
			$controller = new $controllerName($this->austNode);
			return true;
		}
		return false;
	}

	function controllerFile(){
		return MODULES_DIR.$this->directory().MOD_CONTROLLER_DIR."mod_controller.php";
	}
	
	function sectionFile(){
		if( UiPermissions::getInstance()->isPermittedSection() )
			return INC_DIR . $this->controller() . '.inc.php';

		return MSG_DENIED_ACCESS;
	}
	
}
?>