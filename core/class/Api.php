<?php
/**
 * API
 *
 * @since v0.3, 17/06/2011
 */
class Api {

	/**
	 * This prepares the environment for return data through the API.
	 * 
	 * The Dispatcher function is responsible for the other 50% of the process.
	 */
	function __construct() {

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
		 * Loads the whole core functions
		 */
			include(CORE_DIR.'load_core.php');
		
		/*
		 * How's the connection
		 */
		if( !Connection::getInstance()->dbExists() ||
			($this->installationDiagnostics() != 1) )
		{
			echo 'system error 002';
		}
		/*
		 * Diagnostics show everything's fine
		 */
		else {


		}
	}
	
	/**
	 * This methods ignites the process of mining for data and retrieving as JSON/XML
	 */
	public function dispatch($get, $print = true){
		$transaction = new ApiTransaction();
		$solution = $transaction->perform($get);
		if( $print )
			echo $solution;
		return $solution;
	}
	
	public function installationDiagnostics(){
		return dbSchema::getInstance()->verificaSchema();
	}
	
}
?>