<?php
/*
 * Arquivo contendo principais funções para facilitar o processo de codificação
 *
 */
/*
 *
 * STRINGS
 *
 */
	// retorna uma array com valores em letra minúscula (semelhante a strtolower)
	function ArrayToLower($array,$round = 0){
		foreach($array as $key => $value){
			if(is_array($value))
				$array[strtolower($key)] =  $this->arraytolower($value,$round+1);
			else
				$array[strtolower($key)] = strtolower($value);
		}
		return $array;

	}
	/**
	 * Ajusta string, retirando acentos e tornando acessível na URL do browser
	 *
	 *
	 * @param string $str Texto a ser ajustado
	 *
	 * @return string Retorna texto codificado, sem acentos, em minúsculas, sem espaços
	 */
	function encodeText($str=''){

		// troca estas letras
		$changeTheseLetters = array('à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ü','ú','ÿ','À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ü','Ú');
		// por estas
		$forTheseLetters	= array('a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U');

		// troca estes acentos e pontos

		$changeTheseAccents = array('?','!',',','.',';',':','/','\\','|','\'','"','=','#','@','<','>','$','%','¨','&','*','(',')','^','~','´','{','}','[',']','-');
		// por estes acentos e pontos
		$forTheseAccents	= "";

		// efetua troca
		$str = str_replace($changeTheseLetters, $forTheseLetters, $str);
		$str = str_replace($changeTheseAccents, "", $str);

		// tira espaços
		$str = str_replace(" ", "_", $str);

		// modifica a string para minúsculo
		$str = strtolower( $str );

		return $str;

	}

	function redirect($url){
		header("Location: adm_main.php?".$url);
		exit();
	}

	/**
	 * lineWrap()
	 * 
	 * Em textos onde há um termo grande, sem espaços, como o seguinte:
	 * 
	 * 		este_é_um_texto_grande_sem_espaços
	 * 
	 * Pode ocorrer de quebrar a formatação HTML. Esta função quebra a
	 * palavra ao meio caso necessário.
	 */
	function lineWrap($text, $chars = '30'){
		preg_match('/(http:\/\/[^\s]+)/', $text, $link);
		if( !empty($link) ){
			$hypertext = "<a href=\"". $link[0] . "\">" . $link[0] . "</a>";
			$text = preg_replace('/(http:\/\/[^\s]+)/', $hypertext, $text);
		}
		
		// trunca termos
		$text = preg_replace("/([^ <>\"\\-]{".$chars."})/"," \\1 ",$text);
		
		// tira espaços de uma tag a
		$text = preg_replace("/(<a href=\")(.*)(\">)/e", '"$1".str_replace(" ", "", "$2")."$3"', $text);
		
		return $text;
	}

	function retrieveFile($path = "", $type = '', $filename = ''){
		if( empty($path) )
			return false;
		
		return LIBS_DIR.'functions/retrieve_file.php?path='.$path.'&type='.$type.'&filename='.$filename;
	}
	
	function getFileIcon($ext){
		$ext = explode('.', $ext);
		$ext = array_reverse($ext);
		$ext = reset($ext);
		
		$icons = array(
			'file_doc.png' => array(
				'doc', 'docx', 'otf', 'pages', 'dotx'
			),
			'file_ppt.png' => array(
				'ppt', 'pptx', 'pps', 'keynote'
			),
			'file_xls.png' => array(
				'xls', 'xlsx', 'numbers'
			),
			'file_zip.png' => array(
				'zip', 'tar', 'gz', 'ace', 'rar',
				'cab'
			),
			'file_pdf.png' => array(
				'pdf'
			),
			'file_jpg.png' => array(
				'jpg', 'png', 'gif', 'jpeg', 'bmp',
				'psd', 'tiff', 'graffle'
			),
		);
		$url = 'file.png';
		
		foreach( $icons as $file=>$extensions ){
			if( in_array($ext, $extensions) ){
				$url = $file;
				break;
			}
		}
		return IMG_DIR.'icons/files/'.$url;
	}

	/**
	 * convertFilesize()
	 *
	 * Pega os bytes de um arquivo em formato string e transforma
	 * para MB.
	 */
	function convertFilesize($bytes, $return = 'mb'){
		$size = $bytes;
		if( $size < 1000 )
			$decimals = 4;
		else if( $size < 10000 )
			$decimals = 3;
		else if( $size < 100000 )
			$decimals = 2;
		else
			$decimals = 1;
		$size = str_replace('.', ',', number_format($size/1000000, $decimals) );
		echo $size;
	}
	
	/**
	 *
	 * @param array $status
	 */
	function EscreveBoxMensagem($status){
		echo '<div class="box-full">
			<div class="box '.$status['classe'].'">
				'.$status['mensagem'].'
			</div>
		</div>';
	}

/**
 * Retorna o dia, mês, ano, hora, minuto ou segundo atual
 *
 * @param string $formato O que deve ser retornado (dia,mes,ano,hora,minuto,segundo)
 * @return string Número requisitado na data atual
 */
function PegaData($formato){
	$formato = StrToLower($formato);
	if ($formato == "dia") return date("d");
	else if ($formato == "mes") return date("m");
	else if ($formato == "ano") return date("Y");
	else if ($formato == "hora") return date("H");
	else if ($formato == "minuto") return date("i");
	else if ($formato == "segundo") return date("s");
}

/**
 * Função que retorna a data atual pronta para MySQL
 *
 * @param string $param 'horario' se desejar mostra o horário tambem
 * @return date Data atual no formato para o banco de dados
 */
function DataParaMySQL($param){
	if($param == 'horario'){
		return date("Y-m-d H:i:s");
	} else {
		return date("Y-m-d H:i:s");
	}
}

$sdia = PegaData("dia");
$smes = PegaData("mes");
$sano = PegaData("ano");
$shora = PegaData("hora");
$sminuto = PegaData("minuto");
$ssegundo = PegaData("segundo");

// criar class em menu de navegação selecionado
function MenuSelecionado($section, $section_atual, $class=''){
	// se $section atual é igual ao section do menu
	if($section == $section_atual){
		if(empty($class)){
			echo 'class="selecionado"';
		} else {
			echo 'class="'.$class.'"';
		}
	}
}

function mes($mes){
//	$mes = "". $mes;
	$meses = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro","Outubro","Novembro","Dezembro");
	return $meses[$mes-1];
}

/*
 *  escreve somente se $str <> ''. Se $result <> '', escreve result, senão escreve $str
 *
 *  Função ótima para dados do DB, pois ela formata strings (ex.: substitui \n por <br />
 */
function EscreveSeNaoVazioDoDB($str, $result = ''){
	if($str <> ''){
		if($result <> ''){
			echo str_replace("\n", "<br />", $result);
		} else {
			echo str_replace("\n", "<br />", $str);
		}
	}
}

// Dependendo da data, mostra "Hoje", "Ontem" ou a data no $formato especificado
function ShowDate($formato,$hoje="Hoje",$ontem="Ontem",$dia,$mes,$ano){
	$hdia = PegaData("dia");
	$hmes = PegaData("mes");
	$hano = PegaData("ano");
	if($ano == $hano AND $mes == $hmes){
		if($dia == $hdia)
			echo $hoje;
		else if ($dia == $hdia-1)
			echo $ontem;
		else
			echo str_replace(Array('&%dia','&%mes','&%ano'), Array($dia,mes($mes),$ano), $formato);
	} else {
		echo str_replace(Array('&%dia','&%mes','&%ano'), Array($dia,mes($mes),$ano), $formato);
	}
}

// Apaga de $string que estiver entre as strings $start e $end
function delete_between($string, $start, $end, $loop=1) {
	$done = false;
	for($i=0;$i < $loop; $i++){
		if(!$done){
			if (strpos($string, $start) === false || strpos($string,$end) === false) {
				$done = true;
			} else {
				$done = false;
				$start_position = strpos($string, $start);// + strlen($start);
				$end_position = strpos($string, $end);
				$string = substr_replace($string, "", $start_position, $end_position+1);// - $start_position);
			}
		} else {
			$i = $loop;
		}

	}
	return $string;
}			


####################################
#
# Banco de dados e amostragem
#
####################################

function BomDiaTardeNoite($hora){
	
	if($hora >= 18 OR $hora < 06){
		echo "Boa noite";
	} else if($hora >= 12 AND $hora < 18){
		echo "Boa tarde";
	} else if($hora >= 06 AND $hora < 12){
		echo "Bom dia";
	}
}

// faz verificações e escreve selected para <input>
function makeselected($campo, $value){ if(is_string($value) OR !empty($value)) { if($campo == $value) echo 'selected="selected"'; } }

// faz verificações e escreve checked para <input>
function makechecked($campo,$value){ if($campo == $value) echo 'checked'; }

// escreve $str se não estiver vazia, senão escreve $else
function ifisset($str,$else=''){
	if(!empty($str)){
		if($str <> ''){
			echo $str;
		}elseif($else <> ''){
			echo $else;
		}else{
			echo '';
		}
	} else {
		echo '';
	}
}

function PegaDados($campos,$tabela,$onde,$orderby,$orderby2){
	$fsql = "SELECT $campos FROM $tabela $onde ORDER BY $orderby $orderby2";
	echo $fsql;
	$fmysql = mysql_query($fsql);
	$fdados = mysql_fetch_array($fmysql);	
	return $fdados;
}

# cria estatistica (barra)
function stats($votos, $total){
	if($total < 1)
		echo 0;
	else {
		$stats = 100 * $votos;
		$stats = $stats / $total;
		$stats = ceil($stats);
		echo $stats;
	}
}

# ve se ip nao esta cadastrado no
# banco de dados
function ver_ip_db($enquete){
	$select = mysql_query("SELECT ip, enquete FROM votos WHERE ip='".$HTTP_ENV_VARS['REMOTE_ADDR']."' AND enquete='$enquete'") or die ("Erro no select: " . mysql_error());
	if(mysql_num_rows($select) == 0)
		return true;
	else
		return false;
}

function validacpf($cpf) {
/*
*/
	$nulos = array("12345678909","11111111111","22222222222","33333333333",
				   "44444444444","55555555555","66666666666","77777777777",
				   "88888888888","99999999999","00000000000");
	/* Retira todos os caracteres que nao sejam 0-9 */
	$cpf = ereg_replace("[^0-9]", "", $cpf);
	
	/*Retorna falso se houver letras no cpf */
	if (!(ereg("[0-9]",$cpf)))
		return 0;
	
	/* Retorna falso se o cpf for nulo */
	if( in_array($cpf, $nulos) )
		return 0;
	
	/*Calcula o penúltimo dígito verificador*/
	$acum=0;
	for($i=0; $i<9; $i++) {
	  $acum+= $cpf[$i]*(10-$i);
	}
	
	$x=$acum % 11;

	$acum = ($x>1) ? (11 - $x) : 0;
	/* Retorna falso se o digito calculado eh diferente do passado na string */
	if ($acum != $cpf[9]){
	  return 0;
	}
	/*Calcula o último dígito verificador*/
	$acum=0;
	for ($i=0; $i<10; $i++){
	  $acum+= $cpf[$i]*(11-$i);
	}  
	
	$x=$acum % 11;
	$acum = ($x > 1) ? (11-$x) : 0;
	/* Retorna falso se o digito calculado eh diferente do passado na string */
	if ( $acum != $cpf[10]){
	  return 0;
	}  
	/* Retorna verdadeiro se o cpf eh valido */
	return 1;
}

####################################
#
# RETICENCIAS
#
####################################
function FormatarStringNumCaracteres($string, $caracteres) {
	$comp = strlen($string);
	$sair = 0;
	$string2 = "";
	if ( $comp < $caracteres ) {
		$string2 = $string;
	} else {
		for ( $i = $caracteres ; $i > 0 ; $i-- ) {
			$char = substr($string, $i, 1);
			if ( ($char == " ") && ($sair == 0) ) {
				$string2 = substr($string, 0, $i); // Passa o Texto para a variável.
				$sair = 1;
			} // Fim do IF.
		} // Fim fo LOOP.
		$string2 .= "...";
	} // Fim do IF.
	return $string2;
} // Fim da Função


/*********************
*
*	Funções para lidar com DB/MySQL
*
*********************/

		function PegaMysqlNumRows($sql){
			$mysql = mysql_query($sql);
			return mysql_num_rows($mysql);
		}
		

/*********************
*
*	ARQUIVOS E DIRETÓRIOS
*
**********************/

// pega extensão de nome de arquivo

function PegaExtensao($param){
	return getExtension($params);
}

function getExtension($param){
	$ext = explode('.',$param);
	$ext = array_reverse($ext);
	return $ext[0];
}

function loadHtmlEditor($params = ""){
	
	$plugins = '';
	$elements = '';
	
	if( !empty( $params ) &&
		is_string($params) )
	{
		$plugins = ','.$params;
	} else if( !empty( $params ) &&
				is_array($params) )
	{
		$plugins = '';
		$elements = '';
		if( !empty($params['plugins']) )
			$plugins = ','.$params['plugins'];

		if( !empty($params['elements']) )
			$elements = ','.$params['elements'];

	}
	
	echo '<script type="text/javascript">';
	echo 'var pluginsToLoad = "'.$plugins.'";';
	echo 'var elementsToLoad = "'.$elements.'";';
	echo '</script>';
	
	include_once(BASECODE_JS.'html_editor.php');
}

/*
 * DEBUG
 *
 * funções para facilitar debuggar o codigo
 */

	/**
	 * pr()
	 *
	 * Executa print_r() com tag <pre> ao redor
	 */
	function pr($var){
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}

	/**
	 * vd()
	 *
	 * Executa var_dump() com tag <pre> ao redor
	 */
	function vd($var){
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}



?>
