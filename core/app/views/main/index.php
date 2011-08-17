<?php
/*
 * WIDGETS
 */
$widgets = new Widgets($envParams, User::getInstance()->getId());
?>

<h2>Painel Principal</h2>
<p>
	Este é o sistema onde você gerencia o conteúdo do seu site.
</p>


<div id="painel">

	<?php /* Widget Group - Coluna (Primeira) */ ?>
	<div class="widget_group">

		<ul id="sortable1" class="connectedSortable draganddrop">
		<?php
		$c = $widgets->getInstalledWidgets();
		/*
		 * Instala Widgets caso não haja nenhum instalado
		 */
		if( empty($c) AND $uiPermissions->canAccessWidgets() ){
			$widgetsToInstall = array(
				array(
					'name' => 'category_shortcuts',
					'admin_id' => User::getInstance()->getId(),
					'column_nr' => 1,
					'path' => 'dashboard/category_shortcuts',
				),
				array(
					'name' => 'people',
					'admin_id' => User::getInstance()->getId(),
					'column_nr' => 2,
					'path' => 'dashboard/people',
				),
			);

			foreach( $widgetsToInstall as $widgetToInstall ){
				if( $widgets->installWidget($widgetToInstall) ){
					$installStatus[] = 'success';
				} else {
					$installStatus[] = 'insuccess';
				}
			}

			if( !in_array('insuccess', $installStatus) ){
				header( 'Location: adm_main.php' );
				exit();
			} else {
				echo '<h2>Ocorreu um erro desconhecido ao instalar dados do usuário.</h2>';
				exit();
			}
			
		}
		/*
		 * WIDGETS - COLUNA 1
		 */
		if( !empty($c['1']) ):

			foreach( $c['1'] as $widget ){
				?>
					<li id="widgets_<?php echo $widget->getId(); ?>" class="sorted">
						<div class="widget">
							<div class="titulo">
								<h3><?php echo $widget->getTitle(); ?><?php $widget->getTooltip(); ?></h3>
							</div>
							<div class="content">
								<?php echo $widget->getHtml(); ?>
							</div>
							<div class="footer">
							</div>
						</div>
					</li>
				<?php
			}
			
		else:
			//Esta coluna não possui Widgets.
		endif;
		?>
		</ul>
		<?php
		/*
		<br/>
		<a href="adm_main.php?section=widgets&column_nr=1">Adicionar Widget</a>
		 *
		 */
		?>
			  
	</div>

	<?php /* Widget Group - Coluna (Segunda) */ ?>
	
	<div class="widget_group">

		<ul id="sortable2" class="connectedSortable draganddrop">
		<?php
		/*
		 * WIDGETS - COLUNA 2
		 */
		if( !empty($c['2']) ):

			foreach( $c['2'] as $widget ){
				?>
				<li id="widgets_<?php echo $widget->getId(); ?>" class="sorted">
					<div class="widget">
						<div class="titulo">
							<h3><?php echo $widget->getTitle(); ?><?php $widget->getTooltip(); ?></h3>
						</div>
						<div class="content">
							<?php echo $widget->getHtml(); ?>
						</div>
						<div class="footer">
						</div>
					</div>

				</li>
				<?php
			}

		else:
			//Esta coluna não possui Widgets.
		endif;

		?>
		</ul>
		<?php
		/*
		<br/>
		<a href="adm_main.php?section=widgets&column_nr=2">Adicionar Widget</a>
		 *
		 */
		?>

	</div>
	
	
</div>