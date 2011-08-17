<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo Config::getInstance()->getConfig('site_name'); ?> - Gerenciador</title>
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
	if( !empty($_GET["aust_node"]) || !empty($_POST["aust_node"]) ){
		if( !empty($_GET["aust_node"]) )
			$austNode = $_GET["aust_node"];
		elseif( !empty($_POST["aust_node"]) )
			$austNode = $_POST["aust_node"];
		
		$modDir = Aust::getInstance()->structureModule($austNode).'/';
		if(is_file(MODULES_DIR.$modDir.'js/jsloader.php')){
			$include_baseurl = MODULES_DIR. substr($modDir, 0, strlen($modDir)-1); // necessário para o arquivo jsloader.php saber onde está fisicamente
			include_once(MODULES_DIR.$modDir.'js/jsloader.php');
		}
	}

	?>


</head>

<body bgcolor="white" topmargin=0 leftmargin=0 rightmargin=0>

<div id="global">
	<div id="top">

		<div class="title">
			<?php
			/*
			 * NOME DO SITE - EDITÁVEL
			 */
			?>
			<div class="logotipo">
				<h1>
					<a href="adm_main.php"><?php echo Config::getInstance()->getConfig('site_name'); ?></a>
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
						N&iacute;vel de acesso  <strong><?php echo User::getInstance()->LeRegistro('group');?></strong>.
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
			<?php
			/* Navigation bar */
			$usuario_tipo = User::getInstance()->LeRegistro("group");

			if(empty($_GET['section']))
				$_GET['section'] = "";
			?>
			<div id="menu">
			<ul>
				<li><a <?php MenuSelecionado($_GET['section'], "content"); ?> href="adm_main.php?section=content">Gerenciar Conteúdo</a></li>

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
		</div>
	</div>

	<div id="view">
		<noscript>Seu navegador não suporta JavaScript ou ele não está ativado. Por favor ative para ter uma melhor experiência neste site.</noscript>


		<div class="body">
			<div class="body_containner">
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
		
				<?php
				/* MODULE NAVIGATION BAR */
				if( !empty($_GET["aust_node"]) || !empty($_POST["aust_node"]) ){
					if( !empty($_GET["aust_node"]) )
						$austNode = $_GET["aust_node"];
					elseif( !empty($_POST["aust_node"]) )
						$austNode = $_POST["aust_node"];
		

				 	include(MODULES_DIR.$modDir.MOD_CONFIG);
					$action = $_GET['action'];

					/*
					 * Navegação entre actions de um austNode
					 */

					$moreOptions = array();
					foreach( $modInfo['actions'] as $actionName=>$humanName ){
						if( $actionName == $action )
							continue;
						$moreOptions[] = '<a href="adm_main.php?section='.MODULES.'&action='.$actionName.'&aust_node='.$austNode.'">'.$humanName.'</a>';
					}

					$visibleNav = true;
					$relatedMasters = Aust::getInstance()->getRelatedMasters(array($austNode));

					if( !empty($relatedMasters) ){

						$module = Aust::getInstance()->getStructureInstance($austNode);
						if( !$module->getStructureConfig('related_and_visible') ){
							$visibleNav = false;
						}

					}

					if( !empty($moreOptions) && $visibleNav ){
						?>
						<div class="structure_nav_options">
							Navegação: <?php echo implode(", ", $moreOptions); ?>
						</div>
						<?php
					}
				}		
				?>
				<?php echo $content_for_layout; ?>
		
				<?php
				/*
				 * Se for save, redireciona automaticamente
				 */
			 	$action = "";
				if( !empty($_GET['action']) ){
					$action = $_GET['action'];
				}
				if( ( 
						(!empty($_POST['force_redirect']) && $_POST['force_redirect']) || 
						in_array($action, array(SAVE_ACTION, ACTIONS_ACTION))
					) &&
					(
						empty($_SESSION['no_redirect']) ||
						!$_SESSION['no_redirect']
					)
			 	)
				{

					unset($_SESSION['selected_items']);
					?>
					<div class="loading_timer">
						<img src="<?php echo IMG_DIR ?>loading_timer.gif" /> Redirecionando Automaticamente
					</div>
					<?php

					if( !empty($_POST['redirect_to']) )
						$goToUrl = $_POST['redirect_to'];
					else if( !empty($_GET['redirect_to']) )
						$goToUrl = $_GET['redirect_to'];
					else
						$goToUrl = "adm_main.php?section=".$_GET['section'].'&action=listing&aust_node='.$austNode;
					?>
					<script type="text/javascript">
						var timeToRefresh = 2;
						setTimeout(function(){
							window.location.href = "<?php echo $goToUrl ?>";
						}, 2000);
					</script>
					<?php
				}

				$_SESSION['no_redirect'] = false;
		
				?>

				</div>
			</div>
		</div>
	</div>

	<?php /* DEBUG */ ?>
	<div id="footer_admin_dashboard">
		<div class="links_admin">
			<?php
			if(User::getInstance()->LeRegistro('group') == 'Webmaster'){
				?>
				<div class="borda"></div>
				<br />
					<span class="para_webmaster">Para Webmasters:</span>
					<?php tt('Os links à direita aparecem somente para o usuário com conta <em>root</em>.'); ?>
					<a href="adm_main.php?section=control_panel" class="restrito">Configurar Módulos</a>
					<a href="adm_main.php?section=taxonomy" class="restrito">Taxonomia</a>

					<?php
					$showDebugSQL = "hidden";
					$arrowDown = true;
					if( Config::getInstance()->getConfig('show_sql_debug_messages') ){
						$showDebugSQL = "";
						$arrowDown = false;
					}
					?>

					<a href="javascript: void(0)" id="show_sql_debug_messages_button" class="restrito">
						Debug SQL 
						<span class="arrow"><?php if( $arrowDown ) echo "▼"; else echo "▲"; ?></span>
					</a>
					
				<div id="sql_debug_messages" class="<?php echo $showDebugSQL; ?>">
					<?php

					/*
					 * CACHE
					 */
					$cacheDirs = Registry::read('permission_needed_dirs');
					if( is_array($cacheDirs) ){
						foreach( $cacheDirs as $dir ){
							if( !is_writable($dir) OR
								!is_readable($dir))
							{
								$cacheError[] = $dir;
							}
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
					if( is_array($debugVars) ){
						$totalMessages = count($debugVars);
						$i = 0;
						foreach( $debugVars as $vars ){
							$positionClass = "first";
							if( $i > 0 )
								$positionClass = "";
								
							if( $i == $totalMessages-1 )
								$positionClass = "last";

							$i++;

							$sqlCommands = array(
								"SELECT", "UPDATE", "DELETE", "INSERT", "REPLACE",
								"FROM", "ASC", "WHERE", "ORDER BY", "LIMIT", "TABLES",
								"LEFT JOIN", "DISTINCT", "COUNT", "ON", "DESCRIBE", "SHOW",
								"INTO", "VALUES", "SET", "ALTER",
								"IN", "NOT IN", " OR ", " AND ", " AS ", "DESC",
								" and ", " as "
							);
							$boldSqlCommands = array();
							foreach( $sqlCommands as $value ){
								$boldSqlCommands[] = "<strong>".$value."</strong>";
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
								<td valign="top" colspan="3">
									<div class="td_containner <?php echo $positionClass ?>">
										<div class="column sql"><?php echo $sql; ?></div>
										<div class="column result"><?php echo $vars['result']; ?></div>
										<div class="column time"><?php echo substr(number_format($vars['time'], 4, '.', ''), 0, 5); ?></div>
									</div>
								</td>
							</tr>
							<?php
						}
					}
					?>
					</table>
					<?php
				}
			?>
			</div>
		</div>
	</div>
</div>
<div id="mask">
</div>


</body>
</html>