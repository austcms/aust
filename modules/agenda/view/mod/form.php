<?php

/*
 * FORMULÁRIO
 */

/*
 * Carrega configurações automáticas do DB
 */
	$params = array(
		"aust_node" => $_GET["aust_node"],
	);
	$moduloConfig = $module->loadModConf($params);

	//$module->loadHtmlEditor();
	$editorPlugins = '';
	if( $module->getStructureConfig('description_upload_inline_images') )
		$editorPlugins = 'imagemanager';
	
	if( $module->getStructureConfig('description_has_rich_editor') )
		$module->loadHtmlEditor($editorPlugins);


/*
 * Ajusta variáveis iniciais
 */
	$austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';

/*
 * [Se novo conteúdo]
 */
	if($_GET['action'] == 'create'){
		$tagh2 = "Criar: ". Aust::getInstance()->getStructureNameById($_GET['aust_node']);
		$tagp = 'Crie um novo evento.';
		$dados = array('id' => '');
		$start_date = date("d/m/Y");
		$end_date = $start_date;
		$checkedOccursOneDay = 'checked="checked"';
		$startTime = '0700';
		$endTime = '0730';
	}
/*
 * [Se modo edição]
 */
	else if($_GET['action'] == 'edit'){
		$start_date = date("d/m/Y", strtotime( $dados['start_datetime'] ));
		$end_date = date("d/m/Y", strtotime( $dados['end_datetime'] ));
		$occursAllDay = $dados['occurs_all_day'];

		$checkedOccursOneDay = '';
		if( $occursAllDay ){
			$checkedOccursOneDay = 'checked="checked"';
		} else {
			$startTime = str_replace(":", "", date("H:i", strtotime( $dados['start_datetime'] ) ) );
			$endTime =  str_replace(":", "", date("H:i", strtotime( $dados['end_datetime'] ) ) );
		}

	}

?>
<h2><?php echo $tagh2;?></h2>
<p><?php echo $tagp;?></p>



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data" >
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">

<?php if($_GET['action'] == 'create'){ ?>
	<input type="hidden" name="frmcreated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
<?php }?>

<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">

<table cellpadding=0 cellspacing=0 class="form">
	<?php
	/*
	 * QUEM É O ATOR
	 *
	 * Ator é a pessoa agente deste evento.
	 */

	if( $module->getStructureConfig('has_responsible_person') ){
		?>


		<tr>
			<td class="label">
				<label>Quem:</label>
			</td>
			<td>
				<input type="hidden" name="frmactor_is_user" value="1" />
				<?php
				$user = User::getInstance();
				
				$selectAnyOneActor = '';
				if( $_GET['action'] == 'edit' AND
					empty($dados['actor_admin_id'] ) )
				{
					$selectAnyOneActor = 'selected="selected"';
				}
				?>
				<select name="frmactor_admin_id">
					<option value="<?php echo $user->getId(); ?>"><?php echo $user->LeRegistro('nome'); ?> (você)</option>
					<option value="" <?php echo $selectAnyOneActor ?>>Não definir</option>
					<option value="">-----</option>
					<?php
					$allUsers = $user->getAllUsers();
					foreach( $allUsers as $value ){

						if( $value['id'] == $user->getId() )
							continue;

						$selected = '';
						if( !empty($dados['actor_admin_id']) AND
							$value['id'] == $dados['actor_admin_id'] )
							$selected = 'selected="selected"';
						?>
						<option value="<?php echo $value['id'] ?>" <?php echo $selected; ?>><?php echo $value['nome'] ?></option>
						<?php
					}
					?>
				</select>
			  
			</td>
		</tr>
		<?php
	}
	?>

	<tr>
		<td class="label"><label for="frmtitle">Nome do Evento:</label></td>
		<td>
			<INPUT TYPE='text' NAME='frmtitle' id="frmtitle" class='text' value='<?php if( !empty($dados['title']) ) echo $dados['title'];?>' />
		</td>
	</tr>

	<?php
	if( $module->getStructureConfig('has_place') ){
		?>
		<tr>
			<td class="label"><label for="frmplace">Local do evento:</label></td>
			<td>
				<INPUT TYPE='text' NAME='frmplace' id="frmplace" class='text' value='<?php if( !empty($dados['place']) ) echo $dados['place'];?>' />
			</td>
		</tr>
		<?php
	}
	?>
	<input type="hidden" id="hidden_start_datetime" />
	<?php
	/*
	 * CALENDÁRIO
	 *
	 * O arquivo html está em $modulo/js/calendar/for_form.php
	 *
	 */
	?>
		<tr>
			<td class="label">
				<label>Quando:</label>
			</td>
			<td>
				<input type="text" id="start_date" name="start_date" size="10" value="<?php echo $start_date; ?>" class="text date_text" />
				<?php
				if( !$module->getStructureConfig('one_day_only') ){
					?>
					até
					<input type="text" id="end_date" name="end_date" size="10"  value="<?php echo $end_date ?>" class="text date_text" />
					<?php
					tt("Selecione o mesmo dia em <strong>ambos</strong> os campos se o evento ocorrerá em apenas um dia.");
					?>
				<?php
				}
				?>
				<script type="text/javascript">
					agendaSetFromEndDate();
				</script>

			</td>
		</tr>
		<tr>
			<td class="label">
				<label>Horário:</label>
			</td>
			<td>
				<span id="agendaTime">
					<select id="start_time" onchange="agendaAdjustTime('start')" name="start_time">
						<?php
						for( $i = 0; $i < 24; $i++ ){
							$iSprint = sprintf("%02d",$i);

							$select1 = '';
							$select2 = '';

							if( $iSprint.'00' == $startTime )
								$select1 = 'selected="selected"';
							if( $iSprint.'30' == $startTime )
								$select2 = 'selected="selected"';
							?>
							<option value="<?php echo $iSprint ?>00" <?php echo $select1; ?>><?php echo $iSprint ?>:00</option>
							<option value="<?php echo $iSprint ?>30" <?php echo $select2; ?>><?php echo $iSprint ?>:30</option>
							<?php
						}
						?>
					</select>
					até
					<select id="end_time" onchange="agendaAdjustTime('end')" name="end_time">
						<?php
						for( $i = 0; $i < 24; $i++ ){
							$iSprint = sprintf("%02d",$i);
							$select1 = '';
							$select2 = '';

							if( $iSprint.'00' == $endTime )
								$select1 = 'selected="selected"';
							if( $iSprint.'30' == $endTime )
								$select2 = 'selected="selected"';
							?>
							<option value="<?php echo $iSprint ?>00" <?php echo $select1; ?>><?php echo $iSprint ?>:00</option>
							<option value="<?php echo $iSprint ?>30" <?php echo $select2; ?>><?php echo $iSprint ?>:30</option>
							<?php
						}
						?>
					</select>
				</span>
				<input type="hidden" name="durationAllDay" value="0" />
				<input type="checkbox" name="durationAllDay" id="oneDay" <?php echo $checkedOccursOneDay; ?> onclick="agendaSetTimeAllDay(this)" value="1" /> Dura o dia inteiro
				<script type="text/javascript">
					agendaSetTimeAllDay($("#oneDay"));
				</script>

			</td>
		</tr>
	
	<tr>
		<td class="label"><label for="frmdescription">Descrição:</label>
		</td>
		<td>
			<textarea name="frmdescription" id="jseditor" rows="4"><?php if( !empty($dados['description']) ) echo $dados['description'];?></textarea>
		</td>
	</tr>

	<tr>
		<td colspan="2"><center><INPUT TYPE="submit" value="Enviar" name="submit" class="submit"></center></td>
	</tr>
</table>

</form>
