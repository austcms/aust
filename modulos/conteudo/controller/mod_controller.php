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

    public function listar(){
        //$this->render('listar');
    }

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