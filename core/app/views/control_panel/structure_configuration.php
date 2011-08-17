<div id="structure_configuration">

	<div class="title_column">
		<h2>
			Configure: <?php echo $module->information["name"] ?>
		</h2>
		
		<div class="root_user_only"><?php tt("Apenas desenvolvedores acessam esta tela.", "padlock") ?></div>
	</div>

<?php
$configurations = $module->loadModConf();
if( !empty($configurations) && is_array($configurations) ){
	?>

	<div class="structure_variables config_items">

		<div class="header">Variáveis da estrutura</div>
		<form method="post" action="adm_main.php?section=control_panel&aust_node=<?php echo $_GET['aust_node']; ?>&action=structure_configuration">
		<input type="hidden" name="conf_type" value="structure" />
		<input type="hidden" name="aust_node" value="<?php echo $_GET['aust_node']; ?>" />
		<?php
		foreach( $configurations as $key=>$options ){
			?>

			<div class="item">
				<label for="<?php echo $key ?>_input"><?php echo $options["label"] ?></label>
				<div class="input">
					<?php
					if( $options["inputType"] == "checkbox" ){
						$checked = "";
						if( !empty($options['value']) && $options["value"] == "1" ){
							$checked = 'checked="checked"';
						}
						?>
						<input type="hidden" name="data[<?php echo $key; ?>]" value="0" />
						<input type="checkbox" name="data[<?php echo $key; ?>]" <?php echo $checked; ?> value="1" class="input" id="<?php echo $key ?>_input" />
						<?php
					}

					else {
						?>
						<input type="text" name="data[<?php echo $key; ?>]" class="input" value="<?php echo $options['value'] ?>" id="<?php echo $key ?>_input" />
						<?php
					}

					if( !empty($options['help']) )
						tt($options['help']);
					?>

				</div>
			</div>

			<?php
		}
		?>
		<input type="submit" name="submit" value="Salvar" />
		</form>
	
	</div>
	<?php
}
?>


<?php
include($this->directory.MOD_VIEW_DIR.CONTROL_PANEL_DISPATCHER.'/structure_configuration.php');
?>

	<a href="adm_main.php?section=<?php echo CONTROL_PANEL_DISPATCHER ?>">Voltar ao painel de configurações</a>
</div>