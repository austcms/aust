<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function groupedDataFormat($options){

	foreach( $options as $key=>$value ){

		if( $key != "_options" ){
			$field[$key] = $value;
		}

	}

	if( !empty($options["_options"]["type"]) ){
		$t = $options["_options"]["type"];

		if( $t == "date" ){

			$field = array_reverse($field);
			if( in_array("", $field) )
				return false;
		}

	}

	if( !empty($options["_options"]["divisor"]) ){
		$divisor = $options["_options"]["divisor"];
	} else {
		$divisor = "";
	}

	if( !empty($field) )
		return implode($divisor, $field);

	return false;

}


?>