<span class="root_user_only">Apenas desenvolvedores acessam esta tela.</span>
<?php
if(!empty($_POST['categoria_chefe']) AND $_POST['categoria_chefe'] <> ''){
	if(Aust::getInstance()->createSite($_POST['nome'], '')){
		?>
		<h2 class="ok">Categoria-chefe incluida com sucesso!</h2>
		<p>Simples assim. Inserimos a primeira categoria com sucesso.</p>
		<?php
	} else {
		?>
		<h2 class="falha">Ops... Ocorreu um problema!</h2>
		<p>Simples assim. Inserimos a primeira categoria com sucesso.</p>
		<?php
	}
}


if(Aust::getInstance()->Instalado()){
	?>
	<h2>Taxonomia</h2>
	<p>
		Opções:
	</p>
	<p>
		<ul>
			<li><a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=new">Inserir nova categoria</a></li>
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
} else {
	?>
	<h2 class="falha">Nenhuma categoria encontrada!</h2>
	<p>
	Não foi encontrada nenhuma categoria. Provavelmente você está <strong>instalando</strong> o sistema.
	</p>
	<p>
	Crie abaixo a <strong>categoria-chefe</strong> do site.
	</p>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>" class="simples">
	<h3>Formulário</h3>
	<div class="campo">
	<label>Nome da categoria-chefe:</label>
	<div class="input">
	<input type="text" name="nome" />
	<p class="explanation">Não use maiúsculas nem espaços nem acentos.</p>
	<p class="explanation">Ex.: site1; site2; juridico</p>
	</div>
	</div>
	<input type="submit" name="categoria_chefe" value="Enviar!" class="submit" />
	</form>



	<?php
}
?>