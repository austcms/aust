<?php
/**
 * ACTIVE CONTROLLER
 *
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.2, 17/06/2011
 */
class ActionController
{

	public $autoRender = true;
	public $isRendered = false;

	public $completedRequest = false;
	
	public $customAction = false;
	public $params = array();

    public $globalVars = array();

	public $shouldCallAction = true;
	public $beforeFiltered = false;
	public $afterFiltered = false;

	public $testVar;

    function __construct($shouldCallAction = true){
        $this->shouldCallAction = $shouldCallAction;
        /**
         * _trigger() is responsible for triggering methods as actions
         */
        $this->_trigger();
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
     *      'ation': which method should be called
     */
    public function _trigger(){
        $this->beforeFilter();
		
		$this->_setupParams();
        /*
         * Action time!
         */
        if( $this->_actionExists() )
            call_user_func_array( array($this, $this->_action() ), array() );

	    $this->afterFilter();

        if( !$this->isRendered AND $this->autoRender && $this->_action() && $this->shouldCallAction )
            $this->render( $this->_action() );
        else if( !$this->isRendered )
            $this->render( false );

    }

    /*
     * Renders the view
     */
    public function render( $shouldRender = true ){

        /*
         * Variables for views
         */
        foreach( $this->globalVars as $key=>$value ){
            $$chave = $valor;
        }
        
        $content_for_layout = "";
		
		if( empty($this->params) )
			$this->_setupParams();
		
		$params = $this->params;
		
		$viewFile = VIEWS_DIR."".Dispatcher::getInstance()->controller()."/".$this->_action().".php";

		$defaultErrorReporting = ini_get("error_reporting");

        if( $shouldRender && file_exists($viewFile) ){

            ob_start();
            include(VIEWS_DIR."".Dispatcher::getInstance()->controller()."/".$this->_action().".php");
            $content_for_layout = ob_get_contents();
            ob_end_clean();

            ob_start();
			include(UI_STANDARD_FILE);
            $content = ob_get_contents();
            ob_end_clean();
			
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