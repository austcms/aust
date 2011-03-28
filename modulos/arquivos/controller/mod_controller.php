<?php
/**
 * Controller principal deste módulo
 *
 * @package ModController
 * @name nome
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 06/07/2009
 */

class ModController extends ModsController
{

    var $helpers = array('Form');

    public function actions(){
    }

    function listing(){
	
		$categorias = $this->aust->LeCategoriasFilhas('',$_GET['aust_node']);
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
        $this->set('numPorPagina', $num_por_pagina);//($config->LeOpcao($nome_modulo.'_paginacao')) ? $config->LeOpcao($nome_modulo.'_paginacao') : '10';
        $this->set('page', $pagina);//($config->LeOpcao($nome_modulo.'_paginacao')) ? $config->LeOpcao($nome_modulo.'_paginacao') : '10';

        /*
         * SQL para listagem
         */
        $params = array(
            'austNode' => $categorias,
            'page' => $pagina,
            'limit' => $num_por_pagina
        );

		$query = $this->modulo->load($params);
		$query = $this->modulo->replaceFieldsValueIfEmpty($query);
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

}
?>