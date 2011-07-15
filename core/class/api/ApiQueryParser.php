<?php
/**
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.3, 17/06/2011
 */
class ApiQueryParser {
	
	var $dataFormat = 'json';
	var $queryParser;

    function __construct() {

    }
	
	public function structureId($get){
		
		if( !empty($get['query']) )
			$structure = $get['query'];
		elseif( !empty($get['structure']) )
			$structure = $get['structure'];
		else
			return false;
		
		return Aust::getInstance()->getStructureIdByName($structure);
	}
	
	public function fields($get){
		if( empty($get['fields']) || $get['fields'] == "*" )
			return '*';
		
		$fields = explode(';', $get['fields']);
		if( empty($fields) )
			return '*';
			
		return $fields;
	}
	
	public function order($get){
		if( empty($get['order']) )
			return 'id ASC';
			
		$result = array();
		$orders = explode(';', $get['order']);
		foreach( $orders as $order ){
			$order = str_replace('+', ' ', $order);
			if( preg_match('/[^a-z]desc/', $order) || preg_match('/[^a-z]asc/', $order) )
				$rightOrder = $order;
			else
				$rightOrder = $order." asc";

			if( !preg_match('/\./', $order) )
				$rightOrder = "mainTable.".$rightOrder;
				
			$result[] = $rightOrder;
		}
		
		$result = implode(',', $result);
		return $result;
	}
	
	public function limit($get){
		return ( empty($get['limit']) ) ? '100' : $get['limit'];
	}
	
}
?>