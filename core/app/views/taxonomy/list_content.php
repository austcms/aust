<?php
/**
 * Arquivo que lista categorias
 *
 * @todo - Deve ser melhorado
 *
 * @since 01/01/2009
 */

$w = (!empty($_GET['w'])) ? $_GET['w'] : 'NULL';
?>


<div class="title_column">
	<h2>Taxonomia: visão geral</h2>
	
	<div class="root_user_only"><?php tt("Apenas desenvolvedores acessam esta tela.", "padlock") ?></div>
</div>

<p>
	Veja site, estruturas e categorias abaixo. O conceito é o mesmo de uma árvore
	genealógica, onde os filhos ficam abaixo dos pais em hierarquia.
</p>
<p>
	<strong>Passe o mouse</strong> sobre uma categoria para editá-la.
</p>



<?php
if(!empty($_GET['block']) AND $_GET['block'] == "delete"){
	if(empty($_GET['confirm'])){
	?>
		<div style="width: 680px; display: table;">
			<div style="background: yellow; padding: 15px; text-align: center;">
				<p style="color: black; margin: 0px;">
					<strong>
					Tem certeza que deseja apagar o item selecionado?
					</strong>
					<br />
					<a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=<?php echo $_GET['action'];?>&block=delete&w=<?php echo $w;?>&confirm=delete<?php echo $addurl;?>">Sim</a> -
					<a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=<?php echo $_GET['action'];?><?php echo $addurl;?>">Não</a>
				</p>
			</div>
		</div>
	<?php
	} else if($_GET['confirm'] == "delete"){
		$sql = "DELETE FROM ".$_GET['section']."
				WHERE
					id='$w'
				";
		if (Connection::getInstance()->exec($sql)){
		?>
			<div style="width: 680px; display: table;">
				<div style="background: black; padding: 15px; text-align: center;">
					<p style="color: white; margin: 0px;">
						<strong>
						O conteúdo foi apagado definitivamente!
						</strong>
					</p>
				</div>
			</div>
		<?php

		} else {
			echo '<p style="color: red;">Ocorreu um erro desconhecido ao editar as informações do usuário, tente novamente.</p>';
		}
	}
}
?>

<div class="highlights_painel">
	<div class="containner">
	<?php
		$usertipo = User::getInstance()->LeRegistro('group');
		/*
		 * ORGANOGRAMA
		 * Monta organograma das categorias
		 */
		function BuildCategoriasStructure($table, $parent=0, $level=0){
			global $usertipo; // torna local esta variável
			global $conexao;
			$sql = "
					SELECT
						cat.id, cat.father_id, cat.name, cat.admin_id, cat.type,
						( SELECT COUNT(*)
						FROM
							$table AS clp
						WHERE
							clp.father_id=cat.id
						) AS num_sub_nodes
					FROM
						$table AS cat
					WHERE
						cat.father_id = '$parent'
				";
			$query = Connection::getInstance()->query($sql);
			foreach($query as $dados){

				?>
				<div class="structure structure<?php echo $level;?>" style="padding-left: 40px; margin-left: <?php echo ($level-1)*40;?>px">
					<span onmouseover="javascript: est_options('<?php echo $dados['id']?>')"><?php echo $dados['name']?></span>

					<?php
					if($level <= 1 AND !empty($dados['type'])){
						echo '<span style="text-transform: lowercase; color: #999999" class="tipo_legivel">('.$dados['type'].')</span>';
					}
					echo '<div class="est_options" style="color: #333333; text-transform: none; font-weight: normal;" id="est_options_'.$dados['id'].'">';
					echo '<a href="adm_main.php?section='.$_GET['section'].'&action=edit_form&w='.$dados['id'].'" style="color: orange;">Editar descrição</a>';
					echo '</div>';

				echo '</div>';

				if($dados['num_sub_nodes'] > 0){
					BuildCategoriasStructure($table, $dados['id'], $level+1);
				}
			}
		}
		BuildCategoriasStructure('taxonomy');

	?>
	</div>
</div>
<p>
	<a href="adm_main.php?section=taxonomy">Voltar</a>
</p>