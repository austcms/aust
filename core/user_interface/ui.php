<?php
define("IMG_DIR", "core/user_interface/img/");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $config->getConfig('site_name'); ?> - Gerenciador<?php /* ifisset($config->LeOpcao('sitename'), 'Aust'); */ ?></title>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/standard.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/style_principal.css" type="text/css" />
    
    <?php /* Estilo do cabeçalho - Topo e Navegação */ ?>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/header.css" type="text/css" />

    <?php /* Estilo dos Widgets */ ?>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/widget.css" type="text/css" />

    <?php /* Estilo dos hints - Tooltips, e interrogação */ ?>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/hint.css" type="text/css" />

    <?php /* Estilo das tabs - Painel Gerenciar e Configurações */ ?>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/tabs.css" type="text/css" />

    <?php /* Estilo do pane - Conteúdo de cada tab */ ?>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/pane.css" type="text/css" />

    <?php /* Estilo dos forms - Formulários */ ?>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/forms.css" type="text/css" />

    <?php /* Estilo dos lightboxs - Lightbox */ ?>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/lightbox.css" type="text/css" />

    <?php /* Tema default */ ?>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>temas/default/default.css" type="text/css" />


    <!-- TinyMCE -->
    <?php
    /* Para TinyMCE
    Para retirar do site: Verificar textareas nos formulários e em inc_content_gravar.php

    */
    ?>
    <?php
    /**
     * @todo - o comando abaixo é perigoso, pois permite que um usuário altere
     * informações de outro usuário.
     */
    ?>
    <script type="text/javascript">
        var userId = '<?php echo $administrador->getId() ?>';
        var IMG_DIR = '<?php echo IMG_DIR ?>';
    </script>
    <script language="javascript" type="text/javascript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>tiny_mce/tiny_mce.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>tiny_mce_loader.js"></script>

    <!-- <SCRIPT LANGUAGE="JavaScript" SRC="inc/js_forms.js"></script> -->
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/jquery.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/jquery.tools.min.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/jquery-ui-1.7.2.custom.min.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/plugins.js"></script>
    <?php /* <script src="http://cdn.jquerytools.org/1.1.2/full/jquery.tools.min.js"></script> */ ?>

    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>navegation.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>user_helps.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>interacao.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>codigo_principal.js"> </script>


</head>

<body bgcolor="white" topmargin=0 leftmargin=0 rightmargin=0>

<div id="top">

    <div class="title">
        <?php
        /*
         * NOME DO SITE - EDITÁVEL
         */
        ?>
        <div class="logotipo">
            <h1>
                <a href="adm_main.php?section=index"><?php echo $config->getConfig('site_name'); ?></a>
            </h1>
        </div>

        <div class="inicializacaorapida">
  
            <?php
            /*
             * LINK PARA ALTERAR DADOS OU SENHA
             */
            ?>
            <div id="altera_dados">
                <a href="logout.php">Sair</a>
                
                <a href="adm_main.php?section=admins&action=form&fm=editar">Alterar meus dados/senha</a>
                <?php
                    /*
                     * INFORMAÇÕES QUE IRÃO DENTRO DE ALTERAR MEUS DADOS
                     *
                      <a href="adm_main.php?section=admins&action=passw">Minha senha</a>
                    | <a href="adm_main.php?section=admins&action=form&fm=editar">Editar meu perfil</a>
                     *
                     */
                ?>

            </div>
            <div id="conectado_como">
            <?php /*
                <p>
                    Conectado como <strong><?php echo $administrador->LeRegistro('nome');?></strong>.<br />
                    N&iacute;vel de acesso  <strong><?php echo $administrador->LeRegistro('tipo');?></strong>.
                </p>
             *
             */ ?>
            </div>
            <span>
            <br />

            </span>
        </div>
    </div>
</div>
<div id="navegacao">
    <div class="containner">
        <?php include(THIS_TO_BASEURL.INC_DIR.'menu.inc.php'); ?>
    </div>
</div>

<div id="global">
    <noscript>Seu navegador não suporta JavaScript ou ele não está ativado. Por favor ative para ter uma melhor experiência neste site.</noscript>


    <div class="body">
        <div class="content">
        <?php echo $content_for_layout; ?>
        
        </div>
    </div>
    <div id="link_bottom">
        <div class="links_admin">
            <?php
            if($administrador->LeRegistro('tipo') == 'Webmaster'){
            ?>
            <div class="borda"></div>
            <br />
                <a href="adm_main.php?section=conf_modulos" class="restrito">Configurar Módulos</a><?php
            }
            ?>
        </div>
    </div>
        
    <div id="bottom">
    
    </div>

    <div id="body">
        <div id="bottom_top">
        </div>
        <div id="middle">
            <div style="padding-left: 30px; padding-right: 30px; text-align: right;">
                <!--
                <iframe style="float: left;" src="http://www.acgrupo.com.br/chavedomundo/rss/index.php" width="510" height="20" border="0" frameborder="0" scrolling="no"></iframe>
                -->
                <?php
                /*
                <a href="http://www.acgrupo.com.br" style="color: purple"><img src="http://www.acgrupo.com.br/imgout/desenvolvidoacgrupo.png" align="right" border="0" /></a>
                 *
                 */
                ?>
            </div>
        </div>
        <div id="bottom_bottom">
        </div>
    </div>
</div>


<div id="mask">
</div>


</body>
</html>