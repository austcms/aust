<?php
/*
 * MENU PRINCIPAL
 */
$usuario_tipo = User::getInstance()->LeRegistro("tipo");

if(empty($_GET['section']))
    $_GET['section'] = "";
?>
<div id="menu">
<ul>
    <?php /*
	<li><a <?php MenuSelecionado($_GET['section'], "index"); ?> href="adm_main.php?section=index">Painel</a></li>
	*/ ?>
    <li><a <?php MenuSelecionado($_GET['section'], "conteudo"); ?> href="adm_main.php?section=conteudo">Gerenciar Conteúdo</a></li>
    
    <li class="opcao_direita"><a <?php MenuSelecionado($_GET['section'], "themes"); ?> href="adm_main.php?section=themes">Aparência</a></li>
    <?php
    /*
     * CONFIGURAÇÕES
     *
     * Se o usuário é WEBMASTER
     */
    if($usuario_tipo == "Webmaster" OR $usuario_tipo == "Administrador"){ ?>
        <li><a <?php MenuSelecionado($_GET['section'], "config"); ?> href="adm_main.php?section=config">Configurações</a></li>
    <?php
    }
    /*
     * PESSOAS E PERMISSÕES
     */
    if( $usuario_tipo == "Webmaster"
        OR $usuario_tipo == "Administrador"
        OR $usuario_tipo == "Moderador")
    {
        ?>
        <li class="opcao_permissoes"><a <?php MenuSelecionado($_GET['section'], "admins"); ?> href="adm_main.php?section=admins">Pessoas e Permissões</a></li>
        <?php
    }
    ?>
        
</ul>

</div>