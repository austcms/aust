<?php
/*
 * Verifica dados de usuário para Logar no sistema
 *
 * Este arquivo é carregado via include e não deve ser usado individualmente
*/

//sleep(1);

header("Cache-Control: no-cache, must-revalidate");

include_once(CONFIG_CORE_FILE);
include_once(CONFIG_DATABASE_FILE);

/**
 * Carrega classes necessárias
 */
include_once(CLASS_LOADER);

$conexao = Connection::getInstance();

/*********************************
	*
	*	verifica se usuário existe
	*
	*********************************/

$login1 = str_replace("'","''",$_POST["login"]);
$senha1 = str_replace("'","''",$_POST["senha"]);
$sql = "SELECT
			admins.*, admin_groups.name as 'group'
		FROM
			admins
		LEFT JOIN
			admin_groups
		ON admins.admin_group_id=admin_groups.id
		WHERE
			admins.login='$login1' AND
			admins.password='$senha1'
		LIMIT 0,1
		";

$query = Connection::getInstance()->query($sql);

$blocked = reset($query);

if( !empty($blocked['is_blocked']) )
	$blocked = $blocked['is_blocked'];
else
	$blocked = '0';

/*
 * Usuário aceito
 */
if( !empty($query)
	AND $blocked == '0'
	)
{
	$dados = $query[0];

	$_SESSION['login']['id'] = $dados["id"];
	$_SESSION['login']['username'] = $login1;

	$_SESSION['loginlogin'] = $login1;
	$_SESSION['loginid'] = $dados["id"];
	$_SESSION['loginnome'] = $dados["name"];
	$_SESSION['logintipo'] = $dados["group"];
	header("Location: adm_main.php");
}
/*
 * Usuário bloqueado
 */
elseif( $blocked == '1' ) {
	header("Location: index.php?status=102");
}
/*
 * Dados incorretos
 */
else {
	header("Location: index.php?status=101");
}
exit(0);
?>