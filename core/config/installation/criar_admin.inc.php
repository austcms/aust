<?php
	$page_title = 'Criar o primeiro usuário';
	require 'cabecalho.inc.php';

if(!empty($_POST['configurar']) AND ($_POST['configurar'] == 'criar_admin')){
    $sql = Connection::getInstance()->count("SELECT
                id
            FROM
                admins
            WHERE
                login='".$_POST['frmloginaust']."'");
    if($sql > 0){
        ?>
        <h1 style="color: red;">Usuário já existe!</h1>
        <p><a href="../index.php">Próximo passo...</a></p>
        <?php
    } else {

        $sql = "INSERT INTO
                    admins(tipo,nome,login,senha,email,adddate)
                VALUES
                    (
                        (SELECT
                            id
                        FROM
                            admins_tipos
                        WHERE
                            nome='Webmaster'),
                    '{$_POST['frmnomeaust']}','{$_POST['frmloginaust']}','{$_POST['frmsenhaaust']}','{$_POST['frmemailaust']}','".date("Y-m-d H:i:s")."'
                    )";

        $query = Connection::getInstance()->exec($sql);

        if($query){ ?>
            <h1 style="color: green;">Conta criada com sucesso!</h1>
            <p>Pronto, novo usuário cadastrado.</p>
            <p><a href="../index.php">Próximo passo...</a></p>
            <?php
        } else {
            ?>
            <h1 style="color: red;">Ops... Não foi possível cadastrar o usuário.!</h1>
            <p>Ocorreu um erro estranho. Entre em contato com o programador responsável por isto.</p>
            <p><a href="<?php echo $_SERVER['PHP_SELF'];?>">Voltar</a></p>
        <?php
        }
    }
    ?>

    <?php
} else {
?>

    <h1>Criar Conta Webmaster</h1>
    <p>No Aust há uma área para gerenciar todo o site. É necessário ter no mínimo um administrador cadastrado para comandar tudo!</p>
    <p>Não há usuários cadastrados no sistema, portanto você deverá criar um abaixo.</p>
    
    <h2>Formulário</h2>
    
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="simples">
        <input type="hidden" name="configurar" value="criar_admin" />
        
        <label>Seu nome:</label>
        <input type="text" name="frmnomeaust" value="" />

        <label>Seu email:</label>
        <input type="text" name="frmemailaust" value="" />

        <label>Nome de usuário:</label>
        <input type="text" name="frmloginaust" value="" />
    
        <label>Senha:</label>
        <input type="password" name="frmsenhaaust" value="" />
    
        <input type="submit" value="Ok! Ir para o próximo passo..." class="submit" />
    </form>

<?php
}
 require 'rodape.inc.php';
 ?>