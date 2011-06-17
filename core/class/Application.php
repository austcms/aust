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

		$this->showUi = $showUi;
		if( !$this->showUi )
			return true;

		require_once(CONFIG_DATABASE_FILE);

		include_once(LIB_DIR."aust/aust_func.php");
		$conexao = Connection::getInstance();
		$model = new Model($conexao);

		include_once(CORE_CONFIG_DIR."core.php");

		/**
		 * Configurações do core do sistema
		 */
		    include_once(CONFIG_DIR."core.php");

		/**
		 * Permissões de tipos de usuários relacionados à navegação
		 */
		    include_once(CONFIG_DIR."nav_permissions.php");
		/**
		 * Carrega o CORE
		 */
		    include(CORE_DIR.'load_core.php');
		
		/**
		 * Se este arquivo NÃO está sendo carregado pelo responser
		 */
		$isResponser = (empty($isResponser)) ? false : $isResponser;
		if(!$isResponser){

		    /**
		     * Verifica se a conexão ou tabelas existem
		     */
		    if(!$conexao->DBExiste OR ($this->installationDiagnostics() != 1)){
		        echo 'Erro no sistema: 002.';
		    }
    
		    /**
		     * CONEXÃO EXISTE, TUDO ESTÁ OK
		     *
		     * Está tudo certinho, segue adiante para mostrar a UI
		     */
		    else {

		        /**
		         * Instancia objetos necessários ao funcionamento do sistema
		         */
		        $aust = Aust::getInstance();
		        User::getInstance()->verifySession();
		        User::getInstance()->redirectForbiddenSession();

		        /**
		         * Carrega todas as estruturas que não tem categorias.
		         */
		        Aust::getInstance()->EstruturasSemCategorias();
		        /**
		         * Ações quanto a conteúdos e categorias
		         */
		        include(PERMISSIONS_FILE);

		        include_once(INC_DIR.'inc_categorias_functions.php');

				$this->ui();
		    }
		}
    }

	public function installationDiagnostics(){
		return dbSchema::getInstance()->verificaSchema();
	}
	
	public function ui(){
        /**
         * USER INTERFACE (UI)
         *
         * Instancia a classe UI responsável pela interface do usuário.
         *
         * Permissões por usuário e por seção em config/permissions.php.
         */
        /**
         * Instancia o objeto $ui (UserInterface)
         */
        $uiPermissions = UiPermissions::getInstance();
        $uI = new UI;

        $_GET['action'] = (empty($_GET['action'])) ? '' : $_GET['action'];
        $_GET['section'] = (empty($_GET['section'])) ? 'conteudo' : $_GET['section'];

		if( $_GET['section'] == 'index' )
			$_GET['section'] = 'conteudo';

        /**
         * Verifica se o usuário tem permissão para acessar a página requsitada
         */
        //$permitted = $uiPermissions->isPermitted();
        //pr($permitted);
        /**
         * Com permissão, o usuário vai até a página requisitada.
         *
         * Caso contrario, recebe uma mensagem de erro.
         */
        $param = array(
            'permitted' => $uiPermissions->isPermittedSection(),
        );
        $filename = $uI->correctUIPage($param);

        /**
         * Salva em $content_for_layout todo o conteúdo gerado na view
         */
        ob_start();
        include($filename);
        $content_for_layout = ob_get_contents();
        ob_end_clean();

		// show only view?
		$viewOnly = false;
		if( (
				!empty($_GET['viewonly'])
				AND $_GET['viewonly'] == 'yes'
			)
			OR 
			(
				!empty($_POST['viewonly'])
				AND $_POST['viewonly'] == 'yes'
			)
		)
		{
			$viewOnly = true;
		}
        /**
         * Mostra a Interface de usuário no browser
         */
        if( $viewOnly == false
			AND (
				empty($_GET['theme'])
            	OR $_GET['theme'] != 'blank'
			)
		)
        {
            include(UI_STANDARD_FILE);
        } else {
            echo $content_for_layout;
        }
	}

}
?>