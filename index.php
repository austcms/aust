<?php
/**
 * Projeto AustCMS
 *
 * Este arquivo é o centralizador, carregando outros arquivos e classes responsáveis
 * por verificações do setup do sistema, e também login.
 *
 * @package index
 * @name index.php
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1.5
 * @since 25/07/2009
 */
session_name("aust");
session_start();

/**
 * Carrega variáveis do sistema
 */
require_once("core/config/variables.php");

/**
 * Carrega todas as classes do sistema
 */
include(CLASS_DIR."_carrega_classes.inc.php");

include("config/core.php");
include("config/database.php");

require("core/config/installation/dbschema.php");


$conexao = Connection::getInstance();



require_once("core/load_core.php");
//pr( $conexao->query('SELECT * FROM admins') );
/**
 * SCHEMA TEST
 */
/*
pr($dbSchema->verificaSchema());

$verificaSchema = $dbSchema->verificaSchema();

if($verificaSchema == 1){
    echo 'tudo ok';
} elseif($verificaSchema == 0) {
    echo 'Faltando tabelas';
    echo '<br />';
    if($dbSchema->instalarSchema() == 1){
        echo 'instalado';
        echo '<br />';
    } else {
        echo 'alguns erros na instalação';
        echo '<br />';
    }

} elseif($verificaSchema == -1){
    echo 'campos faltando<br />';
} elseif($verificaSchema == -2){
    echo 'tabelas faltando<br />';
}

echo '<br />';
echo '<br />';
echo '<br />';

*/

// verifica se banco de dados existe
if($conexao->DBExiste){

    /**
     * Faz verificação do Schema
     *
     * O resultado é guardado em dbSchema::schemaStatus
     */
    $dbSchema->verificaSchema();

    // verificação tabela por tabela quais existem ($db_tabelas é Array)
    if($dbSchema->schemaStatus == 1){

        /*
         * Há aqui uma série de verificações para fazer o devido include
         */

            // Se deve-se criar um admin no sistema (pois não há um)
            if( !empty($_POST['configurar']) AND ($_POST['configurar'] == 'criar_admin') OR (!$conexao->VerificaAdmin()) ){
                require('core/config/installation/criar_admin.inc.php');

            // Deve-se configurar o sistema
            } elseif(isset($_GET['configurar'])){
                require('core/config/installation/configurar.inc.php');

            // Verificar se usuário (username&password) existe para login
            } elseif(!empty($_GET['login']) AND $_GET['login'] == 'verify') {
                require('core/login/login_engine.php');

            // Página inicial, formulário de login
            } else {
                require('core/login/login_form.php');
            }

    } else {

        // Ops.. algumas tabelas não existem
        echo 'Erro no sistema: 002.';
    }
} else {

    // $conexao->ConstruirDB($sqlparaconstruirdb);

    // Ops.. Não há uma conexão funcionando
    echo 'Erro no Sistema: 001.';
}
?>