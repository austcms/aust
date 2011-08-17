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
		$this->set('h1', Aust::getInstance()->getStructureNameById($this->austNode) );

		if((!empty($filter)) AND ($filter <> 'off')){
			$addurl = "&filter=$filter&filterw=" . urlencode($filterw);
		}

		$categorias = Aust::getInstance()->getNodeChildren($this->austNode);
		$categorias[$this->austNode] = 'Estrutura';


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
		$this->set('numPorPagina', $num_por_pagina);
		$this->set('page', $pagina);

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
		$query = $this->module->replaceFieldsValueIfEmpty($query);

		$this->set('query', $query );

	}

	public function create(){
		$this->render('form');
	}

	public function edit(){

		$this->set('tagh2', "Editar: ". Aust::getInstance()->getStructureNameById($this->austNode) );
		$this->set('tagp', 'Edite o conteúdo abaixo.');

		$w = (!empty($_GET['w'])) ? $_GET['w'] : '';
		$this->set('w', $w);

		$sql = "
				SELECT
					*
				FROM
					".$this->module->getContentTable()."
				WHERE
					id='$w'
				";
		$query = $this->module->connection->query($sql);
		$this->set('dados', $query[0] );

		$this->render('form');
	}

	public function save(){
		if( $_POST['metodo'] == CREATE_ACTION ){
			$_POST['frmcreated_on'] = date("Y-m-d H:i:s");
			
			if( empty($_POST['frmnode_id']) )
				$_POST['frmnode_id'] = $_POST['aust_node'];
		}
		
		$resultado = $this->module->save($_POST, $_FILES);
		
		
		if( $resultado ){
			notice('As informações foram salvas com sucesso.');
		} else {
			failure('Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.');
		}

	}
	
}
?>