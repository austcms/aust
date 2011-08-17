<?php
	$page_title = 'Criar o primeiro usuário';
	require 'header.php';
$showForm = true;
$formOk = false;
$formError = false;
$passwordConfirmationError = false;

if( !empty($_POST['configurar']) AND
	($_POST['configurar'] == 'criar_admin') )
{
	$fieldsOk = true;
	$formOk = true;
	if( empty($_POST['frmnomeaust']) ) $fieldsOk = false;
	if( empty($_POST['frmemailaust']) ) $fieldsOk = false;
	if( empty($_POST['frmloginaust']) ) $fieldsOk = false;
	if( empty($_POST['frmsenhaaust']) ) $fieldsOk = false;
	if( empty($_POST['frmsenhaaust_confirmation']) ) $fieldsOk = false;

	if( $_POST['frmsenhaaust'] != $_POST['frmsenhaaust_confirmation'] ) $passwordConfirmationError = true;
	
	if( !$fieldsOk || $passwordConfirmationError ){
		$formError = true;
		$formOk = false;
	}

}


if( $formOk ){
	$sql = Connection::getInstance()->count("SELECT
				id
			FROM
				admins
			WHERE
				login='".$_POST['frmloginaust']."'");
	if($sql > 0){
		?>
		<h1 style="color: red;">Não!</h1>
		<p>Já existe um usuário cadastrado.</p>
		<p><a href="<?php echo THIS_TO_BASEURL ?>index.php">Ir à tela de login</a></p>
		<?php
		$showForm = false;
	} else {

		$sql = "INSERT INTO
					admins(admin_group_id, name, login, password, email, created_on)
				VALUES
					(
						(SELECT
							id
						FROM
							admin_groups
						WHERE
							name='Webmaster' order by id ASC limit 1),
					'{$_POST['frmnomeaust']}','{$_POST['frmloginaust']}','{$_POST['frmsenhaaust']}','{$_POST['frmemailaust']}','".date("Y-m-d H:i:s")."'
					)";

		$query = Connection::getInstance()->exec($sql);

		Aust::getInstance()->createFirstSiteAutomatically();

		if( $query ){ ?>
			<h1>Conta criada com sucesso</h1>
			<p>Parabéns, a instalação acabou.</p>
			<p><a href="<?php echo THIS_TO_BASEURL; ?>index.php">Ir à tela de login</a></p>
			<?php
			$showForm = false;
		} else {
			?>
			<h1 style="color: red;">Ops... Não foi possível cadastrar o usuário.!</h1>
			<p>Ocorreu um erro estranho. Entre em contato com o programador responsável por isto.</p>
			<p><a href="<?php echo THIS_TO_BASEURL ?>index.php">Voltar</a></p>
			<?php
			$showForm = false;
		}
	}
	?>

	<?php
} 

if( $showForm ){
	?>

	<h1>Primeira instalação</h1>
	<p>Não há administradores cadastrados. Insira a primeira pessoa abaixo.</p>
	<?php
	if( $formError ){
		?>
		<p class="error">Ops.. Você precisa preencher todos os campos.</p>
		<?php
	}
	?>
	
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="simples">
		<input type="hidden" name="configurar" value="criar_admin" />
		
		<label for="frmnomeaust">Seu nome:</label>
		<input type="text" id="frmnomeaust" name="frmnomeaust" value="" />

		<label for="frmemailaust">Seu email:</label>
		<input type="text" id="frmemailaust" name="frmemailaust" value="" />

		<label for="frmloginaust">Nome de usuário:</label>
		<input type="text" id="frmloginaust" name="frmloginaust" value="" />
	
		<label for="frmsenhaaust">Senha:</label>
		<input type="password" id="frmsenhaaust" name="frmsenhaaust" value="" />

		<?php if( $passwordConfirmationError ){
			?>
			<label for="frmsenhaaust_confirmation" class="error">Redigite a senha corretamente:</label>
			<?php
		} else {
			?>
			<label for="frmsenhaaust_confirmation">Redigite a senha:</label>
			<?php
		} ?>
		<input type="password" id="frmsenhaaust_confirmation" name="frmsenhaaust_confirmation" value="" />
	
		<br />
		<input type="submit" value="Enviar" />
	</form>

<?php
}
 require 'footer.php';
 ?>