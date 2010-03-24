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

        $sql = "SELECT
                    id,nome
                FROM
                    ".Aust::$austTable."
                WHERE
                    id='".$_GET['aust_node']."'";


        $query = $this->modulo->connection->query($sql);
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