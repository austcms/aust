<?php
class ContentController extends ActionController {
	
	function beforeFilter(){
		if( !empty($_GET["aust_node"]) ){
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

	        /*
	         * JS DO MÓDULO
	         *
	         * Carrega Javascript de algum módulo se existir
	         */
	        if(!empty($aust_node)){
	            if(is_file(MODULES_DIR.$modDir.'js/jsloader.php')){
	                $include_baseurl = MODULES_DIR. substr($modDir, 0, strlen($modDir)-1); // necessário para o arquivo jsloader.php saber onde está fisicamente
	                include_once(MODULES_DIR.$modDir.'js/jsloader.php');
	            }
	        }

	        $action = $_GET['action'];

			/*
			 * Navegação entre actions de um austNode
			 */

			$moreOptions = array();
			foreach( $modInfo['opcoes'] as $actionName=>$humanName ){
				if( $actionName == $action )
					continue;
				$moreOptions[] = '<a href="adm_main.php?section='.$_GET['section'].'&action='.$actionName.'&aust_node='.$austNode.'">'.$humanName.'</a>';
			}


			$modDispatcher = new ModDispatcher($aust_node);
			$modDispatcher->dispatch();
			return true;
			
			$visibleNav = true;
			$relatedMasters = Aust::getInstance()->getRelatedMasters(array($austNode));

			if( !empty($relatedMasters) ){

				$module = Aust::getInstance()->getStructureInstance($austNode);
				if( !$module->getStructureConfig('related_and_visible') ){
					$visibleNav = false;
				}

			}

			if( !empty($moreOptions) && $visibleNav ){
				?>
				<div class="structure_nav_options">
					Navegação: <?php echo implode(", ", $moreOptions); ?>
				</div>
				<?php
			}

	        $modController = new ModController($param);

		    /*
		     * Se for save, redireciona automaticamente
		     */
		    if( in_array($action, array(SAVE_ACTION, ACTIONS_ACTION)) &&
				(
					empty($_SESSION['no_redirect']) ||
					!$_SESSION['no_redirect']
				)
		 	)
			{

				unset($_SESSION['selected_items']);
		        ?>
		        <div class="loading_timer">
		            <img src="<?php echo IMG_DIR ?>loading_timer.gif" /> Redirecionando Automaticamente
		        </div>
		        <?php

				if( !empty($_POST['redirect_to']) )
					$goToUrl = $_POST['redirect_to'];
				else if( !empty($_GET['redirect_to']) )
					$goToUrl = $_GET['redirect_to'];
				else
	            	$goToUrl = "adm_main.php?section=".$_GET['section'].'&action=listing&aust_node='.$aust_node;
	            ?>
	            <script type="text/javascript">
	                var timeToRefresh = 2;
	                setTimeout(function(){
	                    window.location.href = "<?php echo $goToUrl ?>";
	                }, 2000);
	            </script>
	            <?php
	        }

			$_SESSION['no_redirect'] = false;
			
		}
	}
	
}
?>