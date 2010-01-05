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

    public function listar(){
        //$this->render('listar');
        //$this->autoRender= false;
    }

    /**
     * formulário
     */

    public function form(){

    }

    /**
     * FORMULÁRIO DE INSERÇÃO
     */
    public function criar($params = array() ){
        $this->render('form');
    }

    /**
     * FORMULÁRIO DE INSERÇÃO
     */
    public function editar($params = array() ){
        $this->render('form');
    }

}
?>