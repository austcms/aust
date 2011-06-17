<?php
/*
 * Arquivo SEE_config.php
 */

    $sql = "SELECT
                admins.*,  DATE_FORMAT(admins.adddate, '%d/%m/%Y %H:%i') as adddate,
                admins_tipos.nome AS tipo

            FROM
                admins, admins_tipos
            WHERE
                admins.tipo=admins_tipos.id AND
                admins.id='".$_GET['w']."'
            ";
    $query = $conexao->query($sql);
    $dados = $query[0];
?>
<p>
    <a href="javascript: history.go(-1);"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
<h2>Ver informações</h2>
<p>
    A seguir, informações de <em><?php echo $dados["nome"];?></em>.
</p>
<p>
    Nome: <em><?php echo $dados["nome"];?></em><br />
    Hierarquia: <em><?php echo $dados["tipo"];?></em><br />
    Nome de usuário: <em><?php echo $dados["login"];?></em><br />
    Senha: <em><?php if(User::getInstance()->LeRegistro('tipo') == "Webmaster") echo $dados["senha"]; else echo "*****";?></em><br />
    Email: <em><?php echo $dados["email"];?></em><br />
    Telefone: <em><?php echo $dados["telefone"];?></em><br />
    Celular: <em><?php echo $dados["celular"];?></em><br />
    <!-- Sexo: <em><?php echo $dados["sexo"];?></em><br /> -->
    <!-- Supervisionado: <em><?php echo $dados["supervisionado"];?></em><br /> -->
    Cadastrado desde <?php echo $dados["adddate"];?>
</p>
<p>
    <a href="javascript: history.go(-1);"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>


