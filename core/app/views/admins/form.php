<?php
/*
 * FORMULÁRIO ADMINS
 *
 * Este formulário serve tanto para edição como para criação, dependendo da variável $fm('criar','editar')
 */

$fm = (empty($_GET['fm'])) ? $fm = 'criar' : $fm = $_GET['fm'];
$w = (empty($_GET['w'])) ? $w = User::getInstance()->LeRegistro('id') : $w = $_GET['w'];

$dados = array(
	'id' => '',
	'name' => '',
	'password' => '',
	'email' => '',
	'description' => '',
	'login' => '',
);

if($fm == 'editar'){
	$sql = "SELECT
				admins.*,
				admin_photos.id as pid,
				(
					SELECT id FROM admin_photos WHERE file_type='secondary' AND admin_id=admins.id
				) as sid
			FROM
				admins
			LEFT JOIN
				admin_photos
			ON
				admins.id=admin_photos.admin_id
				AND admin_photos.file_type='primary'
			WHERE
				admins.id='".$w."'";
	$query = Connection::getInstance()->query($sql);
	$dados = $query[0];
	//echo $sql;
}
?>

<h2>Novo Usuário</h2>
<p>
	<strong>Cadastre</strong> a seguir um novo usuário para este gerenciador.
</p>

<form method="post" action="adm_main.php?section=admins&action=save" enctype="multipart/form-data">
<input type="hidden" name="metodo" value="<?php echo $fm?>">
<input type="hidden" name="w" value="<?php ifisset($dados['id'])?>">

<table cellpadding=0 cellspacing="3" class="form">
<tr>
	<td class="first" valign="top">Grupo:</td>
	<td class="second">
		<div class="input_painel">
			<div class="containner">
				<?php
				/*
				 * Seleciona a hierarquia do usuário
				 *
				 * Se for edição do próprio perfil, não permite modificação
				 */
				//vd( in_array(strtolower(User::getInstance()->tipo), array('root','webmaster','administrador' ) ) );
				if( User::getInstance()->LeRegistro('id') != $dados['id'] AND
					in_array(strtolower(User::getInstance()->tipo), array('root','webmaster','administrador' ) ) )
				{
					?><div class="admins_types_radio"><?php

					$sql = "SELECT
								name, id, description
							FROM
								admin_groups
							WHERE
								public = 1";
					$query = Connection::getInstance()->query($sql);
					foreach($query as $result){
						?>
						<input type="radio" <?php if($fm == 'editar') makechecked($result['id'], $dados['admin_group_id']); else echo 'checked'; ?> name="frmadmin_group_id" value="<?php echo $result['id']?>" onclick="javascript: form_hierarquia(this.value);" /> <?php echo $result['name']?><br />
						<?php
					}
					?>
					</div>
					<div class="admins_types_descriptions">

					<?php
						foreach($query as $result){
							?>
							<p class="admin-hierarquia" id="hierarquia<?php echo $result['id']?>"><?php echo str_replace($result['name'], '<strong>'.$result['name'].'</strong>', $result['description']);?></p>
							<?php
						}
				}
				/**
				 * Não pode alterar a hierarquia
				 */
				else {
					$sql = "SELECT
								name, id, description
							FROM
								admin_groups
							WHERE
								id='".$dados['admin_group_id']."'";
						//echo $sql;
					$query = Connection::getInstance()->query($sql);
					$result = $query[0];
					?>
						<strong><?php echo $result['name'];?></strong> do sistema.
					<?php
				}
				?>
				</div>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td valign="top">Nome completo:</td>
	<td>
		<input class="text" type="text" name="frmname" value="<?php ifisset($dados['name'])?>" <?php if($fm == 'criar'){ echo 'onKeyUp="javascript: alreadyexists(this.value, \'name\', \'Digite o nome completo do usuário que será cadastrado.\',\'#999999\',\'Verifique se este usuário já não existe, pois este nome já foi cadastrado.\',\'red\',\'adm\');"'; } ?> />
		<p class="explanation" id="exists_name">
			Digite o nome completo do usuário que será cadastrado.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">Login (nome de usuário): </td>
	<td>
		<input class="text" type="text" name="frmlogin" value="<?php ifisset($dados['login'])?>" <?php if($fm == 'criar'){ echo ' onKeyUp="javascript: alreadyexists(this.value, \'login\', \'Este nome de usuário está disponível para cadastro.\',\'green\',\'Este nome de usuário já foi cadastrado. Escolha outro.\',\'red\',\'adm\');"'; } ?> />
		<p class="explanation" id="exists_login">
			Digite um login para este usuário. Isto nada mais é que um nome de usuário
			que será usado para poder entrar no gerenciador.
		</p>
	</td>
</tr>
<?php
/*
 * Se for para criar novo usuário, mostra campo de senha
 */
if($fm == 'criar'){ ?>
	<tr>
		<td valign="top">Senha de acesso: </td>
		<td>
			<input type="password" name="frmpassword" class="text" />
			<p class="explanation" >
				Senha para acesso ao gerenciador.
			</p>
		</td>
	</tr>
<?php
/*
 * Se for para editar um usuário, mostra campo de senha na forma hidden
 */
} else if($fm == 'editar'){ ?>
	<tr>
		<td valign="top">Senha de acesso: </td>
		<td>
			<input type="password" name="frmpassword" class="text" value="" />
			<p class="explanation" >
				Insira uma nova senha para alterar a atual ou
				deixe este campo em branco para não modificá-la.
			</p>
		</td>
	</tr>
<?php } ?>
	

<tr>
	<td valign="top">Twitter: </td>
	<td>
		<input class="text" type="text" name="frmtwitter" value="<?php if( !empty($dados['twitter']) ) echo $dados['twitter'] ?>" />
		<p class="explanation">
			Você tem Twitter? Ex.: 'usuário' ou '@usuário'.
		</p>
	</td>
</tr>
<tr>
	<td valign="top">Foto: </td>
	<td>
		<?php
		$imagesPath = IMAGE_VIEWER_DIR."visualiza_foto.php?table=admin_photos&fromfile=true&thumbs=yes&minxsize=20&minysize=100&myid=";
		if( !empty($dados['pid']) ){
			?>
			<img src="<?php echo $imagesPath.$dados['pid'] ?>">
			<br />
			<?php
		}
		?>
		<input type="file" name="photo" />
		<p class="explanation" id="exists_login">
			Deixe em branco para não alterar a atual.
		</p>
	</td>
</tr>
<?php
if( Config::getInstance()->getConfig('user_has_secondary_image') ){ ?>

	<tr>
		<td valign="top">Foto secundária: </td>
		<td>
			<?php
			if( !empty($dados['sid']) ){
				?>
				<img src="<?php echo $imagesPath.$dados['sid'] ?>">
				<br />
				<?php
			}
			?>
		
			<input type="file" name="secondary_photo" />
			<p class="explanation" id="exists_login">
				Deixe em branco para não alterar a atual.
			</p>
		</td>
	</tr>

<?php } ?>
<tr>
	<td colspan="2">
		<center>
			<input type="submit" value="Salvar">
		</center>
	</td>
</tr>
</table>

</form>