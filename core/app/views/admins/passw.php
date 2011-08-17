<h2>Alterar senha de acesso</h2>
<?php
$showform = 'yes';
if ($_POST['metodo'] == 'gravar'){
	$showform = 'no';
	$dia = PegaData("dia");
	$mes = PegaData("mes");
	$ano = PegaData("ano");
	$hora = PegaData("hora");
	$minuto = PegaData("minuto");
	
	$sql2 = "SELECT * FROM admins WHERE id='".$_POST['myid']."'";

	$query = Connection::getInstance()->query($sql2);
	if ( !empty($query) ){

		$dados2 = $query[0];
		$senha = $dados2["senha"];
		if ($_POST['frmsenhaatual'] <> $senha){
			echo '
					<h2 style="color: red">
						Ops...
					</h2>
					<p style="color: red">
						A senha atual digitada não corresponde à sua senha real.
					</p>';
			$showform = 'yes';
		} else if ($_POST['frmnovasenha'] == ""){
			echo '
					<h2 style="color: red">
						Ops...
					</h2>
					<p style="color: red">
						É necessário digitar uma nova senha.
					</p>';
			$showform = 'yes';
		} else {
			if ($_POST['frmnovasenha'] <> $_POST['frmconfirmacao']){
				echo '
						<h2 style="color: red">
							Ops...
						</h2>
						<p style="color: red">
							A nova senha não corresponde com a confirmação digitada.
						</p>';
				$showform = 'yes';
			} else {
			
				$sql2 = "UPDATE admins SET senha='".$_POST['frmnovasenha']."' WHERE id='{$_POST['myid']}'";
				if (Connection::getInstance()->exec($sql2)){
					echo '
							<h2 style="color: green">
								Senha modificada com sucesso
							</h2>
							<p>
								Parabéns! Senha modificada com sucesso.
							</p>
							<p>
								<a href="adm_main.php"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
							</p>
							
							';
					$showform = 'no';
				} else {
					echo '
							<h2 style="color: red">
								Falha ao modificar senha
							</h2>
							<p>
							Ocorreu um erro desconhecido ao modificar sua senha!
							</p>';
					$showform = 'yes';
				}
			}
		}
		
		
	} else {
		echo '
				<h2>
				Erro ao mudar senha!
				</h2>
				<p>
				Ocorreu um erro ao modificar sua senha.
				</p>
				<p>
					<a href="adm_main.php"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
				</p>
				';
	}
}



?>

<?php if($showform == 'yes'){ ?>
	<p>Aqui você pode alterar sua senha de acesso ao gerenciador:</p>

	<form method="post" action="adm_main.php?section=<?php echo $_GET['section']?>&action=<?php echo $_GET['action']?>">
	<input type="hidden" name="metodo" value="gravar">
	<input type="hidden" name="myid" value="<?php echo User::getInstance()->LeRegistro('id');?>">
	<table width="670" border=0 cellpadding=0 cellspacing=3>
	<col width="200">
	<col>
	<tr>
		<td>Digite sua senha atual: </td>
		<td>
		  <table cellpadding=0 cellspacing=0 width="100%" height="1%">
		  <col>
		  <col width="370">
		  <tr>
			<td>
				<input type="password" name="frmsenhaatual" value="" class="input_adm">
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>Digite sua nova senha: </td>
		<td>
		  <table cellpadding=0 cellspacing=0 width="100%" height="1%">
		  <col>
		  <col width="370">
		  <tr>
			<td>
				<input type="password" name="frmnovasenha" value="" class="input_adm">
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>Redigite sua nova senha: </td>
		<td>
		  <table cellpadding=0 cellspacing=0 width="100%" height="1%">
		  <col>
		  <col width="370">
		  <tr>
			<td>
				<input type="password" name="frmconfirmacao" value="" class="input_adm">
			</td>
		</tr>
		</table>
		</td>
	</tr>

	<tr height="5">
		<td colspan="2"><center></center></td>
	</tr>
	<tr>
		<td colspan="2"><center><INPUT TYPE="submit" VALUE="Atualizar"></center></td>
	</tr>
	</table>

	</form>

	<p>
		<a href="adm_main.php"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
	</p>
<?php } ?>

