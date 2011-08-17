<?php
/**
 * CONTROLLER
 *
 * Descrição deste arquivo
 *
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModActionController
{
	public $query = array();
	
	public $showControls = true;
	/**
	 * listar()
	 *
	 * Listagem de Contéudos
	 */
	public function listing(){

		/**
		 * <h2> HEADER
		 */
		$this->set('h1', Aust::getInstance()->getStructureNameById($_GET['aust_node']) );

		$categorias = Aust::getInstance()->getNodeChildren($_GET['aust_node']);
		$categorias[$_GET['aust_node']] = 'Estrutura';

		/*
		 * SQL para listagem
		 */
		$params = array(
			'austNode' => $categorias,
		);

		/*
		 * Query com resultado
		 */
		$query = $this->module->load($params);

		$this->set('sql', $this->module->lastSql );
		//$config = $this->module->loadConfig();
//		$query = $this->module->replaceFieldsValueIfEmpty($query);
//		pr($query);
		
		$this->set('query', $query );

	} // fim listar()

	public function create(){
		$this->render('form');
	}

	public function view($param = array()){

		$this->set('tagh2', Aust::getInstance()->getStructureNameById($_GET['aust_node']) );

		$this->showControls = false;
		if( $this->module->getStructureConfig('activate_actions') == '1' )
			$this->showControls = true;

		$w = (!empty($_POST['w'])) ? $_POST['w'] : '';
		$w = (empty($w) && !empty($_GET['w'])) ? $_GET['w'] : $w;
		$this->set('w', $w);

		if( empty($this->query) ){
			$query = $this->module->runFilter($w);
		} else {
			$query = $this->query;
		}

		$this->set('query', $query );
		
		$viewType = 'normal';
		if( !empty($param) &&
		 	$param['view'] )
			$viewType = $param['view'];
		
		$this->set('viewType', $viewType);
		
		$this->render('view');
	}

	public function save(){
		$this->set('resultado', $this->module->save($_POST));
	}

	function actions(){
		
		$_SESSION['no_redirect'] = false;
		if( !empty($_POST['action']) &&
		 	!empty($_POST['itens']) )
		{
			$itemsArray = $_POST['itens'];
			
			$austNode = $_GET['aust_node'];
			$w = $_POST['w'];
			
			$_SESSION['selected_items'][$austNode]['aust_node'] = $austNode;
			$_SESSION['selected_items'][$austNode]['w'] = $w;
			$_SESSION['selected_items'][$austNode]['items'] = $itemsArray;
			
			$items = "'".implode("','", $itemsArray)."'";

			// SUBTRACT
			if( array_key_exists('subtract', $_POST['action']) )
			{
				
				// supossing there's only one key
				$subtractValue = reset( array_keys( $_POST['action']['subtract'] ) );
				/**
				 * @todo - should load from config
				 */
				$targetTable = 'clientes';
				$targetField = 'saldo_de_vendas';
				
				$sql = "
				UPDATE
					$targetTable 
				SET
					$targetField=$targetField-$subtractValue
				WHERE
					id IN ($items)
					AND $targetField>=$subtractValue
				";
				Connection::getInstance()->exec($sql);
				notice('Itens atualizados com sucesso.');
			}
			/*
			 * data separated by semicolons, similar do CSV format
			 */
			else if( array_key_exists('see_data_separated_by_semicolon', $_POST['action']) ){
				$value = reset( array_keys( $_POST['action']['see_data_separated_by_semicolon'] ) );
				
				$_SESSION['no_redirect'] = true;
				/**
				 * @todo - should load from config db
				 */
				$sql = "
				SELECT
					email_paypal
				FROM
					clientes
				WHERE
					id IN ($items)
				";
				$this->query['results'] = Connection::getInstance()->query($sql);
				$this->showControls = false;
				$this->view( array('view' => 'see_data_separated_by_semicolon') );
				
				return true;
			}
		} else {
			$this->view();
		}
		
		$this->autoRender = false;
	}
	
}
?>