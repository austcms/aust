<?php
/*
 * Arquivo SEE_config.php
 */

	$sql = "SELECT
				admins.*,  DATE_FORMAT(admins.created_on, '%d/%m/%Y %H:%i') as created_on,
				admin_groups.name AS tipo

			FROM
				admins, admin_groups
			WHERE
				admins.admin_group_id=admin_groups.id AND
				admins.id='".$_GET['w']."'
			";
	$query = Connection::getInstance()->query($sql);
	$dados = $query[0];
?>
<h2>Ver informações</h2>
<p>
	A seguir, informações de <em><?php echo $dados["name"];?></em>.
</p>
<p>
	Nome: <em><?php echo $dados["name"];?></em><br />
	Hierarquia: <em><?php echo $dados["tipo"];?></em><br />
	Nome de usuário: <em><?php echo $dados["login"];?></em><br />
	Senha: <em><?php if(User::getInstance()->LeRegistro('group') == "Webmaster") echo $dados["password"]; else echo "*****";?></em><br />
	Email: <em><?php echo $dados["email"];?></em><br />
	Cadastrado desde <?php echo $dados["created_on"];?>
</p>
<p>
	<a href="javascript: history.go(-1);">Voltar</a>
</p>


