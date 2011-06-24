<?php
/**
 * Whole application in one file
 *
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.2.0, 17/06/2011
 */
class Application {

	public $showUi = true;

    function __construct($showUi = true) {

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
		
	        include_once(INC_DIR.'inc_categorias_functions.php');
		
		$this->showUi = $showUi;
		if( !$this->showUi )
			return true;

	
	    /*
	     * Verifica se a conexão ou tabelas existem
	     */
	    if( !Connection::getInstance()->dbExists() ||
			($this->installationDiagnostics() != 1) )
		{
	        echo 'Erro no sistema: 002.';
	    }
	    /*
	     * Diagnostics show everything's fine
	     */
	    else {

	        User::getInstance()->verifySession();
	        User::getInstance()->redirectForbiddenSession();
	        Aust::getInstance()->EstruturasSemCategorias();

			$dispatcher = new Dispatcher;
			$dispatcher->dispatch();

		}
    }

	public function installationDiagnostics(){
		return dbSchema::getInstance()->verificaSchema();
	}
	
}
?>