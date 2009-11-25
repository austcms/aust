<?php
/**
 * Arquivo que representa a estrutura controller de uma arquitetura
 * MVC.
 *
 * Contém métodos e propriedades que são usadas ao longo de todo o sistema,
 * tanto módulos como o Core.
 *
 * @package MVC
 * @name Controller
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.5, 22/06/2009
 */
class Controller extends Aust
{
    var $helpers = array('Form');

    function __construct(){
        
        /**
         * HELPERS
         * 
         * Cria helpers solicitados
         */
        if( count($this->helpers) ){
            /**
             * Loop por cada Helper a ser carregado
             */
            foreach($this->helpers as $valor){
                //echo HELPERS_DIR.$valor.CLASS_FILE_SUFIX.".php";
                unset( $$valor );
                /**
                 * Inclui o arquivo do helper
                 */
                include_once( HELPERS_DIR.$valor.CLASS_FILE_SUFIX.".php" );
                $helperName = $valor.HELPER_CLASSNAME_SUFIX;
                $$valor = new $helperName();
                $this->set( strtolower($valor), $$valor);
            }
        }
        
    }




}

?>