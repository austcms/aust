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

	/*
	 * QUANTIDADE DE PERGUNTAS NESTA PESQUISA
	 */
	/*
	 * Enquetes têm uma pergunta apenas
	 */
	if( !empty($moduloConfig["enquete"]) AND $moduloConfig["enquete"]["valor"] == 1 ){
		$perguntasQuantidade = 1;
	} elseif( $_POST["perguntas_quantidade"] > 0 ) {
		$perguntasQuantidade = $_POST["perguntas_quantidade"];
	} elseif( $_GET["action"] == "editar" ) {
		$perguntasQuantidade = true;
	}

/*
 * Ajusta variáveis iniciais
 */
	$austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
	$w = (!empty($_GET['w'])) ? $_GET['w'] : '';

	$perguntas = array();
	$respostas = array();
/*
 * [Se novo conteúdo]
 */
	if($_GET['action'] == 'create'){
		$tagh1 = "Criar: ". Aust::getInstance()->getStructureNameById($_GET['aust_node']);
		$tagp = 'Crie um novo conteúdo abaixo.';
		$dados = array('id' => '');
	}
/*
 * [Se modo edição]
 */
	else if($_GET['action'] == 'edit'){

		$tagh1 = "Editar: ". Aust::getInstance()->getStructureNameById($_GET['aust_node']);
		$tagp = 'Edite o conteúdo abaixo.';
		
		/*
		 * Informações da pesquisa
		 */
		$sql = "
				SELECT
					p.*
				FROM
					".$module->tabela_criar." as p
				LEFT JOIN
					pesqmkt_perguntas as pp
				ON
					pp.pesqmkt_id=p.id
				LEFT JOIN
					pesqmkt_respostas as pr
				ON
					pr.pesqmkt_pergunta_id=pp.id
				LEFT JOIN
					pesqmkt_respostas_textos as prt
				ON
					prt.pesqmkt_pergunta_id=pp.id
				WHERE
					p.id='$w'
				";

		$query = $module->connection->query($sql, "ASSOC");
		$dados = $query[0];
		$pesqAtiva = $dados["ativo"];

		$sql = "SELECT
					pp.id, pp.tipo, pp.texto

				FROM
					pesqmkt_perguntas as pp
				WHERE
					pp.pesqmkt_id='$w'
				";

		$query = $module->connection->query($sql, "ASSOC");
		$perguntas = $query;
		//pr($perguntas);

		$perguntasQuantidade = count($perguntas);

		$sql = "SELECT
					pp.id, pp.tipo,

					pr.id as prid, pr.titulo, pr.pesqmkt_pergunta_id, pr.votos,
					(
						SELECT
							SUM(pr2.votos)
						FROM
							pesqmkt_respostas as pr2
						WHERE pr2.pesqmkt_pergunta_id=pr.pesqmkt_pergunta_id
						GROUP BY
							pr2.pesqmkt_pergunta_id
						LIMIT 1
					)
					AS totalVotos
				FROM
					pesqmkt_perguntas as pp
				LEFT JOIN
					pesqmkt_respostas as pr
				ON
					pr.pesqmkt_pergunta_id=pp.id
				LEFT JOIN
					pesqmkt_respostas_textos as prt
				ON
					prt.pesqmkt_pergunta_id=pp.id
				WHERE
					pp.pesqmkt_id='$w'
				";

		$query = $module->connection->query($sql, "ASSOC");
		$respostasTmp = $query;

		foreach( $respostasTmp as $chave=>$value ){
			if( empty($value["prid"]) )
				$value["prid"] = 0;
			$respostas[ $value["id"] ][ $value["prid"] ] = $value;
		}

		//pr($respostas);
	}
?>

<?php
/*
 * JAVASCRIPT
 */
?>
<script type="text/javascript">
	function somaAlternativa(id){
		$("#alternativa_"+id).append("<div style=\"margin: 3px;\"><div style=\"display: table; text-align: right; margin-left: 0px; width: 150px; float: left;\">Alternativa:&nbsp;</div> <input type=\"text\" size=\"40\" name=\"resposta["+id+"][]\"><br clear=\"both\" /></div>");
	}

	function restauraAlternativas(id){
		$("#alternativa_"+id).html("");
		$(".add_alternativa_"+id).show();
		for( i = 0; i < 4; i++ ){
			somaAlternativa(id);
		}
	}

	function validate(este){
		if( este.frmtitulo.value == "" ){
			alert("Especifique um título para a pesquisa.");
			return false;
		}

		return true;
	}

	var nextQuestion = 0;
	function somaQuestao(nextPid){
		if( nextQuestion == 0 )
			nextQuestion = nextPid;
		else {
			nextQuestion++;
		}

		$(".questaoParaAdicionar .adicionalPergunta").attr("name", "perguntas["+nextQuestion+"]");
		$(".questaoParaAdicionar .adicionalResposta").attr("name", "resposta["+nextQuestion+"][]");
		$(".questaoParaAdicionar .resposta_tipo").attr("name", "resposta_tipo["+nextQuestion+"]");
		$(".questaoParaAdicionar .alternativaidAdicional").attr("id", "alternativa_"+nextQuestion);

		//$(".pergunta_adicional .resposta_tipo").attr("class", "alternativa_"+nextQuestion);

		$(".pergunta_adicional").append( $(".questaoParaAdicionar").html() );
		
		$(".questaoParaAdicionar .alternativaidAdicional").attr("id", "");
		$(".pergunta_adicional .add_alternativa_x").attr("class", "add_alternativa_"+nextQuestion);
		$(".pergunta_adicional .add_alternativa_"+nextQuestion+" a").attr("class", nextQuestion);
		$(".pergunta_adicional #alternativa_"+nextQuestion).attr("class", "");
		$(".pergunta_adicional .resposta_tipo").attr("class", ""+nextQuestion);

	}
	
</script>
<div class="questaoParaAdicionar" style="display: none;">
	<div style="margin: 3px;">
		<div style="font-size: 1.2em; font-weight: bold; display: table; text-align: right; margin-left: 0px; width: 150px; float: left;">
			Nova Questão:&nbsp;
		</div>
		<input type="text" size="43" class="adicionalPergunta" name="perguntas[]" value="" />
		<br clear="both" />
	</div>
	<div class="alternativaidAdicional">
	<div style="margin: 3px;">
		<div style="display: table; text-align: right; margin-left: 0px; width: 150px; float: left;">
			Alternativa:&nbsp;
		</div>
		<input type="text" size="40" class="adicionalResposta" name="resposta[][]" value="" />
		<br clear="both" />
	</div>
	<div style="margin: 3px;">
		<div style="display: table; text-align: right; margin-left: 0px; width: 150px; float: left;">
			Alternativa:&nbsp;
		</div>
		<input type="text" size="40" class="adicionalResposta" name="resposta[][]" value="" />
		<br clear="both" />
	</div>
	<div style="margin: 3px;">
		<div style="display: table; text-align: right; margin-left: 0px; width: 150px; float: left;">
			Alternativa:&nbsp;
		</div>
		<input type="text" size="40" class="adicionalResposta" name="resposta[][]" value="" />
		<br clear="both" />
	</div>
	<div style="margin: 3px;">
		<div style="display: table; text-align: right; margin-left: 0px; width: 150px; float: left;">
			Alternativa:&nbsp;
		</div>
		<input type="text" size="40" class="adicionalResposta" name="resposta[][]" value="" />
		<br clear="both" />
	</div>
	</div>
	<div style="margin-left: 160px; margin-bottom: 0px;">
		<?php
		/*
		 * BOTÕES DE OPÇÕES
		 */
		$perguntaTipo = "fechada";
		?>
		Tipo de resposta: <input class="resposta_tipo" name="resposta_tipo[]" <?php if($perguntaTipo=="aberta")echo 'checked="checked"'; ?> value="aberta" type="radio" onclick='$("#alternativa_"+this.className).html("<div style=\"font-style: italic; margin-left: 160px; width: 330px; margin-bottom: 10px\">Esta será uma pergunta aberta, portanto não serão apresentadas alternativas ao usuário, mas sim uma caixa de texto para escrever sua resposta.</div>"); $(".add_alternativa_"+nextQuestion).hide()' /> Texto
		<input type="radio" class="resposta_tipo" name="resposta_tipo[]" <?php if($perguntaTipo=="fechada")echo 'checked="checked"'; ?> value="fechada" onclick='restauraAlternativas(this.className)' /> Múltipla Escolha
		<span class="add_alternativa_x">| <a href="javascript: void(0);" class="" onclick='somaAlternativa(this.className)'>+ alternativa</a></span>
	</div>
</div>


<h2><?php echo $tagh1;?></h2>

<?php
if( $_GET["action"] == "edit" ){
	?>
	<?php
	/*
	 *
	 * RESULTADOS
	 *
	 */

	if( $module->getStructureConfig('do_not_show_result') != '1' ){
		?>
		<div class="resultados" style="width: 99%; display: block;">
			<div class="resultados_content" style="margin: 5px; padding: 10px 20px 10px 20px; width: 93%; background: #faf9f9; display: table;">
			<h3>Resultados</h3>
			<p>
				A seguir, o resultado da pesquisa: <em><?php echo $dados['titulo'] ?></em>
			</p>
				<?php
				foreach($perguntas as $chave=>$pergunta){
					?>
					<div style="margin-bottom: 15px;">
						<strong>Questão: </strong><?php echo $pergunta["texto"] ?>
						<br />
						<span style="margin-left: 20px; display: table;">
						<?php
						foreach($respostas[$pergunta["id"]] as $resposta){

							if( $pergunta["tipo"] == "fechada" ){
								?>
								<?php
								if( $resposta["totalVotos"] > 0 )
									echo str_replace(".",",", number_format($resposta["votos"]/$resposta["totalVotos"]*100, "1") )."%";
								else
									echo "0";

								?> -
								<?php echo $resposta["titulo"] ?> -
								<span style="color: #999999; font-style: italic;"><?php echo $resposta["votos"] ?> voto(s)</span>
								<br />
								<?php
							} else {
								?>
								<div style="margin-top: 15px;" id="resultados_perguntaaberta_<?php echo $pergunta["id"]; ?>">
								Esta pergunta contém respostas no formato texto. Deseja visualizá-los?
								<br><em>Obs.: Dependendo da quantidade de resultados, o processo pode demorar alguns instantes.</em>
								<br>

								<a href="javascript: void(0);" onclick="mostraResultadosPerguntaAberta(<?php echo $pergunta["id"];?>)">Mostrar resultados desta questão</a>
								</div>
								<?php

							}
						}
						?>
						</span>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	} // fim id(!do_not_show_result)
}
?>

<?php
if( !empty($perguntasQuantidade) ):
?>
	<p><?php echo $tagp;?></p>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" onsubmit="return validate(this);" enctype="multipart/form-data" >
	<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">
	<?php if($_GET['action'] == 'criar'){ ?>
		<input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
		<input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
	<?php } else { ?>

		<input type="hidden" name="frmadddate" value="<?php ifisset( $dados['adddate'] );?>">
		<input type="hidden" name="frmautor" value="<?php ifisset( $dados['autor'] );?>">

	<?php }?>
	<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
	<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">
	<table border=0 cellpadding=0 cellspacing=0 class="form">
		<col width="200">
		<col width="470">

		<?php
		if( $module->getStructureConfig('has_no_title') != '1' ){
			?>
			<tr>
				<td valign="top" class="first"><label>Título da pesquisa:</label></td>
				<td class="second">
					<INPUT TYPE='text' NAME='frmtitulo' class='text' value='<?php if( !empty($dados['titulo']) ) echo $dados['titulo'];?>' />
					<p class="explanation">
						Um título para a pesquisa.
					</p>
				</td>
			</tr>
			<?php
		}

		if( $module->getStructureConfig('has_no_visibility_option') != '1' ){
			?>
		<tr>
			<td valign="top"><label>Pesquisa ativa:</label></td>
			<td>
				<select name="frmativo" class="select">
					<option <?php if( !empty($pesqAtiva) ) makeselected($pesqAtiva, '1'); ?> value="1">Sim, pesquisa visível</option>
					<option <?php if( is_string($pesqAtiva) OR !empty($pesqAtiva) ) makeselected($pesqAtiva, '0'); ?> value="0">Não, esconder pesquisa</option>
				</select>
				<p class="explanation">
					Esta pesquisa está ativa no site? Quando você ativa uma pesquisa,
					todas as outras existentes são desativadas automaticamente.
				</p>
			</td>
		</tr>
		<?php
		}
		
		if( $module->getStructureConfig('has_description') == '1' ){
			?>
			<tr>
				<td valign="top"><label>Texto sobre a pesquisa: </label>
				</td>
				<td>
					<textarea name="frmtexto" id="jseditor" rows="4" style="width: 99%"><?php if( !empty($dados['texto']) ) echo $dados['texto'];?></textarea>
				<p class="explanation">
					Fale sobre a pesquisa, qual seu objetivo e quais seus benefícios.
				</p>
				</td>
			</tr>
			<?php
		}
		?>

		<?php

		?>
		<tr>
			<td colspan="2">
			<div style="display: table; width: 100%; border-top: 1px silver dashed;" id="alternativas">
				<h3>Perguntas e respostas</h3>
				<p>
					A seguir, crie perguntas e suas respostas.
					Alternativas deixadas em branco não serão salvas.
				</p>

				<?php
				$pergunta = reset($perguntas);
				for( $i = 0; $i < $perguntasQuantidade; $i++ ){
					?>

					<div style="margin-bottom: 20px; display: table;">
					<?php
					/*
					 * PERGUNTAS
					 */
					$pid = $i;
					if( $_GET["action"] == "edit" ){
						$pid = $pergunta["id"];
					}
					?>
					<div style="margin: 3px;">
						<div style="font-size: 1.2em; font-weight: bold; display: table; text-align: right; margin-left: 0px; width: 150px; float: left;">
							Questão<?php 
							if( $module->getStructureConfig('enquete') != '1' ){
							echo $i+1;
							}
							?>:&nbsp;
						</div>
						<input type="text" size="43" name="perguntas[<?php echo $pid ?>]" value="<?php if(!empty($pergunta)) echo $pergunta["texto"]; ?>" />
						<br clear="both" />
					</div>
					<?php
					/*
					 * RESPOSTAS
					 */
					?>
					<div id="alternativa_<?php echo $pid ?>">
						<?php
						if( $_GET["action"] == "edit" ){
							$loop = count($respostas[$pergunta["id"]] );
						} else {
							/*
							 * Quantidade padrão de alternativas
							 */
							$loop = 4;
						}
						if($loop < 1)
							$loop = 1;

						/*
						 * Se a pergunta é aberta
						 */
						if( $pergunta["tipo"] == "aberta" ){
							?>
							<div style="font-style: italic; margin-left: 160px; width: 330px; margin-bottom: 10px">Esta será uma pergunta aberta, portanto não serão apresentadas alternativas ao usuário, mas sim uma caixa de texto para escrever sua resposta.</div>
							<?php
						}
						/*
						 * Pergunta fechada, mostra alternativas
						 */
						else {

							if( !empty($respostas) ){
								$respostasTmp = $respostas[$pergunta["id"]];
								$resposta = reset($respostasTmp);
								$prid = $resposta["prid"];
								$prtitulo = $resposta["titulo"];
							} else {
								$prid = "";
								$prtitulo = "";
							}

							for( $r = 1; $r <= $loop; $r++ ){
								?>
								<div style="margin: 3px;">
									<div style="display: table; text-align: right; margin-left: 0px; width: 150px; float: left;">
										
										<?php
										if( $module->getStructureConfig('first_alternative_right') == '1' AND
											$r == 1 )
										{
											?>
											Alternativa Correta:&nbsp;
											<?php
										} else {
											?>
											Alternativa:&nbsp;
											<?php
										}
										?>
									</div>
									<input type="text" size="40" name="resposta[<?php echo $pid ?>][<?php echo $prid ?>]" value="<?php if(!empty($prtitulo)) echo $prtitulo; ?>" />
									<br clear="both" />
								</div>
								<?php
								if( !empty($respostas) ){
									$resposta = next($respostasTmp);
									$prid = $resposta["prid"];
									$prtitulo = $resposta["titulo"];
								} else {
									$prid = "";
									$prtitulo = "";
								}
							}
						}
						?>
					</div>

					<div style="margin-left: 160px;">
						<?php
						/*
						 * BOTÕES DE OPÇÕES
						 */
						$perguntaTipo = "fechada";
						if(!empty($pergunta["tipo"]))
							$perguntaTipo = $pergunta["tipo"];

						/*
						 * É uma enquete?
						 */
						if( $moduloConfig["enquete"]["valor"] == 0 ){
							?>
							Tipo de resposta: <input name="resposta_tipo[<?php echo $pid ?>]" <?php if($perguntaTipo=="aberta")echo 'checked="checked"'; ?> value="aberta" type="radio" onclick='$("#alternativa_<?php echo $pid ?>").html("<div style=\"font-style: italic; margin-left: 160px; width: 330px; margin-bottom: 10px\">Esta será uma pergunta aberta, portanto não serão apresentadas alternativas ao usuário, mas sim uma caixa de texto para escrever sua resposta.</div>"); $(".add_alternativa_<?php echo $pid ?>").hide()' /> Texto
							<input type="radio" name="resposta_tipo[<?php echo $pid ?>]" <?php if($perguntaTipo=="fechada")echo 'checked="checked"'; ?> value="fechada" onclick='restauraAlternativas(<?php echo $pid ?>)' /> Múltipla Escolha
							<?php if( $module->getStructureConfig('can_not_add_alternatives') != '1' ){ ?>
								<span class="add_alternativa_<?php echo $pid ?>">| <a href="javascript: void(0);" onclick='somaAlternativa(<?php echo $pid ?>)'>+ alternativa</a></span>
							<?php
							}
						} else {
							?>
							<input type="hidden" name="resposta_tipo[<?php echo $pid ?>]" value="fechada" />
							<?php if( $module->getStructureConfig('can_not_add_alternatives') != '1' ){ ?>
								<span class="add_alternativa_<?php echo $pid ?>"><a href="javascript: void(0);" onclick='somaAlternativa(<?php echo $pid ?>)'>+ alternativa</a></span>
							<?php
							}
						}
						?>

					</div>
					</div>
					<?php
					$pergunta = next($perguntas);

					if( empty($higherPid) )
						$higherPid = $pid;

					if( $pid > $higherPid )
						$higherPid = $pid;
				}
				?>
				<div class="pergunta_adicional">

				</div>
				<?php
				/*
				 * É uma enquete? ah, então não deixa o cara botar mais
				 * questões :)
				 */
				if( $moduloConfig["enquete"]["valor"] == 0 ){
					?>
					<a href="javascript: void(0);" onclick='somaQuestao(<?php echo $higherPid+1 ?>)'>Acrescentar uma nova questão</a>
					<?php
				}
				?>
			</div>


			</td>
		</tr>

		<tr>
			<td colspan="2" style="padding-top: 10px;"><center><INPUT TYPE="submit" value="Enviar" name="submit" class="submit"></center></td>
		</tr>
	</table>

	</form>

<?php
else:
	?>
	<p>
		Antes de cadastrar sua pesquisa, preciso de algumas informações para
		mostrar um formulário para você:
	</p>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=<?php echo $_GET["action"]?>&aust_node=<?php echo $_GET["aust_node"]?>" enctype="multipart/form-data" >
	<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">
	<table border=0 cellpadding=0 cellspacing=0 class="form">
		<col width="250">
		<col width="320">
		<tr>
			<td valign="top"><label>Quantas perguntas sua pesquisa terá?</label></td>
			<td>
				<select name="perguntas_quantidade">
				<?php
				for( $i=1; $i<=30; $i++ ){ ?>

					<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php
				}
				?>

				</select>
				<p class="explanation">
					Uma pergunta pode ter várias respostas.
				</p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-top: 10px;"><center><INPUT TYPE="submit" value="Enviar" name="submit" class="submit"></center></td>
		</tr>
	</table>
	</form>
	<?php
endif;
?>
