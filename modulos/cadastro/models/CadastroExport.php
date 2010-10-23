<?php
/**
 * Classe do módulo
 *
 * @package Módulos
 * @name Cadastro
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6, 09/07/2009
 */
class CadastroExport extends ModuleExport {

	/*
	 * EXPORT
	 */
	public function export(){

		$data = $this->module->pegaInformacoesCadastro();

		return array( $data );
	}
	
	public function import($data = array(), $stId = ''){
		if( empty($data) || empty($stId) )
			return false;
		
		if( empty($data) || empty($data[0]) )
			return false;
		if( !empty($data[0]) )
			$data = reset($data);
		
		$newData = array();
		
		$i = 0;
		$fieldNames = array();
		$fieldValues = array();

		foreach( $data as $type=>$values){
			foreach( $values as $dataKey=>$value ){

				$value['categorias_id'] = $stId;
				unset($value['id']);
				
				foreach( $value as $fieldName=>$fieldValue ){
					$fieldNames[$i][] = $fieldName;
					$fieldValues[$i][] = $fieldValue;
				}
				$i++;
			}
		}

		$values = array();
		foreach( $fieldNames as $key=>$fields ){
			$fieldsStr = implode(',', $fields);
			$values[] = "('".implode("','", $fieldValues[$key])."')";
		}
		
		$sql = "INSERT INTO cadastros_conf (".$fieldsStr.") VALUES ".implode(",", $values);
		
		$connection = Connection::getInstance();
		$connection->exec($sql);
		return true;
	
	}

}
?>