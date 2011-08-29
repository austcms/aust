<?php
class Control_panelController extends ActionController {
	
	public $directory;
	
	function beforeFilter(){
		if( !empty($_POST['inserirestrutura']) &&
			(
				is_file( MODULES_DIR.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER )
				OR is_file( MODULES_DIR.$_POST['modulo'].'/setup.php' )
			)
		){
			$this->customAction = "setup";
		} 
	}
	
	function index(){
		
		$sites = Aust::getInstance()->getStructures();
		$this->set('sites', $sites);
		$migrationsMods = new MigrationsMods( Connection::getInstance() );
		$this->set('migrationsMods', $migrationsMods);
		
		if( !empty($_GET['instalar_modulo'])
			AND is_dir(MODULES_DIR.$_GET['instalar_modulo']) )
		{
			$this->install_module();
		}
		
		/*
		 * INSTALAR ESTRUTURA SEM SETUP.PHP PRÓPRIO VIA CORE DO AUST
		 *
		 * Se instalar uma estrutura a partir de um módulo com setup.php próprio, faz include neste arquivo para configuração
		 */
		if(!empty($_POST['inserirestrutura'])  AND !is_file(MODULES_DIR.$_POST['modulo'].'/setup.php')) {
			$this->install_structure();
		}
		
		$hasStructures = false;
		foreach( $sites as $site )
			if( !empty($site['Structures']) )
				$hasStructures = true;
				
		$this->set('hasStructures', $hasStructures);

		$this->render("index");
	}
	
	function install_structure(){
		$result = Aust::getInstance()->createStructure(
						array(
							'name'		=> $_POST['nome'],
							'site'		=> $_POST['categoria_chefe'],
							'public'	=> $_POST['publico'],
							'module'	=> $_POST['modulo'],
							'author'	=> User::getInstance()->LeRegistro('id')
						)
					);

		if( $result )
			notice('Sucesso: Estrutura instalada com sucesso!');
		else
			failure('Ocorreu um erro desconhecido. Tente novamente.');

		redirect("section=".CONTROL_PANEL_DISPATCHER);
	}
	
	function install_module(){

		$path = $_GET['instalar_modulo'];
		/**
		 * Carrega arquivos dos módulos
		 */
	 	include_once(MODULES_DIR.$path.'/'.MOD_CONFIG);

		$modName = MigrationsMods::getInstance()->getModNameFromPath(MODULES_DIR.$path);
		/**
		 * Ajusta variáveis para gravação
		 */
			/**
			 * ['structure_only'] indica se a estrutura conterá categorias ou não.
			 */
			$modInfo['structure_only'] = (empty($modInfo['structure_only'])) ? false : $modInfo['structure_only'];

		/*
		 * Caso o módulo não tenha migrations, faz a verificação normal das tabelas
		 * a partir de schemas, o que não é recomendado.
		 */
		if( MigrationsMods::getInstance()->hasMigration($path) ){
			$installStatus = MigrationsMods::getInstance()->updateMigration($path);
			$isInstalled = MigrationsMods::getInstance()->isActualVersion($path);

			$param = array(
				'property' => 'dir',
				'value' => $modName,
				'directory' => $path,
				'modInfo' => $modInfo,
				'admin_id' => User::getInstance()->LeRegistro('id'),
			);
			ModulesManager::getInstance()->configureModule($param);

			notice('Migrations executados com sucesso!');
		} else {
			failure('Este módulo não possui Migrations.');
		}
		
	}
	
	function structure_configuration(){

		$this->directory = MODULES_DIR.Aust::getInstance()->structureModule($_GET['aust_node'])."/";
		$module = ModulesManager::getInstance()->modelInstance($_GET["aust_node"]);
		$configurations = $module->loadModConf();

		$this->set("module", $module);
		$this->set("configurations", $configurations);

		if( !empty($_POST['conf_type']) &&
			$_POST['conf_type'] == "structure" )
		{
			$saved = $module->saveModConf($_POST);
			if( $saved )
				notice("Configurações salvas com sucesso.");
			elseif( $saved === false )
				failure("Ocorreu um erro");

		}
		
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
			//	$include_baseurl = MODULES_DIR.$_POST['modulo']; // necessário para o arquivo jsloader.php saber onde está fisicamente
			//	include_once(MODULES_DIR.$_POST['modulo'].'/js/jsloader.php');
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