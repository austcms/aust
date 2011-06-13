<?php
/**
 * Carrega todas as configurações do CORE neste arquivo
 */

/**
 * Se não está definido o endereço deste arquivo até o root
 */
if(!defined('THIS_TO_BASEURL')){
    define('THIS_TO_BASEURL', '../');
}

/**
 * Carrega variáveis constantes contendo comportamentos e Paths
 */
include_once(THIS_TO_BASEURL."core/config/variables.php");

/**
 * Métodos usados pelo sistema
 */
include_once(CORE_DIR."libs/functions/func.php");
include_once(CORE_DIR."libs/functions/messages.php");
include_once(CORE_DIR."libs/functions/data_types.php");
include_once(CORE_DIR."libs/functions/string_treatment.php");
include_once(CORE_DIR."libs/functions/func_content.php");
include_once(CORE_DIR."libs/functions/func_text_format.php");
include_once(CORE_DIR."libs/functions/func_form_manipulation.php");
include_once(CORE_DIR."libs/functions/date.php");
/**
 * DBSCHEMA
 * Carrega o $dbschema
 */
    require_once(CORE_DIR.'config/installation/dbschema.php');
    $dbSchema = new dbSchema($dbSchema, $conexao);




?>
