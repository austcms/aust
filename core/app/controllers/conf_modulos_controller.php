<?php
class Conf_modulosController extends ActionController {
	function beforeFilter(){
		if( !empty($_POST['inserirestrutura']) &&
			(
                is_file( MODULES_DIR.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER )
                OR is_file( MODULES_DIR.$_POST['modulo'].'/setup.php' )
            )
		){
			$this->customAction = "setup";
		} else
			$this->customAction = "index";

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
                //'modDbSchema' => $modDbSchema,
            );
            $modulo = new $moduloNome($param);
            //unset( $modDbSchema );

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

            /**
             * SetupController
             *
             * Chama o controller que vai carregar o setup
             */
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
//            $setup = new SetupController( $_POST['modulo'], "setup", $params );
			
			$setup = new ModDispatcher( $_POST['modulo'], "setup");
			$setup->dispatch();

            unset($modulo);
        }
	}
}
?>