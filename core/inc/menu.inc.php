<?php
/*
 * MENU PRINCIPAL
 */
$usuario_tipo = $administrador->LeRegistro("tipo");

if(empty($_GET['section']))
    $_GET['section'] = "";
?>
<div id="menu">
<ul>
    <li><a <?php MenuSelecionado($_GET['section'], ""); ?> href="<?php echo $_SERVER['PHP_SELF'];?>">Principal</a></li>
    <li><a <?php MenuSelecionado($_GET['section'], "categorias"); ?> href="adm_main.php?section=categorias">Categorias</a></li>
    <li><a <?php MenuSelecionado($_GET['section'], "conteudo"); ?> href="adm_main.php?section=conteudo">Gerenciar</a></li>
    <?php
    /*
     * CONFIGURAÇÕES
     *
     * Se o usuário é WEBMASTER
     */
    if($usuario_tipo == "Webmaster" OR $usuario_tipo == "Administrador"){ ?>
        <li><a <?php MenuSelecionado($_GET['section'], "config"); ?> href="adm_main.php?section=config">Configurações</a></li>
    <?php }
    /*
     * USUÁRIOS
     *
     * Se o usuário é WEBMASTER ou ADMINISTRADOR
     */
    if($usuario_tipo == "Webmaster" OR $usuario_tipo == "Administrador"){ ?>
        <li><a <?php MenuSelecionado($_GET['section'], "admins"); ?> href="adm_main.php?section=admins">Usuários</a></li>
    <?php } ?>

    <li><a href="logout.php">Sair</a></li>
</ul>
</div>