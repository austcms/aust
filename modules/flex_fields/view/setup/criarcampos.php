<?php
/**
 * Descrição deste arquivo
 *
 * @author nome do autor <email>
 * @since v1.6 25/06/2009
 */
			//echo $_SESSION['exPOST']['nome'];
?>

<h2>Flexfields: configurar campos</h2>
<form action="" method="post" class="normal">

<?php
/**
 * Escreve cada exPOST
 */
foreach($exPOST as $chave=>$value){
	echo '<input type="hidden" name="'.$chave.'" value="'.$value.'" />';
}
?>

	<input type="hidden" name="setupAction" value="setuppronto" />

	<p>
		Foram escolhidos <?php echo ''.$_POST['qtd_campos'].''; if($_POST['qtd_campos'] == 1) echo ' campo'; else echo ' campos';?>.

		Preencha abaixo informações sobre cada campo para a nova estrutura <em><?php echo $_SESSION['exPOST']['nome']?></em>.
	</p>

	<table border="0" class="listing">
	<col width="15">
	<col width="160">
	<col width="400">
	<col>
	<tr class="header">
			<td></td>
			<td valign="top">Nome do campo</td>
			<td valign="top">Tipo de campo</td>
			<td valign="top">Descrição<br /><span style="font-weight: normal">Servirá de ajuda aos usuários</span></td>
	</tr>
	<?php for ($i = 1; $i <= $_SESSION['exPOST']['qtd_campos']; $i++){ ?>
	<tr class="list">
			<td align="center" style="font-weight: bold;" valign="top">
			<?php echo $i;?>
			</td>
			<td valign="top">
					<input type="text" name="campo[]" />
			</td>
			<td valign="top">
				<select name="campo_tipo[]" onchange="javascript: SetupCampoRelacionalTabelas(this, '<?php echo 'campooption'.$i?>', '<?php echo $i?>')">
					<option value="string">Texto pequeno (ex: nome, idade)</option>
					<option value="text">Texto grande (ex: descrição, biografia)</option>
					<option value="date">Data (ex: data de nascimento)</option>
					<option value="pw">Senha</option>
					<option value="files">Arquivo</option>
					<option value="images">Imagens</option>
					<option value="relational_onetoone">Relacional 1-para-1 (tabela)</option>
					<option value="relational_onetomany">Relacional 1-para-muitos (tabela)</option>
				</select>
			<div class="campooptions" id="<?php echo 'campooption'.$i?>">
				<?php
				/*
				 * Se <select campo_tipo> for relacional, então cria dois campos <select>
				 *
				 * -<select relacionado_tabela_<n> onde n é igual a $i (sequencia numérica dos campos)
				 * -<select relacionado_campo_<n> onde n é igual a $i (sequencia numérica dos campos)
				 */
				?>
				<div class="campooptions_tabela" id="<?php echo 'campooption'.$i; ?>_tabela"></div>
				<div class="campooptions_campo" id="<?php echo 'campooption'.$i; ?>_campo"></div>
			</div>

			</td>
			<td valign="top">
				<input type="text" name="campo_descricao[]" />
			</td>
	</tr>
	<?php } ?>
	</table>
	<br clear="both" />
	<br />
	<input type='submit' value="Enviar" name='setup_ready' />

</form>