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
<?php /*
    <li><a <?php MenuSelecionado($_GET['section'], ""); ?> href="<?php echo $_SERVER['PHP_SELF'];?>">Principal</a></li>
*/?>
    <li><a <?php MenuSelecionado($_GET['section'], "index"); ?> href="adm_main.php?section=index">Painel</a></li>
    <li><a <?php MenuSelecionado($_GET['section'], "conteudo"); ?> href="adm_main.php?section=conteudo">Gerenciar Conteúdo</a></li>
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
        <li class="opcao_direita"><a <?php MenuSelecionado($_GET['section'], "admins"); ?> href="adm_main.php?section=admins">Pessoas e Permissões</a></li>
    <?php } ?>

    </ul>
</div>