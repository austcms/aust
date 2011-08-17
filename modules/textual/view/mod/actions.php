<?php
/**
 * ACTIONS
 *
 * Ações especiais
 *
 * Este arquivo contém ações especiais, como deletar conteúdos
 *
 * @since v0.1, 01/01/2009
 */
/**
 *
 * O módulo tem um arquivo específico de actions?
 *
 * Carrega modulo/actions.php se existir
 */
if(is_file( MODULES_DIR .Aust::getInstance()->structureModule($_GET['aust_node']).'/'.MOD_ACTIONS_FILE ) ){
	include( MODULES_DIR .Aust::getInstance()->structureModule($_GET['aust_node']).'/'.MOD_ACTIONS_FILE );
} else {

	/**
	 * Ajusta $block para evitar erros no código
	 */
	if( empty($block) ){
		$block = '';
	}

	/**
	 * Bloquear o acesso a determinado conteúdo
	 */
	if($block == "block"){
		$sql = "UPDATE $section
				SET
					bloqueado='bloqueado'
				WHERE
					id='$w'
				";
		if ($module->connection->exec($sql)){
		?>
			<div style="width: 680px; display: table;">
				<div style="background: red; padding: 15px; text-align: center;">
					<p style="color: white; margin: 0px;">
						O conte&uacute;do foi bloqueado com sucesso! Entretanto, ele n&atilde;o foi deletado.
					</p>

					<?php
					if($escala == "administrador"
					OR $escala == "moderador"
					OR $escala == "webmaster"){
					?>
						<p style="color: white; margin: 0px;">
							<a href="adm_main.php?section=<?php echo $section;?>&action=<?php echo $action;?>&block=delete&w=<?php echo $w; ?><?php echo $addurl;?>" style="text-decoration: underline; color: white;">-> Clique aqui para apagar o conte&uacute;do definitivamente <- </a>
						</p>
					<?php
					}
					?>
				</div>
			</div>
		<?php
		} else {
			echo '<p style="color: red;">Ocorreu um erro desconhecido ao editar as informações do usuário, tente novamente.</p>';
		}
	}
	
	/**
	 * Desbloquear determinado conteúdo
	 */
	else if($block == "unblock"){
		$sql = "UPDATE $section
				SET
					bloqueado='livre',
					publico='sim'
				WHERE
					id='$w'
				";
		if ($module->connection->exec($sql)){
		?>
			<div style="width: 680px; display: table;">
				<div style="background: green; padding: 15px; text-align: center;">
					<p style="color: white; margin: 0px;">
						O conte&uacute;do foi desbloqueado com sucesso! Agora ele aparecer&aacute; no site.
					</p>
				</div>
			</div>
		<?php
		} else {
			echo '<p style="color: red;">Ocorreu um erro desconhecido ao editar as informações do usuário, tente novamente.</p>';
		}
	}

	/**
	 * Deletar determinado(s) conteúdo(s)
	 */
	elseif( !empty($_POST['deletar']) ){
		/*
		 * Identificar tabela que deve ser excluida
		 */

			$itens = $_POST['itens'];
			$c = 0;
			foreach($itens as $key=>$value){
				if($c > 0){
					$where = $where." OR id='".$value."'";
				} else {
					$where = "id='".$value."'";
				}
				$c++;
			}
			$sql = "DELETE FROM
						".$this->module->LeTabelaDaEstrutura($_GET['aust_node'])."
					WHERE
						$where
						";

			if($module->connection->exec($sql)){
				$resultado = TRUE;
			} else {
				$resultado = FALSE;
			}

			if($resultado){
				$status['classe'] = 'sucesso';
				$status['mensagem'] = '<strong>Sucesso: </strong> Os dados foram excluídos com sucesso.';
			} else {
				$status['classe'] = 'insucesso';
				$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao excluir os dados.';
			}
			EscreveBoxMensagem($status);

		} elseif( empty($_POST['itens']) ){
			$status['classe'] = 'alerta';
			$status['mensagem'] = 'Nenhum item selecionado.';
			EscreveBoxMensagem($status);
		}
	
}

?>