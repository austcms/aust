<?php
class ModuleExport
{
	/**
	 * VARIÁVEIS DE AMBIENTE
	 *
	 * Conexão com banco de dados, sistema Aust, entre outros
	 */
		/**
		 *
		 * @var <int> Contém o número do Nodo atual
		 */
		public $module;

	function __construct($className = '', $austNode) {
		
		$this->module = new $className;
		$this->module->austNode = $austNode;
	
	}
	
	function export($params = ''){
		return false;
	}
	
}
?>