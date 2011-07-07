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
class Orders extends Module
{
    public $mainTable = "st_orders";

    public $date = array(
        'standardFormat' => '%m/%d/%Y %H:%i:%s',
        'scheduled_on' => '%m/%d/%Y %H:%i:%s',
        'created_on' => 'created_on',
        'updated_on' => 'updated_on'
    );

	public $fieldsToLoad = array(
	    "Orders.transaction_nr", "Orders.pending", "Orders.paid",
		"Orders.client_id",
		"Orders.gateway_analysing",
		"Orders.gateway_waiting",
		"Orders.gateway_complete",
		"Orders.gateway_cancelled",
		"Orders.paid_on",
		"Orders.freight_service",
		"Orders.freight_value",
		"Orders.sent",
		"Orders.total_price",
		"Orders.deadline_days"
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
            $instance[0] = new Orders;
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
							   ON Orders.client_id=Clients.id";
		}
		
        $sql = "SELECT
					'$clientsSt' as client_node,
					Orders.id as id,
                    ".implode(', ', $this->fieldsToLoad).",
                    Orders.".$this->austField." AS cat,
                    DATE_FORMAT(Orders.".$this->date['created_on'].", '".$this->date['standardFormat']."') as created_on,
                    DATE_FORMAT(Orders.scheduled_on, '".$this->date['scheduled_on']."') as scheduled_on
					$clientFields
                FROM
                    ".$this->useThisTable()." as Orders
				$clientLeftJoin
                WHERE 
					1=1 $id
                ORDER BY Orders.id DESC, Orders.pending DESC, Orders.paid DESC, Orders.scheduled_on DESC
				LIMIT 0,50
                ";
		
		return $sql;
	}

}
?>