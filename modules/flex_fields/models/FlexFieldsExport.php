<?php
/**
 * Module's model class
 *
 * @since v0.1.6, 09/07/2009
 */
class FlexFieldsExport extends ModuleExport {

	/*
	 * EXPORT
	 */
	public function export(){

		$data = $this->module->pegaInformacoesCadastro();

		return array( $data );
	}
	
	public function import($data = array(), $st = ''){
		if( empty($data) || empty($st) )
			return false;
		
		if( empty($data) || empty($data[0]) )
			return false;
		if( !empty($data[0]) )
			$data = reset($data);
		
		$stId = $st['id'];
		
		$newData = array();
		
		$i = 0;
		$fieldNames = array();
		$fieldValues = array();

		$connection = Connection::getInstance();
		/*
		 * Salva configurações das tabelas e campos
		 */
		$connection->exec("DELETE FROM flex_fields_config WHERE categorias_id='".$st['id']."'");

		/*
		 * Cria tabelas e campos físicos
		 */
		if(class_exists('CadastroSetup') != true)
			include_once(dirname(__FILE__).'/CadastroSetup.php');
		
		$setup = CadastroSetup::getInstance();
		$setup->austNode = $stId;
		
		$params = array(
			'name' => $st['nome'],
			'austNode' => $stId,
			'author' => $st['autor'],
		);

		foreach( $data['config'] as $value ){
			$params['options'][$value['chave']] = $value['valor'];
		}
		
		$i = 0;
		foreach( $data['campo'] as $value ){
			$params['fields'][] = array(
				'name' => $value['valor'],
				'type' => $value['especie'],
				'description' => $value['description'],
				'refTable' => $value['ref_tabela'],
				'refField' => $value['ref_campo'],
			);
			$i++;
		}
		
		$setup->createStructure($params);
		
		return true;
	
	}

}
?>