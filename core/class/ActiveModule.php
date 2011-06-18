<?php
/**
 * ACTIVE MODULE
 *
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.2, 17/06/2011
 */
class ActiveModule
{
	
	public $austNode;
	
	function __construct($austNode){
		$this->austNode($austNode); 
	}
	
	public function directory(){
		$this->directory = Aust::getInstance()->LeModuloDaEstrutura($this->austNode()).'/';
		return $this->directory;
	}
	
	public function austNode($int = ""){
		if( empty($int) &&
		 	!empty($this->austNode) ){
			return $this->austNode;
		}
		
		elseif( !empty($int) ){
			$this->austNode = $int;
			return true;
		}
		
		return false;
	}
	
}
?>