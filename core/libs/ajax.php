<?php
/*
 * Bootstrap Ajax
 *
 * Carrega arquivos do diretório libs
 */
session_name("aust");
session_start();

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
$conexao = Connection::getInstance();
$administrador = new Administrador($conexao);
$aust = new Aust($conexao);

if( is_file('ajax/aj_'.$_GET['lib'].'.php') ){
    include('ajax/aj_'.$_GET['lib'].'.php');
} else {
    echo '';
}

//print_r($_POST);

?>                                                                                                           
