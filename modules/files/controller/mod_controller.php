<?php
/**
 * Controller principal deste módulo
 *
 * @since v0.1.6 06/07/2009
 */

class ModController extends ModActionController
{

	public $helpers = array('Form');

	function listing(){
	
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

		$query = $this->module->load($params);
		$query = $this->module->replaceFieldsValueIfEmpty($query);
		$this->set('query', $query);
	}

	/**
	 * formulário
	 */

	public function form(){

	}

	/**
	 * FORMULÁRIO DE INSERÇÃO
	 */
	function create($params = array() ){
		$this->render('form');
	}

	/**
	 * FORMULÁRIO DE INSERÇÃO
	 */
	public function edit($params = array() ){
		$this->render('form');
	}

	public function save(){
		$params = array(
			"aust_node" => $_POST["aust_node"],
		);
		$moduloConfig = $this->module->loadModConf($params);

		if(!empty($_POST)){
			$resultado = $this->module->save($_POST, $_FILES);

			if($resultado){
				notice('As informações foram salvas com sucesso.');
			} else {
				failure('Ocorreu um erro ao salvar informações.');
			}

		}		
	}
	
	public function actions(){
		if(!empty($_POST['deletar']) and !empty($_POST['itens'])){
			/*
			 * Identificar tabela que deve ser excluida
			 */

			$itens = $_POST["itens"];
			$c = 0;
			foreach($itens as $key=>$value){
				$idsToDelete[] = $value;
			}

			$sql = "SELECT
						*
					FROM
						".$this->module->getMainTable()."
					WHERE
						id IN ('".implode("','", $idsToDelete)."')";
	
			$data = $this->module->connection->query($sql);

			foreach( $data as $dados ){
				$unlink = true;
				try{
					if( is_file($dados['systemurl']) &&
						!unlink($dados['systemurl']) )
					{
						$unlink = false;
						while(in_array($dados['id'], $idsToDelete)) {
							$key = array_search($dados['id'], $idsToDelete);
							unset($idsToDelete[$key]);
						}
					}
				} catch (Exception $e) {
					echo 'Erro: ', $e->getMessage(), "<br>";
				}
				
			}

			$sql = "DELETE FROM
						".$this->module->getMainTable()."
					WHERE
						id IN ('".implode("','", $idsToDelete)."')";

			if($this->module->connection->exec($sql)){
				$resultado = TRUE;
			} else {
				$resultado = FALSE;
			}

			if( $resultado && !$unlink ){
				notice('Alguns arquivos foram excluídos, outros não.');
			} else if($resultado){
				notice('Os dados foram excluídos com sucesso.');
			} else {
				failure('Ocorreu um erro ao excluir os dados. '.
						'Verifique se o arquivo já não foi apagado.');
			}

		}
		$_POST['no_redirect'] = true;
	}

}
?>