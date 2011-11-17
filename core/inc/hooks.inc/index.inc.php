<div class="title_column">
	<h2>
		Hooks
	</h2>
</div>

<?php
$hooksClass = new Hook();
$hooks = $hooksClass->loadHookEngines();
if( !empty($hooks) && is_array($hooks) ){
	?>

	<div class="configuration_variables config_items">

		<div class="header">Engines disponíveis</div>
		<?php
		foreach( $hooks as $hook => $attributes ){
			?>

			<div class="item">
				<label class="configuration_variable_label" for="<?php echo $key ?>_input">
					<?php echo $attributes["configuration"]["name"] ?>
				</label>
				<div class="input">
					<a href="adm_main.php?section=hooks&action=new&hook_engine=<?php echo $hook ?>">Novo</a>
				</div>
			</div>

			<?php
		}
		?>

	</div>
	<?php
}
?>

<?php
$hooksList = $hooksClass->allHooks();
if( !empty($hooksList) && is_array($hooksList) ){
	?>
	<br />
	<div class="configuration_variables config_items">

		<div class="header">Hooks</div>
		<?php
		foreach( $hooksList as $attributes ){
			if( empty($attributes["hook_engine"]) )
				continue;
			?>

			<div class="item">
				<label class="configuration_variable_label" for="<?php echo $key ?>_input">
					<?php
					echo $attributes["description"]."<br>";
					echo "<strong>".$attributes["hook_engine"]."</strong>";
					if( !empty($attributes["node_id"]) ){
						echo ', '.$hooksClass->getStructureName( $attributes["node_id"] );
					}
					if( !empty($attributes["when_action"]) ){
						echo ', when '.$attributes["when_action"];
					}
					
					?>
				</label>
				<div class="input">
					<a href="adm_main.php?section=hooks&action=edit&hook_engine=<?php echo $hook ?>&id=<?php echo $attributes["id"] ?>">Editar</a>
				</div>
			</div>

			<?php
		}
		?>

	</div>
	<?php
}
?>
