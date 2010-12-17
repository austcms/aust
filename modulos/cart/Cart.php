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
        'standardFormat' => '%d/%m/%Y %H:%i',
        'scheduled_on' => '%d/%m/%Y %H:%i',
        'created_on' => 'created_on',
        'updated_on' => 'updated_on'
    );

	public $fieldsToLoad = array(
	    "Cart.transaction_nr", "Cart.pending", "Cart.paid"
	);

	public $austField = 'node_id';
	
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

	function loadSql($options = array()){

        $id = empty($options['id']) ? '' : $options['id'];

        if( !empty($id) ){
            if( is_array($id) ){
                $id = " AND id IN ('".implode("','", $id)."')";
            } else {
                $id = " AND id='$id'";
            }
        }
		
        $sql = "SELECT
					Cart.id as id,
                    ".implode(',', $this->fieldsToLoad).",
                    ".$this->austField." AS cat,
                    DATE_FORMAT(".$this->date['created_on'].", '".$this->date['standardFormat']."') as created_on,
                    DATE_FORMAT(scheduled_on, '".$this->date['scheduled_on']."') as scheduled_on
                FROM
                    Cart
                WHERE 
					1=1 $id
                ORDER BY Cart.pending DESC
                ";
		
		return $sql;
	}

}
?>