<?php
/**
 *
 * Listagem das estruturas cadastradas no sistema
 *
 * Contém o atalho para as estrutura
 * "FECHA BARRA"
 * 
 */
$est = $widget->getStructures();

if(count($est) > 0){
	?>

	<table width="100%" summary="Lista de estruturas do site">
	<col width="160"/>
	<col />
	<tbody>

	<?php
	foreach($est as $key=>$value){

		/**
		 * Inclui módulo apropriado
		 *
		 *
		 */
		include(MODULES_DIR.$value['tipo'].'/'.MOD_CONFIG);
		
		echo '<tr>';
		echo '<td valign="top">';
		echo '<a href="#" class="link_pai_do_est_options" onmouseover="javascript: est_options('.$value['id'].')">'.$value['nome'].'</a>';
		echo '<div class="est_options" id="est_options_'.$value['id'].'">';
		if(is_array($modInfo['actions'])){
			$i = 0;
			foreach($modInfo['actions'] as $opcao=>$opcaonome){
				if($i > 0) echo ', ';
				echo '<a href="adm_main.php?section=content&action='.$opcao.'&aust_node='.$value['id'].'">'.$opcaonome.'</a>';
				$i++;
			}
		}
		echo '</div>';
		echo '</td>';
		echo '</tr>';
	}
	?>
	</tbody>
	</table>
	<?php
} else {
	?>
	<p>Não há estruturas cadastradas. Contacte seu administrador.</p>
	<?php
}
?>

