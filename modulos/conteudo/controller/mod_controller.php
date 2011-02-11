<?php
/**
 * CONTROLLER
 *
 * Descrição deste arquivo
 *
 * @package ModController
 * @name nome
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModsController
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
        $this->set('h1', $this->aust->leNomeDaEstrutura($_GET['aust_node']) );

        if((!empty($filter)) AND ($filter <> 'off')){
            $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
        }

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

        /*
         * Query com resultado
         */

        $query = $this->modulo->load($params);
        $this->set('sql', $this->modulo->lastSql );
        //$config = $this->modulo->loadConfig();
        $query = $this->modulo->replaceFieldsValueIfEmpty($query);

        $this->set('query', $query );

    } // fim listar()

    public function create(){
        $this->render('form');
    }

    public function edit(){

        $this->set('tagh2', "Editar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']) );
        $this->set('tagp', 'Edite o conteúdo abaixo.');

        $w = (!empty($_GET['w'])) ? $_GET['w'] : '';
        $this->set('w', $w);


        $sql = "
                SELECT
                    *
                FROM
                    ".$this->modulo->getContentTable()."
                WHERE
                    id='$w'
                ";
        $query = $this->modulo->connection->query($sql);
        $this->set('dados', $query[0] );
        
        $this->render('form');
    }

    public function save(){
		if( $_POST['metodo'] == CREATE_ACTION &&
		 	empty($_POST['frmadddate']) )
		{
			$_POST['frmadddate'] = date("Y-m-d H:i:s");
		}
	
        $this->set('resultado', $this->modulo->save($_POST));
    }
    
}
?>