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

        $categorias = $this->aust->LeCategoriasFilhas('',$_GET['aust_node']);
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
        $query = $this->modulo->load($params);

        $this->set('sql', $this->modulo->lastSql );
        //$config = $this->modulo->loadConfig();
//        $query = $this->modulo->replaceFieldsValueIfEmpty($query);
//		pr($query);
		
        $this->set('query', $query );

    } // fim listar()

    public function create(){
        $this->render('form');
    }

    public function view(){

        $this->set('tagh2', "Editar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']) );
        $this->set('tagp', 'Edite o conteúdo abaixo.');

        $w = (!empty($_GET['w'])) ? $_GET['w'] : '';
        $this->set('w', $w);

		$query = $this->modulo->runFilter($w);
//		pr($query);
        $this->set('query', $query );
        
        $this->render('view');
    }

    public function save(){
        $this->set('resultado', $this->modulo->save($_POST));
    }
    
}
?>