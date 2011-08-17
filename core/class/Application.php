<?php
/**
 * Whole application in one file. This ignites the whole app, the dispatchers,
 * controllers, views, modules, models, connection, everything.
 *
 * @since v0.2.0, 17/06/2011
 */
class Application {

	public $showUi = true;

	function __construct($showUi = true) {

		if( !file_exists(CONFIG_DATABASE_FILE) ){
			header("Location: index.php");
			exit();
		}
			
		
		require_once(CONFIG_DATABASE_FILE);

		include_once(LIB_DIR."aust/aust_func.php");
		$conexao = Connection::getInstance();
		$model = new Model($conexao);

		include_once(CORE_CONFIG_DIR."core.php");

		/*
		 * Configuration about the core
		 */
			include_once(CONFIG_DIR."core.php");

		/*
		 * Loads the whole core
		 */
			include(CORE_DIR.'load_core.php');
		
		Aust::getInstance()->createFirstSiteAutomatically();
		
		$this->showUi = $showUi;
		if( !$this->showUi )
			return true;

		/*
		 * Verifica se a conexão ou tabelas existem
		 */
		if( !Connection::getInstance()->dbExists() ||
			($this->installationDiagnostics() != 1) )
		{
			header("Location: index.php");
			exit();
		}
		/*
		 * Diagnostics show everything's fine
		 */
		else {

			User::getInstance()->verifySession();
			User::getInstance()->redirectForbiddenSession();

			$dispatcher = new Dispatcher;
			$dispatcher->dispatch();

		}
	}

	public function installationDiagnostics(){
		return dbSchema::getInstance()->verificaSchema();
	}
	
}
?>