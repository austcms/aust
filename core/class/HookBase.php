<?php
/**
 * Hooks' superclass
 *
 * This class has methods shared between all Hooks.
 */
class HookBase
{
	
	function cleanUpText($perform){
		$perform = preg_replace('/\{.*?\}/', '', $perform);
		return $perform;
	}
	
	/**
	 * Parses everything sql()
	 */
	function getSqlFunction($perform){

		/*
		 * matches everything {self.field_name}
		 */
		preg_match_all('/sql\((.*?)\)/', $perform, $data);
		
		foreach( $data[1] as $sql ){
			$query = Connection::getInstance()->query($sql);
			$queryResults = array();
			foreach( $query as $field ){
				$queryResults[] = reset($field);
			}
			
			$resultString = implode(';', $queryResults);
			
			$perform = str_replace('sql('.$sql.')', $resultString, $perform);
		}
		
		$result = $perform;
		return $result;
	}
		
	/**
	 * Parses everything {self.field_name}
	 */
	function getSelfData($self, $austNode, $perform){

		$selfObject = Aust::getInstance()->getStructureInstance($austNode);
		$selfQuery = $selfObject->load(
			array(
				'metodo' => 'edit',
				'categorias' => $austNode,
				'austNode' => $austNode,
				'id' => $self
			)
		);
		if( empty($selfQuery) )
			return false;
		
		$selfQuery = reset($selfQuery);
		
		/*
		 * matches everything {self.field_name}
		 */
		preg_match_all('/\{self.(.*?)}/', $perform, $selfData);
		
		foreach( $selfData[1] as $field ){
			if( !array_key_exists($field, $selfQuery) )
				continue;
			
			$perform = str_replace('{self.'.$field.'}', $selfQuery[$field], $perform);
		}
		
		$result = $perform;
		return $result;
	}	
}
?>