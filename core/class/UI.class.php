<?php
/**
 * Classe responsável por lidar com a interface do site.
 *
 * UI significa User Interface.
 *
 * @package Class
 * @name UI
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.5, 30/05/2009
 */
class UI {


    public function correctUIPage($param){

        /**
         * Se o usuário foi permitido acessar a página atual, retorna a
         * página de acordo com o section que foi pedido.
         *
         * Esta permissão é adquirida através de UI::verificaPermissoes();
         */
        if($param['permitted']){
            return INC_DIR . $_GET['section'] . '.inc.php';
        }
        /**
         * O endereço será de uma página com uma mensagem de acesso negado.
         */
        else {
            return MSG_DENIED_ACCESS;
        }
        
    }

    public function verificaPermissoes(){


    }


}

?>