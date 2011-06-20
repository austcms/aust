<?php
class ContentController extends ActionController {
	
	function beforeFilter(){
		if( !empty($_GET["aust_node"]) || !empty($_POST["aust_node"]) ){
			$this->customAction = "load_structure";
		}
	}
	
	function index(){
		
	}
	
	function load_structure(){
		/**
	     * AUST_NODE
	     *
	     * Ajusta $aust_node
	     */
	    if( !empty($_POST['aust_node']) ){
	        $aust_node = $_POST['aust_node'];
	    } else if( !empty($aust_node) ){
	        $aust_node = $aust_node;
	    } else if( !empty($_GET["aust_node"]) ){
	        $aust_node = $_GET['aust_node'];
	    }
	    // @todo - Módulos devem procurar por $aust_node, não $_GET['aust_node']
	    $_GET["aust_node"] = $aust_node;
		$austNode = $aust_node;

	    /*
	     * Tem permissão?
	     */
	    if( !StructurePermissions::getInstance()->verify($aust_node, $_GET['action']) ){

			echo '<p>Sem permissão para esta operação.</p><!-- content.inc -->';

			// tests post and alerts about post_max_size
			$data = file_get_contents('php://input');
			if( !empty($data) ){
				echo '<p>Provavelmente o tamanho dos dados enviados seja '+
					'maior do que o permitido. Verifique post_max_size, upload_max_filesize e max_file_uploads.</p>';
			}

	        exit();
		}

	    /**
	     * Identifica qual é a pasta do módulo responsável por esta
	     * estrutura/categoria
	     */
	    $modDir = Aust::getInstance()->LeModuloDaEstrutura($aust_node).'/';

	    /*
	     *
	     * INSTANCIA MÓDULO
	     *
	     */
	    /**
	     * Carrega arquivos principal do módulo requerido
	     */

     		include(MODULES_DIR.$modDir.MOD_CONFIG);
	        /**
	         * Carrega classe do módulo e cria objeto
	         */
	        $moduloNome = (empty($modInfo['className'])) ? 'Classe' : $modInfo['className'];
	        include(MODULES_DIR.$modDir.$moduloNome.'.php');

	        $modulo = new $moduloNome();
	        unset( $modDbSchema );

	    /**
	     * Module MVC?
	     */
	    if( !empty($modInfo['mvc']) AND $modInfo['mvc'] == true ){

	        /**
	         * ModController é o controller principal do módulo
	         */
	        include(MODULES_DIR.$modDir.MOD_CONTROLLER);

			$this->autoRender = false;
			$modDispatcher = new ModDispatcher($aust_node);
			$modDispatcher->dispatch();
			return true;
			
			
			
			
			

	        $modController = new ModController($param);

			
		}
	}
	
}
?>