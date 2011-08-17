<div class="pessoas">
	<h2>Grupos</h2>
	<p style="width: 500px">
		Adicione grupos adicionais de usuários. Lembre-se, grupos não podem ser excluídos,
		pois senão deixarão usuários órfãos (sem grupo).
	</p>
<?php
$admin = User::getInstance();

/* NOVO GRUPO */
if( !empty($_POST['new_group']) && !empty($_POST['name']) ){
	
	$sql = "SELECT id
			FROM admin_groups
			WHERE
				name LIKE '".$_POST['name']."'";
	
	$query = Connection::getInstance()->query($sql);
	if( count($query) < 1 ){
	
		$sql = "INSERT INTO
					admin_groups
				(name, description, public, created_on)
				VALUES
				('".$_POST['name']."', '".$_POST['description']."', '1', '".date('Y-m-d H:i:s')."')";
		$query = Connection::getInstance()->query($sql);
		?>
		<p style="color: green">Dados salvos com sucesso.</p>
		<?php
	} else {
		?>
		<p style="color: red">Já há um grupo com este nome.</p>
		<?php
	}
	
}

if( !empty($_POST['edit_group']) &&
	!empty($_POST['name']) &&
	!empty($_POST['id']) )
{
	
	$sql = "UPDATE
				admin_groups
			SET
				name='".$_POST['name']."',
				description='".$_POST['description']."'
			WHERE
				id='".$_POST['id']."'
			";
	$query = Connection::getInstance()->query($sql);
	?>
	<p style="color: green">Dados salvos com sucesso.</p>
	<?php
}

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
			*
		FROM
			admin_groups
		ORDER BY id ASC
		";
$query = Connection::getInstance()->query($sql);
//echo $sql;


?>
<script type="text/javascript">
	var groups = new Array();
</script>
<table class="listing pessoas">
<tr class="header">
	<td>
		Nome
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
	if( in_array($dados['name'], array("Webmaster", "Root") ) )
		continue;
?>
	<tr class="list">
		<td>
			<script type="text/javascript">
				groups[<?php echo $dados["id"]?>] = {
					'name': '<?php echo $dados["name"]?>',
					'description' : '<?php echo $dados["description"]; ?>'
				};
			</script>
		
			<?php echo $dados["name"]?>
		</td>

		<td style="color: #666; font-size: 0.8em">
			<?php echo $dados['description']; ?>
		</td>
		<td>
			<a href="javascript: void(0)" onclick="editGroup('<?php echo $dados["id"]; ?>')" style="text-decoration: none;" title="Editar"><img src="<?php echo IMG_DIR?>edit.png" alt="Editar" border="0" /></a>
		</td>
	</tr>
<?php
}
?>
</table>

<br />
<p>
	<a href="javascript: void(0)" onclick="$(this).parent().hide(); $('#new_group').show();">Novo grupo</a>
</p>
<div id="new_group" style="display: none">
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=admins&action=groups">
		
		<input type="hidden" name="id" value="" />
		Nome do novo grupo:
		<br />
		<input type="text" name="name" value="" />
		<br />
		Descrição:
		<br />
		<textarea name="description"></textarea>
		<br />
		
		<input type="submit" name="new_group" value="Salvar" />
	</form>
</div>

<div id="edit_group" style="display: none">
	<h2>Editar grupo</h2>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=admins&action=groups">
		
		<input type="hidden" name="id" value="" />
		Alterar nome:
		<br />
		<input type="text" name="name" value="" />
		<br />
		Alterar descrição:
		<br />
		<textarea name="description"></textarea>
		<br />
		
		<input type="submit" name="edit_group" value="Alterar" />
	</form>
</div>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=admins">Voltar</a>
</p>

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
// 	if( in_array( User::getInstance()->LeRegistro("group"), $navPermissoes['admins']['form'] ) ){
	if( UiPermissions::getInstance()->isPermittedSection() ){

		?>
		<div class="botao">
			<div class="bt_novapessoa">
				<a href="adm_main.php?section=admins&action=form&fm=criar"></a>
			</div>
		</div>
		<?php
	}
	?>
	<div class="botao">
		<div class="bt_grupos">
			<a href="adm_main.php?section=admins&action=groups"></a>
		</div>
	</div>
	<div class="botao">
		<div class="bt_permissoes">
			<a href="adm_main.php?section=permissoes"></a>
		</div>
	</div>
	<div class="botao">
		<div class="bt_dados">
			<a href="adm_main.php?section=admins&action=edit&fm=editar"></a>
		</div>
	</div>


</div>
