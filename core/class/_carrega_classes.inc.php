<?php

/**
 * CARREGA CLASSES
 *
 * ATENÇÃO: Não pode ser aberto diretamente sem o uso de bootstrap
 */
/**
 * Especifique no arquivo que está carregando este arquivo o caminho relativo
 * até o diretório base e salve como $baseUrl
 */
if(!defined('THIS_TO_BASEURL')){
    define('THIS_TO_BASEURL', '');
}

if(!defined('CLASS_FILE_SUFIX')){
    define('CLASS_FILE_SUFIX', '.class');
}

/**
 * Função que carrega classes automaticamente
 *
 * @param string $classe Nome da classe a ser carregada
 */
function __autoload($classe){
    if( is_file(THIS_TO_BASEURL.CLASS_DIR.$classe.".php") ){
        include_once(THIS_TO_BASEURL.CLASS_DIR.$classe.".php");
    } else if( is_file(THIS_TO_BASEURL.CLASS_DIR.$classe."".CLASS_FILE_SUFIX.".php") ){
        include_once(THIS_TO_BASEURL.CLASS_DIR.$classe."".CLASS_FILE_SUFIX.".php");
    }
}

/**
 * HELPERS
 */
include(THIS_TO_BASEURL.CLASS_DIR."Helpers"         .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."helpers/EmbedForms".CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."helpers/Html".CLASS_FILE_SUFIX.".php");

?>