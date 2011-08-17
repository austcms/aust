<?php
/**
 * DATA TYPES
 *
 * Trata variáveis e tipos de dados
 */

/**
 * setDataInArray()
 * 
 * Verifica se $key existe em $options e caso existe, retorna seu valor. Caso
 * contrário, retorna $default.
 * 
 * Isto possibilita não escrever mais comandos extensos como:
 * 
 *	  $var = (empty($options['var'])) ? '' : $options['var'];
 * 
 * Agora, basta substituir o código acima por:
 * 
 *	  $var = getDataInArray($options, 'var');
 * 
 * @param <array> $options
 * @param <string> $key
 * @param <string> $default
 * @return <string> 
 */
function getDataInArray($options = array(), $key = '', $default = ''){
	if( is_array($options)
		AND array_key_exists($key, $options) )
	{
		return $options[$key];
	} else {
		return $default;
	}
}

function serializeArray($array = array()){
	$result = array();
	foreach( $array as $key=>$value ){
		$result[] = $value;
	}
	return $result;
}

?>
