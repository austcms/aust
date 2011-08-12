<?php
if(!empty($_POST['categoria_chefe']) AND $_POST['categoria_chefe'] <> ''){
	if(Aust::getInstance()->createSite($_POST['name'], '')){
		?>
		<h2 class="ok">Site incluído com sucesso!</h2>
		<p>Este foi o primeiro site. Agora você construirá estruturas como notícias.</p>
		<?php
	} else {
		?>
		<h2 class="falha">Ops... Ocorreu um problema!</h2>
		<p>Simples assim. Inserimos a primeira categoria com sucesso.</p>
		<?php
	}
}


if( Aust::getInstance()->anySiteExists() ){
	?>
	<div class="title_column">
		<h2>Taxonomia</h2>
		
		<div class="root_user_only"><?php tt("Apenas desenvolvedores acessam esta tela.", "padlock") ?></div>
	</div>
	<p>
		Opções:
	</p>
	<p>
		<ul>
			<?php if( Aust::getInstance()->anyStructureExists() ){ ?>
				<li><a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=new">Inserir nova categoria</a></li>
			<?php } ?>
			<li><a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=list_content">
			Visualizar árvore taxonômica
			</a></li>
		</ul>
	</p>
	<p>
		Definição de Taxonomia: todo o conteúdo do site está dividido em sessões, como em um jornal.
		Estas divisões chamam-se <em>Categorias</em>. Todas estas categorias juntas formam
		a Taxonomia de um site.
	</p>
	<p>
		<strong>Atenção:</strong> Aqui você pode criar e editar as categorias do site. Se você não sabe o que está fazendo,
		contacte um administrador. Qualquer erro poderá fazer o site parar de funcionar.
	</p>

	<?php
}
?>