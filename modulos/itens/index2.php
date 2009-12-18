<?php
/**
 * Bootstrap do módulo
 *
 * Carrega dados necessário sobre o módulo
 *
 * @package Modulos
 * @name Index
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */
/**
 * Carrega configurações
 */
//echo MOD_DBSCHEMA;
//if( file_exists(MOD_DBSCHEMA) ){
//    include(MOD_DBSCHEMA);
//}
//pr($modDbSchema);

$configFile = MOD_CONFIG;
include($configFile);

/**
 * Classes
 */
/**
 * EDITE:
 *
 * Nome da classe padrão
 */
$standardClassName = 'Classe';

/**
 * !!! NÃO EDITE ABAIXO !!!
 *
 * Carrega classe do módulo e cria objeto
 */
$moduloNome = (empty($modInfo['className'])) ? $standardClassName : $modInfo['className'];
include($moduloNome.'.php');

$param = array(
    'conexao' => $conexao,
    'config' => $modInfo,
);

$modulo = new $moduloNome($param);
?>