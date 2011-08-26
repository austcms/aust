<?php
/**
 * API Specifics
 *
 * This models provides special options in the API query.
 *
 * @since v0.3, 26/08/2011
 */
class FlexFieldsApiSpecificsParser {
	
	/**
	 * Calls every other function that parse options from API.
	 * Acts like a center of distribution.
	 */
	static function parseQuery($query = array()){
		$result = array();
		
		/* Parses each option */
		$lastByFields = self::lastByFields($query);

		if( !empty($lastByFields) ) 
			$result['last_fields'] = $lastByFields;
		
		return $result;
	}
	
	/**
	 * i.e. ?query=whatever&last_images=14
	 *
	 * 		...where images is a field.
	 *
	 * This method will parse the 'last_images' property and
	 * return an array containing all the fields required.
	 */
	static function lastByFields($query = array()){
		if( empty($query) )
			return false;
		
		$result = array();
		foreach( $query as $key=>$value ){
			$properties = array();
			if( substr($key, 0, 5) == 'last_' ){

				if( is_numeric($value) )
					$properties['limit'] = $value;
				
				$field = substr($key, 5);
				$result[$field] = $properties;
			}
		}

		if( empty($result) )
			return false;
		
		return $result;
	}
	
}
?>