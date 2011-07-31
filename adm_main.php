<?php
/**
 * Este é o Bootstrap do sistema, carregando configurações, classes e as interfaces de usuário
 *
 * @package Interface
 * @name adm_main.php
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1.5
 * @since 25/07/2009
 */
/**
 * Cria SESSION
 */

session_name("aust");
session_start();

/**
 * Se não está definido o endereço deste arquivo até o root
 */
if(!defined('THIS_TO_BASEURL')){
    define('THIS_TO_BASEURL', '');
}

/**
 * Variáveis constantes contendo comportamentos e Paths
 */
include_once(THIS_TO_BASEURL."core/config/variables.php");


/**
 * Classes do sistema
 */
include(CLASS_LOADER);

$application = new Application();
?>