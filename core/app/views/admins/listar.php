<h2>Listando Usuários</h2>
<?php
/*
 * LISTAR ADMINS
 *
 * -> Lista os usuários do sistema
 */
$w = (!empty($_GET['w'])) ? $_GET['w'] : 'NULL';
$sql = "SELECT *
		FROM admins
		WHERE
			id='$w'";
$query = Connection::getInstance()->query($sql);
if( !empty($query) ){
	$dados = $query[0];
}

if( !empty($_GET['block'])
	AND $_GET['block'] == "block"
	AND (!empty($dados['login']) AND $dados["login"] <> "kurko"))
{

	$sql = "UPDATE
				admins
			SET
				tipo='0'
			WHERE
				id='$w'
			";

	// se executar query, EscreveBoxMensagem mostra mensagem padrão
	if(Connection::getInstance()->exec($sql)){
		$resultado = TRUE;
	} else {
		$resultado = FALSE;
	}

	if($resultado){
		$status['classe'] = 'sucesso';
		$status['mensagem'] = '<strong>Sucesso: </strong> Usuário bloqueado com sucesso!';
	} else {
		$status['classe'] = 'insucesso';
		$status['mensagem'] = '<strong>Erro: </strong> Erro ao bloquear usuário.';
	}
	EscreveBoxMensagem($status);

} elseif( !empty( $_GET['block'] )
		  AND $_GET['block'] == "unblock" ){
	$sql = "UPDATE
				admins
			SET
				tipo=
				(
					SELECT
						id
					FROM
						admin_groups
					WHERE
						nome='Colaborador')
			WHERE
				id='$w'
			";
	// se executar query, EscreveBoxMensagem mostra mensagem padrão
	if(Connection::getInstance()->exec($sql)){
		$resultado = TRUE;
	} else {
		$resultado = FALSE;
	}

	if($resultado){
		$status['classe'] = 'sucesso';
		$status['mensagem'] = '<strong>Sucesso: </strong> Usuário desbloqueado com sucesso!';
	} else {
		$status['classe'] = 'insucesso';
		$status['mensagem'] = '<strong>Erro: </strong> Erro ao desbloquear usuário.';
	}
	EscreveBoxMensagem($status);
}
?>
<p>
A seguir, a lista dos administradores do site cadastrados. Ao lado, há opções que podem ser tomadas quanto
ao respectivo usuário.
</p>
<p>
Legenda:
<br />
<img src="<?php echo IMG_DIR?>layoutv1/edit.jpg" alt="Editar" border="0" /> = Editar usuário
<br />
<img src="<?php echo IMG_DIR?>layoutv1/lupa.jpg" alt="Ver Informações" /> = Ver informações sobre o usuário
<br />
<img src="<?php echo IMG_DIR?>layoutv1/block.jpg" alt="Bloquear" /> = Bloquear acesso do usuário à administração
<br />
<img src="<?php echo IMG_DIR?>layoutv1/unblock.jpg" alt="Desblosquear" /> = Desbloquear acesso do usuário à administração

</p>
<?php
$sql = "SELECT
			admins.id, admins.name, admins.login, admins.admin_group_id AS atipo,
			admin_groups.nome AS tipo, admin_groups.id AS aid
		FROM
			admins
		LEFT JOIN
			admin_groups
		ON
			admins.admin_group_id=admin_groups.id
		";
$query = Connection::getInstance()->query($sql);
//echo $sql;
?>
<table width="680" class="listing">
	<tr class="header">
		<td>

		</td>
		<td>
			Nome completo
		</td>
		<td>
			Nome de Usuário
		</td>
		<td>
			Tipo
		</td>
		<td>
			Opções
		</td>
	</tr>
<?php
foreach($query as $dados){
?>
	<tr class="list">
		<td>
			<?php echo $dados["id"]?>
		</td>
		<td>
			<?php echo $dados["nome"]?>
		</td>
		<td>
			<?php echo $dados["login"]?>
		</td>
		<td>
			<?php if($dados["atipo"] == '0') echo 'Bloqueado'; else echo $dados["tipo"]; ?>
		</td>
		<td>
			<a href="adm_main.php?section=admins&action=ver_info&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="<?php echo IMG_DIR?>lupa.png" alt="Ver Informações" border="0" /></a>
			<?php
			if($dados["login"] <> "kurko"){
			?>
			<a href="adm_main.php?section=admins&action=form&fm=editar&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="<?php echo IMG_DIR?>edit.png" alt="Editar" border="0" /></a>
			<?php } ?>
				<?php
				if($dados["login"] <> "kurko"){
					if($dados["atipo"] == "0"){ ?>
						<a href="adm_main.php?section=admins&action=listar&block=unblock&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="<?php echo IMG_DIR?>layoutv1/unblock.png" alt="Desbloquear" border="0" /></a>
					<?php } else { ?>
						<a href="adm_main.php?section=admins&action=listar&block=block&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="<?php echo IMG_DIR?>layoutv1/block.png" alt="Bloquear" border="0" /></a>
					<?php } ?>
				<?php } ?>
		</td>
	</tr>
<?php
}
echo '</table>';
?>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=admins"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>