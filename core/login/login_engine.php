<?php
/*
 * Verifica dados de usuário para Logar no sistema
 *
 * Este arquivo é carregado via include e não deve ser usado individualmente
*/

//sleep(1);

header("Cache-Control: no-cache, must-revalidate");

include_once("config/core.php");
include_once("config/database.php");

/**
 * Carrega classes necessárias
 */
include_once(CLASS_DIR."/_carrega_classes.inc.php");

$conexao = new Conexao($dbConn);

/*********************************
	*
	*	verifica se usuário existe
	*
	*********************************/

$login1 = str_replace("'","''",$_POST["login"]);
$senha1 = str_replace("'","''",$_POST["senha"]);
$sql = "SELECT
            admins.*, admins_tipos.nome as tnome
        FROM
            admins
        LEFT JOIN
            admins_tipos
        ON admins.tipo=admins_tipos.id
        WHERE
            admins.login='$login1' AND
            admins.senha='$senha1'
        LIMIT 0,1
        ";

$query = $conexao->query($sql);

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

    $_SESSION['loginlogin'] = $login1;
    $_SESSION['loginid'] = $dados["id"];
    $_SESSION['loginnome'] = $dados["nome"];
    $_SESSION['logintipo'] = $dados["tnome"];
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