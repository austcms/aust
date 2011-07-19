<?php
class Control_panelController extends ActionController {
	
	var $directory;
	
	function beforeFilter(){
		if( !empty($_POST['inserirestrutura']) &&
			(
                is_file( MODULES_DIR.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER )
                OR is_file( MODULES_DIR.$_POST['modulo'].'/setup.php' )
            )
		){
			$this->customAction = "setup";
		} 
		#else
#			$this->customAction = "index";

	}
	
	function index(){
		$sites = Aust::getInstance()->getStructures();
		$this->set('sites', $sites);
	}
	
	function structure_configuration(){

        $this->directory = MODULES_DIR.Aust::getInstance()->LeModuloDaEstrutura($_GET['aust_node'])."/";
		$module = ModulesManager::getInstance()->modelInstance($_GET["aust_node"]);
		$this->set("module", $module);
		
	}
	
	function setup(){
        if( is_file( MODULES_DIR.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER ) ){

            $modDir = $_POST['modulo'].'/';
            include(MODULES_DIR.$modDir.MOD_CONFIG);
            /**
             * Carrega classe do módulo e cria objeto
             */
            $moduloNome = (empty($modInfo['className'])) ? 'Classe' : $modInfo['className'];
            include_once(MODULES_DIR.$modDir.$moduloNome.'.php');

            $param = array(
                'config' => $modInfo,
                'user' => User::getInstance(),
            );
            $modulo = new $moduloNome($param);

            /**
             * JAVASCRIPT
             *
             * Carrega scripts javascript
             */
            //if(is_file(MODULES_DIR.$_POST['modulo'].'/js/jsloader.php')){
            //    $include_baseurl = MODULES_DIR.$_POST['modulo']; // necessário para o arquivo jsloader.php saber onde está fisicamente
            //    include_once(MODULES_DIR.$_POST['modulo'].'/js/jsloader.php');
            //}
            include(MODULES_DIR.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER);

            $setupAction = ( empty( $_POST['setupAction'] ) ) ? '' : $_POST['setupAction'];
            /**
             * 'action': $setupAction contém informações de $_POST['setupAction']
             * 'exPOST': possui $_POST enviados anteriormente.
             */
            $params = array(
                'modDir' => $_POST['modulo'],
                'action' => $setupAction,
                'exPOST' => $_POST,
            );
			$this->autoRender = false;
			
			$setup = new ModDispatcher( $_POST['modulo'], "setup");
			$setup->dispatch();

            unset($modulo);
        }
	}
}
?>