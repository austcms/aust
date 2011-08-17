<?php
/*
 *
 * SETUP
 *
 * Devem ser carregadas configurações de banco de dados, além de classes e o schema das tabelas
 * Se os arquivos a seguir estão comentados, é porque já está sendo carregado a partir de outro script que
 * está carregando este.
 *
 */

/*
 * Configuraçãoes
 *	  include("../config/database.php");
 */

/*
 * Carrega todas as classes
 *	  include("../class/Conexao.class.php");
 *	  include("../class/Config.class.php");
 *	  include("../class/Administrador.class.php");
 */
	
/*
 * Schema das tabelas
 *	  require("config/installation/sql_para_construir_db.php");
 */
	
include_once(CONFIG_DATABASE_FILE);
$dbConn = DATABASE_CONFIG::$dbConn;
$conexao = new Connection($dbConn);

include_once(LOAD_CORE);

/**
 * Verifica se banco de dados existe
 */
if(Connection::getInstance()->dbExists()){

	/**
	 * Faz verificação do Schema
	 *
	 * O resultado é guardado em dbSchema::schemaStatus
	 */
	$dbSchema = dbSchema::getInstance();
	$dbSchema->verificaSchema();

	/**
	 * Defeitos encontrados:
	 *	  0: Nenhuma tabela existe, é necessário instalação completa
	 *	 -1: Todas as tabelas existém, mas alguns campos não
	 *	 -2: Algumas tabelas não existem
	 */
	if(
		$dbSchema->schemaStatus == 0 OR
		$dbSchema->schemaStatus == -1 OR
		$dbSchema->schemaStatus == -2
	) {

		$dbSchema->instalarSchema();
		
	}
	$dbSchema->verificaSchema();
	/**
	 * Todas as tabelas instaladas
	 */
	if($dbSchema->schemaStatus == 1){

		if( !User::getInstance()->hasUser() ){
			require('create_admin.php');
		}
		/**
		 * Está tudo ok, volta para a tela de login no root index.php
		 */
		else {
			header('Location: '.THIS_TO_BASEURL.'index.php');
			exit();
		}
	}

}
/**
 * Banco de dados inexistente
 */
else {
	echo 'Não existe DB. Por favor entre contato com o administrador ou disponibilize acesso a um DB.';
}
?>