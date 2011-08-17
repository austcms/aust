<?php
/**
 * Arquivo que contém interface para configurar estrutura que usa este módulo depois de já estar instalado.
 *
 * Ex: Inserir novos campos
 *
 * Inicialmente há todo o código PHP para executar as funções requisitadas e o FORM html está no final do documento. O action
 * dos FORMs enviam as informações para a própria página
 */
$tabela_da_estrutura = $module->getTable();

include_once $module->getIncludeFolder().'/'.MOD_MODELS_DIR.'FlexFieldsSetup.php';
$setup = new FlexFieldsSetup();
$setup->austNode = $_GET['aust_node'];
$setup->mainTable = $module->getTable();

/*
 * VERIFICAÇÕES $_POST
 * Faz verificações de $_POST o que deve ser feito
 */
/**
 * NOVO CAMPO
 * Insere um novo campo na tabela do cadastro
 */
	if(!empty($_POST['add_field'])){

		$params = array(
			'name' => $_POST['data']['name'],
			'type' => $_POST['data']['type'],
			'order' => $_POST['data']['order'],
			'description' => $_POST['data']['description'],
		);
		
		if( !empty($_POST['relacionado_tabela_0']) )
			$_POST['data']['refTable'] = $_POST['relacionado_tabela_0'];

		if( !empty($_POST['relacionado_campo_0']) )
			$_POST['data']['refField'] = $_POST['relacionado_campo_0'];
			
		$setup->addField($_POST['data']);
	}

/*
 * MOD_CONF
 */
if( !empty($_POST['conf_type']) AND $_POST['conf_type'] == "structure" ){
	/**
	 *
	 */
	
	//pr($_POST);
	$module->saveModConf($_POST);
}

/*
 *
 * DIVISORS
 *
 * Um divisor é um título que há entre campos de um formulário de
 * cadastro deste módulo.
 */
	/*
	 *
	 * NOVO TÍTULO DIVISOR
	 *
	 */
	if( !empty($_POST['create_divisor']) AND
		!empty($_POST['title']) )
		{

		$params = array(
			'title' => $_POST['title'],
			'comment' => $_POST['comment'],
			'before' => $_POST['before']
		);

		if( $module->saveDivisor($params) ){
			$status[] = 'Divisor criado com sucesso!';
		} else {
			$status[] = "Erro ao gravar informações sobre o novo campo. Nada foi criado.";
		}
	}
	/*
	 * Excluir Divisor
	 */
	if( !empty($_GET['deleteDivisor']) AND
		$_GET['deleteDivisor'] > 0 )
		{

		if( $module->deleteDivisor($_GET['deleteDivisor']) ){
			$status[] = 'Divisor excluído com sucesso!';
		} else {
			$status[] = "Erro. Nada foi excluído.";
			
		}
	}

/**
 * SALVAR CONFIGURAÇÕES
 */
if(!empty($_POST['configurar_opcoes'])){

	foreach($_POST as $key=>$value){

		if(!get_magic_quotes_gpc()){
			$value = addslashes($value);
		}
		// se o argumento $_POST contém 'frm' no início
		if(strpos($key, 'frm') === 0){
			$key = str_replace("frm", "", $key);
			$sql = "UPDATE
						flex_fields_config
					SET
						value='".$value."'
					WHERE
						property='".$key."' AND
						type='config' AND
						node_id='".$_GET['aust_node']."'
			";
			
			if($module->connection->exec($sql)){
				$status[] = "Informação \"".$key."\" salva com sucesso.";
			} else {
				$status[] = "<span style=\"color:red;\">Erro ao salvar \"".$key."\".";
			}
		}
	}
}


/**
 * FUNÇÃO
 *
 * Campo necessário? Desativar campo? Usar em listagem?
 *
 * Se $_GET['function'] existir
 */
if(!empty($_GET['function'])){
	/**
	 * DESATIVAR CAMPO
	 */
	if($_GET['function'] == "desativar"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					deactivated='1'
				WHERE
					property='".$_GET['w']."' AND
					node_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Campo desativado com sucesso";
		else
			$status[] = "Erro ao desativar campo.";
	}
	/**
	 * ATIVAR CAMPO
	 */
	if($_GET['function'] == "ativar"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					deactivated='0'
				WHERE
					property='".$_GET['w']."' AND
					node_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Campo ativado com sucesso";
		else
			$status[] = "Erro ao ativar campo.";
	}

	/**
	 * NECESSARIO
	 */
	if($_GET['function'] == "necessario"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					needed='1'
				WHERE
					property='".$_GET['w']."' AND
					node_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Preenchimento do campo ajustado para necessário com sucesso.";
		else
			$status[] = "Erro ao executar ação.";
	}

	/**
	 * CAMPO NÃO OBRIGATÓRIO
	 */
	if($_GET['function'] == "desnecessario"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					needed='0'
				WHERE
					property='".$_GET['w']."' AND
					node_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Não é necessário preenchimento obrigatório do campo ajustado com sucesso.";
		else
			$status[] = "Erro ao executar ação.";
	}

	/**
	 * LISTAR
	 *
	 * Campo deve aparecer em listagens
	 */
	if($_GET['function'] == "listar"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					listing='1'
				WHERE
					property='".$_GET['w']."' AND
					node_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "Campo aparecerá na listagem de cadastro.";
		else
			$status[] = "Erro ao executar ação.";
	}

	/**
	 * NÃO LISTAR
	 *
	 * Campo não deve aparecer em listagens
	 */
	if($_GET['function'] == "naolistar"){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					listing='0'
				WHERE
					property='".$_GET['w']."' AND
					node_id='".$_GET['aust_node']."'
		";
		if($module->connection->exec($sql))
			$status[] = "O campo selecionado não aparecerá mais em listagens.";
		else
			$status[] = "Erro ao executar ação.";
	}

}

/**
 * Desativar campos
 */
if(!empty($_GET['function'])){
	if($_GET['function'] == 'desativar' AND !empty($_GET['w'])){
		$sql = "
				UPDATE
					flex_fields_config
				SET
					type='campodesativado'


";
	}
}
?>


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
	 * GERENCIAMENTO DE CAMPOS
	 *
	 * Listagem dos campos deste cadastro e configuração destes
	 */
	?>
	<div class="widget">
		<div class="titulo">
			<h3>Gerenciamento dos campos</h3>
		</div>
		<div class="content">
			<p>A seguir, você tem a lista dos campos existentes neste cadastro.</p>
			<ul>
			<?php
			$fields = $module->getFields(false);

			foreach($fields as $chave=>$value){
				/**
				 * Verifica se o campo é editável ou infra-estrutura (ex. de campos: id, adddate, aprovado)
				 */
				$sql = "SELECT
							value, deactivated, listing, IFNULL(needed, '0') as needed
						FROM
							flex_fields_config
						WHERE
							property='".$chave."' AND
							node_id='".$_GET['aust_node']."'
						LIMIT 0,2
						";
				$result = $module->connection->query($sql);
				if( count($result) > 0 ){
					$dados = $result[0];
					?>
					<li>
					<?php echo $chave; ?>
					<?php
					if($dados['deactivated'] == '1'){
						?>
						<a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=ativar&w=<?php echo $chave; ?>">
							Ativar
						</a>
						<?php
					} else {
					?>
						<a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=desativar&w=<?php echo $chave; ?>">
							Desativar
						</a>
					<?php
					}
					?> -
					<?php
					if($dados['needed'] == '0'){
						?>
						<a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=necessario&w=<?php echo $chave; ?>">
							Necessario
						</a>
						<?php
					} else {
					?>
						<a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=desnecessario&w=<?php echo $chave; ?>">
							Não necessário
						</a>
					<?php
					}
					?> -
					<?php
					if( $dados['listing'] < '1' ){
						?>
						<a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=listar&w=<?php echo $chave; ?>">
							Listar
						</a>
						<?php
					} else {
						?>
						<a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=naolistar&w=<?php echo $chave; ?>">
							Não Listar
						</a>
						<?php
					}
					?>
					</li>
					<?php

				}
			}
			?>
			</ul>

		</div>
		<div class="footer"></div>
	</div>

	<?php
	/**
	 * NOVOS CAMPOS
	 *
	 * Form para inserir novos campos em um cadastro
	 */
	?>
	<div class="widget">
		<div class="titulo">
			<h3>Novo Campo</h3>
		</div>
		<div class="content">
			<p>Insira um novo campo.</p>
			<form method="post" action="<?php echo Config::getInstance()->self;?>" class="simples pequeno">
				<input type="hidden" name="add_field" value="1" />

				<?php
				/*
				 * Input CAMPO: Contém o nome do campo
				 */
				?>
				<div class="campo">
					<label>Nome:</label>
					<div class="input">
						<input type="text" name="data[name]" class="input" />
					</div>
				</div>
				<br clear="both" />
				<?php
				/*
				 * Input CAMPO_TIPO: Contém o tipo do campo
				 */
				?>
				<div class="campo">
					<label>Tipo: </label>
					<select name="data[type]" onchange="javascript: SetupCampoRelacionalTabelas(this, '<?php echo 'campooption0'?>', '0')">
						<option value="string">Texto pequeno</option>
						<option value="text">Texto médio ou grande</option>
						<option value="date">Data</option>
						<option value="pw">Senha</option>
						<option value="images">Imagens</option>
						<option value="files">Arquivo</option>
						<option value="relational_onetoone">Relacional 1-para-1 (tabela)</option>
						<option value="relational_onetomany">Relacional 1-para-muitos (tabela)</option>
						<?php
						/*
						 * faltam os campos relacionais
						 */
						?>
					</select>
				</div>
				<?php // <select> em ajax ?>
				<div class="campooptions" id="<?php echo 'campooption0'?>">
					<?php
					/*
					 * Se <select campo_tipo> for relacional, então cria dois campos <select>
					 *
					 * -<select relacionado_tabela_<n> onde n é igual a $i (sequencia numérica dos campos)
					 * -<select relacionado_campo_<n> onde n é igual a $i (sequencia numérica dos campos)
					 */
					?>
					<div class="campooptions_tabela" id="<?php echo 'campooption0'?>_tabela"></div>
					<div class="campooptions_campo" id="<?php echo 'campooption0'?>_campo"></div>
				</div>

				<?php
				/*
				 * Input CAMPO_DESCRICAO: Contém uma descrição do campo
				 */
				?>
				<br clear="both" />
				<div class="campo_descricao">
					<label>Descrição: </label>
					<input type="text" name="data[description]" />
				</div>
				<br clear="both" />
				<?php
				/*
				 * Input CAMPO_LOCAL: Indica onde será inserido o novo campo
				 */
				?>
				<div class="campo">
					<label>Local de inserção do novo campo: </label>
					<select name="data[order]">
						<?php


						// pega o valor físico do campo da tabela
						$fields = $module->getFields();
						$i = 0;
						foreach($fields as $campo=>$value){
							// verifica se o campo é editável ou infra-estrutura (ex. de campos: id, adddate, aprovado)
							$sql = "SELECT
										value, property
									FROM
										flex_fields_config
									WHERE
										property='".$campo."' AND
										node_id='".$_GET['aust_node']."'
									LIMIT 0,2
									";
							$result = $module->connection->query($sql,"ASSOC");
							$result = $result[0];
							if( count($result) > 0 ){
								$i++;
								// se for primeiro registro, escreve <option> com opção de "ANTES DE <campo>"
								if($i == "1"){
									echo '<option value="first_field">Antes de '.$result["value"].'</option>';
								}
								echo '<option value="'.$result["property"].'">Depois de '.$result["value"].'</option>';
							}

						}
						unset($campo);
						unset($dados);
						unset($value);
						?>
						
					</select>
				</div>
				<br />
				<input type="submit" name="novo_campo" value="Criar!" />

			</form>
		</div>
		<div class="footer"></div>
	</div>

	<?php
	/**
	 * TÍTULOS DIVISORES
	 *
	 * No formulário, há estes títulos que servem para dividir os
	 * inputs, como por exemplo, 'Informações Pessoais' e
	 * 'Informações Profissionais'.
	 */
	?>
	<div class="widget">
		<div class="titulo">
			<a name="divisors"><h3>Títulos Divisores</h3></a>
		</div>
		<div class="content">
			<p>
				Insira um novo título divisor no formulário de cadastro.
			</p>
			<form method="post" action="<?php echo Config::getInstance()->self;?>" class="simples pequeno">
				<input type="hidden" name="table" value="<?php echo $tabela_da_estrutura?>" />

				<?php
				/*
				 * Input CAMPO: Contém o nome do campo
				 */
				?>
				<div class="campo">
					<label>Nome do título:</label>
					<div class="input">
						<input type="text" name="title" class="input" />
					</div>
				</div>
				<br clear="both" />
				<div class="campo">
					<label>Parágrafo de comentário:</label>
					<div class="input">
						<input type="text" name="comment" class="input" />
					</div>
				</div>
				<br clear="both" />
				<?php
				/*
				 * Input CAMPO_LOCAL: Indica onde será inserido o novo campo
				 */
				?>
				<div class="campo">
					<label>Antes de: </label>
					<?php
					/*
					 * Busca campos do DB
					 */
					$sql = "SELECT
								property, value
							FROM
								flex_fields_config
							WHERE
								type='campo' AND
								node_id='".$_GET['aust_node']."'
							";
					$dados = $module->connection->query($sql,"ASSOC");
					?>

					<select name="before">
						<?php
						/*
						 * Lista campos para criar título divisor
						 */
						foreach($dados as $value){
							?>
							<option value="BEFORE <?php echo $value["property"]?>"><?php echo $value["value"]?></option>
							<?php
						}
						?>

					</select>
				</div>
				<br />
				<input type="submit" name="create_divisor" value="Criar!" />
			</form>

			<h4>Divisores atuais</h4>
			<?php
			$divisorTitles = $module->loadDivisors();
			if( empty($divisorTitles) ){
				?>

				<?php
			} else {
				foreach( $divisorTitles as $div ){
					?>
					<strong><?php echo $div['value'];?></strong>
					<br clear="all" />
					<em><?php echo $div['description'];?></em>
					<a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&deleteDivisor=<?php echo $div['id'] ?>#divisors">Excluir</a>
					<br clear="all" />
					<br clear="all" />
					<?php
				}
			}
			?>

		</div>
		<div class="footer"></div>
	</div>

	<?php
	/*
	 * FILTROS ESPECIAIS
	 */
	?>
	<div class="widget">
		<div class="titulo">
			<h3>Opções de Filtragem</h3>
		</div>
		<div class="content">
			<p>
				Se você especificar o campo de email abaixo, será mostrado um input
				na listagem para que o usuário possa ver os emails dos usuários
				cadastrados e copiá-los.
			</p>
			<?php
			if( !empty($_POST["filtro_especial_campo_email"])
				AND $_POST["filtro_especial_campo_email"] == "Salvar" ){

				$sql = "DELETE FROM flex_fields_config WHERE tipo='filtros_especiais'";
				$module->connection->exec($sql);

				if( !empty($_POST['email']) ){
					$sql = "INSERT INTO
								flex_fields_config
								(type, property, value, node_id)
							VALUES
								('filtros_especiais', 'email', '".$_POST['email']."', '".$_GET["aust_node"]."')
							";
					$module->connection->exec($sql);
				}
			}

			$sql = "SELECT value
					FROM
						flex_fields_config
					WHERE
						type='filtros_especiais' AND
						property='email' AND
						node_id='".$_GET["aust_node"]."'
					";
			$dados = $module->connection->query($sql);
			if( !empty($dados[0]["value"]) )
				$dados = $dados[0]["value"];
			else 
				$dados = '';

			?>
			<form method="post" action="<?php echo Config::getInstance()->self;?>" class="simples pequeno">
				<input type="hidden" name="table" value="<?php echo $tabela_da_estrutura ?>" />
				Campo de email? <input type="text" name="email" value="<?php echo $dados ?>" />
				<br />
				<input type="submit" name="filtro_especial_campo_email" value="Salvar" />
			</form>

		   

		</div>
		<div class="footer"></div>
	</div>
</div>

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
			<h3>Configurações</h3>
		</div>
		<div class="content">
			<?php
			$configurations = $module->loadModConf();
			if( !empty($configurations) && is_array($configurations) ){
				?>

				<p>Configure este módulo.</p>
				<form method="post" action="adm_main.php?section=control_panel&aust_node=<?php echo $_GET['aust_node']; ?>&action=structure_configuration" class="simples pequeno">
				<input type="hidden" name="conf_type" value="structure" />
				<input type="hidden" name="aust_node" value="<?php echo $_GET['aust_node']; ?>" />
				<?php

				foreach( $configurations as $key=>$options ){
					?>

					<div class="campo">
						<label><?php echo $options["label"] ?></label>
						<div class="input">
							<?php
							if( $options["inputType"] == "checkbox" ){

								/*
								 * Verifica valores no banco de dados.
								 */
								$checked = "";
								if( !empty($options['value']) ){
									if( $options["value"] == "1" ){
										$checked = 'checked="checked"';
									}
								}
								?>
								<input type="hidden" name="data[<?php echo $key; ?>]" value="0" />

								<input type="checkbox" name="data[<?php echo $key; ?>]" <?php echo $checked; ?> value="1" class="input" />
								<?php
							}

							else {
								?>
								<input type="text" name="data[<?php echo $key; ?>]" value="<?php echo $options['value'] ?>" class="input" />
								<?php
							}
							?>

						</div>
					</div>
					<br clear="both" />

					<?php
				}
				?>
				<input type="submit" name="submit" value="Salvar" />
				</form>
				<?php
			}
			?>

		</div>
		<div class="footer"></div>
	</div>

	<?php
	/**
	 * CONFIGURAÇÕES ESPECÍFICAS DE CAMPOS
	 */
	?>
	<div class="widget">
		<div class="titulo">
			<h3>Configurações de Campos</h3>
		</div>
		<div class="content">
			<?php
			$configurations = $module->loadModConf(null,'field');
			$fields = $module->getFields(false);
			if( !empty($configurations) && is_array($configurations) ){
				?>

				<p>Configure os campos abaixo:</p>
				<form method="post" action="adm_main.php?section=control_panel&aust_node=<?php echo $_GET['aust_node']; ?>&action=structure_configuration" class="simples pequeno">
				<input type="hidden" name="conf_type" value="structure" />
				<input type="hidden" name="conf_class" value="field" />
				<input type="hidden" name="aust_node" value="<?php echo $_GET['aust_node']; ?>" />
				<?php

				foreach( $fields as $fieldName=>$fieldOptions ){
					if( empty($fieldOptions["value"]) )
						continue;
					?>

					<div class="campo">
						<div><?php echo $fieldOptions["value"] ?></div>
						<div style="margin-left: 15px">
							<?php
							if( empty($configurations[$fieldName]) )
								$configurations[$fieldName] = array();

							foreach( $configurations[$fieldName] as $key=>$options ){
								
								if( !empty($options['field_type']) AND
									$options['field_type'] != $fieldOptions['specie']
								)
									continue;
									
								?>
								<div>
								<?php
								if( !empty($options["inputType"]) &&
									$options["inputType"] == "checkbox" )
								{

									/*
									 * Verifica valores no banco de dados.
									 */
		
									$checked = "";
									if( !empty($options['value'])
										AND $options['ref_field'] == $fieldName
		 								)
									{
										if( $options["value"] == "1" ){
											$checked = 'checked="checked"';
										}
									}
									?>
									<input type="hidden" name="data[<?php echo $fieldName ?>][<?php echo $key; ?>]" value="0" />

									<input type="checkbox" name="data[<?php echo $fieldName ?>][<?php echo $key; ?>]" <?php echo $checked; ?> value="1" class="input" />
						
									<?php
								}

								else {
									$size = '';
									if( !empty($options['size']) &&
									 	$options['size'] == 'small' )
										$size = '5';
									?>
									<input type="text" size="<?php echo $size?>" name="data[<?php echo $fieldName ?>][<?php echo $key; ?>]" value="<?php echo $options['value'] ?>" class="input" />
									<?php
								}
								if( !empty($options['label']) ){
									echo $options['label'];
								} else {
									echo "não possui label.";
								}
								if( !empty($options["help"]) )
									tt($options["help"]);
								?>
								
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
				?>
				<input type="submit" name="submit" value="Salvar" />
				</form>
				<?php
			}
			?>

		</div>
		<div class="footer"></div>
	</div>

	<?php
	/*
	 * Opções gerais do cadastro
	 */
	?>
	<div class="widget">
		<div class="titulo">
			<h3>Opções do cadastro</h3>
		</div>
		<div class="content">
			<p>A seguir, você configurar as principais opções deste cadastro.</p>
			<form method="post" action="<?php echo Config::getInstance()->self;?>" class="simples pequeno">
				<?php
				// busca todos os campos da tabela do cadastro
				$sql = "SELECT
							*
						FROM
							flex_fields_config
						WHERE
							type='config' AND
							node_id='".$_GET['aust_node']."'
						";
				$result = $module->connection->query($sql);
				foreach($result as $dados){
					?>
						<div class="campo">
							<label><?php echo $dados['name']?>:</label>
							<?php
							/*
							 * Mostra o campo de acordo
							 */
							if($dados['specie'] == 'bool'){ ?>
								<select name="frm<?php echo $dados['property']?>">
									<option <?php makeselected($dados['value'], '1') ?> value="1">Sim</option>
									<option <?php makeselected($dados['value'], '0') ?> value="0">Não</option>
								</select>
							<?php } elseif($dados['specie'] == 'string') { ?>
								<input type="text" name="frm<?php echo $dados['property']?>" value="<?php echo $dados['value']?>" />
							<?php } elseif($dados['specie'] == 'blob') { ?>
								<textarea name="frm<?php echo $dados['property']?>" cols="35" rows="3"><?php echo $dados['value']?></textarea>

							<?php } else { ?>
								<textarea name="frm<?php echo $dados['property']?>" cols="30" rows="3"><?php echo $dados['value']?></textarea>
							<?php } ?>
						</div>
					<?php
				}
				?>
				<br />
				<input type="submit" name="configurar_opcoes" value="Enviar" />
			</form>

		</div>
		<div class="footer"></div>
	</div>
</div>
