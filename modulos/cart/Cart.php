<?php
/**
 * CLASSE DO MÓDULO
 *
 * Classe contendo funcionalidades deste módulo
 *
 * @package Modulos
 * @name Conteúdos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */
class Cart extends Module
{
    public $mainTable = "cart";

    public $date = array(
        'standardFormat' => '%d/%m/%Y',
        'created_on' => 'adddate',
        'updated_on' => 'addate'
    );

    function __construct(){
        parent::__construct(array());
    }

    /**
     * getInstance()
     *
     * Para Singleton
     *
     * @staticvar <object> $instance
     * @return <Conexao object>
     */
    static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new Cart;
        }

        return $instance[0];

    }

}
?>