<?php
/* 
 * INSTALLATION MODEL
 */

/**
 * Description of CadastroInstall
 *
 * @author kurko
 */
class CadastroSetup extends ModsSetup {

	/**
	 * @var $fieldTypes array Contém os tipos de campos permitidos
	 */
	private $fieldTypes = array(
		'string' => array(
			
		),
		'text' => array(
		
		),
		'date' => array(
	
		),
		'pw' => array(

		),
		'file' => array(

		),
		'relacional_umparaum' => array(

		),
		'relacional_umparamuitos' => array(

		),
	);
	
    function  __construct() {  }

	/**
	 * isAllowedFieldType()
	 *
	 * Retorna true se o tipo de campo passado é válido
	 *
	 * @return boolean
	 * @author Alexandre de Oliveira
	 **/
	function isAllowedFieldType($type = ""){
		if( empty($type) ) return false;
		if( array_key_exists($type, $this->fieldTypes) ) return true;
		else return false;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Alexandre de Oliveira
	 **/
	function encodeTableName(){
	}
}
?>
