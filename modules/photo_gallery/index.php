<?php
/**
 * Bootstrap do módulo
 *
 * Carrega dados necessário sobre o módulo
 *
 * @since v0.1.5, 30/05/2009
 */
/**
 * Carrega configurações
 */
include(MOD_DBSCHEMA);
/**
 * Classes
 */
/**
 * EDITE:
 */



/**
 * !!! NÃO EDITE ABAIXO !!!
 *
 * Carrega classe do módulo e cria objeto
 */
$moduloNome = (empty($modInfo['className'])) ? 'Classe' : $modInfo['className'];
include($moduloNome.'.php');

$param = array(
	'conexao' => $conexao,
	'config' => $modInfo,
	'modDbSchema' => $modDbSchema,
);

$modulo = new $moduloNome($param);
unset( $modDbSchema );
?>