<?php
/**
* Permissões de Usuários
*/

$conditions = (empty($_POST['id'])) ? array() : array('id' => $_POST['id']);

/**
 * Lê informações do usuário ou grupo selecionado, para que
 * quando for configurar uma permissão, associe ao respectivo ID
 */
if( !empty($_POST['id']) ){
	if( $_POST['tipo'] == 'userTipo'){
		$agente = Connection::getInstance()->find(array(
									'table' => 'admin_groups',
									'conditions' => array(
										'id' => $_POST['id'],
									),
									'fields' => array('id', 'name'),
								), 'all'
		);
	} else {
		$agente = Connection::getInstance()->find(array(
									'table' => 'admins',
									'conditions' => array(
										'id' => $_POST['id'],
									),
									'fields' => array('id', 'name'),
								), 'all'
		);
	}
}

	//pr($agente);
/**
 *
 * CHECKBOXES PARA DEFINIÇÃO DE PERMISSÕES
 *
 * Se nenhuma ação deve ser tomada, escreve checkboxes.
 *
 * Mostra as actions possíveis em cada estrutura (create, edit, listing, etc).
 * Lista as estruturas e abaixo os actions.
 *
 *
 */
if(empty($_GET['action'])){
	?>
	<p>Permissões > <strong><?php echo $agente['0']['name']; ?></strong></p>
	<?php

	$categorias = Connection::getInstance()->find(array(
									'table' => 'taxonomy',
									'conditions' => array(
										//'id' => $_POST['id'],
										'class' => 'structure',
									),
									'fields' => array('id', 'name', 'class', 'type'),
								), 'all'
	);



	/**
	 * CarregaPermissões
	 */

	if($_POST['tipo'] == 'userTipo'){
		$permissoesCondition = array('admin_group_id' => $_POST['id']);
	} elseif($_POST['tipo'] == 'user'){
		$permissoesCondition = array('admin_id' => $_POST['id']);
	}
	$permissoes = Connection::getInstance()->find(array(
									'table' => 'admin_permissions',
									'conditions' => $permissoesCondition,

									'fields' => array('node_id', 'action'),
								), 'all'
	);

	/*
	 * Define os actions principais
	 */
	$actions = Registry::read('default_actions');

	$categoriasChecked = array();
	foreach($permissoes as $value){
		if( !empty($value['action']) )
			$categoriasChecked[ $value['node_id'] ][$value['action']] = true;
	}

	/*
	 * HelperFunction
	 *
	 * Escreve 'checked' nos devidos actions a seguir
	 */
	function isCheckedPermission($structure, $action){
		global $categoriasChecked;

		if( empty($categoriasChecked[$structure]) )
			return false;

		if( !empty($categoriasChecked[$structure][$action])
			AND $categoriasChecked[$structure][$action] == true )
		{
			return 'checked="true"';
		}
		return false;

	}

	foreach($categorias as $value){

		/**
		 * Se for estrutura, deixa negrito
		 */
		?>
		<div class="structure">
		<div class="title">
			<?php echo $value['name']; ?>
		</div>
		<div class="actions">
			<?php
			/*
			 *
			 * ACTIONS POSSÍVEIS
			 *
			 * Lista os actions (create,edit, listing, etc)
			 * com um checkbox em cada um.
			 * 
			 */
			foreach( $actions as $action_name=>$action ){
				?>
				<input
					type="checkbox"
					id="<?php echo $value['name'].'_'.$action; ?>"
					 <?php echo isCheckedPermission($value['id'], $action) ?>
					onchange="alteraPermissao('tipo=<?php echo $_POST['tipo']; ?>&agentid=<?php echo $agente['0']['id']; ?>&categoria=<?php echo $value['id']; ?>&action=<?php echo $action; ?>', this)"
					value="<?php echo $value['name']; ?>" />
					<?php echo $action_name; ?>
				<?php
			}
			?>
		</div>
		</div>
		<?php
	}
/**
 * 'ACTION == altera_permissao'
 *
 * Se é para alterar uma permissão
 *
 *
 *
 */
} elseif($_GET['action'] == 'altera_permissao'){

	/**
	 * Cria permissão
	 */
	if($_POST['value'] == 'true'){
		/**
		 * Se for para uma categoria de usuários (ex.: Administradores, Moderadores, etc)
		 */
		if($_POST['tipo'] == 'userTipo'){
			$agenteTipo = 'admin_group_id';
		/**
		 * Permissão relacionada a um usuário
		 */
		} elseif($_POST['tipo'] == 'user'){
			$agenteTipo = 'admin_id';
		}
		/**
		 * Cria SQL
		 */
		$sql = "INSERT INTO
					admin_permissions
					(".$agenteTipo.",node_id,type,created_on, action)
				VALUES
					('".$_POST['agentid']."','".$_POST['categoria']."','permit','".date("Y-m-d H:i:s")."', '".$_POST['action']."')
				";

	/**
	 * Deleta Permissão
	 */
	} else {
		/**
		 * Se for para uma categoria de usuários (ex.: Administradores, Moderadores, etc)
		 */
		if($_POST['tipo'] == 'userTipo'){
			$agenteTipo = 'admin_group_id';
		/**
		 * Permissão relacionada a um usuário
		 */
		} elseif($_POST['tipo'] == 'user'){
			$agenteTipo = 'admin_id';
		}

		$sql = "DELETE FROM
					admin_permissions
				WHERE
					".$agenteTipo."='".$_POST['agentid']."' AND
					node_id='".$_POST['categoria']."' AND
					action='".$_POST['action']."'
				";
	}

	if(Connection::getInstance()->exec($sql)){
		echo '1';
	} else {
		echo '0';
	}
}

?>
