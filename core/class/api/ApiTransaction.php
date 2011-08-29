<?php
/**
 * API
 *
 * Handles the requests, find out what structure was asked for and retrieves.
 *
 * @since v0.3, 17/06/2011
 */
class ApiTransaction {
	
	public $dataFormat = 'json';
	public $queryParser;
	
	/**
	 * var Contains the GET in string format.
	 */
	public $getString = '';

	function __construct() {
		$this->queryParser = new ApiQueryParser();
	}
	
	public function perform($get){
		$result = array();
		
		$this->getString = $get;
		$get = $this->ensureArray($get);
		
		if( array_key_exists('version', $get) ){
			$result = $this->version();
		} else {
			$result = $this->getData($get);
		}
		
		if( empty($result) )
			$result = '';
		
		$finalResult = array('result' => $result);
		
		if( $this->dataFormat == 'json' )
			$finalResult = $this->json($finalResult);
			
		return $finalResult;
	}
	
	public function version(){
		return '0.0.1';
	}
	
	public function json($value){
		return json_encode($value);
	}
	
	public function queryParser(){
		return $this->queryParser;
	}
	
	public function getData($get){

		if( $this->retrieveFrom($get) == "configuration" ){
			$properties = $this->getConfigurationFields($get);
			$value = Config::getInstance()->getConfigs($properties, true);
			return $value;
		} else if( $this->retrieveFrom($get) == "structure" ){
		
			$structureIds = $this->queryParser()->structureId($get);
			if( !is_array($structureIds) || empty($structureIds) || count($structureIds) == 0 )
				return false;

			$where = $this->queryParser()->where($get);
			$order = $this->queryParser()->order($get);
			$limit = $this->queryParser()->limit($get);
			$fields = $this->queryParser()->fields($get);
			$includeFields = $this->queryParser()->includeFields($get);
			$result = array();
		
			$queryParameters = array(
				'fields' 			=> $fields,
				'where' 			=> $where,
				'order' 			=> $order,
				'limit' 			=> $limit,
				'include_fields' 	=> $includeFields,
				'api_query'			=> $get,
			);

			foreach( $structureIds as $structureId ){
				$structureInstance = ModulesManager::getInstance()->modelInstance($structureId);
				$items = $structureInstance->load( $queryParameters );
				$result = array_merge($result, $items);
			}

			if( empty($result) )
				$result = 0;
			return $result;
		}
		
	}
	
	public function ensureArray($get){
		if( is_array($get) )
			return $get;
		
		if( empty($get) or !is_string($get) )
			return false;
		
		$get = preg_replace('/(.*\?)/', '', $get);
		
		$splitE = explode("&", $get);
		
		$get = array();
		foreach( $splitE as $values ){
			$keyValue = explode("=", $values);
			if( !empty($keyValue[1]) )
				$get[$keyValue[0]] = $keyValue[1];
			else if( !empty($keyValue[0]) )
				$get[] = $keyValue[0];
			$keyValue = null;
		}
		
		return $get;
	}
	
	public function retrieveFrom($get){
		if( array_key_exists('configuration', $get) || array_key_exists('config', $get) )
			return "configuration";

		return "structure";
	}
	
	public function getConfigurationFields($get){
		$fields = false;
		if( is_string($get['configuration']) )
			$fields = explode(';', $get['configuration']);
			
		return $fields;
	}
	
}
?>