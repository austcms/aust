<?php
// este é o arquivo onde está o formulário para efetuar login
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Aust - Gerenciador de Conteúdo</title>
	<link rel="stylesheet" href="core/login/index.css" type="text/css" />
	<script type="text/javascript" src="<?php echo BASECODE_JS; ?>100_jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#login').focus();
		});
		function formFadeOut(){
			$('.inside').fadeOut('fast');
			setTimeout( function(){
				$('.inside').html('<h2 class="wait">Verificando dados...</h2>');
				$('.inside').fadeIn('fast');

			}, 170);

			return true;
		}
	</script>

</head>

<body>

<div class="body">
	<div class="inside">

	<h1>Gerenciador Restrito</h1>
	<?php
	if (!empty($_GET['status'])){
		/*
		 * Senha incorreta
		 */
		if ($_GET['status'] == "101"){
			echo '<p class="incorrect">Dados incorretos.</p>';
		}
		/*
		 * Usuário existente mas bloqueado
		 */
		else if ($_GET['status'] == "102"){
			echo '<p class="incorrect">Sinto muito, seu acesso está bloqueado.</p>';
		}
		/*
		 * Usuário existente mas bloqueado enquanto navegava
		 */
		else if ($_GET['status'] == "103"){
			echo '<p class="incorrect">Ops... Seu acesso foi bloqueado '.
				'por um administrador neste exato momento.</p>';
			echo '<p>Entre em contato com um administrador.</p>';
		}
	}

	?>

	<form method="post" name="login_form" style="margin: 0;" action="index.php?login=verify"
		  onsubmit="return formFadeOut();">


		<table width="250" border="0" style="margin: 0 auto;" cellpadding="0" cellspacing="3">
		<col width="60" />
		<col />
		<tr>
			<td class="label">
				<label for="login">Usuário:</label>
			</td>
			<td class="input">
				<input type="text" id="login" name="login" />
			</td>
		</tr>
		<tr>
			<td class="label">
				<label for="passw">Senha:</label>
			</td>
			<td class="input">
				<input type="password" id="passw" name="senha" />
			</td>
		</tr>
		<tr>
			<td class="submit" colspan="2">
				<input type="submit" value="Entrar" />
			</td>
		</tr>
		</table>

	</form>
	</div>
</div>

</body>
</html>
