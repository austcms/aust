<?php
/**
 * @since v0.3, 17/06/2011
 */
class ApiQueryParser {
	
	public $dataFormat = 'json';
	public $queryParser;

	function __construct() {

	}
	
	public function structureId($get){
		
		if( !empty($get['query']) )
			$structure = $get['query'];
		elseif( !empty($get['structure']) )
			$structure = $get['structure'];
		else
			return false;

		$params = array();
		if( !empty($get['module']) )
			$params['module'] = $get['module'];
		
		return Aust::getInstance()->getStructureIdByName($structure, $params);
	}
	
	public function fields($get){
		if( empty($get['fields']) || $get['fields'] == "*" )
			return '*';
		
		$fields = explode(';', $get['fields']);
		if( empty($fields) )
			return '*';
			
		return $fields;
	}
	
	public function where($get){
		$result = array();
		foreach( $get as $key=>$value ){
			if( substr($key, 0, 6) == 'where_' ){
				$value = str_replace("+", ' ', $value);
				if( strlen($value) > 1 )
					$value = str_replace("*", '%', $value); // for using on SQL LIKE '%word'

				if( strstr($value, ';') )
					$value = explode(";", $value);
				
				$field = substr($key, 6);
				$result[$field] = $value;
			}
		}
		
		return $result;
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
	
	public function includeFields($get){
		if( empty($get['include_fields']) )
			return false;

		if( $get['include_fields'] == "*" )
			return "*";
		
		$fields = explode(';', $get['include_fields']);
		if( empty($fields) )
			return false;
			
		return $fields;
	}
	
	
}
?>