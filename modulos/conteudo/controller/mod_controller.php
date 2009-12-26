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
    public function listar(){

        /**
         * <H1> HEADER
         */
        $this->set('h1', $this->aust->leNomeDaEstrutura($_GET['aust_node']) );

        $this->set('cat', $query[0]['nome'] );

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
        $pagina = (empty($_GET['pagina'])) ? $pagina = 1 : $pagina = $_GET['pagina'];
        /*
         * Resultados por página
         */
        $num_por_pagina = '20';
        $this->set('numPorPagina', $num_por_pagina);//($config->LeOpcao($nome_modulo.'_paginacao')) ? $config->LeOpcao($nome_modulo.'_paginacao') : '10';

        /*
         * SQL para listagem
         */
        $params = array(
            'categorias' => $categorias,
            'pagina' => $pagina,
            'resultadosPorPagina' => $num_por_pagina
        );
        $sql = $this->modulo->getSQLForListing($params);

        /*
         * Query com resultado
         */
        $this->set('query', $this->modulo->conexao->query($sql) );

    } // fim listar()

    public function criar(){


        $this->render('form');
    }

    public function editar(){

        
        $this->render('form');
    }

    public function save(){
        
    }
    
}
?>