<?php
/**
 * ACTIVE CONTROLLER
 *
 * @since v0.2, 17/06/2011
 */
class ActionController
{

	public $helpers = array();
	public $autoRender = true;
	public $isRendered = false;

	public $completedRequest = false;
	
	public $customAction = false;
	public $params = array();

	public $globalVars = array();

	public $shouldCallAction = true;
	public $beforeFiltered = false;
	public $afterFiltered = false;
	public $renderized = "";
	
	public $testVar;

	function __construct($shouldCallAction = true){
		$this->shouldCallAction = $shouldCallAction;
		/**
		 * _trigger() is responsible for triggering methods as actions
		 */
		$this->_trigger($shouldCallAction);
		$this->completedRequest = true;
	}

	
	public function set($varName, $varValue){
		$this->globalVars[$varName] = $varValue;
	}

	/*
	 * PRIVATE METHODS
	 */
	function _action(){
		if( $this->customAction )
			return $this->customAction;
		return Dispatcher::getInstance()->action();
	}

	function _controllerPathName(){
		$controllerName = get_class($this);
		$controllerName = str_replace("Controller", "", $controllerName);
		$controllerName = strtolower($controllerName);
		return $controllerName;
	}
	
	function _actionExists(){
		return method_exists($this, $this->_action());
	}

	public function _setupParams(){
		$this->params["controller"] = Dispatcher::getInstance()->controller();
		$this->params["action"] = $this->_action();
	}
	
	/**
	 * _TRIGGER()
	 *
	 * Responsible for calling actions, preppending beforeFilter() and appending
	 * afterFilter() and calling render().
	 *
	 * @param array $param
	 *	  'ation': which method should be called
	 */
	public function _trigger($shouldCallAction = true){
		$this->beforeFilter();
		
		$this->_setupParams();
		/*
		 * Action time!
		 */
		if( $this->_actionExists() && $shouldCallAction ){
			call_user_func_array( array($this, $this->_action() ), array() );
		}

		$this->afterFilter();

		if( !$this->isRendered AND $this->autoRender && $this->_action() && $this->shouldCallAction )
			$this->render( $this->_action() );
		else if( !$this->isRendered && $this->autoRender )
			$this->render( false );

	}

	public function _viewFile(){
		return VIEWS_DIR."".Dispatcher::getInstance()->controller()."/";
	}
	
	/*
	 * Renders the view
	 */
	public function render( $shouldRender = true ){

		/*
		 * Variables for views
		 */
		foreach( $this->globalVars as $key=>$value ){
			$$key = $value;
		}
		
		$content_for_layout = "";
		
		if( empty($this->params) )
			$this->_setupParams();
		
		$params = $this->params;
		$defaultErrorReporting = ini_get("error_reporting");
		
		if( is_string($shouldRender) ){
			$viewFile = $this->_viewFile().$shouldRender.".php";
		} else {
			$viewFile = $this->_viewFile().$this->_action().".php";
		}

		if( $shouldRender && !file_exists($viewFile) ){
			trigger_error("Module's view (".$viewFile.") doesn't exist");
		}

		if( $shouldRender && file_exists($viewFile) ){

			ob_start();
			include($viewFile);
			$content_for_layout = ob_get_contents();
			ob_end_clean();

			ob_start();
			include(UI_STANDARD_FILE);
			$content = ob_get_contents();
			ob_end_clean();
			
			$this->renderized = $content;
			
			if( $shouldRender && !empty($content) ){
				
				if( !defined('TESTING') || !TESTING )
					echo $content;
				
				$this->isRendered = true;
			}
			
			return $content;
		}

		return false;
	}

	public function beforeFilter(){ $this->beforeFiltered = true; return true; }
	public function afterFilter(){ $this->afterFiltered = true; return true; }
	public function test_action(){
		$this->testVar = 	"Action ". $this->params["action"] .
							" from controller ".$this->params["controller"]." working.";
		$this->autoRender = false;
	}

}

?>