<h2>Gerencie seu conteúdo</h2>
<p>
	Selecione qual estrutura você deseja gerenciar.
	<?php tt('Uma estrutura é uma área do site,
		como <em>Notícias</em>, <em>Artigos</em> e outros, por exemplo.') ?>
</p>
<?php /* INICIO DO DIV PAINEL GERENCIAR  - É GLOBAL */ ?>
<div class="painel">

	<?php /* TABS */ ?>
	<div class="tabs_area">
		<!-- the tabs -->
		<ul class="tabs">
			<?php foreach( $sites as $site ): ?>
			<li><a href="#"><?php echo $site['Site']['name'] ?></a></li>
			<?php endforeach; ?>
		</ul>
			
	</div>
	<?php /* PANES */ ?>
	<div class="panes">
			<?php
			/*
			 * LOOP POR CADA SITE
			 */
			foreach( $sites as $site ): ?>
			<div>
				<table border="0" class="pane_listing listing">
				<?php if( !empty($site['Structures']) ){ ?>
					<tr class="header">
						<td class="secoes">Conteúdos</td>
						<td class="acao">Opções</td>
						<td class="tipo">Tipo</td>
					</tr>
				<?php } else { ?>
					<tr class="list">
						<td class="sem_conteudo">Não há conteúdos nesta área.</td>
					</tr>
					</table>
					</div>
					<?php
					continue;
				}
				
				/*
				 * LOOP POR CADA ESTRUTURA
				 */
				foreach( $site['Structures'] as $structure ):

					if( !is_dir(MODULES_DIR.$structure['type']."/") ){
						?>
						<tr class="list">
							<td class="title" colspan="1">
								<span><?php echo $structure['nome'] ?></span>
							</td>
							<td class="options" colspan="2">
								<span><em>(módulo com problemas, contacte o administrador)</em></span>
							</td>
						</tr>	
						<?php
						continue;
					}
					/*
					 * Use o comando 'continue' para pular o resto do loop atual
					 */
					unset($modInfo);
					if(is_file(MODULES_DIR.$structure['type'].'/'.MOD_CONFIG)){
						/*
						 * Pega dados do módulo. $modInfo existe.
						 */
						include(MODULES_DIR.$structure['type'].'/'.MOD_CONFIG);

						$type = $modInfo['name'];
					} else {
						$type = $structure['tipo'];
					}

											$module = null;
											if( !empty($structure['masters']) ){

												$module = Aust::getInstance()->getStructureInstance($structure['id']);
												$relatedAndVisible = $module->getStructureConfig('related_and_visible');
												if( !empty($relatedAndVisible)
														&& !$relatedAndVisible )
													continue;
						
											}

					if( !StructurePermissions::getInstance()->verify($structure['id']) )
						continue;
					?>
					
					<tr class="list">
						<td class="title">
							<span><?php echo $structure['name'] ?></span>
						</td>
						<td class="options">
							<ul>
							<?php
							$options = (is_array($modInfo['actions'])) ? $modInfo['actions'] : Array();
							foreach ($options as $chave=>$value) {
								if( StructurePermissions::getInstance()->verify($structure['id'], $chave) )
									echo '<li><a href="adm_main.php?section='.$params['controller'].'&action='.$chave.'&aust_node='.$structure['id'].'">'.$value.'</a></li>';
							}
							?>
							</ul>
						</td>
						<td class="tipo">
							<?php
							/*
							 * TIPO
							 */
							echo $type;
							?>
						</td>
					</tr>
					<?php endforeach; ?>
					<tr class="footer">
						<td colspan="3"></td>
					</tr>
					</table>

				</div>
			<?php
			unset($module);
			endforeach; ?>
	</div>

</div>

<?php
$cacheDirs = Registry::read('permission_needed_dirs');
$permissionGranted = true;

if( !empty($cacheDirs) && is_array($cacheDirs) ){
	foreach( $cacheDirs as $dir ){
		if( !is_writable($dir) OR
			!is_readable($dir))
		{
			$permissionGranted = false;
		}
	}
}

if( !$permissionGranted ){
	?>
	<br clear="all" />
	<div style="display:table">
		<p>
			Os seguintes diretórios estão sem permissão de escrita. Por favor, conceda permissão.
		</p>
		<ul>
		<?php
		foreach( $cacheDirs as $dir ){
			if( !is_writable($dir) OR
				!is_readable($dir))
			{
				?>
				<li><?php echo $dir ?></li>
				<?php
			}
		}
		?>
		</ul>
		<p>Erros acontecerão se você não ajustar as permissões destes diretórios</p>
	</div>
	<?php
}
?>

<br clear="all" />