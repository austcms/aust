<?php
/**
 * Descrição deste arquivo
 *
 * @package ModController
 * @name nome
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModsController
{

    public function listing(){
        $this->set('h2', 'Listando conteúdo: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']) );
        $this->set('nome_modulo', $this->aust->LeModuloDaEstrutura($_GET['aust_node']) );

		$categorias = $this->aust->LeCategoriasFilhas('',$_GET['aust_node']);
		$categorias[$_GET['aust_node']] = 'Estrutura';

		$params = array(
			'austNode' => $categorias
		);
		
		$query = $this->modulo->load($params);
		
        $this->set('query', $query);
    }

    public function create(){


        $this->render('form');
    }

    public function edit(){

        
        $this->render('form');
    }

    public function save(){
        
    }
    
}
?>