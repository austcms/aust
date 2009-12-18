<?php
/**
 * Instalação Bootstrap
 *
 * Este arquivo é o centralizador da instalação, carregando outros arquivos
 * responsáveis por verificações e instalação do sistema.
 *
 * @package Instalação
 * @name instalar.php
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.5 25/07/2009
 */
/**
 * Carrega instalação
 */
/**
 * !!!EDITAR!!!
 *
 * Caminho relativo deste arquivo para o diretório base. Edite
 * este arquivo quando você modificar o arquivo instalar.php de local.
 */
define('THIS_TO_BASEURL', '../');

/**
 * Configurações do core
 */
include(THIS_TO_BASEURL."core/config/variables.php");

/**
 * Carrega todas as classes do sistema
 */
include(THIS_TO_BASEURL.CLASS_LOADER);

/**
 * Carrega configurações do sistema
 */
include(THIS_TO_BASEURL.CONFIG_DIR."core.php");
include(THIS_TO_BASEURL.CONFIG_DIR."database.php");

/**
 * Carrega o setup
 */
require(THIS_TO_BASEURL.RELATIVE_PATH_TO_INSTALLATION.'instalar.php');
?>