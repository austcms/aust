<?php
/**
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.5, 22/06/2009
 */
class ModActionController extends ActionController
{

	public $module;
	public $austNode;
	public $modDispatcher;
	/**
	 * 
	 * 
	 * @param $param:array
	 * 			'austNode':int
	 */
    function __construct($austNode){

		if( $austNode === false )
			return false;
		
		$this->austNode = $austNode;

		$action = $this->_action();
		if( empty($action) )
			return false;
		
		$this->modDispatcher = new ModDispatcher($this->austNode);
		$this->module = $this->modDispatcher->modelInstance();

		if( defined('DO_ACT') && !DO_ACT ){
			$this->shouldCallAction = false;
		}
		
        /**
         * $_POST e $_FILES:
         *
         * 'data': se alguma coisa for enviada para ser salva no DB
         */
        if( !empty($_POST["data"])){
            if( is_array($_POST["data"]) ){
                $this->{"data"} = $_POST["data"];
            }
        }
        if( !empty($_FILES["data"]) AND is_array($_FILES["data"])){
			// percorre os models
			foreach( $_FILES["data"]['name'] as $model=>$fields ){
				
				// percorre os campos de um model
				foreach( $fields as $fieldName=>$values ){
					
					// percorre o valor de cada campo
					foreach( $values as $key=>$value ){
						
						$type = $_FILES["data"]['type'][$model][$fieldName][$key];
						$tmp_name = $_FILES["data"]['tmp_name'][$model][$fieldName][$key];
						$error = $_FILES["data"]['error'][$model][$fieldName][$key];
						$size = $_FILES["data"]['size'][$model][$fieldName][$key];
						
						if( empty($value) OR
							$size == 0 OR
							empty($tmp_name) OR
							empty($type) )
							continue;
							
						$this->{"data"}[$model][$fieldName][$key]['name'] = $value;
						$this->{"data"}[$model][$fieldName][$key]['type'] = $type;
						$this->{"data"}[$model][$fieldName][$key]['tmp_name'] = $tmp_name;
						$this->{"data"}[$model][$fieldName][$key]['error'] = $error;
						$this->{"data"}[$model][$fieldName][$key]['size'] = $size;
					}
				}
			}
	
        }

		/*
	     * HELPERS
	     * 
	     * Cria helpers solicitados
	     */
	    if( count($this->helpers) ){
	        /**
	         * Loop por cada Helper a ser carregado
	         */
	        foreach($this->helpers as $valor){
	            unset( $$valor );
	            /**
	             * Inclui o arquivo do helper
	             */
	            include_once( HELPERS_DIR.$valor.CLASS_FILE_SUFIX.".php" );
	            $helperName = $valor.HELPER_CLASSNAME_SUFIX;
	            $$valor = new $helperName();
	            $this->set( strtolower($valor), $$valor);
	        }
	    }

		$this->_trigger();
    }

	function austNode($int = ""){
		if( empty($int) )
			return $this->austNode;
		elseif( is_numeric($int) )
			$this->austNode = $int;
		
		return true;
	}
	
    /*
     * PRIVATE METHODS
     */
	function _action(){
		if( $this->customAction )
			return $this->customAction;
			
		if( empty($_GET['action']) )
			return false;
		return $_GET['action'];
	}

	function _coreController(){
		if( empty($_GET['section']) )
			return false;
		return $_GET['section'];
	}
	
	function _actionExists(){
        return method_exists($this, $this->_action());
	}

	public function _setupParams(){
		$this->params["action"] = $this->_action();
	}


    protected function actions(){
        $this->set('aust', $this->aust);
        $this->render('actions', 'content_trigger');
    }

	public function _viewFile(){
		return MODULES_DIR.$this->module->directory().MOD_VIEW_DIR."mod/".$this->_action().".php";
	}
	
    public function test_action(){
		$this->testVar = 	"Action ". $this->params["action"] .
							" from module.";
		$this->autoRender = false;
	}
	
    /*
     * Renders the view
     */
    public function render( $shouldRender = true ){

		$this->set("austNode", $this->austNode());
		$this->set("module", $this->modDispatcher->modelInstance());
		
		parent::render();
	}

    /*
     *
     * MODELS
     *
     */
    /**
     * loadModel()
     *
     * Carrega models especiais do módulo atual. O model é alocado
     * em $this->{nome_do_model}.
     *
     * @param <string> $str
     * @return <bool>
     */
    public function loadModel($str = ""){

        if( !empty($this->{$str}) )
            return false;

        if( empty($str) )
            return false;
        if( !is_file(MODULES_DIR.$this->modDir.MOD_MODELS_DIR.$str.".php") )
            return false;

        include_once MODULES_DIR.$this->modDir.MOD_MODELS_DIR.$str.".php";
        $this->{$str} = new $str;

        return true;
    }

}

?>