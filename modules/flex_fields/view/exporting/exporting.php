<?php
/*
 * Client asked for exporting emails one per line. This file does that.
 */

session_name("aust");
session_start();

/**
 * Se não está definido o endereço deste arquivo até o root
 */
if(!defined('THIS_TO_BASEURL')){
	define('THIS_TO_BASEURL', '../../../../');
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
$model = new Model($conexao);

include(CORE_CONFIG_DIR."core.php");

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

	$aust = new Aust(Connection::getInstance());
	$modDir = Aust::getInstance()->structureModule($aust_node).'/';
	include(MODULES_DIR.$modDir.MOD_CONFIG);
	include(MODULES_DIR.$modDir.'Cadastro.php');
	$param = array(
		'config' => $modInfo,
		'user' => array(),
	);
	$modulo = new Cadastro($param);

	$sql = "SELECT valor
			FROM
				flex_fields_config
			WHERE
				tipo='filtros_especiais' AND
				chave='".$_GET["option"]."' AND
				categorias_id='".$_GET["aust_node"]."'";

	$filtroEspecial = Connection::getInstance()->query($sql);
	
	$module->configurations();
	$tabela = $module->configurations['structure']['table']['valor'];
	
	if( !empty($filtroEspecial[0]) )
		$filtroEspecial = $filtroEspecial[0]["valor"];

	if( !empty($filtroEspecial) ){
		$sql = "SELECT
					t.".$filtroEspecial."
				FROM
					".$tabela." as t
				GROUP BY
					t.".$filtroEspecial."
				ORDER BY t.id DESC
				";
		
		$emails = array();
		$email = Connection::getInstance()->query($sql);
		foreach( $email as $value ){
			$emails[] = $value[$filtroEspecial];
		}

		echo implode("<br />", $emails);
	}
	exit();
		?>
		Emails: <input type="text" size="25" value="<?php  ?>" />
		<br clear="all" />
?>