<?php
/**
 * Instalação Bootstrap
 *
 * Este arquivo é o centralizador da instalação, carregando outros arquivos
 * responsáveis por verificações e instalação do sistema.
 *
 * @since v0.1.5 25/07/2009
 */
/**
 * Carrega instalação
 */

// PHP 5.3 needs this
date_default_timezone_set('America/Sao_Paulo');

/**
 * !!!EDITAR!!!
 *
 * Caminho relativo deste arquivo para o diretório base. Edite
 * este arquivo quando você modificar o arquivo install.php de local.
 */
if( !defined('THIS_TO_BASEURL') )
	define('THIS_TO_BASEURL', '../');

/**
 * Configurações do core
 */
include_once(THIS_TO_BASEURL."core/config/variables.php");

/**
 * Carrega todas as classes do sistema
 */
include_once(CLASS_LOADER);

/**
 * Carrega configurações do sistema
 */
include_once(CONFIG_CORE_FILE);
include_once(CONFIG_DATABASE_FILE);

/**
 * Carrega o setup
 */
require_once(INSTALLATION_DIR.'install.php');
?>