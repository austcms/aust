<?php

/*
 * MOD_CONF
 */
if( !empty($_GET['export']) ){
	$export = Export::getInstance();
	if( $_GET['export'] == "true" ){
		/**
		 *
		 */
		$export->export();
	} else if( $_GET['export'] == "clean" ){
		$handle = fopen(EXPORTED_FILE, "w");
		fwrite( $handle, '');
		fclose($handle);
		
	} else if( $_GET['export'] == "import" ){
		$export->import();
	}
}
?>


<h2>Importar & Exportar</h2>
<?php if(!empty($status)){ ?>
	<div class="box-full">
		<div class="box alerta">
			<div class="titulo">
				<h3>Status</h3>
			</div>
			<div class="content">
				<?php
				if(is_string($status))
					echo $status;
				elseif(is_array($status)){
					foreach($status as $value){
						echo '<span>'.$value.'</span><br />';
					}
				}
				?>
			</div>
		</div>
	</div>
<?php } ?>

<div class="widget_group">
	<?php
	/**
	 * CONFIGURAÇÕES
	 *
	 * Listagem dos campos deste cadastro e configuração destes
	 */
	?>
	<div class="widget">
		<div class="titulo">
			<h3>Exportar Estruturas</h3>
		</div>
		<div class="content">
			<p>
				<a href="adm_main.php?section=<?php echo $_GET['section'] ?>&export=true">
					Exportar para arquivo no código-fonte
				</a>
			</p>
			<p>
				<?php
				if( !is_writable(EXPORTED_FILE) ){
					?>
					<span style="color: red">
					Sem permissão de escrita no arquivo <?php echo EXPORTED_FILE; ?>
					</span>
					<?php
				} else {
					?>
					<span style="color: green">
					Arquivo <?php echo EXPORTED_FILE; ?> com permissão de escrita.
					</span>
					<?php
				}
				
				?>
			</p>
			<p>
				<a href="<?php  ?>">
					Exportar para download
				</a>
			</p>

		</div>
		<div class="footer"></div>
	</div>

	<div class="widget">
		<div class="titulo">
			<h3>Outras opções</h3>
		</div>
		<div class="content">
			<p>
				<a href="adm_main.php?section=<?php echo $_GET['section'] ?>&export=clean">
					Limpar dados exportados
				</a>
				
			</p>

		</div>
		<div class="footer"></div>
	</div>
</div>

<div class="widget_group">
	<?php
	/**
	 * LISTAGEM DE CAMPOS
	 *
	 * Listagem dos campos deste cadastro e configuração destes
	 */
	?>
	<div class="widget">
		<div class="titulo">
			<h3>Importar</h3>
		</div>
		<div class="content">
			<p>
				<a href="adm_main.php?section=<?php echo $_GET['section'] ?>&export=import">
					Importar dados agora
				</a>
			</p>

		</div>
		<div class="footer"></div>
	</div>
</div>
