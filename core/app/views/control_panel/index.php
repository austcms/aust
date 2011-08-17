<?php
/*
 * Somente webmasters tem acesso a esta página
 */

if(User::getInstance()->LeRegistro('group') == 'Webmaster'):

/*
 * MIGRATIONS
 *
 * Verificações de Migrations de módulos
 */

	/*
	 * Migration Status
	 */
	$migrationsStatus = MigrationsMods::getInstance()->status();

	$modulesStatus = ModulesManager::getInstance()->getModuleInformation( array_keys($migrationsStatus) );

	/*
	 * Module's JS
	 */
	if(!empty($_GET['aust_node'])){
		$modulo = Aust::getInstance()->structureModule($_GET['aust_node']);
		if(is_file(MODULES_DIR.$modulo.'/js/jsloader.php')){
			$include_baseurl = MODULES_DIR.$modulo;
			include_once(MODULES_DIR.$modulo.'/js/jsloader.php');
		}
	} elseif($_GET['action'] == 'configurar_modulo' AND !empty($_GET['modulo'])){
		if(is_file($_GET['modulo'].'/js/jsloader.php')){
			$include_baseurl = $_GET['modulo'];
			include_once($_GET['modulo'].'/js/jsloader.php');
		}
	}

	/*
	 * NENHUMA DAS OPÇÕES ACIMA, CARREGAR A PÁGINA NORMALMENTE
	 *
	 * Carrega toda a interface normalmente
	 */
	else {
		?>
		
		<div class="title_column">
			<h2>Configurar Módulos e Estruturas</h2>
			
			<div class="root_user_only"><?php tt("Apenas desenvolvedores acessam esta tela.", "padlock") ?></div>
		</div>

		<div class="widget_group">
			<div class="widget">
				<div class="titulo">
					<h3>
						Estruturas instaladas
						<?php tt("Estruturas são seções do site, como Notícias, Artigos, entre outros."); ?>
					</h3>
				</div>
				<div class="content">
					<?php if( $hasStructures ){ ?>
						<p>Abaixo, as estruturas instaladas.</p>
						<div>
						<?php
						foreach( $sites as $site ){
						
							foreach( $site['Structures'] as $structure ){
								?>
								<div class="item">
									<strong><?php echo $structure["name"] ?></strong>
									(módulo <?php echo $structure["type"] ?>)
									<a href="adm_main.php?section=<?php echo CONTROL_PANEL_DISPATCHER ?>&aust_node=<?php echo $structure["id"] ?>&action=structure_configuration">Configurar</a>
								</div>
								<?php
							}
						}
						?>
	
						</div>
					<?php } else { ?>
						<p>Não há estruturas instaladas ainda.</p>
					<?php } ?>
				</div>
			</div>


			<?php
			/*
			 * FORM INSTALAR NOVAS ESTRUTURAS
			 */
			?>
			<div class="widget">
				<div class="titulo">
					<h3>Instalar Estrutura</h3>
				</div>
				<div class="content">
					<?php
					$modulesList = ModulesManager::getInstance()->LeModulos();
					if( !empty($modulesList) ){ ?>
						<p>
							Selecione abaixo o site, o módulo adequado e o nome da estrutura (ex.: Notícias, Artigos, Arquivos).
						</p>
						<form action="adm_main.php?section=<?php echo $_GET['section'];?>" method="post" class="simples pequeno">
							<input type="hidden" value="1" name="publico" />
							<div class="campo">
								<label>Categoria-chefe: </label>
									<select name="categoria_chefe">
										<?php
										Aust::getInstance()->getAllSites(Array('id', 'name'), '<option value="&%id">&%name</option>', '', '');
										?>
									</select>
							</div>
							<br />
							<div class="campo">
								<label>Módulo: </label>
									<select name="modulo">
										<?php									
										foreach( $modulesList as $moduloDB ){

											?>
											<option value="<?php echo $moduloDB["value"] ?>">
												<?php echo $moduloDB["name"] ?>
											</option>
											<?php
										}
										$moduloDB = null;
										?>
									</select>
							</div>
							<br />
							<div class="campo">
								<label>Nome da estrutura:</label>
								<div class="input">
									<input type="text" name="nome" class="input" />
									<p class="explanation">Ex.: Notícias, Artigos</p>
								</div>
							</div>
							<div class="campo">
								<input type="submit" name="inserirestrutura" value="Criar estrutura" class="submit" />
							</div>

						</form>

					<?php } else { ?>
						<p>
							Não é possível instalar estruturas sem módulos.
						</p>
						<p>
							Primeiro, instale um módulo na coluna da direita.
							<?php tt("Após instalar um módulo, você poderá instalar uma estrutura."); ?>
						</p>
					<?php } ?>
				</div>
			</div>
		</div>



		<div class="widget_group">

			<?php
			/*
			 * INSTALAÇÃO DE MÓDULOS
			 */
			?>
			<div class="widget">
				<div class="titulo">
					<h3>
						Módulos disponíveis
						<?php tt("Módulos compõem os formulários e funcionalidades das estruturas, ".
								 "dando formato a elas.<br /><br />Ao instalar um módulo, tabelas ".
								 "são criadas e tornam-se disponíveis para uso."); ?>
					</h3>
				</div>
				<div class="content">

					<div style="margin-bottom: 10px;">
						<div class="modules_available">
						<?php
						foreach( $modulesStatus as $modulo) {
							$path = $modulo['path'];
							$stable = true;
							
							if( empty($modulo['config']['state']) ||
								$modulo['config']['state'] != 'stable' )
								$stable = false;
							?>
							<div class="item <?php if( !$stable ) echo "unstable"; ?>">

								<div class="header">
									<span class="title">
									<?php echo $modulo['config']['name']; ?>
									</span>
									<?php if( !$stable ){ ?>
										<span class="state">
										(não estável)
										</span>
									<?php } ?>
								</div>
								<div class="description">
									<?php echo $modulo['config']['description']; ?>
								</div>
								<div class="status">
								<?php
								/*
								 * Totalmente Atualizado.
								 */
								if( MigrationsMods::getInstance()->isActualVersion($path)
									AND ModulesManager::getInstance()->verificaInstalacaoRegistro(array("pasta"=>$path)) )
								{
									echo '<span class="green">Instalado</span><br />';
								} elseif( MigrationsMods::getInstance()->isActualVersion($path)
									AND !ModulesManager::getInstance()->verificaInstalacaoRegistro(array("pasta"=>$path)) )
								{
									echo '<div style="color: orange;">Migration atualizado, mas não há registro do módulo no DB.<br />';
									echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Tentar registrar agora</a></div>';
								}
								/*
								 * Não atualizado,
								 * contém alguma versão no DB.
								 */
								elseif( MigrationsMods::getInstance()->hasSomeVersion($path)
										AND ModulesManager::getInstance()->verificaInstalacaoRegistro(array("pasta"=>$path)) )
								{
									echo '<div style="color: orange;">Tabela instalada, mas requer atualização.<br />';
									echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Rodar Migration</a></div>';
								} elseif( MigrationsMods::getInstance()->hasSomeVersion($path)
										AND !ModulesManager::getInstance()->verificaInstalacaoRegistro(array("pasta"=>$path)) )
								{
									echo '<div style="color: orange;">Tabela instalada, mas requer atualização e registro do módulo no DB.<br />';
									echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Rodar Migration</a></div>';
								} else {
									echo '<span class="red">Não Instalado,</span> ';
									echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">instalar agora</a><br />';
								}

								?>
								</div>
							</div>
							<?php
						}
						?>
						</div>
					</div>

				</div>
			</div>

		</div>


		<?php

	}

endif;

?>