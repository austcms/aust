<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo Config::getInstance()->getConfig('site_name'); ?> - Gerenciador<?php /* ifisset(Config::getInstance()->LeOpcao('sitename'), 'Aust'); */ ?></title>
    <!-- TinyMCE -->
    <?php
    $html = HtmlHelper::getInstance();
    $html->css();
    /* Para TinyMCE
     *
     * Para retirar do site: Verificar textareas nos formulários e em inc_content_gravar.php
     *
     * O que carrega o editor HTML é a função loadHtmlEditor() em
     * conjunto com a classe Modulos
     *
     */
    ?>
    <?php /* Estilo da seleção de temas */ ?>
    <link rel="stylesheet" href="<?php echo UI_PATH; ?>css/theme.css" type="text/css" />

    <?php /* Tema Azul */ ?>
    <link rel="stylesheet" href="<?php echo THEMES_DIR; ?><?php echo Themes::getInstance()->currentTheme(User::getInstance()->getId()); ?>/default.css" type="text/css" />
    <?php
    /**
     * @todo - o comando abaixo é perigoso, pois permite que um usuário altere
     * informações de outro usuário.
     */
	if( !empty($_GET['page']) )
		$page = $_GET['page'];
	else if( !empty($_GET['pagina']) )
		$page = $_GET['pagina'];
	else
		$page = 1;
		
    ?>

    <script type="text/javascript">
	    var userId = '<?php echo User::getInstance()->getId() ?>';
	    var austNode = '<?php if( !empty($_GET["aust_node"]) ) echo $_GET["aust_node"]; ?>';
        var IMG_DIR = '<?php echo IMG_DIR ?>';
		var page = '<?php echo $page ?>';
    </script>

    <?php
    $html->js();

    /*
     * JS DO MÓDULO
     *
     * Carrega Javascript de algum módulo se existir
     */
    if( !empty($_GET["aust_node"]) || $_POST["aust_node"] ){
		if( !empty($_GET["aust_node"]) )
			$austNode = $_GET["aust_node"];
		elseif( !empty($_POST["aust_node"]) )
			$austNode = $_POST["aust_node"];
		
		$modDir = Aust::getInstance()->LeModuloDaEstrutura($austNode).'/';
        if(is_file(MODULES_DIR.$modDir.'js/jsloader.php')){
            $include_baseurl = MODULES_DIR. substr($modDir, 0, strlen($modDir)-1); // necessário para o arquivo jsloader.php saber onde está fisicamente
            include_once(MODULES_DIR.$modDir.'js/jsloader.php');
        }
    }

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
                <a href="adm_main.php?section=index"><?php echo Config::getInstance()->getConfig('site_name'); ?></a>
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
                    Conectado como <strong><?php echo User::getInstance()->LeRegistro('nome');?></strong>.<br />
                    N&iacute;vel de acesso  <strong><?php echo User::getInstance()->LeRegistro('tipo');?></strong>.
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
		<?php if( notice() ){ ?>
			<div id="notice">
			<?php echo notice(); ?>
			</div>
		<?php } ?>

		<?php if( failure() ){ ?>
			<div id="failure">
			<?php echo failure(); ?>
			</div>
		<?php } ?>
		
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
            if(User::getInstance()->LeRegistro('tipo') == 'Webmaster'){
                ?>
                <div class="borda"></div>
                <br />
                    <span class="para_webmaster">Para Webmasters:</span><a href="adm_main.php?section=conf_modulos" class="restrito">Configurar Módulos</a>
                    <a href="adm_main.php?section=categorias" class="restrito">Categorias</a>
                    <a href="adm_main.php?section=import_export_structures" class="restrito">Importar/Exportar Estruturas</a>
                <?php
                if( Registry::read('debugLevel') > 1 ){

                    /*
                     * CACHE
                     */
                    $cacheDirs = Registry::read('permission_needed_dirs');
                    foreach( $cacheDirs as $dir ){
                        if( !is_writable($dir) OR
                            !is_readable($dir))
                        {
                            $cacheError[] = $dir;
                        }
                    }
                    if( !empty($cacheError) ){

                        ?>
                        <table class="debug">
                        <tr class="header">
                            <td>
                            <strong>Cache</strong>
                            </td>
                        </tr>
                        <?php
                        foreach( $cacheError as $dir ){
                            ?>
                            <tr class="list">
                                <td>
                                <span>
                                <strong><?php echo $dir ?></strong>
                                com permissão negada.
                                </span>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </table>
                        <?php
                    }
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
                            "INTO", "VALUES", "SET", "ALTER",
                            "IN", "NOT IN", " OR ", " AND ", " AS ", "DESC",
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