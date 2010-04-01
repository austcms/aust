<?php
/**
 * Ajax do Módulo
 *
 * @package ModCadastro
 * @name adm_main.php
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 25/07/2009
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
    define('THIS_TO_BASEURL', '../../../');
}

/**
 * Variáveis constantes contendo comportamentos e Paths
 */
include_once(THIS_TO_BASEURL."core/config/variables.php");


/**
 * Classes do sistema
 */
include(THIS_TO_BASEURL.CLASS_DIR."_carrega_classes.inc.php");
/**
 * Propriedades editáveis do sistema. Carrega todas as configurações da aplicação
 */
/**
 * Configurações de conexão do banco de dados
 */
include(THIS_TO_BASEURL.CONFIG_DIR."database.php");

include(THIS_TO_BASEURL.LIB_DIR."aust/aust_func.php");
/**
 * Conexão principal
 */
$conexao = Connection::getInstance();

/**
 * Configurações do core do sistema
 */
    include(THIS_TO_BASEURL.CONFIG_DIR."core.php");
/**
 * Permissões de tipos de usuários relacionados à navegação
 */
    include(THIS_TO_BASEURL.CONFIG_DIR."nav_permissions.php");
/**
 * Carrega o CORE
 */
    include(THIS_TO_BASEURL.CORE_DIR.'load_core.php');

    
include('../index.php');

$modulo = new Cadastro;


header("Content-Type: text/html; charset=".$aust_charset['view'],true);

/**
 * Função para retornar os cadastros do sistema no formato para <select>
 */
/**
 * LER TABELAS
 */
if($_POST['action'] == 'LeCadastros'){
    $sql = "SELECT
                *
            FROM
                categorias
            WHERE
                tipo='cadastro'";
    //echo $sql;
    $arraytmp = $conexao->listaTabelasDoDBParaArray();
    //pr($arraytmp);
    foreach($arraytmp AS $valor){
        echo '<option value="'.$valor.'">'.$valor.'</option>';
    }
    
}
/**
 * Ler campos
 */
elseif($_POST['action'] == 'LeCampos'){

    /**
     * Lê os campos da tabela e depois mostra um html <select> para o usuário
     * escolher o relacionamento de tabelas
     */
    $query = $conexao->listaCampos($_POST['tabela']);
    foreach ( $query as $chave=>$valor ){
        echo '<option value="'.$valor['campo'].'">'.$valor['campo'].'</option>';
    }

}
?>