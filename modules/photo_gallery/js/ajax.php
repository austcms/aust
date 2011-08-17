<?php
/**
 * Ajax do Módulo
 *
 * @since v0.1.6 25/07/2009
 */

// PHP 5.3 needs this
date_default_timezone_set('America/Sao_Paulo');


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
include(CLASS_LOADER);
/**
 * Propriedades editáveis do sistema. Carrega todas as configurações da aplicação
 */
/**
 * Configurações de conexão do banco de dados
 */
include(CONFIG_DATABASE_FILE);

include(LIB_DIR."aust/aust_func.php");
/**
 * Conexão principal
 */
$conexao = Connection::getInstance();

/**
 * Configurações do core do sistema
 */
	include(CONFIG_DIR."core.php");
/**
 * Permissões de tipos de usuários relacionados à navegação
 */
	include(CONFIG_DIR."nav_permissions.php");
/**
 * Carrega o CORE
 */
	include(CORE_DIR.'load_core.php');

	
include('../index.php');

header("Content-Type: text/html; charset=".$aust_charset['view'],true);

/**
 * Função para retornar os cadastros do sistema no formato para <select>
 */
/**
 * LER TABELAS
 */
if($_POST['action'] == 'saveImageComment'){
	$sql = "UPDATE
				galeria_fotos_imagens
			SET
				texto='".addslashes( $_POST['comment'] )."'
			WHERE
				id='".$_POST["id"]."'";
	
	if( Connection::getInstance()->exec($sql) !== false )
		echo "1";
	else
		echo "0";
	
}
/**
 * Ler campos
 */
elseif($_POST['action'] == 'LeCampos'){

	/**
	 * Lê os campos da tabela e depois mostra um html <select> para o usuário
	 * escolher o relacionamento de tabelas
	 */
	$query = Connection::getInstance()->listaCampos($_POST['table']);
	foreach ( $query as $chave=>$value ){
		echo '<option value="'.$value['campo'].'">'.$value['campo'].'</option>';
	}

}
?>