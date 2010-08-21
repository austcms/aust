<?php

/**
 * @todo - arrumar código todo
 */

function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality) {
    // Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
    // Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
    // Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
    // Author: Tim Eckel - Date: 12/17/04 - Project: FreeRingers.net - Freely distributable.
    //
    // Optional "quality" parameter (defaults is 3).  Fractional values are allowed, for example 1.5.
    // 1 = Up to 600 times faster.  Poor results, just uses imagecopyresized but removes black edges.
    // 2 = Up to 95 times faster.  Images may appear too sharp, some people may prefer it.
    // 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled.
    // 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
    // 5 = No speedup.  Just uses imagecopyresampled, highest quality but no advantage over imagecopyresampled.

    if (empty($src_image) || empty($dst_image)) { return false; }
    if ($quality <= 1) {
        $temp = imagecreatetruecolor ($dst_w + 1, $dst_h + 1);
        imagecopyresized ($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
        imagecopyresized ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
        imagedestroy ($temp);
    } elseif ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
        $tmp_w = $dst_w * $quality;
        $tmp_h = $dst_h * $quality;
        $temp = imagecreatetruecolor ($tmp_w + 1, $tmp_h + 1);
        imagecopyresized ($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
        imagecopyresampled ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
        imagedestroy ($temp);
    } else {
        imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    }
    return true;
}

//echo 'oaijdoiwad';

/**
 * Caminho deste arquivo até o root
 */
define('THIS_TO_BASEURL', '../../../');

/**
 * Variáveis constantes contendo comportamentos e Paths
 */
include_once(THIS_TO_BASEURL."core/config/variables.php");

/**
 * Carrega dados do DB
 */
require_once(THIS_TO_BASEURL.CONFIG_DIR.'database.php');
require_once(THIS_TO_BASEURL.CLASS_LOADER);
$conexao = Connection::getInstance();

/*
 * VARIÁVEIS
 *
 * TAMANHOS
 * - maxxsize: largura máxima. É necessário especificar maxysize também.
 *
 */

$myid       = (empty($_GET['myid']))        ? ''        : $_GET['myid'];        // id da imagem a ser aberta
$table      = (empty($_GET['table']))       ? 'imagens' : $_GET['table'];       // tabela onde a imagem se encontra
$thumbs     = (empty($_GET['thumbs']))      ? ''        : $_GET['thumbs'];      // yes|no: diz se deve ser tratada a imagem
$xsize      = (empty($_GET['xsize']))       ? ''        : $_GET['xsize'];       // xsize: tamanho X
$maxxsize   = (empty($_GET['maxxsize']))    ? ''        : $_GET['maxxsize'];    //
$ysize      = (empty($_GET['ysize']))       ? ''        : $_GET['ysize'];       // ysize: tamanho Y
$maxysize   = (empty($_GET['maxysize']))    ? ''        : $_GET['maxysize'];    //
$minxsize   = (empty($_GET['minxsize']))    ? ''        : $_GET['minxsize'];       // ysize: tamanho Y
$minysize   = (empty($_GET['minysize']))    ? ''        : $_GET['minysize'];    //

if (!empty($myid)){
	if (empty($myordem))
		$ordem = "";
	else if ($myordem > 0)
		$ordem = "AND ordem=$myordem";
	else
		$ordem = "AND ordem=1";
	if(empty($idfrom))
		$idfrom = "id";
        
//    switch($idfrom){
//        case "ref" :
		    $sql = "SELECT * FROM $table WHERE $idfrom='$myid' $ordem";
//            break; 
//    }   
        
} else {
    $sql = "SELECT id FROM Imagens";
    $result = mysql_query($sql);

    while ($r = mysql_fetch_array($result)){
        $ids[] = $r["id"];
    }
    $sql = "SELECT * FROM $tabelaimg WHERE id=".$ids[rand(0,count($ids)-1)];
}

//echo $sql;

$query = $conexao->query($sql);
//print_r($query);
$dados = $query[0];
if ($conexao->count($sql) > 0){

    //echo '<pre>';
    //print_r($query);
    //echo '</pre>';
    //echo 'oi';
    $fileType = $dados["tipo"];
    $fileContent = $dados["dados"];
    if($thumbs == "yes"){

        header("Content-Type: ".$fileType);

        $im = imagecreatefromstring($fileContent); //criar uma amostra da imagem original
        $largurao = imagesx($im);// pegar a largura da amostra
        $alturao = imagesy($im);// pegar a altura da amostra

        if( !empty($minxsize) AND !empty($minysize) ){
	
            $alturad = $minysize; // calcula a largura da imagem a partir da altura da miniatura
            $largurad = ($largurao*$alturad)/$alturao; // proporção

			if( $largurad < $minxsize ){
	            $largurad = $minxsize; // calcula a largura da imagem a partir da altura da miniatura
                $alturad = ($alturao*$largurad)/$largurao;
			}
        } else if(!empty($xsize) AND !empty($ysize)){
            $largurad = $xsize; // definir a altura da miniatura em px
            $alturad = $ysize;// calcula a largura da imagem a partir da altura da miniatura
        } else if(!empty($maxxsize) AND !empty($maxysize)){
            if($largurao > $maxxsize){
                $largurad = $maxxsize; // definir a altura da miniatura em px
                $alturad = ($alturao*$largurad)/$largurao;
            } else {
                $largurad = $largurao;
                $alturad = $alturao;
            }

            if($alturad > $maxysize){
                $alturad = $maxysize; // definir a altura da miniatura em px
                $largurad = ($largurao*$alturad)/$alturao;// calcula a largura da imagem a partir da altura da miniatura
            } 
            //echo $alturad;
        } else if(!empty($xsize)){
            $largurad = $xsize; // definir a altura da miniatura em px
            $alturad = ($alturao*$largurad)/$largurao;// calcula a largura da imagem a partir da altura da miniatura
        } else if(!empty($ysize)){
            $alturad = $ysize; // definir a altura da miniatura em px
            $largurad = ($largurao*$alturad)/$alturao;// calcula a largura da imagem a partir da altura da miniatura
        } else {
            $largurad = 60; // definir a altura da miniatura em px
            $alturad = ($alturao*$largurad)/$largurao;// calcula a largura da imagem a partir da altura da miniatura
        }


        $nova = imagecreatetruecolor($largurad,$alturad);//criar uma imagem em branco

//        $nova = imagecreatetruecolor($largurad,$alturad);//criar uma imagem em branco
        if(empty($quality)) $quality = 3;
        if($quality > 5) $quality = 3;
        if(empty($resample))
            fastimagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao,$quality);//copiar sobre a imagem em branco a amostra diminuindo conforma as especificações da miniatura
        else if($resample == "yes")
            fastimagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao,$quality);//copiar sobre a imagem em branco a amostra diminuindo conforma as especificações da miniatura
        else if($resample == "no")
            imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);//copiar sobre a imagem em branco a amostra diminuindo conforma as especificações da miniatura
        //if(empty($quality)) $quality = 80;

        /* INSERE MARCADAGUA */
        $automarca = "no";
        if ($automarca == "yes"){
            $watermark = imagecreatefrompng('../img/v2/marcapedroosorionet.png');
            $watermark_width = imagesx($watermark);  
            $watermark_height = imagesy($watermark);
            //$size = getimagesize($nova);  
            $dest_x = $largurad - $watermark_width;// - 5;  
            $dest_y = $alturad - $watermark_height;// - 5;
            //$dest_x = $size[0] - $watermark_width - 5;  
            //$dest_y = $size[1] - $watermark_height - 5;
            
            $ptr_white = imageColorAllocate($watermark,0,0,0);
            imageColorTransparent($watermark,$ptr_white);
            //imagealphablending($watermark, true);
            
            imagecopymerge($nova, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 60);
            //imagecopy($nova, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
            imagedestroy($watermark);            
            //echo "done";

            if($resample == "no") 
                imagejpeg($nova, '', 85);
            else
                imagejpeg($nova);//, '', $quality);
        } else {

            if( !empty($resample) AND $resample == "no")
                imagejpeg($nova, '', 90);
            else
                imagejpeg($nova);//, '', $quality);
        }
        
        
        imagedestroy($nova);
    } else {
        header("Content-Type: ". $dados["tipo"]);
        echo $dados["dados"];
    }
} else {
  $sql = "SELECT tipo,dados FROM $tabelaimg WHERE id='0'";
  $r = mysql_query($sql);

  Header("Content-Type: ".mysql_result($r,0,"tipo"));
  echo mysql_result($r,0,"dados");
}

?>