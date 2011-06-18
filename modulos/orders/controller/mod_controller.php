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
        $this->set('h1', Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node']) );

        $this->set('cat', $query[0]['nome'] );

        if((!empty($filter)) AND ($filter <> 'off')){
            $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
        }

        $categorias = Aust::getInstance()->LeCategoriasFilhas('',$_GET['aust_node']);
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


				$products = $this->modulo->getStructureConfig("aust_products");
		        $sql = "
		                SELECT
		                    *
		                FROM
		                    st_order_items
		                WHERE
		                    order_id='".$_GET['w']."'
		                ";

		        $query = $this->modulo->connection->query($sql);

				$cartSql = $this->modulo->loadSql( array('id' => $_GET['w']) );
				$cart = $this->connection->query($cartSql);
				$cart = reset($cart);
		
				if( !empty($_GET['pending']) || is_string($_GET['pending']) ){
					if( $_GET['pending'] == '1' || $_GET['pending'] == '0' ){
						$sql = "UPDATE st_orders SET pending='".$_GET['pending']."' WHERE id='".$_GET['w']."'";
						$this->connection->exec($sql);
						$cart['pending'] = $_GET['pending'];
					}
				}
		
        $this->set('cart', $cart );
        $this->set('dados', $query );
        
        $this->render('form');
    }

    public function save(){
        $this->set('resultado', $this->modulo->save($_POST));
    }
    
}
?>