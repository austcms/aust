<?php
/*
 * Bootstrap Ajax
 *
 * Carrega arquivos do diretório libs
 */

/**
 * Caminho deste arquivo até o root
 */
define('THIS_TO_BASEURL', '../../');

/**
 * Carrega variáveis contendo comportamentos e Paths
 */
include(THIS_TO_BASEURL.'core/config/variables.php');


/**
 * Propriedades editáveis do sistema. Carrega todas as configurações da aplicação
 */
/**
 * Carrega as configurações de conexão do banco de dados
 */
include(THIS_TO_BASEURL.CONFIG_DIR.'database.php');
/**
 * Configurações do core do sistema
 */
include(THIS_TO_BASEURL.CONFIG_DIR.'core.php');

header("Content-Type: text/html; charset=".$aust_charset['view']);

/**
 * Propriedades do core
 */
/**
 * Métodos usados pelo sistema
 */
include(THIS_TO_BASEURL.LIB_DIR.'functions/func.php');
include(THIS_TO_BASEURL.LIB_DIR.'functions/func_content.php');
include(THIS_TO_BASEURL.LIB_DIR.'functions/func_text_format.php');

/**
 * Função que carrega classes automaticamente
 *
 * @param string $classe Nome da classe a ser carregada
 */
function __autoload($classe){
    include_once(THIS_TO_BASEURL.CLASS_DIR.$classe.'.class.php');
}

// aust_func
include(THIS_TO_BASEURL.LIBS_DIR.'aust/aust_func.php');

// Conexão
$conexao = new Conexao($dbConn);

if($_GET['lib'] == 'user_permissoes'){
    include('ajax/user_permissoes.php');
} elseif($_GET['lib'] == 'widgets'){
    include('ajax/widgets.php');
}

//print_r($_POST);

?>                                                                                                           
