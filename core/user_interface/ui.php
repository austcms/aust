<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Aust - Gerenciador de sites<?php /* ifisset($config->LeOpcao('sitename'), 'Aust'); */ ?></title>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>style.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>forms.css" type="text/css" />


    <!-- TinyMCE -->
    <?php
    /* Para TinyMCE
    Para retirar do site: Verificar textareas nos formulários e em inc_content_gravar.php

    */
    ?>
    <script language="javascript" type="text/javascript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>tiny_mce/tiny_mce.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>tiny_mce_loader.js"></script>

    <!-- <SCRIPT LANGUAGE="JavaScript" SRC="inc/js_forms.js"></script> -->
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/jquery.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>codigo_principal.js"> </script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>navegation.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>interacao.js"></script>
</head>

<body bgcolor="white" topmargin=0 leftmargin=0 rightmargin=0>

<div id="global">
    <noscript>Seu navegador não suporta JavaScript ou ele não está ativado. Por favor ative para ter uma melhor experiência neste site.</noscript>
    <div id="top">
        <div class="border">
        </div>
        <div class="title">
        	<div class="logotipo">
            	<a href="adm_main.php"><h1 style="margin: 0;"><img border="0" src="<?php echo THIS_TO_BASEURL; ?>core/user_interface/img/layoutv1/logo/logo.png" /></h1></a>
            </div>
            <div class="inicializacaorapida">
                <span>
				<br />
                Conectado como <strong><?php echo $administrador->LeRegistro('nome');?></strong>.
                Seu n&iacute;vel de acesso &eacute; <strong><?php echo $administrador->LeRegistro('tipo');?></strong>.
				<br />
				<br />
                <div style="color: black; font-weight: bold;">Atalhos rápidos:</div>

				<a href="adm_main.php?section=admins&action=passw">Minha senha</a>
				| <a href="adm_main.php?section=admins&action=form&fm=editar">Editar meu perfil</a>
                <?php
                if($administrador->LeRegistro('tipo') == 'Webmaster'){
                    ?>
                    | <a href="adm_main.php?section=conf_modulos" class="restrito">Configurar Módulos</a>
                    <?php
                }
                if(in_array( $administrador->LeRegistro('tipo'), array('Webmaster', 'Administrador') )){ ?>
                    | <a href="adm_main.php?section=permissoes" class="restrito">Permissões</a>
                <?php }?>

                </span>
			</div>
        </div>
    </div>
    <div id="navegacao">
    	<div class="containner">
	        <?php include(THIS_TO_BASEURL.INC_DIR.'menu.inc.php'); ?>
        </div>
    </div>
    <div class="body">
        <div class="content">


        <?php echo $content_for_layout; ?>


        </div>
    </div>
    <div class="bottom">

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

</BODY>
</HTML>