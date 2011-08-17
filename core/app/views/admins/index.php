<?php
if(	!empty($_GET['action']) &&
 	file_exists(INC_DIR.'admins.inc/'.$_GET['action'].'.inc.php') )
{
	include(INC_DIR.'admins.inc/'.$_GET['action'].'.inc.php');
} else {

	?>
	<div class="pessoas">
		<h2>Pessoas</h2>
		<p>
			Nesta página você pode gerenciar todos os usuários que administram o site.
		</p>
		<p>
			A seguir, a lista das pessoas cadastrados.
		</p>
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
	?>

	<?php
	$sql = "SELECT
				admins.*,
				admins.id, admins.name as 'name', admins.login, admins.admin_group_id AS 'atipo',
				admin_groups.name AS tipo, admin_groups.id AS aid
			FROM
				admins
			LEFT JOIN
				admin_groups
			ON
				admins.admin_group_id=admin_groups.id
			ORDER BY aid ASC
			";
	$query = Connection::getInstance()->query($sql);

	?>
	<table class="listing pessoas">
	<tr class="header">
		<td>

		</td>
		<td>
			Nome completo
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
				<?php echo $dados["name"]?>
			</td>

			<td>
				<?php if($dados["atipo"] == '0') echo 'Bloqueado'; else echo $dados["tipo"]; ?>
			</td>
			<td>
				<?php
				if($dados["login"] <> "kurko"){
					?>
					<a href="adm_main.php?section=admins&action=edit&fm=editar&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;" title="Editar"><img src="<?php echo IMG_DIR?>edit.png" alt="Editar" border="0" /></a>

				<?php
				}
				?>
				<a href="adm_main.php?section=admins&action=ver_info&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;" title="Ver Informações"><img src="<?php echo IMG_DIR?>lupa.png" alt="Ver Informações" border="0" /></a>
				<?php
				if( $dados["login"] <> "kurko"
					AND strtolower($dados["tipo"]) <> "webmaster"
					AND (
						UiPermissions::getInstance()->isPermittedSection(array('action' => 'form'))
						OR strtolower($dados["tipo"]) == "colaborador" 
					)
				){
					if($dados["is_blocked"] == "1"){
						?>
						<a href="adm_main.php?section=admins&block=unblock&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;" title="Desbloquear usuário"><img src="<?php echo IMG_DIR?>layoutv1/unblock.jpg" alt="Desbloquear usuário" border="0" /></a>
						<?php
					} else {
						?>
						<a href="adm_main.php?section=admins&block=block&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;" title="Bloquear usuário"><img src="<?php echo IMG_DIR?>layoutv1/block.png" alt="Bloquear usuário" border="0" /></a>
						<?php
					}
					?>
					<?php
				}
				?>
			</td>
		</tr>
	<?php
	}
	?>
	</table>

	</div>

	<div class="divisoria">
	</div>
	<div class="mais_opcoes">
		<h3>Mais opções</h3>
		<?php
		/*
		 * Verifica permissões
		 */
		/*
		 * Nova pessoa
		 */
		if( UiPermissions::getInstance()->isPermittedSection(array('action' => 'form')) ){
			?>
			<div class="botao">
				<div class="bt_novapessoa">
					<a href="adm_main.php?section=admins&action=form&fm=criar"></a>
				</div>
			</div>
			<?php
		}
		if( in_array( User::getInstance()->LeRegistro("group"), array("Webmaster", "Root", "Administrador") ) ){
			?>
			<div class="botao">
				<div class="bt_grupos">
					<a href="adm_main.php?section=admins&action=groups"></a>
				</div>
			</div>
			<?php
		}
		?>
		<div class="botao">
			<div class="bt_permissoes">
				<a href="adm_main.php?section=admins&action=permissions"></a>
			</div>
		</div>
		<div class="botao">
			<div class="bt_dados">
				<a href="adm_main.php?section=admins&action=edit&fm=editar"></a>
			</div>
		</div>


	</div>

	<?php
}
?>
