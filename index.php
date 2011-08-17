<?php
/**
 * Projeto AustCMS
 *
 * Este arquivo é o centralizador, carregando outros arquivos e classes responsáveis
 * por verificações do setup do sistema, e também login.
 *
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
include(CLASS_LOADER);

include("config/core.php");

if( !file_exists(CONFIG_DATABASE_FILE) ){
	$errorStatus = "no_database_configuration";
	require INSTALLATION_DIR."messages.php";
	exit();
}

include(CONFIG_DATABASE_FILE);

require(INSTALLATION_DIR."dbschema.php");

$conexao = Connection::getInstance();

if( !is_dir('uploads') ){
	if( !@mkdir('uploads', 0777) ){
		$errorStatus = "no_permission_create_uploads";
		require INSTALLATION_DIR."messages.php";
		exit();
	}
}
if( !is_dir('uploads/editor') ){
	if( !@mkdir('uploads/editor', 0777) ){
		$errorStatus = "no_permission_create_uploads_editor";
		require INSTALLATION_DIR."messages.php";
		exit();
	}
}
require_once("core/load_core.php");

// verifica se banco de dados existe
if( Connection::getInstance()->dbExists() ){

	/**
	 * Faz verificação do Schema
	 *
	 * O resultado é guardado em dbSchema::schemaStatus
	 */
	$dbSchema = dbSchema::getInstance();
	$dbSchema->verificaSchema();

	// verificação tabela por tabela quais existem ($db_tabelas é Array)
	if($dbSchema->schemaStatus == 1){
		/*
		 * Há aqui uma série de verificações para fazer o devido include
		 */

			// Se deve-se criar um admin no sistema (pois não há um)
			if( !empty($_POST['configurar']) &&
				($_POST['configurar'] == 'criar_admin') ||
				(!User::getInstance()->hasUser()) ){
				require(INSTALLATION_DIR.'create_admin.php');

			// Verificar se usuário (username&password) existe para login
			} elseif(!empty($_GET['login']) AND $_GET['login'] == 'verify') {
				require(LOGIN_PATH.'login_engine.php');

			// Página inicial, formulário de login
			} else {
				require(LOGIN_PATH.'login_form.php');
			}

	} elseif( in_array($dbSchema->schemaStatus, array(0, -1, -2) ) ) {
		require INSTALLATION_DIR."index.php";
	} else {

		// Ops.. algumas tabelas não existem
		echo 'Erro no sistema: 002.';
	}
} else {

	// Connection::getInstance()->ConstruirDB($sqlparaconstruirdb);

	// Ops.. Não há uma conexão funcionando
	echo 'Erro no Sistema: 001.';
}
?>