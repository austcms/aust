<?php
/**
 * Controller principal deste módulo
 *
 * @package ModController
 * @name nome
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 06/07/2009
 */

class ModsSetup
{

	public $connection;

    function __construct($param = array()){
        $param['controllerName'] = 'setup';
        $param['action'] = (empty($param['action'])) ? "" : $param['action'];
        
		$this->connection = Connection::getInstance();
        //parent::__construct($param);
    }

	function createStructure($params = array()){
		
	}
	
	/*
	 *
	 * SUPPORT METHODS
	 *
	 */
	
	/**
	 * encodeString
	 *
	 * @return string
	 * @author Alexandre de Oliveira
	 **/
	function encodeString($str){
		return encodeString($str);
	}

	/**
	 * encodeTableName
	 *
	 * @return string
	 * @author Alexandre de Oliveira
	 **/
	function encodeTableName($str){
		return encodeDatabaseTableName($str);
	}
	
	function setMainTableName($str){
		return $this->mainTable = $str;
	}
	
	/**
	 * encodeFieldName
	 *
	 * @return string
	 * @author Alexandre de Oliveira
	 **/
	function encodeFieldName($str){
		return encodeDatabaseFieldName($str);
	}
	
	/**
	 * Sanitize String
	 *
	 * @return void
	 * @author Alexandre de Oliveira
	 **/
	function sanitizeString($str){
		return sanitizeString($str);
	}
	
	/*
	 *
	 * SQL OUTPUT
	 *
	 */
	function setCommentForSql($str){
		$str = $this->sanitizeString($str);
		$str = "COMMENT '$str'";
		return $str;
		
	}

    
}
?>