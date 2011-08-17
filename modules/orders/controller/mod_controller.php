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

		if((!empty($filter)) AND ($filter <> 'off')){
			$addurl = "&filter=$filter&filterw=" . urlencode($filterw);
		}

		$categorias = Aust::getInstance()->getNodeChildren($_GET['aust_node']);
		$categorias[$_GET['aust_node']] = 'Estrutura';


		/*
		 * PAGINAÇÃO
		 */
		/*
		 * Página atual
		 */
		$pagina = (empty($_GET['pagina'])) ? 1 : $_GET['pagina'];
		/*
		 * Resultados por página
		 */
		$num_por_pagina = '20';
		$this->set('numPorPagina', $num_por_pagina);//(Config::getInstance()->LeOpcao($nome_modulo.'_paginacao')) ? Config::getInstance()->LeOpcao($nome_modulo.'_paginacao') : '10';
		$this->set('page', $pagina);//(Config::getInstance()->LeOpcao($nome_modulo.'_paginacao')) ? Config::getInstance()->LeOpcao($nome_modulo.'_paginacao') : '10';

		/*
		 * SQL para listagem
		 */
		$params = array(
			'austNode' => $categorias,
			'page' => $pagina,
			'limit' => $num_por_pagina
		);

		/*
		 * Query com resultado
		 */
		$query = $this->module->load($params);

		$this->set('sql', $this->module->lastSql );
		//$config = $this->module->loadConfig();
		$query = $this->module->replaceFieldsValueIfEmpty($query);

		$this->set('query', $query );

	} // fim listar()

	public function create(){
		$this->render('form');
	}

	public function edit(){

		$products = $this->module->getStructureConfig("aust_products");
		$sql = "
				SELECT
					*
				FROM
					st_order_items
				WHERE
					order_id='".$_GET['w']."'
				";

		$query = $this->module->connection->query($sql);

		$cartSql = $this->module->loadSql( array('id' => $_GET['w']) );
		$cart = Connection::getInstance()->query($cartSql);
		$cart = reset($cart);

		if( !empty($_GET['pending']) || is_string($_GET['pending']) ){
			if( $_GET['pending'] == '1' || $_GET['pending'] == '0' ){
				$sql = "UPDATE st_orders SET pending='".$_GET['pending']."' WHERE id='".$_GET['w']."'";
				Connection::getInstance()->exec($sql);
				$cart['pending'] = $_GET['pending'];
			}
		}


		$this->set('cart', $cart );
		$this->set('dados', $query );
		
		$this->render('form');
	}

	public function save(){
		$this->set('resultado', $this->module->save($_POST));
	}
	
}
?>