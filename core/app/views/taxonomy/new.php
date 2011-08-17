<div class="title_column">
	<h2>Taxonomia: nova categoria</h2>
	
	<div class="root_user_only"><?php tt("Apenas desenvolvedores acessam esta tela.", "padlock") ?></div>
</div>

<p>
	Para criar uma nova subcategoria, selecione abaixo uma estrutura ou categoria.
</p>

<form method="post" action="adm_main.php?section=<?php echo $_GET['section'];?>&action=save" enctype="multipart/form-data">
<input type="hidden" name="action" value="gravar">
<table width="670" border=0 cellpadding=0 cellspacing=0 class="form">
<tr>
	<td valign="top"><label>Categoria:</label> </td>
	<td>

		<div id="categoriacontainer">
		<?php
		$escala = (empty($escala)) ? '' : $escala;

		echo BuildDDList(Aust::$austTable, 'frmfather_id', $escala);
		?>
		</div>
		<p class="explanation">
			Selecione acima a categoria ao qual a nova categoria estará subordinada. Exemplo:
			Se você escolher um categoria acima, a que você inserir ficará dentro dela. Para
			a categoria <em>notícias</em>, você pode inserir a categoria <em>futebol</em>, por exemplo.
		</p>
	</td>
</tr>
<tr>
	<td valign="top"><label>Nome:</label> </td>
	<td>
		<input type="text" name="frmname" class="text">
		<p class="explanation">
			Digite o nome da categoria. (Começa com letra maiúscula e não leva
			ponto final)
		</p>
		<p class="explanation" id="exists_titulo">
		</p>
	</td>
</tr>
<tr>
	<td valign="top"><label>Descrição:</label> </td>
	<td>
		<textarea name="frmdescription" id="jseditor"></textarea>
		<p class="explanation">
			Digite uma breve descrição desta categoria.
		</p>
	</td>
</tr>
<tr>
	<td valign="top"><label>Imagem:</label> </td>
	<td>
		<input type="file" name="arquivo" value="" />
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-top: 30px;"><center><input type="submit" value="Enviar"></center></td>
</tr>
</table>

</form>
<p>
	<a href="javascript: history.back();">Voltar</a>
</p>