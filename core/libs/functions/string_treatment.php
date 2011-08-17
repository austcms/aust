<?php

/**
 * @todo - esta função carece ser refatorada. Devia ter uma função
 * que fizesse todo o trabalho de codificar uma string, tirando acentos,
 * espaços, tornar minúsculas as letras, entre outros.
 */
//função que tira todos os acentos
function encodeString($str){
	$str = str_replace(' ', '_', $str );
	$str = mb_strtolower(  $str, "UTF-8" );

	$unwantedChars = array(
		'!','@','#','$','%','^','&',
		'\'', '"', '`', '´', '¨', '^', '~'
		//'0','1','2','3','4','5','6','7','8','9'
	);
	$useUnderline = array(
		'-', '/', '\\'
	);
	
	$str = str_replace($unwantedChars, "", $str);
	$str = str_replace($useUnderline, "_", $str);

	$str = str_replace(array('à','á','â','ã','ä'), "a", $str);
	$str = str_replace(array('è','é','ê','ë'), "e", $str);
	$str = str_replace(array('ì','í','î','ï'), "i", $str);
	$str = str_replace(array('ò','ó','ô','ö','õ'), "o", $str);
	$str = str_replace(array('ú','ù','û','ü'), "u", $str);

	$str = str_replace(array('À','Á','Â','Ä','Ã'), "A", $str);
	$str = str_replace(array('È','É','Ê','Ë'), "E", $str);
	$str = str_replace(array('Ì','Í','Î','Ï'), "I", $str);
	$str = str_replace(array('Ò','Ó','Ô','Ö','Õ'), "O", $str);
	$str = str_replace(array('Ù','Ú','Û','Ü'), "U", $str);
	$str = str_replace(array('ç'), "c", $str);
	$str = str_replace(array('Ç'), "C", $str);
	$str = str_replace(array('ñ'), "n", $str);
	$str = str_replace(array('Ñ'), "N", $str);

	return $str;
}

//alias
function RetiraAcentos($str){
	return encodeString($str);
}

/**
 * Takes out unwanted chars from the string
 */
function encodeDatabaseTableName($str){
	$unwantedChars = array(
		'!','@','#','$','%','^','&', '?', '+', '=', '(', ')',
		'<', '>', '*'
	);
	
	$str = str_replace($unwantedChars, "", $str);
	$str = encodeString($str);
	return $str;
}
/**
 * Treats string to be a database field name
 */
function encodeDatabaseFieldName($str){
	$unwantedChars = array(
		'!','@','#','$','%','^','&', '?', '+', '=', '(', ')',
		'<', '>', '*'
	);
	$str = str_replace($unwantedChars, "", $str);
	$str = encodeString($str);
	return $str;
}

/**
 * Sanitize String
 *
 * Add slashes and others.
 */
function sanitizeString($str){
	if( is_string($str) ){
		$str = addslashes($str);
	} else if( is_array($str) ){
		
		foreach( $str as $key=>$value){
			$str[$key] = addslashes($value);
		}
		
	}
	return $str;
}
?>