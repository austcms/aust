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
        'standardFormat' => '%m/%d/%Y %H:%i:%s',
        'scheduled_on' => '%m/%d/%Y %H:%i:%s',
        'created_on' => 'created_on',
        'updated_on' => 'updated_on'
    );

	public $fieldsToLoad = array(
	    "Cart.transaction_nr", "Cart.pending", "Cart.paid",
		"Cart.client_id",
		"Cart.gateway_analysing",
		"Cart.gateway_waiting",
		"Cart.gateway_complete",
		"Cart.gateway_cancelled",
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
		
		$clientsSt = $this->getStructureConfig('aust_clients_id');
		$clientsName = $this->getStructureConfig('aust_clients_name_field');

		$client = Aust::getInstance()->getStructureInstance($clientsSt);

		$clientFields = '';
		$clientLeftJoin = '';

		if( !empty($client) &&
		 	!empty($clientsName) )
		{
			$clientFields = ", Clients.".$clientsName." as 'client_name'";
			$clientLeftJoin = "LEFT JOIN ".$client->dataTable($clientsSt)." as Clients 
							   ON Cart.client_id=Clients.id";
		}
		
        $sql = "SELECT
					'$clientsSt' as client_node,
					Cart.id as id,
                    ".implode(', ', $this->fieldsToLoad).",
                    Cart.".$this->austField." AS cat,
                    DATE_FORMAT(Cart.".$this->date['created_on'].", '".$this->date['standardFormat']."') as created_on,
                    DATE_FORMAT(Cart.scheduled_on, '".$this->date['scheduled_on']."') as scheduled_on
					$clientFields
                FROM
                    Cart
				$clientLeftJoin
                WHERE 
					1=1 $id
                ORDER BY Cart.pending DESC, Cart.paid DESC, Cart.scheduled_on DESC
				LIMIT 0,50
                ";
		
		return $sql;
	}

}
?>