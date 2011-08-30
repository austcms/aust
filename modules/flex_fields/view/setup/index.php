<?php
/**
 * @since v1.6 25/06/2009
 */
?>
<div id="installation">
	
	<h2>Flexfields: nova estrutura</h2>
	<form action="" method="post" class="normal">

		<?php
		foreach($exPOST as $chave=>$value){
			echo '<input type="hidden" name="'.$chave.'" value="'.$value.'" />';
		}
		?>
		<input type="hidden" name="setupAction" value="setuppronto" />

		<p>
			Preencha abaixo informações sobre cada campo para a nova estrutura <em><?php echo $_SESSION['exPOST']['name']?></em>.
		</p>

		<div class="listing">
			<div class="header">
				<div class="header_container">
					<div class="column field_number">&nbsp;</div>
					<div class="column field_name">Nome do campo</div>
					<div class="column field_type">Tipo</div>
					<div class="column field_description">Descrição de ajuda</div>
				</div>
			</div>
		
			<div class="fields_setup">
				<div class="fields_setup_container">			
					<?php for ($i = 1; $i <= $fieldsQuantity; $i++){ ?>
					<div class="field">
						<div class="column field_number">
							<?php echo $i;?>
						</div>
						<div class="column field_name">
							<input type="text" class="field_name" name="campo[]" />
						</div>
						<div class="column field_type">
							<select name="campo_tipo[]" onchange="javascript: SetupCampoRelacionalTabelas(this, '<?php echo 'campooption'.$i?>', '<?php echo $i?>')">
								<option value="string">Texto pequeno (ex: nome, idade)</option>
								<option value="text">Texto grande (ex: descrição, biografia)</option>
								<option value="date">Data (ex: data de nascimento)</option>
								<option value="pw">Senha</option>
								<option value="files">Arquivo</option>
								<option value="images">Imagens</option>
								<?php /* ?>
								<option value="relational_onetoone">Relacional 1-para-1 (tabela)</option>
								<option value="relational_onetomany">Relacional 1-para-muitos (tabela)</option>
								*/ ?>
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
						</div>
						<div class="column field_description">
							<input type="text" name="campo_descricao[]" />
						</div>

					</div>
					<?php } ?>
				</div>
			</div>
			<div id="new_field_button">
				<a href="javascript: void(0)" id="add_new_field">Acrescentar campo</a>
			</div>
		</div>
		
		<input type='submit' value="Enviar" name='setup_ready' />
	
	</form>
		
	<!-- div template used to insert new fields -->
	<div class="field_template" style="display: none">
		<div class="field">
			<div class="column field_number">
				<?php echo $i;?>
			</div>
			<div class="column field_name">
				<input type="text" name="campo[]" />
			</div>
			<div class="column field_type">
				<select name="campo_tipo[]" onchange="javascript: SetupCampoRelacionalTabelas(this, '<?php echo 'campooption'.$i?>', '<?php echo $i?>')">
					<option value="string">Texto pequeno (ex: nome, idade)</option>
					<option value="text">Texto grande (ex: descrição, biografia)</option>
					<option value="date">Data (ex: data de nascimento)</option>
					<option value="pw">Senha</option>
					<option value="files">Arquivo</option>
					<option value="images">Imagens</option>
					<?php /* ?>
					<option value="relational_onetoone">Relacional 1-para-1 (tabela)</option>
					<option value="relational_onetomany">Relacional 1-para-muitos (tabela)</option>
					*/ ?>
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
			</div>
			<div class="column field_description">
				<input type="text" name="campo_descricao[]" />
			</div>
		</div>
	</div>

</div>
<?php /* ?>

		<table border="0" class="listing">
		<col width="15">
		<col>
		<col>
		<tr class="header">
			<td></td>
			<td>Nome do campo</td>
			<td>Tipo de campo</td>
			<td>Descrição de ajuda</td>
		</tr>
		<tbody>
			<?php for ($i = 1; $i <= $fieldsQuantity; $i++){ ?>
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
						<?php /* ?>
						<option value="relational_onetoone">Relacional 1-para-1 (tabela)</option>
						<option value="relational_onetomany">Relacional 1-para-muitos (tabela)</option>
						* ?>
					</select>
				<div class="campooptions" id="<?php echo 'campooption'.$i?>">
					<?php
					/*
					 * Se <select campo_tipo> for relacional, então cria dois campos <select>
					 *
					 * -<select relacionado_tabela_<n> onde n é igual a $i (sequencia numérica dos campos)
					 * -<select relacionado_campo_<n> onde n é igual a $i (sequencia numérica dos campos)
					 *
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
		</tbody>
		</table>
		<br clear="both" />
		<br />
		<input type='submit' value="Enviar" name='setup_ready' />
	</form>
	*/ ?>