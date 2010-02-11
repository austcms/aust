<?php

/**
 * CARREGA CLASSES
 *
 * ATENÇÃO: Não pode ser aberto diretamente se o uso de bootstrap
 */
/**
 * Especifique no arquivo que está carregando este arquivo o caminho relativo
 * até o diretório base e salve como $baseUrl
 */
if(!defined('THIS_TO_BASEURL')){
    define('THIS_TO_BASEURL', '');
}

/**
 * Carrega as classes
 */
include(THIS_TO_BASEURL.CLASS_DIR."CoreConfig"      .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."SQLObject"       .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."dbSchema"        .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."Conexao"         .CLASS_FILE_SUFIX.".php");

include(THIS_TO_BASEURL.CLASS_DIR."Migrations"      .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."MigrationsMods"  .CLASS_FILE_SUFIX.".php");

//include(THIS_TO_BASEURL.CLASS_DIR."Form".CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."Config"          .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."Aust"            .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."Administrador"   .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."Modulos"         .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."UI"              .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."Themes"          .CLASS_FILE_SUFIX.".php");

include(THIS_TO_BASEURL.CLASS_DIR."Widgets"         .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."Widget"          .CLASS_FILE_SUFIX.".php");

include(THIS_TO_BASEURL.CLASS_DIR."Permissoes"      .CLASS_FILE_SUFIX.".php");
/**
 * MVC
 */
include(THIS_TO_BASEURL.CLASS_DIR."Controller"      .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."Model"           .CLASS_FILE_SUFIX.".php");
/**
 * APP-MVC
 */
include(THIS_TO_BASEURL.CLASS_DIR."ModsController"      .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."ModsSetup"      .CLASS_FILE_SUFIX.".php");

/**
 * HELPERS
 */
include(THIS_TO_BASEURL.CLASS_DIR."Helpers"         .CLASS_FILE_SUFIX.".php");
include(THIS_TO_BASEURL.CLASS_DIR."helpers/EmbedForms".CLASS_FILE_SUFIX.".php");
//include(THIS_TO_BASEURL.HELPERS_DIR."Form"          .CLASS_FILE_SUFIX.".php");

/**
 * Função que carrega classes automaticamente
 *
 * @param string $classe Nome da classe a ser carregada
 */
function __autoload($classe){
    //include_once(THIS_TO_BASEURL.CLASS_DIR.$classe."".CLASS_FILE_SUFIX.".php");
}


?>