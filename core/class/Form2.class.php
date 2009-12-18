<?php

class Form {
	var $Propriedades;
	
	function __construct($propriedades){ //$argclass = 'simples', $argaction = '', $argmethod = 'post', $argnome = 'form', $argenctype='', $argonsubmit=''){
		foreach($propriedades as $key=>$valor){
			$this->Propriedades[$key] = $valor;
		
		}
	}

	function IniciaForm(){
		echo '<form';
		foreach($this->Propriedades as $key=>$valor){
			echo ' '.$key.'="'.$valor.'"';
		}
		if(empty($this->Propriedades[action])) echo ' action="'.$_SERVER['PHP_SELF'].'"';
		echo '>';
	}
	
	function FinalizaForm(){
		echo '</form>';
	}
/*	
	function CriaCampos($campos){
		foreach ($campos as $key1=>$valor1) {
			echo '<div class="campo">';
			if(!empty($campos[$key1][label])){
				echo '<label>';
				echo $campos[$key1][label];
				echo '</label>';
			}
	        echo '<input';
			
		    foreach ($valor1 as $key2=>$valor2) {
				
				if($key2 <> 'label'){
					echo ' '.$key2.'="'.$valor2.'"';
				}
	        echo ' />';
			echo '</div>';
			
		}
	
	
	}
*/
}

class Campo extends Form {
	function __construct($propriedades = Array('')){ //$argclass = 'simples', $argaction = '', $argmethod = 'post', $argnome = 'form', $argenctype='', $argonsubmit=''){
		echo '<div class="campo">';
		if(!empty($propriedades[label])){
			echo '<label>';
			echo $propriedades[label];
			echo '</label>';
		}
		echo '<input';
		
		foreach ($propriedades as $key1=>$valor1) {
			if($key1 <> 'label'){
				echo ' '.$key1.'="'.$valor1.'"';
			}
		}
		echo ' />';
		echo '</div>';
	}

}

?>