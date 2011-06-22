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
include(CONFIG_DATABASE_FILE);


/**
 * 
 * Carrega classes automaticamente
 *
 */
    include_once(CLASS_LOADER);


/**
 * Configurações do core do sistema
 */
include(CONFIG_DIR.'core.php');
include(CORE_CONFIG_DIR.'core.php');

header("Content-Type: text/html; charset=".$aust_charset['view']);

/**
 * Propriedades do core
 */
/**
 * Métodos usados pelo sistema
 */
include(LIB_DIR.'functions/func.php');
include(LIB_DIR.'functions/func_content.php');
include(LIB_DIR.'functions/func_text_format.php');

// aust_func
include(LIBS_DIR.'aust/aust_func.php');

// Conexão
$conexao = Connection::getInstance();
$administrador = User::getInstance();
$aust = new Aust($conexao);

if( is_file('ajax/aj_'.$_GET['lib'].'.php') ){
    include('ajax/aj_'.$_GET['lib'].'.php');
} else {
    echo '';
}

//print_r($_POST);

?>                                                                                                           
