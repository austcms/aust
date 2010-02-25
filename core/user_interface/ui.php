<?php
define("IMG_DIR", "core/user_interface/img/");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="expires" content="Mon, 19 Feb 2024 11:12:01 GMT" />
    <title><?php echo $config->getConfig('site_name'); ?> - Gerenciador<?php /* ifisset($config->LeOpcao('sitename'), 'Aust'); */ ?></title>
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/standard.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/style_principal.css" type="text/css" />
    
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/header.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/widget.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/hint.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/tabs.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/pane.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/forms.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>css/lightbox.css" type="text/css" />

    <link rel="stylesheet" href="<?php echo THIS_TO_BASEURL.UI_PATH; ?>temas/classic_blue/default.css" type="text/css" />

    <?php
    /* Para TinyMCE
     *
     * Para retirar do site: Verificar textareas nos formulários e em inc_content_gravar.php
     *
     * O que carrega o editor HTML é a função loadHtmlEditor() em
     * conjunto com a classe Modulos
     *
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

    <?php
    $html = HtmlHelper::getInstance();
    $html->js();
    /*
    <script type="text/javascript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/jquery.js"></script>
    <script type="text/javascript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/jquery.tools.min.js"></script>
    <script type="text/javascript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/jquery-ui-1.7.2.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>libs/plugins.js"></script>
     * 
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>navegation.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>user_helps.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>interacao.js"></script>
    <script language="JavaScript" src="<?php echo THIS_TO_BASEURL.BASECODE_JS; ?>codigo_principal.js"> </script>
     * 
     */
    ?>


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
                
                <a href="adm_main.php?section=admins&action=edit&fm=editar">Alterar meus dados/senha</a>
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
    <?php
    /*
     * DEBUG
     */
    ?>
    <div id="link_bottom">
        <div class="links_admin">
            <?php
            if($administrador->LeRegistro('tipo') == 'Webmaster'){
                ?>
                <div class="borda"></div>
                <br />
                    <span class="para_webmaster">Para Webmasters:</span><a href="adm_main.php?section=conf_modulos" class="restrito">Configurar Módulos</a>
                    <a href="adm_main.php?section=categorias" class="restrito">Categorias</a>
                <?php
                if( Registry::read('debugLevel') > 1 ){
                    ?>

                    <table class="debug">
                    <tr class="header">
                        <td class="sql">
                        <strong>SQLs</strong>
                        </td>
                        <td class="result">
                        <strong>Results</strong>
                        </td>
                        <td class="time">
                        <strong>Seconds</strong>
                        </td>
                    </tr>
                    <?php

                    $debugVars = Registry::read('debug');
                    foreach( $debugVars as $vars ){
                        $sqlCommands = array(
                            "SELECT", "UPDATE", "DELETE", "INSERT", "REPLACE",
                            "FROM", "ASC", "WHERE", "ORDER BY", "LIMIT", "TABLES",
                            "LEFT JOIN", "DISTINCT", "COUNT", "ON", "DESCRIBE", "SHOW",
                            "INTO", "VALUES", "SET",
                            "IN", "NOT IN", "OR", "AND", "AS", "DESC",
                            " and ", " as "
                        );
                        $boldSqlCommands = array();
                        foreach( $sqlCommands as $valor ){
                            $boldSqlCommands[] = "<strong>".$valor."</strong>";
                        }
                        $sql = str_replace($sqlCommands, $boldSqlCommands, $vars['sql'] );

                        /*
                         * Result
                         */
                        $errorClass = '';
                        if( is_string($vars['result']) ){
                            $errorClass = 'error';
                        }
                        ?>
                        <tr class="list <?php echo $errorClass; ?>">
                        <td class="sql "  valign="top">
                            <?php echo $sql; ?>
                        </td>
                        <td class="result" valign="top">
                            <?php echo $vars['result']; ?>
                        </td>
                        <td class="time" valign="top">
                            <?php echo substr(number_format($vars['time'], 4, '.', ''), 0, 5); ?>
                        </td>
                        </tr>
                        <tr style="height: 1px;">
                            <td colspan="3" style="font-size: 0px; background: #eeeeee;">
                            </td>
                        </tr>
                        <tr style="height: 5px;">
                            <td colspan="3" style="font-size: 0px;">
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </table>
                    <?php
                }
            }
            ?>
        </div>
    </div>
        
    <div id="bottom">
    
    </div>

</div>


<div id="mask">
</div>


</body>
</html>