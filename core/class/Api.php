<?php
/**
 * API
 *
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.3, 17/06/2011
 */
class Api {

    function __construct() {

		require_once(CONFIG_DATABASE_FILE);

		include_once(LIB_DIR."aust/aust_func.php");
		$conexao = Connection::getInstance();
		$model = new Model($conexao);

		include_once(CORE_CONFIG_DIR."core.php");

		/*
		 * Configurações do core do sistema
		 */
		    include_once(CONFIG_DIR."core.php");

		/*
		 * Permissões de tipos de usuários relacionados à navegação
		 */
		/*
		 * Carrega o CORE
		 */
		    include(CORE_DIR.'load_core.php');
		
	    /*
	     * Verifica se a conexão ou tabelas existem
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
	
	public function dispatch($get){
		pr($get);
		$transaction = new ApiTransaction();
		$solution = $transaction->perform($get);
		echo $solution;
	}
	
	public function installationDiagnostics(){
		return dbSchema::getInstance()->verificaSchema();
	}
	
}
?>